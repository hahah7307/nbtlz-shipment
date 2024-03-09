<?php

namespace app\Manage\model;

use think\Config;
use think\Exception;
use think\exception\DbException;
use think\Model;
use think\Session;

class ExportLogModel extends Model
{
    protected $name = 'export_log';

    protected $resultSetType = 'collection';

    protected $insert = ['created_at'];

    protected function setCreatedAtAttr()
    {
        return date('Y-m-d H:i:s');
    }

    public function export(): \think\model\relation\HasOne
    {
        return $this->hasOne('ExportModel', 'id', 'export_id');
    }

    public function account(): \think\model\relation\HasOne
    {
        return $this->hasOne('AccountModel', 'id', 'created_id');
    }

    static public function  createNewLog($post, $export_id): ExportLogModel
    {
        if (empty($post['abnormal'])) {
            $abnormal = '';
            $etd = empty($post['etd']) ? '' : '预计派送' . $post['etd'];
        } else {
            $abnormal = Config::get('EXPORT_ABNORMAL')[$post['abnormal']];
            $etd = '';
        }
        $logData = [
            'export_id'     =>  $export_id,
            'export_state'  =>  $post['state'],
            'abnormal'      =>  $abnormal . $etd,
            'created_id'    =>  Session::get(Config::get('USER_LOGIN_FLAG')),
            'created_ip'    =>  get_real_ip()
        ];
        return self::create($logData);
    }
}
