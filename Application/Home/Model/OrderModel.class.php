<?php
namespace Home\Model;
use Think\Model;
class OrderModel extends Model
{
    protected $dbName;
    protected $trueTableName  = 'tp_order';
    public function __construct($dbName) 
    {
        $this->dbName = $dbName;
        parent::__construct();
    }
    /**
     *获取订单信息数量
     * $simDB sim卡数据库 string
     * $where 条件
     */
    public function orderCnt($where)
    {
        $sql ="SELECT count(1) as cnt from {$this->dbName}.tp_order as `order`
         inner join {$this->dbName}.tp_user as `user` on `order`.user_id = `user`.user_id
         inner join {$this->dbName}.tp_device d on d.device_id = `user`.device_id
         inner join {$this->dbName}.tp_sim_card as sim on d.device_id = sim.device_id
         inner join {$this->dbName}.tp_order_detail as od on `order`.order_no = od.order_no
         inner join {$this->dbName}.tp_plan as `set` on od.goods_code = `set`.plan_id
         inner join {$this->dbName}.tp_unicom_plan as unicom on `set`.unicom_plan_id = unicom.unicom_plan_id
         where {$where} limit 1";
        $row = $this->query($sql);
        return $row;
    }
    /**
     * 获取所有订单信息
     * $simDB sim卡数据库 string
     * $where 查询条件 string
     * $firstRow 分页
     * $listRows 分页
     */
    public function orderList($where,$firstRow,$listRows)
    {
        $sql ="SELECT 
                    `order`.order_id as id,`user`.tel,`order`.order_no,sim.iccid as sim_iccid, 
                    unicom.unicom_plan as unicom_set,od.goods_point integral_cost,set.discount_price as `money`, 
                    `order`.order_status as status,`order`.order_status_time as status_time 
                    from {$this->dbName}.tp_order as `order`
                inner join {$this->dbName}.tp_user as `user` on `order`.user_id = `user`.user_id
                inner join {$this->dbName}.tp_device d on d.device_id = `user`.device_id
                inner join {$this->dbName}.tp_sim_card as sim on d.device_id = sim.device_id
                inner join {$this->dbName}.tp_order_detail as od on `order`.order_no = od.order_no
                inner join {$this->dbName}.tp_plan as `set` on od.goods_code = `set`.plan_id
                inner join {$this->dbName}.tp_unicom_plan as unicom on `set`.unicom_plan_id = unicom.unicom_plan_id
                where {$where} 
                order by `order`.order_id desc 
                limit {$firstRow},{$listRows}
                ";
        $row = $this->query($sql);
        return $row;
    }
    /**
     * 获取订单详情
     * $id 订单id string
     */
    public function orderInfo($id)
    {
        $sql ="SELECT `user`.tel,`order`.order_no,`order`.order_status_time as status_time from {$this->dbName}.tp_order as `order`
         inner join {$this->dbName}.tp_user as `user` on `order`.user_id = `user`.user_id
         where `order`.order_no in ({$id})";
        $row = $this->query($sql);
        return $row;
    }
    /**
    * 获取订单手机号
    * 查询条件
    */
    public function getOrderTel($where)
    {
        return M("$this->dbName.order")
        ->alias('`order`')
        ->join("$this->dbName.tp_user u on `order`.user_id = u.user_id")
        ->join("$this->dbName.tp_order_detail od on `order`.order_no = od.order_no")
        ->join("$this->dbName.tp_plan `set` on od.goods_code = `set`.plan_id")
        ->join("$this->dbName.tp_unicom_plan unicom on `set`.unicom_plan_id = unicom.unicom_plan_id")
        ->field('u.tel,unicom.unicom_plan as unicom_set')
        ->where($where)
        ->select();
    }

    /**
    * 获取套餐id获取订单信息
    * 查询条件
    */
    public function getOrderByPlan($where)
    {
        return M("$this->dbName.order")
        ->alias('`order`')
        ->join("$this->dbName.tp_order_detail od on `order`.order_no = od.order_no")
        ->join("$this->dbName.tp_plan `set` on od.goods_code = `set`.plan_id")
        ->field('`order`.order_id')
        ->where($where)
        ->select();
    }
}