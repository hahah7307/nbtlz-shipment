<?php

namespace app\Manage\validate;

use think\Validate;

class ProcurementContractValidate extends Validate
{
    protected $rule = [
        'state'                 =>  'require',
        'contract_no'           =>  'require',
        'contract_no_origin'    =>  'require',
        'supplier_code'         =>  'require',
        'created_id'            =>  'require',
    ];

    protected $message = [
        
    ];

    protected $field = [
        'state'                 =>  '出口国家',
        'contract_no'           =>  '合同编号',
        'contract_no_origin'    =>  '合同编号起源',
        'supplier_code'         =>  '供应商代码',
        'created_id'            =>  '采购人',
    ];

    protected $scene = [
        'add'           =>  ['state', 'contract_no', 'contract_no_origin', 'supplier_code', 'created_id'],
        'edit'          =>  ['sku', 'product_quantity'],
    ];
}
