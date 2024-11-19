<?php

namespace app\Manage\model;

use think\Model;

class FinanceOperationFactoryClaimModel extends Model
{
    protected $name = 'finance_operation_factory_claim';

    protected $resultSetType = 'collection';

    protected $insert = ['created_time'];

    protected function setCreatedTimeAttr()
    {
        return date('Y-m-d H:i:s');
    }
}
