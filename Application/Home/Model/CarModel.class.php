<?php
namespace Home\Model;
use Think\Model;
class CarModel extends Model
{
	protected $dbName;
	protected $trueTableName = 'tp_policy';
	public function __construct($dbName) 
	{
		$this->dbName = $dbName;
        parent::__construct();
	}
	/**
	*获取维保券信息
	* $where 条件
	*/
	public function policyList($where)
	{
		$sql = "SELECT p.user_id as car_id,u.tel,car.vin as v_code,p.policy_no as bd_num,p.status as is_acceapt,p.create_time as bd_date from {$this->dbName}.tp_policy as p
		 left join {$this->dbName}.tp_user as u on p.user_id = u.user_id 
		 left join {$this->dbName}.tp_car as car on u.car_id = car.car_id
		 where {$where} order by car.car_id desc";
		$row = $this->query($sql);
		return $row;
	}
	/**
	*获取用户信息
	* $id 车辆id
	*/
	public function getUser($id)
	{
		$sql = "SELECT tel from {$this->dbName}.tp_user where user_id = '{$id}'";
		$row = $this->query($sql);
		return $row;
	}

}