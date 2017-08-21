<?php
namespace Home\Controller;
use Home\Common\MyController;
class IntegralController extends MyController 
{
    function __construct()
    {
        parent::__construct();
    }
    /**
    *积分列表页
    */
    public function integralList()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('integralList',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('integralList');
    }
    /**
    *积分列表数据
    */
    public function integralListAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('integralList',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $where = "1=1";
        //手机号搜索
        if(I('param.tel'))
        {
            $where .= " and user.tel like '%".I('param.tel')."%'";
        }
        //积分数量区间搜索
        if(I('param.s_no'))
        {
            $where .= " and inte.total_point >= '".I('param.s_no')."'";
        }
        //积分数量区间搜索
        if(I('param.e_no'))
        {
            $where .= " and inte.total_point <= '".I('param.e_no')."'";
        }

        //查询数据库条件
        $dbWhere['TABLE_SCHEMA'] = array('like','ubi_'.session('organ_channel_id').'_0_%');
        $dbWhere['TABLE_NAME'] = array('like','%tp_sgl_jny_analysis_%');

        $changeDay = null;
        $searchWhere = "";
        if (I("post.change_day")) {
            $changeDay = array_map(function (&$v) {
                return strtotime($v);
            }, explode("+-+", I("post.change_day")));

            $changeDay[1] = $changeDay[1] + 86399;

            $searchWhere .= " and pr.occur_time between {$changeDay[0]} and {$changeDay[1]}";
        }

        if (I("post.change_no")) {
            $change_no = I("post.change_no");
            if ($change_no == "3000以上") {
                $searchWhere .= " and abs(pr.point) > 3000";
            } else {
                $changeNo = explode("-", I("post.change_no"));
                $searchWhere .= " and pr.point between {$changeNo[0]} and {$changeNo[1]}";
            }
        }

        if (I("post.change_type")) {
            $changeType = I("post.change_type");

            $searchWhere .= $changeType == "+" ? " and pr.point > 0" : "and pr.point < 0";
        }

        // 查询满足要求的总记录数
        $count = $this->integralDB->inteCnt($where, $searchWhere);
        // 实例化分页类
        $page = $this->getPage($count[0]['cnt'],$where);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $data = $this->integralDB->integralList($where, $page['firstRow'],$page['listRows'],I('param.order'), $searchWhere);

        echo json_encode(array('data' => $data,'page' => $page['show']));
        exit;
    }
    /**
    *积分日志
    */
    public function integralInfo()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('integralInfo',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('integralInfo');
    }
    /**
    *积分日志数据
    */
    public function integralInfoAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('integralInfo',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        //获取行程表 
        $ubitb = A('DBUtil')->getJnyTableName(session('organ_channel_id').'_0', I('get.id'), time());
        $where = array('user_id' => I('get.id'));
        $info = $this->integralDB->field('total_point as total_integral')->where($where)->find();
        // 查询满足要求的总记录数
        $count = $this->integralDB->getUbiInteCnt($ubitb,I('get.id'));
        // 实例化分页类
        $page = $this->getPage($count[0]['cnt'],$where);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $ubi_inte = $this->integralDB->getUbiInte($ubitb,I('get.id'),$page['firstRow'],$page['listRows']);
        
        foreach ($ubi_inte as $key => $val) 
        {
            $ubi_inte[$key]['integral'] = ($val['integral'] > 0) ? '+'.$val['integral'] : $val['integral'];
            $ubi_inte[$key]['total_integral'] = $info['total_integral'];
        }
        echo json_encode(array('data' => $ubi_inte,'page' => $page['show']));
        exit;
    }
}