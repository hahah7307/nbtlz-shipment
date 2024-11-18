<?php
namespace app\Manage\controller;

use app\Manage\model\FinanceExcelInit;
use app\Manage\model\FinanceOperationFactoryClaimModel;
use Exception;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Exception;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Session;
use think\Config;

class FinanceOperationController extends BaseController
{
    /**
     * @throws DbException
     */
    public function factory_claim(): \think\response\View
    {
        $keyword = $this->request->get('keyword', '', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        if ($keyword) {
            $where['sku|type|content'] = ['like', '%' . $keyword . '%'];
        } else {
            $where = [];
        }

        $month = $this->request->get('month', date('Y-m', strtotime('-1 month')));
        $calculate_month = date('Ym', strtotime($month . '-01'));
        $where['month'] = $calculate_month;
        $this->assign('month', $month);

        $page_num = $this->request->get('page_num', Config::get('PAGE_NUM'));
        $this->assign('page_num', $page_num);

        //
        $factory_claim = new FinanceOperationFactoryClaimModel();
        $list = $factory_claim->where($where)->order('id asc')->paginate($page_num, false, ['query' => ['keyword' => $keyword, 'page_num' => $page_num]]);
        $this->assign('list', $list);
        $this->assign('list_sum', $factory_claim->where($where)->sum('total'));

        Session::set(Config::get('BACK_URL'), $this->request->url(), 'manage');
        return view();
    }

    /**
     * @throws PHPExcel_Reader_Exception
     */
    public function factory_claim_import()
    {
        // phpexcel
        require_once './static/classes/PHPExcel/Classes/PHPExcel.php';

        $filename = input('filename');
        $file= "./upload/excel/" . $filename;
        $excelReader = PHPExcel_IOFactory::createReaderForFile($file);
        $excelObj = $excelReader->load($file);
        $worksheet = $excelObj->getSheet(0);
        $data = $worksheet->toArray();
        unset($data[0]);

        Db::startTrans();
        try {
            $factory_claimData = [];
            $financeOperationFactoryObj = new FinanceOperationFactoryClaimModel();
            foreach ($data as $item) {
                $factory_claimData[] = [
                    "month"                 =>  intval($item[0]),
                    "sku"                   =>  $item[1],
                    "currency"              =>  $item[3],
                    "total"                 =>  $item[2],
                    "content"               =>  $item[4],
                    "type"                  =>  $item[5],
                ];
            }
            $financeOperationFactoryObj->insertAll($factory_claimData);

            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage(), url('factory_claim'));
        }
        $this->redirect(url('factory_claim'));
    }

    /**
     * @throws DataNotFoundException
     * @throws \PHPExcel_Writer_Exception
     * @throws ModelNotFoundException
     * @throws PHPExcel_Reader_Exception
     * @throws DbException
     */
    public function factory_claim_export()
    {
        $financeOperationFactoryClaimObj = new FinanceOperationFactoryClaimModel();
        $list = $financeOperationFactoryClaimObj->select();
        if (empty($list)) {
            $this->error('异常操作！', url('report'));
        }

        // phpexcel
        require_once './static/classes/PHPExcel/Classes/PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $financeExcelInit = new FinanceExcelInit($objPHPExcel);
        $financeExcelInit->getFinanceOperationFactoryClaimExport(0, $list);
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

    public function factory_claim_delete()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $skuRelationObj = new FinanceOperationFactoryClaimModel();
            if ($skuRelationObj->where('id', $post['id'])->delete()) {
                echo json_encode(['code' => 1, 'msg' => '删除成功']);
            } else {
                echo json_encode(['code' => 0, 'msg' => '删除失败，请重试']);
            }
        } else {
            echo json_encode(['code' => 0, 'msg' => '异常操作']);
        }
        exit;
    }
}
