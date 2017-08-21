<?php
namespace Home\Controller;
use Home\Common\MyController;
set_time_limit(0);
class ExcelController extends MyController 
{
    public $objPHPExcel;
    public $objWriter;
    public $objActSheet;
	function __construct()
	{
		parent::__construct();
        vendor("PHPExcel.PHPExcel");
		vendor("PHPExcel.PHPExcel.IOFactory");
		vendor("PHPExcel.PHPExcel.PHPExcel_Cell");
        vendor("PHPExcel.PHPExcel.Settings");
        vendor("PHPExcel.PHPExcel.CachedObjectStorageFactory");
        $this->objPHPExcel = new \PHPExcel();
        $this->objWriter = new \PHPExcel_Writer_Excel5($this->objPHPExcel);
        $this->objActSheet = $this->objPHPExcel->getActiveSheet(); 
	}
    /**
     * excel导入
     * $filename 文件名
     * $type excel扩展类型
     * $encode 字符编码
     */
	public function read($filename,$type,$encode='utf-8')
	{
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = \PHPExcel_IOFactory::load($filename);
        $objWorksheet = $objPHPExcel->getSheet(0);
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn(); 
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++)
        {
        	for ($col = 0; $col < $highestColumnIndex; $col++) 
    		{
    			$excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
    		}
    	}
    	return $excelData;
    } 
    /**
     * 维保券导出
     * $result 导出的数据
     */
    public function policyOut($result)
    { 
        //列
        $col = array('A','B','C','D','E');
        //标题
        $title = array('用户状态','手机号','车架号','保单号','申请日期');
        //列值
        
        foreach($result as $key => $val)
        {
            $value = array(
                $val['is_acceapt'],
                $val['tel'],
                $val['v_code'],
                $val['bd_num'],
                $val['bd_date']
            );
            $this->excel_text($col,$title,$value,$key);
        }
        $fileName = 'policy_list.xls';
        $this->export_excel($fileName);
    }
    /**
     * 订单导出
     * $result 导出的数据
     */
    public function OrderOut($result)
    { 
        //列
        $col = array('A','B','C','D','E','F','G','H');
        //标题
        $title = array('手机号','订单编号','ICCID','对应联通套餐','消耗积分','订单金额(元)','订单状态','订单状态日期');
        //列值
        foreach($result as $key => $val)
        {
            $value = array(
                $val['tel'],
                $val['order_no'],
                $val['sim_iccid'],
                $val['unicom_set'],
                $val['integral_cost'],
                $val['money'],
                $val['status_type'],
                $val['status_time']
            );
            $this->excel_text($col,$title,$value,$key);
        }
        $fileName = 'order_list.xls';
        $this->export_excel($fileName);
    }
    /**
    *excel文档内容处理
    */
    public function excel_text($col,$title,$value,$key) 
    {
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
        $cacheSettings = array();
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
        $obj = $this->objPHPExcel->setActiveSheetIndex(0);
        $row = $key+2;//内容行值
        //居中
        for ($i=0; $i <count($col) ; $i++) 
        { 
            $obj->setCellValue($col[$i].'1',$title[$i]);//标题行
            $obj->setCellValue($col[$i].$row,$value[$i]);
            //标题
            //$this->objActSheet->getStyle($col[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //正文
            //$this->objActSheet->getStyle($col[$i].$row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
            $this->objActSheet->setCellValueExplicit($col[$i].$row,$value[$i],\PHPExcel_Cell_DataType::TYPE_STRING);
            $this->objActSheet->getColumnDimension($col[$i])->setWidth(30);
        }
    }
    /**
    *导出excel
    */
    public function export_excel($fileName) 
    {
        header ( 'Pragma:public');

        header ( 'Expires:0');

        header ( 'Cache-Control:must-revalidate,post-check=0,pre-check=0');

        header ( 'Content-Type:application/force-download');

        header ( 'Content-Type:application/vnd.ms-excel;charset=UTF-8');

        header ( 'Content-Type:application/octet-stream');

        header ( 'Content-Type:application/download');

        header ( 'Content-Disposition:attachment;filename='. $fileName );

        header ( 'Content-Transfer-Encoding:binary');

        $this->objWriter->save('php://output');
        exit;
    }
}
