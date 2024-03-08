<?php

namespace app\Manage\validate;

use think\Validate;

class ExportValidate extends Validate
{
    protected $rule = [
        'export_no'             =>  'require',
        'from_port'             =>  'require',
        'to_port'               =>  'require',
        'state'                 =>  'require',
        'created_id'            =>  'require',
    ];

    protected $message = [
        
    ];

    protected $field = [
        'export_no'             =>  '外销编号',
        'from_port'             =>  '始发港',
        'to_port'               =>  '目的港',
        'state'                 =>  '状态',
        'created_id'            =>  '跟单人员',
    ];

    protected $scene = [
        'add'           =>  ['export_no', 'from_port', 'to_port', 'state', 'created_id'],
        'edit'          =>  ['export_no', 'from_port', 'to_port', 'state'],
    ];
}
