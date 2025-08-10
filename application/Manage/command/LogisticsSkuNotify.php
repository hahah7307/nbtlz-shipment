<?php
namespace app\Manage\command;

use app\Manage\model\LogisticsExportSkuModel;
use app\Manage\model\LogisticsMonthModel;
use Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class LogisticsSkuNotify extends Command
{
    protected function configure()
    {
        $this->setName('LogisticsSkuNotify')->setDescription('Here is the LogisticsSkuNotify');
    }

    /**
     * @throws Exception
     */
    protected function execute(Input $input, Output $output)
    {
        $logisticsSkuObj = new LogisticsExportSkuModel();
        $skuData = $logisticsSkuObj->where(['is_notify' => 0])->order('id asc')->limit(50)->select();

        Db::startTrans();
        try {
            foreach ($skuData as $key => $item) {
                $sku = $logisticsSkuObj->find($item['id']);
                if ($sku['is_notify'] != 0) {
                    continue;
                } else {
                    $created_month = date('Ym');
                    $created_date = date('Ymd');
                    $maxIndexSku = max($logisticsSkuObj->where(['is_notify' => 1, 'created_month' => $created_month, 'origin_sku' => $item['origin_sku']])->order('id asc')->column('month_index'));
                    $sameOriginSku = $logisticsSkuObj->where(['table_id' => $item['table_id'], 'export_no' => $item['export_no'], 'origin_sku' => $item['origin_sku'], 'is_notify' => 1])->count();
                    if (empty($sameOriginSku)) {
                        $maxIndexSku = intval($maxIndexSku) + 1;
                    }

                    $monthObj = new LogisticsMonthModel();
                    $logisticsMonth = $monthObj->where(['month_char' => date('m')])->find();

                    $originNum = intval(substr($item['origin_sku'], 3, 3));
                    $newNumStr = sprintf("%03d", $originNum + $maxIndexSku);

                    $newSku = $logisticsMonth['month_code_pre'] . substr($item['origin_sku'], 0, 3) . $logisticsMonth['month_code_index'] . $newNumStr . substr($item['origin_sku'], 6, 2) . substr($item['warehouse_sku'], 8, 2);

                    $updateData = [
                        'new_sku'       =>  $newSku,
                        'created_month' =>  $created_month,
                        'created_date'  =>  $created_date,
                        'month_index'   =>  $maxIndexSku,
                        'is_notify'     =>  1,
                        'notify_date'   =>  date('Y-m-d H:i:s')
                    ];
                    if (!$logisticsSkuObj->update($updateData, ['id' => $item['id']])) {
                        throw new Exception("更新失败！");
                    }
                }
            }

            Db::commit();
            echo "success";
        } catch (\SoapFault $e) {
            Db::rollback();
            dump('SoapFault:'.$e);
        } catch (\Exception $e) {
            Db::rollback();
            dump('Exception:'.$e);
        }
    }
}