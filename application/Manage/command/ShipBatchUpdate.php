<?php
namespace app\Manage\command;

use app\Manage\model\ApiClient;
use app\Manage\model\ShipBatchCaptureModel;
use app\Manage\model\ShipBatchDgOrderInfoModel;
use app\Manage\model\ShipBatchModel;
use app\Manage\model\ShipBatchPackingInfoModel;
use app\Manage\model\ShipBatchPackingReceivingModel;
use app\Manage\model\ShipBatchProductInfoModel;
use app\Manage\model\ShipBatchReceivingPurchaseModel;
use Exception;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class ShipBatchUpdate extends Command
{
    protected function configure()
    {
        $this->setName('ShipBatchUpdate')->setDescription('Here is the ShipBatchUpdate');
    }

    /**
     * @throws Exception
     */
    protected function execute(Input $input, Output $output)
    {
        // 加载自定义配置
        Config::load(APP_PATH . 'Manage/config.php');

        $shipBatchCaptureObj = new ShipBatchCaptureModel();
        $captureData = $shipBatchCaptureObj->find(1);

        Db::startTrans();
        try {
            $shipBatchRes = ApiClient::EcWarehouseApi(Config::get("ec_wms_uri"), "getShipBatch", '{"page":' . $captureData['page'] . ', "pageSize": 50}');
            if (empty($shipBatchRes['data'])) {
                throw new Exception($shipBatchRes['msg']);
            }
            $data = $shipBatchRes['data'];
            if (count($data) != 0) {
                foreach ($data as $item) {
                    $shipBatchObj = new ShipBatchModel();
                    $shipBatchItem = $shipBatchObj->where(['reference_no' => $item['reference_no']])->find();
                    if ($shipBatchItem) {
                        continue;
                    }

                    $addData = [];
                    $data = $item;
                    unset($data['receiving_and_purchase']);
                    unset($data['product_info']);
                    unset($data['packing_info']);
                    unset($data['dg_order_info']);
                    unset($data['packing_receiving_and_purchase_info']);
                    $data['expected_date'] = $data['expected_date'] == "0000-00-00 00:00:00" ? null : $data['expected_date'];
                    if ($createdId = $shipBatchObj->insertGetId($data)) {
                        foreach ($item['receiving_and_purchase'] as $key => $receiving_and_purchase) {
                            foreach ($receiving_and_purchase as $k => $value) {
                                $item['receiving_and_purchase'][$key][$k]['ship_batch_id'] = $createdId;
                                $addData[] = $item['receiving_and_purchase'][$key][$k];
                            }
                        }
                        $receivingPurchaseObj = new ShipBatchReceivingPurchaseModel();
                        if (!empty($addData) && !$receivingPurchaseObj->insertAll($addData)) {
                            throw new \think\Exception("插入失败1！");
                        }
                        unset($addData);

                        foreach ($item['product_info'] as $key => $product_info) {
                            if ($product_info['op_ref_paydate'] == '0000-00-00 00:00:00') {
                                $item['product_info'][$key]['op_ref_paydate'] = null;
                            }
                            $item['product_info'][$key]['ship_batch_id'] = $createdId;
                        }
                        $productInfoObj = new ShipBatchProductInfoModel();
                        if (!$productInfoObj->insertAll($item['product_info'])) {
                            throw new \think\Exception("插入失败2！");
                        }

                        foreach ($item['packing_info'] as $key => $packing_info) {
                            if ($packing_info['reference_no'] == '') {
                                $item['packing_info'][$key]['reference_no'] = null;
                            }
                            $item['packing_info'][$key]['ship_batch_id'] = $createdId;
                        }
                        $packingInfoObj = new ShipBatchPackingInfoModel();
                        if (!empty($item['packing_info']) && !$packingInfoObj->insertAll($item['packing_info'])) {
                            throw new \think\Exception("插入失败3！");
                        }

                        foreach ($item['dg_order_info'] as $key => $dg_order_info) {
                            $item['dg_order_info'][$key]['ship_batch_id'] = $createdId;
                        }
                        $dgOrderInfoObj = new ShipBatchDgOrderInfoModel();
                        if (!empty($item['dg_order_info'])) {
                            if (!$dgOrderInfoObj->insertAll($item['dg_order_info'])) {
                                throw new \think\Exception("插入失败4！");
                            }
                        }

                        foreach ($item['packing_receiving_and_purchase_info'] as $key => $packing_receiving_and_purchase_info) {
                            if ($packing_receiving_and_purchase_info['reference_no'] == '') {
                                $item['packing_receiving_and_purchase_info'][$key]['reference_no'] = null;
                            }
                            $item['packing_receiving_and_purchase_info'][$key]['ship_batch_id'] = $createdId;
                        }
                        $productFeeDetailInfoObj = new ShipBatchPackingReceivingModel();
                        if (!empty($item['packing_receiving_and_purchase_info']) && !$productFeeDetailInfoObj->insertAll($item['packing_receiving_and_purchase_info'])) {
                            throw new \think\Exception("插入失败5！");
                        }
                    } else {
                        throw new \think\Exception("插入失败6！");
                    }
                }
            }

            Db::commit();
//            $shipBatchCaptureObj->update(['page' => $captureData['page'] + 1], ['id' => 1]);
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