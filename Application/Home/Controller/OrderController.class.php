<?php
namespace Home\Controller;
use Home\Common\MyController;
class OrderController extends MyController 
{
    function __construct()
    {
        parent::__construct();
    }
    /**
    *订单列表页
    */
    public function orderList()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('orderList',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('orderList');
    }
    /**
    *订单列表数据
    */
    public function orderListAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('orderList',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $where = "1=1 and `order`.order_type = 1";
        //订单状态
        if(I('param.status') != '')
        {
            $where .= " and `order`.order_status = ".I('param.status');
        }
        else
        {
            $where .= " and `order`.order_status = 1";
        }
        //手机号
        if(I('param.tel'))
        {
            $where .= " and `user`.tel = ".I('param.tel');
        }
        //订单号
        if(I('param.order_no'))
        {
            $where .= " and `order`.order_no = ".I('param.order_no');
        }
        //时间
        if(I('param.status_time'))
        {
            $time = array_map(function (&$v) {
                if ($v) {
                    return strtotime($v);
                }
                return null;
            }, explode(' - ',I('param.status_time')));
            if ($time[0]) {
                $where .= " and `order`.order_status_time >= {$time[0]}";
            }

            if ($time[1]) {
                $where .= " and `order`.order_status_time <= {$time[1]}";
            }
        }
        //套餐种类
        if(I('param.unicom_id'))
        {
            $where .= " and unicom.unicom_plan_id = ".I('param.unicom_id');
        }
        // 查询满足要求的总记录数
        $count = $this->orderDB->orderCnt($where);
        // 实例化分页类
        $page = $this->getPage($count[0]['cnt'],$where);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $data = $this->orderDB->orderList($where,$page['firstRow'],$page['listRows']);
        $status = $this->orderStatusDB->field('order_no,status,status_time')->select();
        foreach ($status as $key => &$val) 
        {
            switch ($val['status']) 
            {
                case 0:
                    $val['status_type'] = '待支付';
                    break;
                case 1:
                    $val['status_type'] = '待支付';
                    break;
                case 2:
                    $val['status_type'] = '已取消';
                    break;
                case 3:
                    $val['status_type'] = '已完成';
                    break;
                
                default:
                    break;
            }
            $val['status_time'] = date("Y-m-d H:i:s",$val['status_time']);
            $new_status[$val['order_no']][] = $val;
        }
        //导出标识
        if(I('param.arr'))
        {
            if(I('param.arr') != 'all')
            {
                $where .= " and `order`.order_id in (".I('param.arr').")";
            }
            $data = $this->orderDB->orderList($where,0,$count[0]['cnt']);
        }
        foreach ($data as $key => &$val) 
        {
            switch ($val['status']) 
            {
                case 0:
                    $val['status_type'] = '待支付';
                    break;
                case 1:
                    $val['status_type'] = '待处理';
                    break;
                case 2:
                    $val['status_type'] = '已取消';
                    break;
                case 3:
                    $val['status_type'] = '已完成';
                    break;
                default:
                    break;
            }
            $data[$key]['status_time'] = date("Y-m-d H:i:s",$val['status_time']);
            $data[$key]['order_status'] = $new_status[$val['order_no']];
        }
        //导出
        if(I('param.fileOut') == '1')
        {
            A('Excel')->OrderOut($data);
            exit;
        }
        echo json_encode(array('data' => $data,'page' => $page['show']));
        exit;
    }
    /**
    *订单处理
    */
    public function orderHandle()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('orderHandle',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        //事务开启
        M()->startTrans();
        if(I('param.status') == 'more')
        {
            $where['order_no'] = array('in',I('param.id'));
            $telWhere['`order`.order_no'] = array('in',I('param.id'));

            $idArr = explode(',',I('param.id'));
            foreach ($idArr as $key => $val) 
            {
               $data[$key]['status'] = 3;
               $data[$key]['status_time'] = time();
               $data[$key]['order_no'] = $val;
            }
            $statusId = $this->orderStatusDB->addAll($data);
        }
        else
        {
            $where['order_no'] = I('param.id');
            $telWhere['`order`.order_no'] = I('param.id');
            $statusId = $this->orderStatusDB->data(array('status' => 3,'status_time' => time(),'order_no' => I('param.id')))->add();
        }
        $handleId = $this->orderDB->data(array('order_status' => 3,'order_status_time' => time()))->where($where)->save();
        if($handleId > 0 && $statusId > 0)
        {
            $telArr = array();
            $tel = $this->orderDB->getOrderTel($telWhere);
            foreach ($tel as $key => $val) 
            {
                A('Public')->sms($val['tel'],$val['unicom_set']);
            }
            //事务提交
            M()->commit();
            $msg = '订单处理成功!';
            $status = 1;
        }
        else
        {
            //事务回滚
            M()->rollback();
            $msg = '订单处理失败';
            $status = 0;
        }
        echo json_encode(array('msg' => $msg,'status' => $status));
        $order = $this->orderDB->orderInfo(I('param.id'));
        foreach ($order as $key => $val) 
        {
            $log = '手机号为'.$val['tel'].'的用户在'.date('Y-m-d H:i:s',$val['status_time']).'创建的编号为'.$val['order_no'].'的';
            A('System')->addLog(array('text' => $log.$msg,'platform_id' => 2,'account_id' => session('account_id')));
        }
        exit;
    }
    
}