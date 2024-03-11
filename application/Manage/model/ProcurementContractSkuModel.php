<?php

namespace app\Manage\model;

use think\Model;

class ProcurementContractSkuModel extends Model
{
    const STATE_ACTIVE = 1;

    protected $name = 'procurement_contract_sku';

    protected $resultSetType = 'collection';

    public function contract(): \think\model\relation\HasOne
    {
        return $this->hasOne('ProcurementContractModel', 'id', 'contract_id');
    }

    public function sku(): \think\model\relation\HasOne
    {
        return $this->hasOne('SkuModel', 'id', 'sku_id');
    }
}
