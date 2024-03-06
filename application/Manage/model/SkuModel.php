<?php

namespace app\Manage\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Model;

class SkuModel extends Model
{
    const STATE_ACTIVE = 1;

    protected $name = 'sku';

    protected $resultSetType = 'collection';

    protected $insert = ['created_at', 'updated_at'];

    protected $update = ['updated_at'];

    protected function setCreatedAtAttr()
    {
        return date('Y-m-d H:i:s');
    }

    protected function setUpdatedAtAttr()
    {
        return date('Y-m-d H:i:s');
    }

    public function category(): \think\model\relation\HasOne
    {
        return $this->hasOne('CategoryModel', 'id', 'category_id');
    }

    public function attribute(): \think\model\relation\HasOne
    {
        return $this->hasOne('AttributeModel', 'id', 'attribute_id');
    }

    /**
     * @throws DbException
     */
    static public function createSku($category_id, $attribute_id): string
    {
        $category = CategoryModel::get($category_id);
        $attribute = AttributeModel::get($attribute_id);
        $num = count(self::all(['sku' => ['like', $category['code'] . '%']]));
        $num2str = sprintf("%03d", $num + 1);

        return $category['code'] . $num2str . $attribute['code'];
    }
}
