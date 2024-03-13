<?php

namespace app\Manage\model;

use think\Config;
use think\exception\DbException;
use think\Model;

class WarehouseModel extends Model
{
    const STATE_ACTIVE = 1;

    protected $name = 'warehouse';

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

    public function port(): \think\model\relation\HasOne
    {
        return $this->hasOne('PortModel', 'id', 'port_id');
    }

    /**
     * @throws DbException
     */
    static public function getWarehouseByPort($port)
    {
        return self::all(['state' => self::STATE_ACTIVE, 'port_id' => $port['id']]);
    }
}
