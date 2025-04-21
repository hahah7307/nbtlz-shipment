<?php
namespace app\Manage\controller;

use app\Manage\model\FinanceExcelInit;
use app\Manage\model\PurchaseOrderModel;
use PHPExcel;
use PHPExcel_IOFactory;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Session;
use think\Config;

class ShipController extends BaseController
{
    /**
     * @throws DbException
     */
    public function warehouse(): \think\response\View
    {
        $where = [];
        $keyword = $this->request->get('keyword', '', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        if ($keyword) {
            $purchaseOrderModel = new PurchaseOrderModel();
            $purchaseOrder = $purchaseOrderModel->where(['supplier_code' => strtoupper($keyword)])->select()->count();
            if (!empty($purchaseOrder)) {
                $where['supplier_code'] = strtoupper($keyword);
            } else {
                $where['remark'] = ['like', '%' . strtoupper($keyword) . '%'];
            }
        } else {
            $where['remark'] = "no data";
        }

        $page_num = $this->request->get('page_num', Config::get('PAGE_NUM'));
        $this->assign('page_num', $page_num);

        // 列表
        $list = Db::name('ecang_ship_batch')
            ->alias('a')
            ->join('ecang_ship_batch_packing_receiving_and_purchase_info b', 'a.id = b.ship_batch_id', 'LEFT')
            ->join('ecang_ship_batch_product_info c', 'b.product_barcode = c.product_barcode AND a.id = c.ship_batch_id', 'LEFT')
            ->join('ecang_purchase_order d', 'b.po_code = d.po_code', 'LEFT')
            ->join('ecang_purchase_order_detail e', 'd.id = e.order_id AND b.product_barcode = e.product_sku', 'LEFT')
            ->where($where)
            ->group('a.id,
	d.id,
	a.remark,
	b.product_barcode,
	b.po_code,
	c.product_title,
	d.ref_no,
	d.supplier_code,
	e.qty_expected,
	d.payable_amount,
	e.unit_price')
            ->field('a.id,
	d.id,
	a.remark,
	b.product_barcode,
	b.po_code,
	c.product_title,
	d.ref_no,
	d.supplier_code,
	e.qty_expected,
	d.payable_amount,
	e.unit_price,
	SUM(quantity) sum')
            ->paginate($page_num, false, ['query' => ['keyword' => $keyword, 'page_num' => $page_num]]);
        $this->assign('list', $list);

        $total = array_sum(array_map(function($item) {
            return $item['unit_price'] * $item['sum'];
        }, $list->toArray()['data']));
        $this->assign('total', $total);
        $this->assign('sum', array_sum(array_column($list->toArray()['data'],'sum')));

        Session::set(Config::get('BACK_URL'), $this->request->url(), 'manage');
        return view();
    }

    /**
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws \PHPExcel_Writer_Exception
     */
    public function warehouse_export($keyword)
    {
        $where = [];
        if ($keyword) {
            $purchaseOrderModel = new PurchaseOrderModel();
            $purchaseOrder = $purchaseOrderModel->where(['supplier_code' => strtoupper($keyword)])->select()->count();
            if (!empty($purchaseOrder)) {
                $where['supplier_code'] = strtoupper($keyword);
            } else {
                $where['remark'] = ['like', '%' . strtoupper($keyword) . '%'];
            }
        } else {
            $where['remark'] = "no data";
        }

        // 列表
        $list = Db::name('ecang_ship_batch')
            ->alias('a')
            ->join('ecang_ship_batch_packing_receiving_and_purchase_info b', 'a.id = b.ship_batch_id', 'LEFT')
            ->join('ecang_ship_batch_product_info c', 'b.product_barcode = c.product_barcode AND a.id = c.ship_batch_id', 'LEFT')
            ->join('ecang_purchase_order d', 'b.po_code = d.po_code', 'LEFT')
            ->join('ecang_purchase_order_detail e', 'd.id = e.order_id AND b.product_barcode = e.product_sku', 'LEFT')
            ->where($where)
            ->group('a.id,
	d.id,
	a.remark,
	b.product_barcode,
	b.po_code,
	c.product_title,
	d.ref_no,
	d.supplier_code,
	e.qty_expected,
	d.payable_amount,
	e.unit_price')
            ->field('a.id,
	d.id,
	a.remark,
	b.product_barcode,
	b.po_code,
	c.product_title,
	d.ref_no,
	d.supplier_code,
	e.qty_expected,
	d.payable_amount,
	e.unit_price,
	SUM(quantity) sum')
            ->select();

        // phpexcel
        require_once './static/classes/PHPExcel/Classes/PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $financeExcelInit = new FinanceExcelInit($objPHPExcel);
        $financeExcelInit->getShipBatch(0, $list);
        $objPHPExcel = $financeExcelInit->excelSheetSet();

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        $filename = date("YmdHis") . time() . mt_rand(100000, 999999);
        ob_end_clean();
        header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
