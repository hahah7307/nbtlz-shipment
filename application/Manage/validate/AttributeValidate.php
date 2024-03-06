<?php

namespace app\Manage\validate;

use think\Validate;

class AttributeValidate extends Validate
{
    protected $rule = [
        'parent_id'     =>  'require',
        'name'          =>  'require',
        'code'          =>  'require',
    ];

    protected $message = [
        
    ];

    protected $field = [
        'parent_id'     =>  '上级类目',
        'name'          =>  '名称',
        'code'          =>  '参考码',
    ];

    protected $scene = [
        'add'           =>  ['parent_id', 'name', 'code'],
        'edit'          =>  ['parent_id', 'name', 'code'],
    ];
}
