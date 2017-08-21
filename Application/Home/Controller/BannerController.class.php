<?php
namespace Home\Controller;
use Home\Common\MyController;
class BannerController extends MyController 
{
    function __construct()
    {
        parent::__construct();
    }
    /**
    *banner列表页
    */
    public function bannerList()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('bannerList',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('bannerList');
    }
    /**
    *banner列表数据
    */
    public function bannerListAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('bannerList',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $where = "is_deleted = '0'";
        if(I('param.active_name'))
        {
            $where .= " and name like '%".I('param.active_name')."%'";
        }
        $data = $this->bannerDB->bannerList($where);
        foreach ($data as $key => &$val) 
        {
            if($val['native_name'])
            {
                $article = $this->articleDB->field('title')->where(array('article_id' => $val['native_name']))->find();
                $val['active_url'] = '文章内链:'.$article['title'];
            }
            $val = array_map(array($this, 'filterNull'), $val);
        }
        echo json_encode(array('data' => $data));
        exit;
    }
    /**
    *添加banner页
    */
    public function addBanner()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('addBanner',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('addBanner');
    }
    /**
    *添加banner数据
    */
    public function addBannerAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('addBanner',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        if(I('post.active_url_type') == 1)
        {
            $url = trim(I('post.active_url'));
        }
        else
        {
            $url = 'http://'.$_SERVER['HTTP_HOST'].'/Home/ArticleInfo/index/id/'.I('post.native_name').'/channel/'.session('organ_channel_id');
        }
        $array = array(
            'type' => I('post.new_banner_type'),
            'name' => trim(I('post.active_name')),
            'begin_date' => I('post.begin_date') ? I('post.begin_date') : date('Y-m-d'),
            'end_date' => I('post.end_date') ? I('post.end_date') : '9999-12-31',
            'redirect_url' => $url,
            'redirect_item_id' => I('post.native_name'),
            'redirect_url_type' => I('post.active_url_type'),
            'image_url' => I('post.picture_url')
            );

        $insertId = $this->bannerDB->data($array)->add();

        $msg = ($insertId > 0) ? '添加成功' : '添加失败';
        $status = ($insertId > 0) ? 1 : 0;

        A('System')->addLog(array('text' => 'banner-'.trim(I('post.active_name')).$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *编辑banner页
    */
    public function editBanner()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('editBanner',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('editBanner');
    }
    /**
    *获取banner详情
    */
    public function getInfo()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('editBanner',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $field = array('banner_id as id','type as new_banner_type','name as active_name','redirect_url as active_url','image_url as picture_url','begin_date','end_date','redirect_url_type as active_url_type','redirect_item_id as native_name');
        $info = $this->bannerDB->field($field)->where(array('banner_id' => I('get.id')))->find();

        $article = $this->articleDB->field('title')->where(array('article_id' => $info['native_name']))->find();
        $info['article_title'] = $article['title'];
        $info = array_map(array($this,'filterNull'),$info);
        echo json_encode($info);
        exit;
    }
    /**
    *编辑banner数据
    */
    public function editBannerAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('editBanner',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        if(I('post.active_url_type') == 1)
        {
            $url = trim(I('post.active_url'));
        }
        else
        {
            $url = 'http://'.$_SERVER['HTTP_HOST'].'/Home/ArticleInfo/index/id/'.I('post.native_name').'/channel/'.session('organ_channel_id');
        }
        $array = array(
            'type' => I('post.new_banner_type'),
            'name' => trim(I('post.active_name')),
            'begin_date' => I('post.begin_date') ? I('post.begin_date') : date('Y-m-d'),
            'end_date' => I('post.end_date') ? I('post.end_date') : '9999-12-31',
            'redirect_url' => $url,
            'redirect_item_id' => I('post.native_name'),
            'redirect_url_type' => I('post.active_url_type'),
            'image_url' => I('post.picture_url')
            );
        
        $saveId = $this->bannerDB->data($array)->where(array('banner_id' => I('post.id')))->save();

        $msg = ($saveId > 0) ? '编辑成功' : '编辑失败或未编辑';
        $status = ($saveId > 0) ? 1 : 0;

        A('System')->addLog(array('text' => 'banner-'.trim(I('post.active_name')).$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *删除banner
    */
    public function delBanner()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('delBanner',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $info = $this->bannerDB->field('name')->where(array('banner_id' => I('get.id')))->find();
        $id = $this->bannerDB->data(array('is_deleted' => 1,'is_sticked' => 0,'is_available' => 0,'stick_time' => '0000-00-00 00:00:00'))->where(array('banner_id' => I('get.id')))->save();
        $msg = ($id > 0) ? '删除成功' : '删除失败';
        $status = ($id > 0) ? '1' : '0';
        
        A('System')->addLog(array('text' => 'banner-'.$info['name'].$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *置顶
    */
    public function changeTop()
    {
        if(I('param.top') != '0')
        {
            $top = $this->bannerDB->field('banner_id')->where(array('is_sticked' => I('param.top')))->find();
        }
        if($top)
        {
            echo json_encode(array('msg' => '该置顶层级已存在,请检查','status' => 0));
            exit;
        }
        $info = $this->bannerDB->field('name')->where(array('banner_id' => I('param.id')))->find();
        switch (I('param.top')) 
        {
            case '1':
                $data = array('is_sticked' => 1,'is_available' => 1,'top_time' => date('Y-m-d H:i:s'));
                $log = 'banner-'.$info['name'].'一级置顶';
                break;
            case '2':
                $data = array('is_sticked' => 2,'is_available' => 1,'top_time' => date('Y-m-d H:i:s'));
                $log = 'banner-'.$info['name'].'二级置顶';
                break;
            case '3':
                $data = array('is_sticked' => 3,'is_available' => 1,'top_time' => date('Y-m-d H:i:s'));
                $log = 'banner-'.$info['name'].'三级置顶';
                break;
            case '4':
                $data = array('is_sticked' => 4,'is_available' => 1,'top_time' => date('Y-m-d H:i:s'));
                $log = 'banner-'.$info['name'].'四级置顶';
                break;
            case '5':
                $data = array('is_sticked' => 5,'is_available' => 1,'top_time' => date('Y-m-d H:i:s'));
                $log = 'banner-'.$info['name'].'五级置顶';
                break;
            default:
                $data = array('is_sticked' => 0,'is_available' => 0,'top_time' => '0000-00-00 00:00:00');
                $log = 'banner-'.$info['name'].'失效';
                break;
        }
        $id = $this->bannerDB->data($data)->where(array('banner_id' => I('param.id')))->save();
        $msg = ($id > 0) ? '状态修改成功' : '状态修改失败';
        $status = ($id > 0) ? '1' : '0';
        
        A('System')->addLog(array('text' => $log.$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *生效
    */
    // public function changeUse()
    // {
    //     $info = $this->bannerDB->field('active_name')->where(array('id' => I('param.id')))->find();
    //     $log = (I('param.use') == 1) ? 'banner'.$info['active_name'].'失效' : 'banner'.$info['active_name'].'生效';
        
    //     $id = $this->bannerDB->data(array('status' => I('param.use')))->where(array('id' => I('param.id')))->save();
    //     $msg = ($id > 0) ? '状态修改成功' : '状态修改失败';
    //     $status = ($id > 0) ? '1' : '0';
        
    //     A('System')->addLog(array('log' => $log.$msg,'system_name' => '欧尚评驾运营管理平台','account_id' => session('account_id')));

    //     echo json_encode(array('msg' => $msg,'status' => $status));
    //     exit;
    // }
}