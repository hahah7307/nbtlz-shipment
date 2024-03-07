<?php

namespace app\Manage\validate;

use think\Validate;

class PortValidate extends Validate
{
    protected $rule = [
        'name'          =>  'require',
        'code'          =>  'require',
        'type'          =>  'require',
        'state'         =>  'require',
    ];

    protected $message = [
        
    ];

    protected $field = [
        'name'          =>  '港口名称',
        'code'          =>  '港口标识',
        'type'          =>  '港口类型',
        'state'         =>  '状态',
    ];

    protected $scene = [
        'add'           =>  ['name', 'code', 'type', 'state'],
        'edit'          =>  ['name', 'code', 'type', 'state'],
    ];
}
