<?php

namespace app\Manage\validate;

use think\Validate;

class WarehouseValidate extends Validate
{
    protected $rule = [
        'port_id'       =>  'require',
        'name'          =>  'require',
        'code'          =>  'require',
    ];

    protected $message = [
        
    ];

    protected $field = [
        'port_id'       =>  '仓库名称',
        'name'          =>  '仓库名称',
        'code'          =>  '短描述',
    ];

    protected $scene = [
        'add'           =>  ['port_id', 'name', 'code'],
        'edit'          =>  ['port_id', 'name', 'code'],
    ];
}
