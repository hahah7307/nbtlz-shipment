<?php
namespace app\Manage\controller;

use app\Manage\model\AccountModel;
use app\Manage\model\FinanceExcelInit;
use app\Manage\model\LogisticsExportSkuModel;
use app\Manage\model\LogisticsExportTableModel;
use app\Manage\model\LogisticsMonthModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Exception;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\Session;
use think\Config;

class LogisticsController extends BaseController
{
    /**
     * @throws DbException
     * @throws Exception
     */
    public function export_table(): \think\response\View
    {
        $keyword = $this->request->get('keyword', '', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        if ($keyword) {
            $where['table_name'] = ['like', '%' . $keyword . '%'];
        } else {
            $where = [];
        }

        // 查看权限
        $access_ids = AccountModel::account_access_ids();
        $where['user_id'] = ['in', $access_ids];

        // 报价单列表
        $quoteTableObj = new LogisticsExportTableModel();
        $list = $quoteTableObj->with(['user'])->where($where)->order('id desc')->paginate(Config::get('PAGE_NUM'), false, ['keyword' => $keyword]);
        $this->assign('list', $list);

        Session::set(Config::get('BACK_URL'), $this->request->url(), 'manage');
        return view();
    }

    /**
     * @throws PHPExcel_Reader_Exception
     */
    public function table_import()
    {
        // phpexcel
        require_once './static/classes/PHPExcel/Classes/PHPExcel.php';

        $filename = input('filename');
        $origin = input('origin');
        $file = "./upload/excel/" . $filename;
        $excelReader = PHPExcel_IOFactory::createReaderForFile($file);
        $excelObj = $excelReader->load($file);
        $worksheet = $excelObj->getSheet(0);
        $data = $worksheet->toArray();
        unset($data[0]);
        $data = array_values($data);

        Db::startTrans();
        try {
            $tableObj = new LogisticsExportTableModel();
            $table = [
                'user_id'       =>  Session::get(Config::get('USER_LOGIN_FLAG')),
                'table_name'    =>  $origin,
                'created_date'  =>  date('Y-m-d H:i:s')
            ];
            if ($id = $tableObj->insertGetId($table)) {
                $productData = [];
                foreach ($data as $item) {
                    if (empty($item[0])) {
                        continue;
                    }
                    $productData[] = [
                        "table_id"          =>  $id,
                        "export_no"         =>  $item[0],
                        "warehouse_sku"     =>  strtoupper($item[1]),
                        "origin_sku"        =>  substr(strtoupper($item[1]), 0, 8),
                    ];
                }
                $productObj = new LogisticsExportSkuModel();
                if (!$productObj->insertAll($productData)) {

                    throw new Exception('表格导入失败');
                }
            } else {
                throw new Exception('表格导入失败');
            }

            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage(), session('back_url', '', 'manage'));
        }
        $this->redirect(session('back_url', '', 'manage'));
    }

    /**
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws DataNotFoundException
     */
    public function export_detail($id): \think\response\View
    {
        $where = [];
        $where['table_id'] = $id;

        $page_num = $this->request->get('page_num', Config::get('PAGE_NUM'));
        $this->assign('page_num', $page_num);

        $storage = new LogisticsExportSkuModel();
        $list = $storage->where($where)->order('id asc')->paginate($page_num, false, ['query' => ['id' => $id, 'page_num' => $page_num]]);
        $newSkuList = $storage->where($where)->order('id asc')->column('new_sku');
        $this->assign('newSku', implode("\n", $newSkuList));
        $this->assign('list', $list);
        $this->assign('id', $id);

        return view();
    }

    /**
     * @throws DataNotFoundException
     * @throws \PHPExcel_Writer_Exception
     * @throws ModelNotFoundException
     * @throws PHPExcel_Reader_Exception
     * @throws DbException
     */
    public function export_detail_export($id)
    {
        $where = [];
        $where['table_id'] = $id;
        $storage = new LogisticsExportSkuModel();
        $list = $storage->where($where)->order('id asc')->select();

        // phpexcel
        require_once './static/classes/PHPExcel/Classes/PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $financeExcelInit = new FinanceExcelInit($objPHPExcel);
        $financeExcelInit->getExportDetail(0, $list);
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

    /**
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws DataNotFoundException
     */
    public function export_sku(): \think\response\View
    {
        $where = [];
        $keyword = $this->request->get('keyword', '', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        if ($keyword) {
            $logisticsExportSkuModel = new LogisticsExportSkuModel();
            $export = $logisticsExportSkuModel->where(['export_no' => ['like', '%' . strtoupper($keyword) . '%']])->select()->count();
            if (!empty($export)) {
                $where['export_no'] = ['like', '%' . strtoupper($keyword) . '%'];
            } else {
                $where['warehouse_sku|origin_sku|new_sku'] = ['like', '%' . strtoupper($keyword) . '%'];
            }
        }

        $page_num = $this->request->get('page_num', Config::get('PAGE_NUM'));
        $this->assign('page_num', $page_num);

        // 查看权限
        $access_ids = AccountModel::account_access_ids();
        $table['user_id'] = ['in', $access_ids];
        $tableObj = new LogisticsExportTableModel();
        $tableIds = $tableObj->where($table)->column('id');
        $where['table_id'] = ['in', $tableIds];

        $storage = new LogisticsExportSkuModel();
        $list = $storage->where($where)->order('table_id desc id asc')->paginate($page_num, false, ['query' => ['keyword' => $keyword, 'page_num' => $page_num]]);
        $newSkuList = $storage->where($where)->order('table_id desc id asc')->column('new_sku');
        $this->assign('newSku', implode("\n", $newSkuList));
        $this->assign('list', $list);

        return view();
    }

    /**
     * @throws DataNotFoundException
     * @throws \PHPExcel_Writer_Exception
     * @throws ModelNotFoundException
     * @throws PHPExcel_Reader_Exception
     * @throws DbException
     */
    public function export_sku_export($keyword = "")
    {
        $where = [];
        if ($keyword) {
            $logisticsExportSkuModel = new LogisticsExportSkuModel();
            $export = $logisticsExportSkuModel->where(['export_no' => ['like', '%' . strtoupper($keyword) . '%']])->select()->count();
            if (!empty($export)) {
                $where['export_no'] = ['like', '%' . strtoupper($keyword) . '%'];
            } else {
                $where['warehouse_sku|origin_sku|new_sku'] = ['like', '%' . strtoupper($keyword) . '%'];
            }
        }

        $storage = new LogisticsExportSkuModel();
        $list = $storage->where($where)->order('table_id desc id asc')->select();

        // phpexcel
        require_once './static/classes/PHPExcel/Classes/PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $financeExcelInit = new FinanceExcelInit($objPHPExcel);
        $financeExcelInit->getExportSku(0, $list);
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

    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function month_mapping(): \think\response\View
    {
        $model = new LogisticsMonthModel();

        $this->assign('list', $model->select());
        return view();
    }
}
