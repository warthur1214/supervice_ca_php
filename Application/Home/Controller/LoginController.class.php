<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller 
{
    function __construct()
    {
        parent::__construct();
    }
	/**
	*登录页面或者首页
	*/
    public function index()
    {
    	if(session('account_id'))
    	{
        	$this->display('Index/index');
    	}
    	else
    	{
        	$this->display('Index/login');	
    	}
    }
	/**
	*登录数据处理
	*/
    public function loginAjax()
    {
    	$accountDB = D('Account');
        $account = $accountDB->field('account_id,password')->where(array('is_available' => 1,'account_name' => I('post.account_name')))->find();
        if(!empty($account))
        {

            $msg = (I('post.password') == $account['password']) ? '登录成功' : '账号或密码错误';
            $status = (I('post.password') == $account['password']) ? 1 : 0;

            if(I('post.password') == $account['password'])
            {

                $loginRole = D('AccountRoleRel')->field('role_id')->where(array('account_id' => $account['account_id']))->select();
                $login_role_id = [];
                foreach ($loginRole as $key => $val) {
                    $login_role_id[] = $val['role_id'];
                }
                $where['role_id'] = array('in', implode(',', $login_role_id));
                $data = D('RoleModule')->where($where)->select();
                if (!$data) {
                    $msg = '账号所属角色未分配功能权限，请联系管理员';
                    $status = 0;
                    echo json_encode(array('msg' => $msg, 'status' => $status));
                    exit;
                }

                session('account_id',$account['account_id']);
                //根据当前登录帐号获取帐号所属企业
                $organ_id = $accountDB->field('belonged_organ_id')->where(array('account_id' => $account['account_id']))->find();
                if($organ_id['belonged_organ_id'])
                {
                    //获取帐号所属企业的标识
                    $organ = M('organ')->field('channel_id,parent_organ_id')->where(array('organ_id' => $organ_id['belonged_organ_id']))->find();
                    //存储标识
                    session('organ_channel_id',$organ['channel_id']);
                    session('parent_organ_id',$organ['parent_organ_id']);
                    session('organ_id',$organ_id['belonged_organ_id']);
                }
                else
                {
                    //存储标识
                    session('organ_channel_id','ca_9999');
                    session('parent_organ_id','0');
                    session('organ_id','72');
                }
                //登录成功更新登录时间
                $accountDB->data(array('last_login_time' => date("Y-m-d H:i:s")))->where(array('account_id' => $account['account_id']))->save();

                A('System')->addLog(array('text' => $msg,'platform_id' => 2,'account_id' => session('account_id')));
            }
        }
    	else
    	{
    		$msg = '账号不存在或被冻结，请联系管理员';
    		$status = 0;
    	}
    	echo json_encode(array('msg' => $msg,'status' => $status));
    	exit;
    }
	/**
	*退出
	*/
    public function loginOut()
    {
        A('System')->addLog(array('text' => '退出登录','platform_id' => 2,'account_id' => session('account_id')));
    	header("Location:/");
    	session('account_id',null);
        exit(); 
    }
}