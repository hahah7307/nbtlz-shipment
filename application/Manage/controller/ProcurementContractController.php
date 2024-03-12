<?php
namespace app\Manage\controller;

use app\Manage\model\AdminUserRoleModel;
use app\Manage\model\ProcurementContractModel;
use app\Manage\model\ProcurementContractSkuModel;
use app\Manage\model\SkuModel;
use app\Manage\validate\ProcurementContractValidate;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
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
            $where['contract_no|supplier_code'] = ['like', '%' . strtoupper($keyword) . '%'];
        } else {
            $where = [];
        }

        $userRole = new AdminUserRoleModel();
        $role = $userRole->where(['user_id' => Session::get(Config::get('USER_LOGIN_FLAG'))])->column('role_id');
        if (in_array(7, $role) && Session::get(Config::get('USER_LOGIN_FLAG')) != 14) {
            $where['created_id'] = Session::get(Config::get('USER_LOGIN_FLAG'));
        }

        $page_num = $this->request->get('page_num', Config::get('PAGE_NUM'));
        $this->assign('page_num', $page_num);

        $list = new ProcurementContractModel();
        $list = $list->with(['sku.sku', 'account'])->where($where)->order('id asc')->paginate($page_num, false, ['query' => ['keyword' => $keyword, 'page_num' => $page_num]]);
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

            Db::startTrans();
            try {
                $contract['contract_no'] = ProcurementContractModel::createContract();
                $contract['supplier_code'] = $post['supplier_code'];
                $contract['created_id'] = Session::get(Config::get('USER_LOGIN_FLAG'));
                $dataValidate = new ProcurementContractValidate();
                if ($dataValidate->scene('add')->check($contract)) {
                    $model = new ProcurementContractModel();
                    if ($model->allowField(true)->save($contract)) {
                        $contract_id = $model->getLastInsID();
                        $skuList = [];
                        $skuObj = new SkuModel();
                        foreach ($post['sku'] as $k => $item) {
                            $product_sku = $item;
                            $product_quantity = $post['product_quantity'][$k];
                            if (empty($product_sku) xor empty(intval($product_quantity))) {
                                throw new Exception('请先检查没有填写完整的SKU或数量');
                            }
                            if (!empty($product_sku)) {
                                if ($skuObj->where(['sku' => $product_sku, 'state' => SkuModel::STATE_ACTIVE])->count() < 1) {
                                    throw new Exception('提交了不存在的SKU，请重试');
                                }
                                $skuList[] = [
                                    'contract_id'       =>  $contract_id,
                                    'sku'               =>  $product_sku,
                                    'product_quantity'  =>  $product_quantity
                                ];
                            }
                        }
                        $contractSkuObj = new ProcurementContractSkuModel();
                        if ($contractSkuObj->insertAll($skuList)) {
                            Db::commit();
                            echo json_encode(['code' => 1, 'msg' => '添加成功']);
                            exit();
                        } else {
                            throw new Exception('添加失败，请重试');
                        }
                    } else {
                        throw new Exception('添加失败，请重试');
                    }
                } else {
                    throw new Exception($dataValidate->getError());
                }
            } catch (\Exception $e) {
                Db::rollback();
                echo json_encode(['code' => 0, 'msg' => $e->getMessage()]);
                exit();
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
