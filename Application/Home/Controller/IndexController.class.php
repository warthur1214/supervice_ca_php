<?php
namespace Home\Controller;
use Home\Common\MyController;
class IndexController extends MyController 
{
    function __construct()
    {
        parent::__construct();
    }
    /**
    *首页 frame
    */
    public function index()
    {
        $this->display('Index/index');
    }
    /**
    *frame头部 
    */
    public function top()
    {
        $this->display('Index/top');
    }
    /**
    *机构归属
    */
    public function sonParent()
    {
        if($this->belong_organ())
        {
            $id = $this->belong_organ_parent();
        }
        else
        {
            $id = '0';
        }
        $pid = I('get.pid') ? I('get.pid') : $id;
        $data = $this->getList($pid,'organ.organ_id,organ.organ_name,organ.channel_id','organ.parent_organ_id',' and organ.parent_organ_id = 0');
        echo json_encode(array('data' => $data,'organ_id' => session('organ_id')));
        exit;
    }
    /**
    *获取选择的企业机构标识 
    */
    public function saveChannel()
    {
        session('organ_id',I('post.organ_id'));
        $organ = $this->organDB->field('channel_id,parent_organ_id')->where(array('organ_id' => I('post.organ_id')))->find();
        session('organ_channel_id',$organ['channel_id']);
        session('parent_organ_id',$organ['parent_organ_id']);
        echo json_encode(array('status' => 1));
        exit;
    }
    /**
    *frame选项栏
    */
    public function menu()
    {
        //获取当前帐号的角色的模块信息
        $module = $this->login_role_module();
        $pWhere = array('parent_module_id' => 0,'is_visible' => 1,'platform_id' => C('PLATFORM_ID'));
        $menu = $this->moduleDB->field('module_id,module_name,module_url')->where($pWhere)->order('sort_no desc,module_id asc')->select();
        foreach ($menu as $key => $val) 
        {
            $where['module_id'] = array('in',$module);
            $where['parent_module_id'] = array('eq',$val['module_id']);
            $where['is_visible'] = array('eq',1);
            $menu[$key]['menu_two'] = $this->moduleDB->field('module_id,module_name,module_url')->where($where)->order('sort_no desc,module_id asc')->select();
        }
        $this->assign('menu',$menu);
        $this->display('Index/menu');
    }
    /**
    *首页
    */
    public function main()
    {
        //获得当前时间
        $week = array("日","一","二","三","四","五","六");
        $date = date('Y年m月d日',time()).'，星期'.$week[date('w')].' 北京时间：'.date('H:i:s',time());
        $accountId = session('account_id');
        //获取当前角色
        $display_name = $this->accountDB->field('real_name as display_name')->where(array('account_id' => session('account_id')))->find();
        
        $this->assign('display_name',$display_name['display_name']);
        $this->assign('date',$date);
        $this->display('Index/main');
    }
    /**
    *账号信息
    */
    public function accountInfo()
    {
        //获取当前角色
        $info = $this->accountDB->field('account_id,real_name as display_name')->where(array('account_id' => session('account_id')))->find();
        echo json_encode($info);
        exit;
    }

}