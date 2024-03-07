<?php

namespace app\Manage\validate;

use think\Validate;

class ProcurementContractValidate extends Validate
{
    protected $rule = [
        'contract_no'           =>  'require',
        'sku_id'                =>  'require',
        'product_sku'           =>  'require',
        'product_name'          =>  'require',
        'product_quantity'      =>  'require',
        'created_id'            =>  'require',
    ];

    protected $message = [
        
    ];

    protected $field = [
        'contract_no'           =>  '合同编号',
        'sku_id'                =>  'SKUID',
        'product_sku'           =>  '产品SKU',
        'product_name'          =>  '产品名称',
        'product_quantity'      =>  '产品数量',
        'created_id'            =>  '采购人',
    ];

    protected $scene = [
        'add'           =>  ['contract_no', 'sku_id', 'product_sku', 'product_name', 'product_quantity', 'created_id'],
        'edit'          =>  ['contract_no', 'sku_id', 'product_sku', 'product_name', 'product_quantity'],
    ];
}
