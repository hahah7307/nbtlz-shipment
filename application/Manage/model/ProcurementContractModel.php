<?php

namespace app\Manage\model;

use think\exception\DbException;
use think\Model;

class ProcurementContractModel extends Model
{
    const STATE_ACTIVE = 1;

    protected $name = 'procurement_contract';

    protected $resultSetType = 'collection';

    protected $insert = ['created_at', 'updated_at'];

    protected $update = ['updated_at'];

    protected function setCreatedAtAttr()
    {
        return date('Y-m-d H:i:s');
    }

    protected function setUpdatedAtAttr()
    {
        return date('Y-m-d H:i:s');
    }

    public function sku(): \think\model\relation\HasMany
    {
        return $this->hasMany('ProcurementContractSkuModel', 'contract_id', 'id');
    }

    public function account(): \think\model\relation\HasOne
    {
        return $this->hasOne('AccountModel', 'id', 'created_id');
    }

    /**
     * @throws DbException
     */
    static public function createContract(): string
    {
        $y = date('y');
        $m = date('m');
        $prefix = $y . 'TI' . $m;
        $contractObj = new ProcurementContractModel();
        $contractList = $contractObj->where(['contract_no' => ['like', $prefix . '%']])->order('contract_no desc')->select();
        $num = empty($contractList) ? 0 : intval(substr($contractList[0]['contract_no'], 6,9));
        $num2str = sprintf("%03d", $num + 1);

        return $prefix . $num2str;
    }
}
