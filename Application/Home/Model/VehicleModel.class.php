<?php
namespace Home\Model;
use Think\Model;
class VehicleModel extends Model
{
	protected $dbName;
	// protected $tablePrefix;
	protected $trueTableName = 'tp_device';
	public function __construct($dbName) 
	{
		$this->dbName = $dbName;
		// $this->tablePrefix = $tablePrefix;
        parent::__construct();
	}
    /**
    * 获取设备车辆数量
    * $where 查询条件 string
    */
	// public function getCon($where)
	// {
	// 	$sql = "select count(*) as con from {$this->dbName}.tp_dvc_vehicle as v
	// 	 left join {$this->dbName}.tp_dvc_car as car on v.id = car.device_id where {$where} limit 1";
	// 	 $row = $this->query($sql);
	// 	 return $row;
	// }
    /**
    * 获取设备车辆信息
    * $where 查询条件 string
    * $firstRow 分页 从哪开始
    * $listRows 分页 条数
    */
	// public function getList($where,$firstRow = '0',$listRows = '20')
	// {
	// 	$sql = "select car.car_no,car.car_brand,car.car_driver,car.car_status,car.car_group,v.id,v.device_no,v.active_status,v.is_car,v.is_use,v.device_type FROM {$this->dbName}.tp_dvc_vehicle as v
	// 	 LEFT JOIN {$this->dbName}.tp_dvc_car as car on v.id = car.device_id
	// 	 WHERE {$where} order by v.id desc limit {$firstRow},{$listRows}";
	// 	 $row = $this->query($sql);
	// 	 return $row;
	// }
	/**
	*获取设备单个信息
	* $where 条件 array
	* $field 指定字段
	*/
	public function getInfo($where,$field = "*")
	{
		return $this->field($field)->where($where)->find();
	}
	/**
	*获取设备信息
	* $where 条件 array
	* $field 指定字段
	*/
	public function getData($where,$field = "*")
	{
		return $this->field($field)->where($where)->select();
	}
	/**
	*添加设备信息
	* $array 添加数据 array
	*/
	public function addVehicle($array)
	{
		try
		{
			$row = @$this->add($array);
		}
		catch(Exception $e)
		{
			$row = null;
		}
		return $row;
	}
	/**
	*批量添加设备
	* $array 添加数据 array
	*/
	// public function importVehicle($array)
	// {
	// 	if(is_array($array))
	// 	{
	// 		$sql = "insert into {$this->dbName}.tp_dvc_vehicle (device_no,device_com,device_type,device_model,organ_id,city_code) values ";
	// 		foreach ($array as $key => $val) 
	// 		{
	// 			$sql .= "('".$val['device_id']."',".$val['device_com'].",".$val['device_type'].",".$val['device_model'].",".$val['organ_id'].",'".$val['city_code']."'),";
	// 		}
	// 		$sql = substr($sql,0,-1);
	// 		try {
	// 			$row = $this->execute($sql);
	// 		} catch (Exception $e) {
	// 			$row = null;
	// 		}
	// 		return $row;
	// 	}
	// }

	/**
	*修改设备信息
	* $where 条件 array
	* $array 修改数据 array
	*/
	public function editVehicle($where,$array)
	{
		return $this->where($where)->data($array)->save();
	}
	/**
	*删除设备信息
	* $id 设备主键
	*/
	public function delVehicle($id)
	{
		return $this->delete($id);
	}
	//获取导出数据
	public function getVehicleOut($where,$table)
	{
		$sql = "select v.id,v.device_no,vender.vender_name as device_com,type.device_type_name as device_type,model.device_model_name as device_model,v.organ_id,v.active_status,v.is_car,v.is_use,sim.imsi,sim.sim_iccid,sim.msisdn,sim.total_flow,sim.package_month,user.tel,car.car_no,car.v_code from {$this->dbName}.tp_dvc_vehicle as v
		 left join dvc.tp_dvc_hardware_vender as vender on v.device_com = vender.vender_id 
		 left join dvc.tp_dvc_device_type as type on v.device_type = type.device_type_id 
		 left join dvc.tp_dvc_device_model as model on v.device_model = model.device_model_id 
		 left join {$this->dbName}.tp_dvc_sim as sim on v.device_no = sim.device_no 
		 left join {$table}.tp_user as user on v.device_no = user.device_no 
		 left join {$table}.tp_car as car on v.id = car.device_id 
		 where {$where}";
		 $row = $this->query($sql);
		 return $row;
	}
}