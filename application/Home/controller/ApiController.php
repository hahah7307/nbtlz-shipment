<?php
namespace app\Home\controller;

use app\Manage\model\CategoryModel;
use app\Manage\model\SkuModel;
use think\exception\DbException;

class ApiController extends BaseController
{
    /**
     * @throws DbException
     */
    public function getSkuCategory($page, $pageSize)
    {
        $ip = get_real_ip();
        if (!in_array($ip, ['127.0.0.1', '122.227.159.146', '139.224.106.228'])) {
            echo json_encode(['code' => 100, 'msg' => '未被允许的请求！']);
            exit();
        }

        $skuObj = new SkuModel();
        $list = $skuObj->with(['category'])->order('id asc')->paginate($pageSize, false, ['query' => ['page_num' => $pageSize, 'page' => $page]])->toArray();
        $category = new CategoryModel();
        foreach ($list['data'] as $key => $item) {
            $cate = $category->where(['id' => $item['category']['parent_id']])->find();
            $list['data'][$key]['cate'] = $cate['name'];
        }

        echo json_encode(['code' => 200, 'data' => $list['data']]);
        exit();
    }
}
