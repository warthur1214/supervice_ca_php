<?php
namespace Home\Controller;
use Home\Common\MyController;
class UserController extends MyController 
{
    function __construct()
    {
        parent::__construct();
    }
    /**
    *用户列表页
    */
    public function userList()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('userList',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $where['car_brand'] = array('like','长安%');
        $brand = M('biz.car_type_view')->field('car_type_id,car_brand,car_series')->where($where)->group('car_series')->select();

        foreach ($brand as $key => $val) 
        {
            $data[$key]['car_brand_id'] = $val['car_series'];
            $data[$key]['car_brand'] = $val['car_brand'].' - '.$val['car_series'];
        }

        $this->assign('brand',$data);
        $this->display('userList');
    }
    /**
    *用户列表数据
    */
    public function userListAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('userList',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $where = "1=1";
        //手机号搜索
        if(I('param.tel'))
        {
            $where .= " and u.tel like '%".I('param.tel')."%'";
        }
        //车架号搜索
        if(I('param.v_code'))
        {
            $where .= " and car.vin like '%".I('param.v_code')."%'";
        }
        //经销商id搜索
        if(I('param.sale_id'))
        {
            $where .= " and car.sale_id = '".I('param.sale_id')."'";
        }
        //车型搜索
        if(I('param.car_band'))
        {
            $where .= " and series.car_series like '%".I('param.car_band')."%'";
        }
        //车系搜索
        if(I('param.car_serious'))
        {
            $where .= " and type.car_type_id = ".I('param.car_serious');
        }
        //有车牌号搜索
        if(I('param.is_carNo'))
        {
            $where .= " and (cpr.plate_id != 0)";
        }
        //有车主搜索
        if(I('param.is_owner'))
        {
            $where .= " and (car.owner_name != '')";
        }
        //有发动机号搜索
        if(I('param.is_eCode'))
        {
            $where .= " and (car.engine_no != '')";
        }
        //有硬件搜索
        if(I('param.is_device'))
        {
            $where .= " and (u.is_binded_device  = 1)";
        }
        if(I('param.create_time'))
        {
            $time = explode('+-+',I('param.create_time'));
            $where .= " and u.create_time between '".$time[0]."' and '".$time[1]."'";
        }
        // 查询满足要求的总记录数
        $count = $this->userDB->userCnt($where);
        // 实例化分页类
        $page = $this->getPage($count[0]['cnt'],$where);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $data = $this->userDB->userList($where,$page['firstRow'],$page['listRows']);
        foreach ($data as $key => &$val) 
        {
            $val = array_map(array($this, 'filterNull'), $val);
        }
        echo json_encode(array('data' => $data,'page' => $page['show']));
        exit;
    }
    /**
    *车系数据
    */
    public function getCarName()
    {
        $where['car_series'] = I('get.id');

        $arr = M('biz.car_type_view')->field('car_type_id,car_type')->where($where)->select();
        foreach ($arr as $key => $val) 
        {
            $data[$key]['car_series_id'] = $val['car_type_id'];
            $data[$key]['car_series'] = $val['car_type'];
        }

        echo json_encode($data);
        exit;
    }
    /**
     *经销商id数据
     */
    public function serviceId()
    {
        $serviceDB = D('Service');
        if(I('get.id'))
        {
            $where['service_provider_code'] = array('like',"%".I('param.id')."%");
        }
        $data = $serviceDB->field('service_provider_code')->where($where)->select();

        echo json_encode($data);
        exit;
    }
    /**
    *用户详细信息
    */
    public function userInfo()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('userInfo',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('userInfo');
    }
    /**
    *用户详细信息数据
    */
    public function userInfoAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('userInfo',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $info = $this->userDB->getInfo(I('get.id'));
        $info = $info[0];
        switch ($info['active_status']) {
            case '1':
                $info['active_status'] = "已激活";
                break;
            case '2':
                $info['active_status'] = "已损坏";
                break;
            default:
                $info['active_status'] = "未激活";
                break;
        }
        $info['is_bind'] = ($info['is_bind'] == '1') ? "有" : "无";
        $info['bind_status'] = ($info['bind_status'] == '1') ? "已激活" : "未激活";
        $year = $info['package_month'] / 12;
        $info['package_month'] = $year;
        $info['total_flow'] = $info['total_flow'] / 1024;
        if($info['active_time'])
        {
            $info['use_time'] = date('Y-m-d H:i:s',strtotime('+'.$year.' years',strtotime($info['active_time'])));
        }
        else
        {
            $info['use_time'] = '';
        }
        $info = array_map(array($this, 'filterNull'), $info);
        echo json_encode($info);
        exit;
    }
    
}