<?php
namespace Home\Model;
use Think\Model;
class OrderStatusModel extends Model
{
    protected $dbName;
    protected $trueTableName  = 'tp_order_status';
    public function __construct($dbName) 
    {
        $this->dbName = $dbName;
        parent::__construct();
    }
    /**
    * 获取所有订单状态信息
    * $where 查询条件 string
    */
    public function orderStatusList($where)
    {

    }
}