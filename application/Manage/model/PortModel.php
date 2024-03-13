<?php

namespace app\Manage\model;

use think\Config;
use think\exception\DbException;
use think\Model;

class PortModel extends Model
{
    const STATE_ACTIVE = 1;

    const TYPE_FROM = 1;

    const TYPE_TO = 2;

    protected $name = 'port';

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

    /**
     * @throws DbException
     */
    static public function getToPort()
    {
        return self::all(['state' => self::STATE_ACTIVE, 'type' => self::TYPE_TO]);
    }
}
