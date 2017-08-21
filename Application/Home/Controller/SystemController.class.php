<?php
namespace Home\Controller;
use Home\Common\MyController;
class SystemController extends MyController 
{
    function __construct()
    {
        parent::__construct();
    }
    /**
    *系统日志列表页
    */
    public function logList()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('logList',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('logList');
    }
    /**
    *系统日志列表数据
    */
    public function logListAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('logList',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $where = "platform_id = 2";
        if(session('account_id') != '1')
        {
            $where .= " and log.account_id != '1'";
            if($this->belong_organ() != '0')
            {
                $where .= " and log.account_id = '".session('account_id')."'";
            }
        }
        // 查询满足要求的总记录数
        $count = $this->logDB->logCnt($where);
        // 实例化分页类
        $page = $this->getPage($count[0]['cnt'],$where);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $data = $this->logDB->logList($where,$page['firstRow'],$page['listRows']);
        
        echo json_encode(array('data' => $data,'page' => $page['show']));
        exit;
    }
    /**
    *生成日志
    */
    public function addLog($data)
    {
        $this->logDB->data($data)->add();
    }
    /**
    *修改密码数据
    */
    public function editPwdAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('editPwd',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/
        
        $where = array('account_id' => I('param.account_id'));
        //获取当前角色密码
        $info = $this->accountDB->field('password,real_name')->where($where)->find();
        if(I('param.password') != $info['password'])
        {

            echo json_encode(array('msg' => '请确认原密码是否正确','status' => 0));
            exit;
        }
        $saveId = $this->accountDB->data(array('password' => I('param.new_password')))->where($where)->save();

        $msg = ($saveId > 0) ? '密码修改成功' : '密码修改失败';
        $status = ($saveId > 0) ? 1 : 0;
        
        A('System')->addLog(array('text' => $info['real_name'].$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status)); 
        exit;
    }
}