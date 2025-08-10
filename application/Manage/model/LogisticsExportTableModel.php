<?php

namespace app\Manage\model;

use think\Config;
use think\exception\DbException;
use think\Model;

class LogisticsExportTableModel extends Model
{
    protected $name = 'logistics_export_table';

    protected $resultSetType = 'collection';

    protected $insert = ['created_at', 'updated_at'];

    public function user(): \think\model\relation\HasOne
    {
        return $this->hasOne('AccountModel', 'id', 'user_id');
    }
}
