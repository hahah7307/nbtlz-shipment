<?php
namespace app\Manage\controller;

use app\Manage\model\AccountModel;
use app\Manage\model\AlibabaCloudCredentialsWrapper;
use app\Manage\model\AttributeModel;
use app\Manage\model\CategoryModel;
use app\Manage\model\FilesModel;
use app\Manage\model\SkuInstructionsModel;
use app\Manage\model\SkuModel;
use app\Manage\validate\FilesValidate;
use app\Manage\validate\SkuValidate;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Session;
use think\Config;

class SkuController extends BaseController
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
            $where['sku|name'] = ['like', '%' . strtoupper($keyword) . '%'];
        } else {
            $where = [];
        }

        // 查看权限
        $access_ids = AccountModel::account_access_ids();
        $where['purchaser_id'] = ['in', $access_ids];

        $state = $this->request->get('state');
        $this->assign('state', $state);
        if ($state != "") {
            $where['state'] = $state;
        }

        $order = $this->request->get('order');
        $this->assign('order', $order);
        if ($order == "sku") {
            $orderBy = 'sku asc';
        } elseif ($order == "time") {
            $orderBy = 'created_at desc';
        } else {
            $orderBy = 'sku asc';
        }

        $page_num = $this->request->get('page_num', Config::get('PAGE_NUM'));
        $this->assign('page_num', $page_num);

        $list = new SkuModel();
        $list = $list->with(['category.parent', 'attribute', 'purchaser'])->where($where)->order($orderBy)->paginate($page_num, false, ['query' => ['keyword' => $keyword, 'state' => $state, 'page_num' => $page_num]]);
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
    public function add($id = 0)
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $post['sku'] = SkuModel::createSku($post['category_id'], $post['attribute_id'], $post['is_new'], $post['code']);
            $post['sku_origin'] = substr($post['sku'], 0, 8);
            $post['created_id'] = Session::get(Config::get('USER_LOGIN_FLAG'));
            if ($post['box'] > 1) {
                $addData = [];
                for ($i = 0; $i < $post['box']; $i ++) {
                    $addData[] = [
                        'category_id'   =>  $post['category_id'],
                        'attribute_id'  =>  $post['attribute_id'],
                        'sku'           =>  $post['sku'] . '-' . ($i + 1),
                        'sku_origin'    =>  $post['sku_origin'],
                        'name'          =>  $post['name'],
                        'description'   =>  $post['description'],
                        'created_id'    =>  $post['created_id'],
                        'created_at'    =>  date('Y-m-d H:i:s'),
                        'updated_at'    =>  date('Y-m-d H:i:s'),
                        'purchaser_id'  =>  $post['purchaser_id']
                    ];
                }
                $model = new SkuModel();
                if ($model->insertAll($addData)) {
                    echo json_encode(['code' => 1, 'msg' => '添加成功']);
                    exit;
                } else {
                    echo json_encode(['code' => 0, 'msg' => '添加失败，请重试']);
                    exit;
                }
            } else {
                $dataValidate = new SkuValidate();
                if ($dataValidate->scene('add')->check($post)) {
                    $model = new SkuModel();
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
            }
        } else {
            $category = CategoryModel::category_format(SkuModel::STATE_ACTIVE, [], 1);
            $this->assign('category', $category);
            $attribute = AttributeModel::attribute_format(AttributeModel::STATE_ACTIVE, [], 1);
            $this->assign('attribute', $attribute);
            $this->assign('id', $id);
            $this->assign('purchaser', SkuModel::getActivePurchaser());

            return view();
        }
    }

    // 状态切换
    public function status()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $category = SkuModel::get($post['id']);
            $category['state'] = $category['state'] == SkuModel::STATE_ACTIVE ? 0 : SkuModel::STATE_ACTIVE;
            $category->save();
            echo json_encode(['code' => 1, 'msg' => '操作成功']);
            exit;
        } else {
            echo json_encode(['code' => 0, 'msg' => '异常操作']);
            exit;
        }
    }

    // 角色添加

    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function create($id = 0)
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $post['sku'] = SkuModel::manualSku($post['category_id'], $post['attribute_id'], $post['index']);
            $model = new SkuModel();
            $sku = $model->where(['sku_origin|sku' => $post['sku']])->find();
            if (!empty($sku)) {
                echo json_encode(['code' => 0, 'msg' => '该货号已存在！']);
                exit;
            }
            $post['sku_origin'] = substr($post['sku'], 0, 8);
            $post['created_id'] = Session::get(Config::get('USER_LOGIN_FLAG'));
            if ($post['box'] > 1) {
                $addData = [];
                for ($i = 0; $i < $post['box']; $i ++) {
                    $addData[] = [
                        'category_id'   =>  $post['category_id'],
                        'attribute_id'  =>  $post['attribute_id'],
                        'sku'           =>  $post['sku'] . '-' . ($i + 1),
                        'sku_origin'    =>  $post['sku_origin'],
                        'name'          =>  $post['name'],
                        'description'   =>  $post['description'],
                        'created_id'    =>  $post['created_id'],
                        'created_at'    =>  date('Y-m-d H:i:s'),
                        'updated_at'    =>  date('Y-m-d H:i:s')
                    ];
                }
                if ($model->insertAll($addData)) {
                    echo json_encode(['code' => 1, 'msg' => '添加成功']);
                    exit;
                } else {
                    echo json_encode(['code' => 0, 'msg' => '添加失败，请重试']);
                    exit;
                }
            } else {
                $dataValidate = new SkuValidate();
                if ($dataValidate->scene('add')->check($post)) {
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
            }
        } else {
            $category = CategoryModel::category_format(SkuModel::STATE_ACTIVE, [], 1);
            $this->assign('category', $category);
            $attribute = AttributeModel::attribute_format(AttributeModel::STATE_ACTIVE, [], 1);
            $this->assign('attribute', $attribute);
            $this->assign('id', $id);

            return view();
        }
    }

    // 说明书
    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function instructions($id): \think\response\View
    {
        $list = new SkuInstructionsModel();
        $list = $list->where(['sku_id' => $id])->select();
        $this->assign('list', $list);
        $this->assign('id', $id);

        return view();
    }

    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function createTmpUrl($id)
    {

        if ($this->request->isPost()) {
            $instructionsObj = new SkuInstructionsModel();
            $instructions = $instructionsObj->find($id);
            $signUrl = AlibabaCloudCredentialsWrapper::signUrl($instructions['file_path']);

            $newData = [
                'file_tmp_url'      =>  $signUrl['url'],
                'file_tmp_expire'   =>  $signUrl['expire'],
            ];
            $model = new SkuInstructionsModel();
            if ($model->allowField(true)->save($newData, ['id' => $id])) {
                echo json_encode(['code' => 1, 'msg' => '生成成功']);
            } else {
                echo json_encode(['code' => 0, 'msg' => '生成失败，请重试']);
            }
        } else {
            echo json_encode(['code' => 0, 'msg' => '操作异常！']);
            exit;
        }
    }

    // 删除
    /**
     * @throws DbException
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $block = SkuInstructionsModel::get($post['id']);
            if ($block->delete()) {
                echo json_encode(['code' => 1, 'msg' => '操作成功']);
            } else {
                echo json_encode(['code' => 0, 'msg' => '操作失败，请重试']);
            }
        } else {
            echo json_encode(['code' => 0, 'msg' => '异常操作']);
        }
        exit;
    }
}
