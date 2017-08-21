<?php
namespace Home\Model;
use Think\Model;
class UnicomSetModel extends Model
{
    protected $dbName;
    protected $trueTableName  = 'tp_unicom_plan';
    public function __construct($dbName) 
    {
        $this->dbName = $dbName;
        parent::__construct();
    }
    /**
    * 获取所有联通套餐信息
    * $where 查询条件 string
    */
    public function unicomList($where)
    {

    }
}