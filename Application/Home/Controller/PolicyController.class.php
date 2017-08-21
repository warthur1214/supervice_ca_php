<?php
namespace Home\Controller;
use Home\Common\MyController;
class PolicyController extends MyController 
{
    function __construct()
    {
        parent::__construct();
    }
    /**
    *维保券列表页
    */
    public function policyList()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('policyList',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('policyList');
    }
    /**
    *维保券列表数据
    */
    public function policyListAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('policyList',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $where = "1=1";
        $where .= " and p.policy_no != ''";
        //手机号搜索
        if(I('param.tel'))
        {
            $where .= " and u.tel like '%".I('param.tel')."%'";
        }
        //保单号搜索
        if(I('param.bd_num'))
        {
            $where .= " and p.policy_no like '%".I('param.bd_num')."%'";
        }
        //维保券状态搜索
        if(I('param.is_acceapt'))
        {
            $where .= " and p.status = ".I('param.is_acceapt');
        }
        else
        {
            $where .= " and p.status = 0 ";
        }
        //导出标识
        if(I('param.arr'))
        {
            if(I('param.arr') != 'all')
            {
                $where .= " and car.car_id in (".I('param.arr').")";
            }
        }
        $data = $this->carDB->policyList($where);
        foreach ($data as $key => &$val) 
        {
            $val = array_map(array($this, 'filterNull'), $val);
            $out[$key] = $val;
            switch ($val['is_acceapt']) 
            {
                case '0':
                    $out[$key]['is_acceapt'] = '待审核';
                    break;
                case '1':
                    $out[$key]['is_acceapt'] = '审核失败';
                    break;
                case '2':
                    $out[$key]['is_acceapt'] = '审核成功';
                    break;
            }
        }

        if(I('param.fileOut') == '1')
        {
            A('Excel')->policyOut($out);
            exit;
        }
        echo json_encode(array('data' => $data));
        exit;
    }
    /**
    *通过审核
    */
    public function passPolicy()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('passPolicy',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $tel = $this->carDB->getUser(I('get.id'));
        $id = $this->carDB->data(array('status' => '2'))->where(array('user_id' => I('get.id')))->save();


        $msg = ($id > 0) ? '通过成功' : '通过失败';
        $status = ($id > 0) ? '1' : '0';

        A('System')->addLog(array('text' => $tel[0]['tel'].'提交的维保券审核'.$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *拒绝审核
    */
    public function refusePolicy()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('refusePolicy',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $tel = $this->carDB->getUser(I('get.id'));
        $id = $this->carDB->data(array('status' => '1'))->where(array('user_id' => I('get.id')))->save();
        $msg = ($id > 0) ? '拒绝成功' : '拒绝失败';
        $status = ($id > 0) ? '1' : '0';
        
        A('System')->addLog(array('text' => $tel[0]['tel'].'提交的维保券审核'.$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    
}