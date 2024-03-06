<?php

namespace app\Manage\validate;

use think\Validate;

class SkuValidate extends Validate
{
    protected $rule = [
        'sku'           =>  'require',
        'name'          =>  'require',
        'created_id'    =>  'require',
        'category_id'   =>  'require',
        'attribute_id'  =>  'require',
    ];

    protected $message = [
        
    ];

    protected $field = [
        'sku'           =>  'SKU',
        'name'          =>  '名称',
        'created_id'    =>  '领取人',
        'category_id'   =>  '所属类目',
        'attribute_id'  =>  '所属属性',
    ];

    protected $scene = [
        'add'           =>  ['sku', 'name', 'created_id', 'category_id', 'attribute_id'],
        'edit'          =>  ['sku', 'name', 'created_id', 'category_id', 'attribute_id'],
    ];
}
