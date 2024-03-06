<?php
namespace app\Manage\controller;

use app\Manage\model\AttributeModel;
use app\Manage\validate\AttributeValidate;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Session;
use think\Config;

class AttributeController extends BaseController
{
    // 节点
    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function index(): \think\response\View
    {
        $list = AttributeModel::attribute_format();
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
            $parent = AttributeModel::get(['id' => $id]);
            $post['level'] = ++ $parent['level'];
            $dataValidate = new AttributeValidate();
            if ($dataValidate->scene('add')->check($post)) {
                $model = new AttributeModel();
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
            $attribute = AttributeModel::attribute_format(AttributeModel::STATE_ACTIVE);
            $this->assign('attribute', $attribute);
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
            $parent = AttributeModel::get(['id' => $post['parent_id']]);
            if ($parent['level'] == 3) {
                echo json_encode(['code' => 0, 'msg' => '请选择正确的父级品类']);
                exit;
            } else {
                $post['level'] = ++ $parent['level'];
            }
            $dataValidate = new AttributeValidate();
            if ($dataValidate->scene('edit')->check($post)) {
                $model = new AttributeModel();
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
            $attribute = AttributeModel::attribute_format(AttributeModel::STATE_ACTIVE);
            $this->assign('attribute', $attribute);
            $info = AttributeModel::get(['id' => $id,]);
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
            $node = AttributeModel::get($post['id']);
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
            $node = new AttributeModel();
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
            $attribute = AttributeModel::get($post['id']);
            $attribute['state'] = $attribute['state'] == AttributeModel::STATE_ACTIVE ? 0 : AttributeModel::STATE_ACTIVE;
            $attribute->save();
            echo json_encode(['code' => 1, 'msg' => '操作成功']);
            exit;
        } else {
            echo json_encode(['code' => 0, 'msg' => '异常操作']);
            exit;
        }
    }
}
