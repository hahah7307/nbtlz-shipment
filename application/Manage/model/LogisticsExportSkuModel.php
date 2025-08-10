<?php

namespace app\Manage\model;

use think\Config;
use think\exception\DbException;
use think\Model;

class LogisticsExportSkuModel extends Model
{
    protected $name = 'logistics_export_sku';

    protected $resultSetType = 'collection';

    protected $insert = ['created_at', 'updated_at'];

    public function user(): \think\model\relation\HasOne
    {
        return $this->hasOne('AccountModel', 'id', 'user_id');
    }

    public function table_name(): \think\model\relation\HasOne
    {
        return $this->hasOne('QuoteTableModel', 'id', 'table_id');
    }
}
