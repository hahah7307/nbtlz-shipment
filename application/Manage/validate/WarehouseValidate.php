<?php

namespace app\Manage\validate;

use think\Validate;

class WarehouseValidate extends Validate
{
    protected $rule = [
        'name'          =>  'require',
        'short'         =>  'require',
    ];

    protected $message = [
        
    ];

    protected $field = [
        'name'          =>  '仓库名称',
        'short'         =>  '短描述',
    ];

    protected $scene = [
        'add'           =>  ['name', 'short'],
        'edit'          =>  ['name', 'short'],
    ];
}
