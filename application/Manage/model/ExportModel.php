<?php

namespace app\Manage\model;

use think\Config;
use think\Exception;
use think\exception\DbException;
use think\Model;

class ExportModel extends Model
{
    const STATE_ACTIVE = 1;

    protected $name = 'export';

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

    public function fromPort(): \think\model\relation\HasOne
    {
        return $this->hasOne('PortModel', 'id', 'from_port');
    }

    public function toPort(): \think\model\relation\HasOne
    {
        return $this->hasOne('PortModel', 'id', 'to_port');
    }

    public function account(): \think\model\relation\HasOne
    {
        return $this->hasOne('AccountModel', 'id', 'created_id');
    }


    /**
     * @throws DbException
     */
    static public function createNewExport(): string
    {
        $y = date('y');
        $m = date('m');
        $prefix = 'A' . $y . 'TI' . $m;
        $num = count(self::all(['export_no' => ['like', $prefix . '%']]));
        $num2str = sprintf("%03d", $num + 1);

        return $prefix . $num2str;
    }
}
