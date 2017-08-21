<?php
namespace Home\Common;
use Think\Controller;
class MyController extends Controller 
{
    public $organDB;//企业model
    public $accountDB;//帐号model
    public $accountRoleDB;//帐号角色关系model
    public $moduleDB; //模块model
    public $roleDB;  //角色model
    public $roleModuleDB;  //角色模块关系model
    public $roleOrganDB;  //角色企业关系model
    public $vehicleDB; //设备model
    public $carDB; //车辆model
    public $brandDB; //车辆品牌model
    public $userDB; //用户信息model
    public $integralDB;//用户积分model
    public $articleDB; //文章model
    public $bannerDB;//banner的model
    public $policyDB; //维保券model
    public $logDB;//系统日志的model
    public $orderDB; //订单model
    public $orderStatusDB;//订单状态的model
    public $setDB; //套餐model
    public $unicomSetDB;//联通套餐的model
    public $login_role;  //当前登录帐号所属角色
    public $organ_id;  //当前登录帐号所属企业
    public $organ_channel_id;  //选择要展示的信息所属企业id
	function __construct()
	{
		parent::__construct();
		$this->checkLogin();
        $dbName = "biz_".session('organ_channel_id');
        $this->organDB = D('Organ');
        $this->accountDB = D('Account');
        $this->accountRoleDB = D('AccountRoleRel');
        $this->moduleDB = D('Module');
        $this->roleDB = D('Role');
        $this->roleModuleDB = D('RoleModule');
        $this->roleOrganDB = D('RoleOrganRel');
        $this->vehicleDB = new \Home\Model\VehicleModel($dbName);
        $this->carDB = new \Home\Model\CarModel($dbName);
        $this->brandDB = D('Brand');
        $this->userDB = new \Home\Model\UserModel($dbName);
        $this->integralDB = new \Home\Model\IntegralModel($dbName);
        $this->articleDB = new \Home\Model\ArticleModel($dbName);
        $this->bannerDB = new \Home\Model\BannerModel($dbName);
        $this->logDB = D('Log');
        $this->orderDB = new \Home\Model\OrderModel($dbName);
        $this->orderStatusDB = new \Home\Model\OrderStatusModel($dbName);
        $this->setDB = new \Home\Model\SetModel($dbName);
        $this->unicomSetDB = new \Home\Model\UnicomSetModel($dbName);
        //根据当前登录帐号获取帐号角色
        $loginRole = $this->accountRoleDB->field('role_id')->where(array('account_id' => session('account_id')))->select();
        foreach ($loginRole as $key => $val) 
        {
            $login_role_id[] = $val['role_id'];
        }
        $this->login_role = implode(',',$login_role_id);
	}
    /**
     * 验证登陆状态
     */
    private function checkLogin()
    {
        //验证是否已经登陆
        if(!$_SESSION['account_id'])
        {
            echo "<script>window.top.location.href='/';</script>"; 
            exit;       
        }
	}
    /**
    *信息数组赋值null为空
    */
    public function filterNull($v) 
    {
        if (is_null($v)) 
        {
            return '';
        }
        return $v;
    }
    /**
    *企业信息无限极递归函数
    */
    public function getList($pid = '0',$field,$one = 'organ.parent_organ_id',$otherWhere = '')
    {
        $where = "1=1 and {$one} = '{$pid}' and organ.channel_id like 'ca_%' and organ.is_available = '1'".$otherWhere;
        if($this->login_role_organ())
        {
            $organ_id = $this->belong_organ().",".$this->login_role_organ();
        }
        else
        {
            $organ_id = $this->belong_organ();
        }
        $where .= " and organ.organ_id in (".$organ_id.")";
        $list = $this->organDB->organList($where,$field);
        if($list)
        {
            foreach ($list as $key => $val) 
            {
                if ($val['organ_id']) 
                {
                    $val['son'] = $this->getList($val['organ_id'],$field,$one,$otherWhere);
                }
                $array[] = $val;
            }
        }
        return $array;
    }
    /**
    *根据帐号获取所属机构信息
    */
    public function belong_organ()
    {
        $data = $this->accountDB->field('belonged_organ_id')->where(array('account_id' => session('account_id')))->find();
        return $data['belonged_organ_id'];
    }
    /**
    *根据帐号所属机构获取上级机构信息
    */
    public function belong_organ_parent()
    {
        $data = $this->organDB->field('parent_organ_id')->where(array('organ_id' => $this->belong_organ()))->find();
        return $data['parent_organ_id'];
    }
    /**
    *根据帐号角色获取机构信息
    */
    public function login_role_organ()
    {
        $where['role_id'] = array('in',$this->login_role);
        $data = $this->roleOrganDB->where($where)->select();
        foreach ($data as $key => $val) 
        {
            $role_organ[] = $val['organ_id'];
        }
        $organ = implode(',',array_filter(array_unique($role_organ)));
        return $organ;
    }
    /**
    *重组帐号角色管理机构信息
    */
    public function new_login_role_organ()
    {
        if($this->login_role_organ())
        {
            $organ_id = $this->login_role_organ().",".session('organ_id');
        }
        else
        {
            $organ_id = session('organ_id');
        }
        $where['organ_id'] = array('in',$organ_id);
        $data = $this->organDB->field('organ_id,channel_id')->where($where)->select();
        $info = array();
        foreach ($data as $key => $val) 
        {
            $info[$val['channel_id']][] = $val['organ_id'];
        }
        return $info;
    }
    /**
    *获取企业机构id
    */
    public function ownOrgan()
    {
        $organ_id = $this->new_login_role_organ()[session('channel_id')];
        
        $organ_id = implode(',',$organ_id);
        return $organ_id;
    }
    /**
    *根据帐号角色获取模块信息
    */
    public function login_role_module()
    {
        $where['role_id'] = array('in',$this->login_role);
        $data = $this->roleModuleDB->where($where)->select();
        foreach ($data as $key => $val) 
        {
            $role_module[] = $val['module_id'];
        }
        $module = implode(',',array_filter(array_unique($role_module)));
        return $module;
    }
    /**
    *分页公共函数
    */
    public function getPage($count,$where)
    {
        // 实例化分页类 传入总记录数和每页显示的记录数(20)
        $Page = new \Think\Page($count,20);
        $Page->setConfig('header','共<b>%TOTAL_ROW%</b>条记录&nbsp;&nbsp;每页<b>20</b>条&nbsp;&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页&nbsp;&nbsp;');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','首页');
        $Page->setConfig('last','末页');
        //分页跳转的时候保证查询条件
        foreach($where as $key=>$val) 
        {
            $Page->parameter[$key] = urlencode($val);
        }
        $show = $Page->show();// 分页显示输出
        return array('show' => $show,'firstRow' => $Page->firstRow,'listRows' => $Page->listRows);
    }	
}