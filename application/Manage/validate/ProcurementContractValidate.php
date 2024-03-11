<?php

namespace app\Manage\validate;

use think\Validate;

class ProcurementContractValidate extends Validate
{
    protected $rule = [
        'contract_no'           =>  'require',
        'supplier_code'         =>  'require',
        'created_id'            =>  'require',
    ];

    protected $message = [
        
    ];

    protected $field = [
        'contract_no'           =>  '合同编号',
        'supplier_code'         =>  '产品数量',
        'created_id'            =>  '采购人',
    ];

    protected $scene = [
        'add'           =>  ['contract_no', 'supplier_code', 'created_id'],
        'edit'          =>  ['contract_no', 'supplier_code'],
    ];
}
