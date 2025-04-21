<?php

namespace app\Manage\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Model;

class FinanceExcelInit extends Model
{
    private $objPHPExcel;

    public function __construct($objPHPExcel)
    {
        // phpexcel
        require_once './static/classes/PHPExcel/Classes/PHPExcel.php';

        // Create new PHPExcel object
        $this->objPHPExcel = $objPHPExcel;

        parent::__construct($objPHPExcel);
    }

    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function getFinanceOperationFactoryClaimExport($index, $list)
    {
        if ($index) {
            // create new sheet
            $this->objPHPExcel->createSheet();
        }

        // Set name sheet
        $this->objPHPExcel->setActiveSheetIndex($index)->setTitle('工厂索赔列表');

        // Add some data
        $this->objPHPExcel->setActiveSheetIndex($index)
            ->setCellValue('A1', '支付月份')
            ->setCellValue('B1', '仓库SKU')
            ->setCellValue('C1', '合计金额')
            ->setCellValue('D1', '币种')
            ->setCellValue('E1', '备注')
            ->setCellValue('F1', '类型')
        ;

        $expensesIndex = 1;
        foreach ($list as $expensesItem) {
            $expensesIndex ++;
            $this->objPHPExcel->setActiveSheetIndex($index)
                ->setCellValue('A' . $expensesIndex, $expensesItem['month'])
                ->setCellValue('B' . $expensesIndex, $expensesItem['sku'])
                ->setCellValue('C' . $expensesIndex, $expensesItem['total'])
                ->setCellValue('D' . $expensesIndex, $expensesItem['currency'])
                ->setCellValue('E' . $expensesIndex, $expensesItem['content'])
                ->setCellValue('F' . $expensesIndex, $expensesItem['type'])
            ;
        }
    }

    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function getShipBatch($index, $list)
    {
        if ($index) {
            // create new sheet
            $this->objPHPExcel->createSheet();
        }

        // Set name sheet
        $this->objPHPExcel->setActiveSheetIndex($index)->setTitle('海外仓头程明细');

        // Add some data
        $this->objPHPExcel->setActiveSheetIndex($index)
            ->setCellValue('A1', '采购合同号')
            ->setCellValue('B1', '工厂代码')
            ->setCellValue('C1', 'SKU')
            ->setCellValue('D1', '中文品名')
            ->setCellValue('E1', '采购单价')
            ->setCellValue('F1', '采购总数')
            ->setCellValue('G1', '采购合计')
            ->setCellValue('H1', '外销合同号')
            ->setCellValue('I1', '本票出运数量')
            ->setCellValue('J1', '本票出运合计')
        ;

        $shipBatchIndex = 1;
        foreach ($list as $shipBatchItem) {
            $shipBatchIndex ++;
            $this->objPHPExcel->setActiveSheetIndex($index)
                ->setCellValue('A' . $shipBatchIndex, $shipBatchItem['ref_no'])
                ->setCellValue('B' . $shipBatchIndex, $shipBatchItem['supplier_code'])
                ->setCellValue('C' . $shipBatchIndex, $shipBatchItem['product_barcode'])
                ->setCellValue('D' . $shipBatchIndex, $shipBatchItem['product_title'])
                ->setCellValue('E' . $shipBatchIndex, $shipBatchItem['unit_price'])
                ->setCellValue('F' . $shipBatchIndex, $shipBatchItem['qty_expected'])
                ->setCellValue('G' . $shipBatchIndex, $shipBatchItem['payable_amount'])
                ->setCellValue('H' . $shipBatchIndex, $shipBatchItem['remark'])
                ->setCellValue('I' . $shipBatchIndex, $shipBatchItem['sum'])
                ->setCellValue('J' . $shipBatchIndex, $shipBatchItem['sum'] * $shipBatchItem['unit_price'])
            ;
        }
    }

    public function excelSheetSet()
    {
        return $this->objPHPExcel;
    }
}
