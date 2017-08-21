<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model
{
	protected $dbName;
	protected $trueTableName = 'tp_user';
	public function __construct($dbName) 
	{
		$this->dbName = $dbName;
        parent::__construct();
	}
    /**
    * 获取所有用户信息
    * $where 查询条件 string
    * $firstRow 分页
    * $listRows 分页
    */
	public function userList($where,$firstRow,$listRows)
	{
		$sql = "SELECT u.user_id,u.device_id,if(u.is_binded_device='2','无','有') as is_device,u.tel,u.create_time,cb.car_series as car_band,car.vin as v_code,car.service_provider_code as sale_id from {$this->dbName}.tp_user as u
		 left join {$this->dbName}.tp_car as car on u.car_id = car.car_id
         left join biz.tp_car_type_view as cb on car.car_type_id = cb.car_type_id
         left join {$this->dbName}.tp_car_plate_rel as cpr on car.car_id = cpr.car_id
         left join {$this->dbName}.tp_plate as cp on cpr.plate_id = cp.plate_id
		 left join {$this->dbName}.tp_device as v on u.device_id = v.device_id
		 left join {$this->dbName}.tp_sim_card as sim on sim.device_id = v.device_id
         left join biz.tp_car_series as series on car.car_series_id = series.car_series_id
		 left join biz.tp_car_type as type on car.car_type_id = type.car_type_id
		 where {$where} 
		 group by u.user_id
		 order by u.user_id desc limit {$firstRow},{$listRows}";
		$row = $this->query($sql);
		return $row;
	}
    /**
    * 获取所有用户数量信息
    * $where 查询条件 string
    */
    public function userCnt($where)
    {
        $sql = "SELECT count(1) as cnt from {$this->dbName}.tp_user as u
         left join {$this->dbName}.tp_car as car on u.car_id = car.car_id
         left join {$this->dbName}.tp_car_plate_rel as cpr on car.car_id = cpr.car_id
         left join {$this->dbName}.tp_plate as cp on cpr.plate_id = cp.plate_id
		 left join {$this->dbName}.tp_device as v on u.device_id = v.device_id
		 left join {$this->dbName}.tp_sim_card as sim on sim.device_id = v.device_id
         left join biz.tp_car_series as series on car.car_series_id = series.car_series_id
		 left join biz.tp_car_type as type on car.car_type_id = type.car_type_id
         where {$where}";
        $row = $this->query($sql);
        return $row;
    }
    /**
    * 获取用户详情
    * $id 用户id
    * $db 设备数据库
    */
	public function getInfo($id)
	{
		$sql = "SELECT u.user_id,u.nickname,u.device_id,v.device_no,u.portrait_url as portrait,u.tel,u.create_time as user_create_time,u.is_binded_device as is_bind,car.service_provider_code as sale_id,type.device_type as device_type_name,v.status as active_status,v.create_time as v_create_time,cb.car_type as car_band,cb.car_series as car_serious,car.vin as v_code,car.owner_name as owner,cp.plate_no as car_no,car.engine_no as e_code,sim.imsi,sim.is_binded_device as bind_status,sim.activated_time as active_time,sim.plan_term as package_month,sim.total_flow from {$this->dbName}.tp_user as u
         left join {$this->dbName}.tp_car as car on u.car_id = car.car_id
         left join biz.tp_car_type_view as cb on car.car_type_id = cb.car_type_id
         left join {$this->dbName}.tp_car_plate_rel as cpr on car.car_id = cpr.car_id
         left join {$this->dbName}.tp_plate as cp on cpr.plate_id = cp.plate_id
		 left join {$this->dbName}.tp_device as v on u.device_id = v.device_id
		 left join {$this->dbName}.tp_sim_card as sim on sim.device_id = v.device_id
         left join biz.tp_device_series as series on v.device_series_id = series.device_series_id
		 left join biz.tp_device_type as type on series.device_type_id = type.device_type_id
		 where u.user_id = '{$id}'";
		$row = $this->query($sql);
		return $row;
	}

}