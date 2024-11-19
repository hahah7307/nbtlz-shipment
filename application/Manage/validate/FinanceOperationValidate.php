<?php

namespace app\Manage\validate;

use think\Validate;

class FinanceOperationValidate extends Validate
{
    protected $rule = [
        'month'             =>  'require',
        'sku'               =>  'require',
        'total'             =>  'require',
        'currency'          =>  'require',
        'content'           =>  'require',
    ];

    protected $message = [

    ];

    protected $field = [
        'month'             =>  '支付月份',
        'sku'               =>  '仓库SKU',
        'total'             =>  '总费用',
        'currency'          =>  '支付币种',
        'content'           =>  '备注',
        'type'              =>  '类型'
    ];

    protected $scene = [
        'add'           =>  ['month', 'sku', 'total', 'currency', 'content'],
        'edit'          =>  ['month', 'sku', 'total', 'currency', 'content'],
    ];
}
