<?php

namespace app\Manage\model;

use think\Model;

class ShipBatchModel extends Model
{
    protected $name = 'ecang_ship_batch';

    protected $resultSetType = 'collection';

    public function packingInfo(): \think\model\relation\HasMany
    {
        return $this->hasMany('ShipBatchPackingInfoModel', 'ship_batch_id', 'id');
    }

    public function packingReceiving(): \think\model\relation\HasMany
    {
        return $this->hasMany('ShipBatchPackingReceivingModel', 'ship_batch_id', 'id');
    }

    public function dgOrder(): \think\model\relation\HasMany
    {
        return $this->hasMany('ShipBatchDgOrderInfoModel', 'ship_batch_id', 'id');
    }

    public function productInfo(): \think\model\relation\HasMany
    {
        return $this->hasMany('ShipBatchProductInfoModel', 'ship_batch_id', 'id');
    }

    public function receivingPurchase(): \think\model\relation\HasMany
    {
        return $this->hasMany('ShipBatchReceivingPurchaseModel', 'ship_batch_id', 'id');
    }
}
