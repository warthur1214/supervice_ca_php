<?php
namespace Home\Controller;
use Home\Common\MyController;
class SetController extends MyController 
{
    function __construct()
    {
        parent::__construct();
    }
    /**
    *套餐列表页
    */
    public function setList()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('setList',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('setList');
    }
    /**
    *套餐列表数据
    */
    public function setListAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('setList',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $where = "1=1";

        // 查询满足要求的总记录数
        $count = $this->setDB->setCnt($where);
        // 实例化分页类
        $page = $this->getPage($count[0]['cnt'],$where);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $data = $this->setDB->setList($where,$page['firstRow'],$page['listRows']);

        echo json_encode(array('data' => $data,'page' => $page['show']));
        exit;
    }
    /**
    *添加套餐页
    */
    public function addSet()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('addSet',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('addSet');
    }
    /**
    *添加套餐数据
    */
    public function addSetAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('addSet',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $array = array(
            'plan_name' => I('post.set_name'),
            'image_url' => I('post.img_url'),
            'unicom_plan_id' => I('post.unicom_set_id'),
            'cost_price' => I('post.market_price'),
            'price' => I('post.current_price'),
            'point' => I('post.exchange_integral'),
            'is_limit_time_discount' => I('post.limited_time_discount'),
            'discount_price' => I('post.discount_price') ? I('post.discount_price') : 0,
            'discount_start_time' => strtotime(I('post.discount_start_time')) ? strtotime(I('post.discount_start_time')) : 0,
            'discount_end_time' => strtotime(I('post.discount_end_time'))? strtotime(I('post.discount_end_time')) : 0,
            'desc' => I('post.integral_explain'),
            'plan_detail' => I('post.set_detail')
            );

        $insertId = $this->setDB->data($array)->add();

        $msg = ($insertId > 0) ? '添加成功' : '添加失败';
        $status = ($insertId > 0) ? 1 : 0;

        A('System')->addLog(array('text' => '套餐'.I('post.set_name').$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *编辑套餐页
    */
    public function editSet()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('editSet',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('editSet');
    }
    /**
    *获取套餐详情
    */
    public function getInfo()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('editSet',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $field = array('plan_name as set_name','image_url as img_url','unicom_plan_id as unicom_set_id','cost_price as market_price','price as current_price','`point` as exchange_integral','is_limit_time_discount as limited_time_discount','discount_price','discount_start_time','discount_end_time','`desc` as integral_explain','plan_detail as set_detail');
        $info = $this->setDB->field($field)->where(array('plan_id' => I('get.id')))->find();
        $info['discount_start_time'] = $info['discount_start_time'] != 0 ? date('Y-m-d H:i',$info['discount_start_time']) : null;
        $info['discount_end_time'] = $info['discount_end_time'] != 0 ? date('Y-m-d H:i',$info['discount_end_time']) : null;
        
        $info = array_map(array($this,'filterNull'),$info);

        echo json_encode($info);
        exit;
    }
    /**
    *编辑套餐数据
    */
    public function editSetAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('editSet',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $array = array(
            'plan_name' => I('post.set_name'),
            'image_url' => I('post.img_url'),
            'unicom_plan_id' => I('post.unicom_set_id'),
            'cost_price' => I('post.market_price'),
            'price' => I('post.current_price'),
            'point' => I('post.exchange_integral'),
            'is_limit_time_discount' => I('post.limited_time_discount'),
            'discount_price' => I('post.discount_price') ? I('post.discount_price') : 0,
            'discount_start_time' => strtotime(I('post.discount_start_time')) ? strtotime(I('post.discount_start_time')) : 0,
            'discount_end_time' => strtotime(I('post.discount_end_time')) ? strtotime(I('post.discount_end_time')) : 0,
            'desc' => I('post.integral_explain'),
            'plan_detail' => I('post.set_detail')
            );
        $saveId = $this->setDB->data($array)->where(array('plan_id' => I('post.id')))->save();
        
        $msg = ($saveId > 0) ? '编辑成功' : '编辑失败或未编辑';
        $status = ($saveId > 0) ? 1 : 0;

        A('System')->addLog(array('text' => '套餐'.I('post.set_name').$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *删除套餐
    */
    public function delSet()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('delSet',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $where['`set`.plan_id'] = I('get.id');
        $where['`order`.order_type'] = 1;
        $order = $this->orderDB->getOrderByPlan($where);
        $set = $this->setDB->field('plan_name')->where(array('plan_id' => I('get.id')))->find();
        if($order)
        {
            $msg = '该套餐有订单记录,不可删除';
            $status = 0;
        }
        else
        {
            $id = $this->setDB->where(array('plan_id' => I('get.id')))->delete();
            $msg = ($id > 0) ? '删除成功' : '删除失败';
            $status = ($id > 0) ? 1 : 0;
        }

        A('System')->addLog(array('text' => '套餐'.$set['plan_name'].$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *置顶
    */
    public function changeTop()
    {
        //事务开启
        M()->startTrans();
        $this->setDB->data(array('is_sticked' => 0))->where(array('is_sticked' => 1))->save();
        $id = $this->setDB->data(array('is_sticked' => I('param.is_top')))->where(array('plan_id' => I('param.id')))->save();
        $top = (I('param.is_top') == 1) ? '置顶' : '非置顶';
        if($id >= 0)
        {
            //事务提交
            M()->commit();
            $msg = $top.'状态修改成功';
            $status = 1;
        }
        else
        {
            //事务回滚
            M()->rollback();
            $msg = $top.'状态修改失败';
            $status = 0;
        }

        $set = $this->setDB->field('plan_name')->where(array('plan_id' => I('param.id')))->find();
        A('System')->addLog(array('text' => '套餐'.$set['plan_name'].$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *上架
    */
    public function changeSale()
    {
        $id = $this->setDB->data(array('is_on_shelve' => I('param.is_sale')))->where(array('plan_id' => I('param.id')))->save();
        $sale = (I('param.is_sale') == 1) ? '上架' : '下架';
        $msg = ($id > 0) ? $sale.'状态修改成功' : $sale.'状态修改失败';
        $status = ($id > 0) ? '1' : '0';

        $set = $this->setDB->field('plan_name')->where(array('plan_id' => I('param.id')))->find();
        A('System')->addLog(array('text' => '套餐'.$set['plan_name'].$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *联通套餐包
    */
    public function unicomSet()
    {
        $data = $this->unicomSetDB->field('unicom_plan_id as id,unicom_plan as unicom_set')->select();

        echo json_encode(array('data' => $data));
        exit;
    }
    
}