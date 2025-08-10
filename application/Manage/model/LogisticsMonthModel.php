<?php

namespace app\Manage\model;

use think\Config;
use think\exception\DbException;
use think\Model;

class LogisticsMonthModel extends Model
{
    protected $name = 'logistics_month';

    protected $resultSetType = 'collection';

    protected $insert = ['created_at', 'updated_at'];
}
