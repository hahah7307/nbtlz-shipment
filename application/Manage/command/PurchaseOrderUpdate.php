<?php
namespace app\Manage\command;

use app\Manage\model\ApiClient;
use app\Manage\model\PurchaseOrderCaptureModel;
use app\Manage\model\PurchaseOrderDetailModel;
use app\Manage\model\PurchaseOrderModel;
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

class PurchaseOrderUpdate extends Command
{
    protected function configure()
    {
        $this->setName('PurchaseOrderUpdate')->setDescription('Here is the PurchaseOrderUpdate');
    }

    /**
     * @throws Exception
     */
    protected function execute(Input $input, Output $output)
    {
        // 加载自定义配置
        Config::load(APP_PATH . 'Manage/config.php');

        $purchaseOrderCaptureModel = new PurchaseOrderCaptureModel();
        $purchaseOrderCapture = $purchaseOrderCaptureModel->find(1);

        Db::startTrans();
        try {
            $purchaseOrderRes = ApiClient::EcWarehouseApi(Config::get("ec_wms_uri"), "getPurchaseOrders", '{"page":' . $purchaseOrderCapture['page'] . ', "pageSize": 50}');
            if (empty($purchaseOrderRes['data'])) {
                throw new Exception($purchaseOrderRes['msg']);
            }
            $data = $purchaseOrderRes['data'];
            if (count($data) != 0) {
                $dataDetail = [];
                foreach ($data as $item) {
                    $purchaseOrderObj = new PurchaseOrderModel();
                    $shipBatchItem = $purchaseOrderObj->where(['po_id' => $item['po_id']])->find();
                    if ($shipBatchItem) {
                        continue;
                    }

                    $order = $item;

                    unset($order['systemTrack']);
                    unset($order['track']);
                    unset($order['tracking_no_set']);
                    unset($order['single_net_number']);
                    unset($order['detail']);
                    $order['pt_add_time'] = $order['pt_add_time'] == "0000-00-00 00:00:00" || $order['pt_add_time'] == "" ? null : $order['pt_add_time'];
                    $order['date_eta'] = $order['date_eta'] == "0000-00-00 00:00:00" || $order['date_eta'] == "" ? null : $order['date_eta'];
                    $order['date_release'] = $order['date_release'] == "0000-00-00 00:00:00" || $order['date_release'] == "" ? null : $order['date_release'];
                    $order['po_completion_time'] = $order['po_completion_time'] == "0000-00-00 00:00:00" || $order['po_completion_time'] == "" ? null : $order['po_completion_time'];
                    $order['date_expected'] = $order['date_expected'] == "0000-00-00 00:00:00" || $order['date_expected'] == "" ? null : $order['date_expected'];
                    $order['latest_receiving_time'] = $order['latest_receiving_time'] == "0000-00-00 00:00:00" || $order['latest_receiving_time'] == "" ? null : $order['latest_receiving_time'];
                    if ($createdId = $purchaseOrderObj->insertGetId($order)) {
                        if ($item['detail']) {
                            foreach ($item['detail'] as $detail) {
                                $itemDetail = $detail;
                                unset($itemDetail['team_list']);
                                $itemDetail['team_list'] = json_encode($detail['team_list']);
                                $itemDetail['order_id'] = $createdId;
                                $dataDetail[] = $itemDetail;
                            }
                        }
                        unset($createdId);
                    } else {
                        throw new \think\Exception("插入失败6！");
                    }
                }

                $purchaseOrderDetailObj = new PurchaseOrderDetailModel();
                if (!$purchaseOrderDetailObj->insertAll($dataDetail)) {
                    throw new \think\Exception("插入失败5！");
                }
            }

            if (count($data) >= 50) {
                $purchaseOrderCaptureModel->update(['page' => $purchaseOrderCapture['page'] + 1], ['id' => 1]);
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