<?php
namespace app\Manage\controller;

use app\Manage\model\CategoryModel;
use app\Manage\validate\CategoryValidate;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Session;
use think\Config;

class CategoryController extends BaseController
{
    // 节点
    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function index(): \think\response\View
    {
        $list = CategoryModel::category_format();
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
            $parent = CategoryModel::get(['id' => $id]);
            $post['level'] = ++ $parent['level'];
            $dataValidate = new CategoryValidate();
            if ($dataValidate->scene('add')->check($post)) {
                $model = new CategoryModel();
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
            $category = CategoryModel::category_format(CategoryModel::STATE_ACTIVE);
            $this->assign('category', $category);
            $this->assign('id', $id);

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
            if ($post['parent_id'] == $id) {
                echo json_encode(['code' => 0, 'msg' => '无法选择自己作为父级品类']);
                exit;
            }
            $parent = CategoryModel::get(['id' => $post['parent_id']]);
            if ($parent['level'] == 3) {
                echo json_encode(['code' => 0, 'msg' => '请选择正确的父级品类']);
                exit;
            } else {
                $post['level'] = ++ $parent['level'];
            }
            $dataValidate = new CategoryValidate();
            if ($dataValidate->scene('edit')->check($post)) {
                $model = new CategoryModel();
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
            $category = CategoryModel::category_format(CategoryModel::STATE_ACTIVE);
            $this->assign('category', $category);
            $info = CategoryModel::get(['id' => $id,]);
            $this->assign('info', $info);

            return view();
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
            $node = CategoryModel::get($post['id']);
            if ($node->delete()) {
                echo json_encode(['code' => 1, 'msg' => '操作成功']);
                exit;
            } else {
                echo json_encode(['code' => 0, 'msg' => '操作失败，请重试']);
                exit;
            }
        } else {
            echo json_encode(['code' => 0, 'msg' => '异常操作']);
            exit;
        }
    }

    // 排序

    /**
     * @throws \Exception
     */
    public function sort()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $node = new CategoryModel();
            $data = [];
            foreach ($post['sort'] as $k => $v) {
                $data[] = ['id' => $k, 'sort' => $v];
            }
            $node->saveAll($data);
            echo json_encode(['code' => 1, 'msg' => '操作成功']);
            exit;
        } else {
            echo json_encode(['code' => 0, 'msg' => '异常操作']);
            exit;
        }
    }

    // 状态切换
    public function status()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $category = CategoryModel::get($post['id']);
            $category['state'] = $category['state'] == CategoryModel::STATE_ACTIVE ? 0 : CategoryModel::STATE_ACTIVE;
            $category->save();
            echo json_encode(['code' => 1, 'msg' => '操作成功']);
            exit;
        } else {
            echo json_encode(['code' => 0, 'msg' => '异常操作']);
            exit;
        }
    }
}
