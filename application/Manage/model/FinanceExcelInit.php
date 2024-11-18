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

    public function excelSheetSet()
    {
        return $this->objPHPExcel;
    }
}
