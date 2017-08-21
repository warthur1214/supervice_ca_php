<?php
namespace Home\Model;
use Think\Model;
class SetModel extends Model
{
    protected $dbName;
    protected $trueTableName  = 'tp_plan';
    public function __construct($dbName) 
    {
        $this->dbName = $dbName;
        parent::__construct();
    }
    /**
     *获取套餐信息数量
     * $where 条件
     */
    public function setCnt($where)
    {
        $sql = "SELECT count(1) as cnt from {$this->dbName}.tp_plan as `set`
         inner join {$this->dbName}.tp_unicom_plan as unicom on `set`.unicom_plan_id = unicom.unicom_plan_id
         where {$where} limit 1";
        $row = $this->query($sql);
        return $row;
    }
    /**
     * 获取所有套餐信息
     * $where 查询条件 string
     * $firstRow 分页
     * $listRows 分页
     */
    public function setList($where,$firstRow,$listRows)
    {
        $sql = "SELECT `set`.plan_id as id,`set`.plan_name as set_name,`set`.image_url as img_url,unicom.unicom_plan as unicom_set,`set`.cost_price as market_price,`set`.price as current_price,`set`.`point` as exchange_integral,`set`.create_time,`set`.is_sticked as is_top,`set`.is_on_shelve as is_sale from {$this->dbName}.tp_plan as `set`
         inner join {$this->dbName}.tp_unicom_plan as unicom on `set`.unicom_plan_id = unicom.unicom_plan_id
         where {$where} order by `set`.is_sticked desc,`set`.is_on_shelve desc,`set`.plan_id desc limit {$firstRow},{$listRows}";
        $row = $this->query($sql);
        return $row;
    }
}