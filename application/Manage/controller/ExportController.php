<?php
namespace app\Manage\controller;

use app\Manage\model\AccountModel;
use app\Manage\model\ExportLogModel;
use app\Manage\model\ExportModel;
use app\Manage\model\PortModel;
use app\Manage\model\SkuModel;
use app\Manage\validate\ExportValidate;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Session;
use think\Config;

class ExportController extends BaseController
{
    // 节点
    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function index(): \think\response\View
    {
        $keyword = $this->request->get('keyword', '', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        if ($keyword) {
            $where['export_no|bol_no|box_no|warehouse'] = ['like', '%' . strtoupper($keyword) . '%'];
        } else {
            $where = [];
        }

        $state = $this->request->get('state', 4);
        $this->assign('state', $state);
        if ($state != '-1') {
            $where['state'] = $state;
        }

        $page_num = $this->request->get('page_num', Config::get('PAGE_NUM'));
        $this->assign('page_num', $page_num);

        $list = new ExportModel();
        $list = $list->with(['fromPort', 'toPort', 'account'])->where($where)->order('id asc')->paginate($page_num, false, ['query' => ['keyword' => $keyword, 'page_num' => $page_num]]);
        $this->assign('list', $list);

        Session::set(Config::get('BACK_URL'), $this->request->url(), 'manage');
        return view();
    }

    // 角色添加

    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $skuObj = new SkuModel();
            $sku = $skuObj->find($post['sku_id']);
            $post['sku_id'] = $sku['id'];
            $post['product_sku'] = $sku['sku'];
            $post['created_id'] = Session::get(Config::get('USER_LOGIN_FLAG'));
            $post['export_no'] = ExportModel::createNewExport();
            $post['state'] = ExportModel::STATE_ACTIVE;
            $dataValidate = new ExportValidate();
            if ($dataValidate->scene('add')->check($post)) {
                $model = new ExportModel();
                if ($model->allowField(true)->save($post)) {
                    echo json_encode(['code' => 1, 'msg' => '添加成功']);
                    exit;
                } else {
                    echo json_encode(['code' => 0, 'msg' => '添加失败，请重试']);
                    exit;
                }
            } else {
                echo json_encode(['code' => 0, 'msg' => $dataValidate->getError()]);
                exit;
            }
        } else {
            $portObj = new PortModel();
            $fromPort = $portObj->where(['state' => PortModel::STATE_ACTIVE, 'type' => PortModel::TYPE_FROM])->select();
            $this->assign('fromPort', $fromPort);

            $toPort = $portObj->where(['state' => PortModel::STATE_ACTIVE, 'type' => PortModel::TYPE_TO])->select();
            $this->assign('toPort', $toPort);

            return view();
        }
    }

    // 编辑

    /**
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $exportObj = new ExportModel();
            $export = $exportObj->find($id);

            if ($export['state'] == $post['state'] && empty($post['abnormal'])) {
                echo json_encode(['code' => 0, 'msg' => '请选择新状态或者发生的异常']);
                exit;
            }
            if ($export['state'] != $post['state'] && !empty($post['abnormal'])) {
                echo json_encode(['code' => 0, 'msg' => '发生异常时不可更换外销编号状态']);
                exit;
            }

            // 记录异常日志
            if (!empty($post['abnormal'])) {
                ExportLogModel::createNewLog($post, $id);
                echo json_encode(['code' => 1, 'msg' => '修改成功']);
                exit;
            }

            // 非异常情况
            if ($post['state'] == 2) {
                $saveData = [
                    'container_date'    =>  !empty($post['time']) ? $post['time'] : date('Y-m-d H:i:s'),
                ];
            } elseif ($post['state'] == 3) {
                if (empty($post['warehouse'])) {
                    echo json_encode(['code' => 0, 'msg' => '请先填写目的仓']);
                    exit;
                }
                $saveData = [
                    'compartment_date'  =>  !empty($post['time']) ? $post['time'] : date('Y-m-d H:i:s'),
                    'warehouse'         =>  $post['warehouse'],
                    'export_no'         =>  explode('-', $export['export_no'])[0] . '-' . $post['warehouse']
                ];
            } elseif ($post['state'] == 4) {
                if (empty($post['bol_no']) || empty($post['box_no']) || empty($post['eta']) || empty($post['shipping_company'])) {
                    echo json_encode(['code' => 0, 'msg' => '请先填写提单号、箱号和预计到港时间']);
                    exit;
                }
                $saveData = [
                    'bol_no'            =>  $post['bol_no'],
                    'box_no'            =>  $post['box_no'],
                    'shipping_company'  =>  $post['shipping_company'],
                    'shipping_date'     =>  !empty($post['time']) ? $post['time'] : date('Y-m-d H:i:s'),
                    'eta'               =>  $post['eta'],
                ];
            } elseif ($post['state'] == 5) {
                $saveData = [
                    'arrival_date'      =>  !empty($post['time']) ? $post['time'] : date('Y-m-d H:i:s'),
                ];
            } elseif ($post['state'] == 6) {
                $saveData = [
                    'unloading_date'    =>  !empty($post['time']) ? $post['time'] : date('Y-m-d H:i:s'),
                    'etd'               =>  $post['etd'],
                ];
            } elseif ($post['state'] == 7) {
                $saveData = [
                    'dispatch_date'     =>  !empty($post['time']) ? $post['time'] : date('Y-m-d H:i:s'),
                ];
            } elseif ($post['state'] == 8) {
                $saveData = [
                    'grounding_date'    =>  !empty($post['time']) ? $post['time'] : date('Y-m-d H:i:s'),
                ];
            }
            $saveData['state'] = $post['state'];
            $saveData['content'] = $post['content'];
            $saveData['procure_group'] = implode(',', $post['procure']);

            $model = new ExportModel();
            if ($model->save($saveData, ['id' => $id])) {
                ExportLogModel::createNewLog($post, $id);
                echo json_encode(['code' => 1, 'msg' => '修改成功']);
                exit;
            } else {
                echo json_encode(['code' => 0, 'msg' => '修改失败，请重试']);
                exit;
            }
        } else {
            $skuObj = new SkuModel();
            $sku = $skuObj->where(['state' => SkuModel::STATE_ACTIVE])->order('sku asc')->select();
            $this->assign('sku', $sku);

            $exportObj = new ExportModel();
            $info = $exportObj->with(['fromPort', 'toPort'])->find($id);
            $this->assign('info', $info);
            $this->assign('abnormal', Config::get('EXPORT_ABNORMAL'));
            $this->assign('procure', AccountModel::get_all_procure());
            $this->assign('procure_group', explode(',', $info['procure_group']));

            return view();
        }
    }

    /**
     * @throws DbException
     */
    public function log($id): \think\response\View
    {
        $where['export_id'] = $id;

        $list = new ExportLogModel();
        $list = $list->with(['export', 'account'])->where($where)->order('id desc')->paginate(Config::get('PAGE_NUM'));
        $this->assign('list', $list);
        $this->assign('abnormal', Config::get('EXPORT_ABNORMAL'));

        return view();
    }

    // 状态切换
    public function status()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $category = ExportModel::get($post['id']);
            $category['state'] = $category['state'] == ExportModel::STATE_ACTIVE ? 0 : ExportModel::STATE_ACTIVE;
            $category->save();
            echo json_encode(['code' => 1, 'msg' => '操作成功']);
            exit;
        } else {
            echo json_encode(['code' => 0, 'msg' => '异常操作']);
            exit;
        }
    }
}