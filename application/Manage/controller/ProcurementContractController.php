<?php
namespace app\Manage\controller;

use app\Manage\model\AttributeModel;
use app\Manage\model\ProcurementContractModel;
use app\Manage\model\SkuModel;
use app\Manage\validate\ProcurementContractValidate;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Session;
use think\Config;

class ProcurementContractController extends BaseController
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
            $where['contract_no|product_sku|product_name'] = ['like', '%' . strtoupper($keyword) . '%'];
        } else {
            $where = [];
        }

        $page_num = $this->request->get('page_num', Config::get('PAGE_NUM'));
        $this->assign('page_num', $page_num);

        $list = new ProcurementContractModel();
        $list = $list->with(['sku'])->where($where)->order('id asc')->paginate($page_num, false, ['query' => ['keyword' => $keyword, 'page_num' => $page_num]]);
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
            $post['contract_no'] = ProcurementContractModel::createContract();
            $dataValidate = new ProcurementContractValidate();
            if ($dataValidate->scene('add')->check($post)) {
                $model = new ProcurementContractModel();
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
            $skuObj = new SkuModel();
            $sku = $skuObj->where(['state' => SkuModel::STATE_ACTIVE])->order('sku asc')->select();
            $this->assign('sku', $sku);

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
            $skuObj = new SkuModel();
            $sku = $skuObj->find($post['sku_id']);
            $post['sku_id'] = $sku['id'];
            $post['product_sku'] = $sku['sku'];
            $dataValidate = new ProcurementContractValidate();
            if ($dataValidate->scene('edit')->check($post)) {
                $model = new ProcurementContractModel();
                if ($model->allowField(true)->save($post, ['id' => $id])) {
                    echo json_encode(['code' => 1, 'msg' => '修改成功']);
                    exit;
                } else {
                    echo json_encode(['code' => 0, 'msg' => '修改失败，请重试']);
                    exit;
                }
            } else {
                echo json_encode(['code' => 0, 'msg' => $dataValidate->getError()]);
                exit;
            }
        } else {
            $skuObj = new SkuModel();
            $sku = $skuObj->where(['state' => SkuModel::STATE_ACTIVE])->order('sku asc')->select();
            $this->assign('sku', $sku);

            $info = ProcurementContractModel::get(['id' => $id,]);
            $this->assign('info', $info);

            return view();
        }
    }

    // 状态切换
    public function status()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $category = ProcurementContractModel::get($post['id']);
            $category['state'] = $category['state'] == ProcurementContractModel::STATE_ACTIVE ? 0 : ProcurementContractModel::STATE_ACTIVE;
            $category->save();
            echo json_encode(['code' => 1, 'msg' => '操作成功']);
            exit;
        } else {
            echo json_encode(['code' => 0, 'msg' => '异常操作']);
            exit;
        }
    }
}