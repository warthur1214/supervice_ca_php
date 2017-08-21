<?php
namespace Home\Controller;
use Home\Common\MyController;
class ArticleController extends MyController 
{
    function __construct()
    {
        parent::__construct();
    }
    /**
    *文章列表页
    */
    public function articleList()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('articleList',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('articleList');
    }
    /**
    *文章列表数据
    */
    public function articleListAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('articleList',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $where = "1=1";
        if(I('param.article_title'))
        {
            $where .= " and art.title like '%".I('param.article_title')."%'";
        }
        $data = $this->articleDB->articleList($where);
        foreach ($data as $key => &$val) 
        {
            $val = array_map(array($this, 'filterNull'), $val);
        }
        echo json_encode(array('data' => $data));
        exit;
    }
    /**
    *添加文章页
    */
    public function addArticle()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('addArticle',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('addArticle');
    }
    /**
    *添加文章数据
    */
    public function addArticleAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('addArticle',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $array = array(
            'title' => trim(I('post.article_title')),
            'author' => trim(I('post.article_author')),
            'text' => I('post.article_content'),
            'article_type' => I('post.article_type'),
            'desc' => I('post.article_depict'),
            'last_edit_account_id' => session('account_id'),
            'create_account_id' => session('account_id')
            );

        $insertId = $this->articleDB->data($array)->add();

        $msg = ($insertId > 0) ? '添加成功' : '添加失败';
        $status = ($insertId > 0) ? 1 : 0;
        
        A('System')->addLog(array('text' => '文章-'.trim(I('post.article_title')).$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *编辑文章页
    */
    public function editArticle()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('editArticle',0); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $this->display('editArticle');
    }
    /**
    *获取文章详情
    */
    public function getInfo()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('editArticle',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $field = array('article_id','title as article_title','author as article_author','text','image_url as article_img','article_type','`desc` as article_depict');
        $info = $this->articleDB->field($field)->where(array('article_id' => I('get.id')))->find();
        $info['article_content'] = html_entity_decode($info['text']);
        $info = array_map(array($this,'filterNull'),$info);
        echo json_encode($info);
        exit;
    }
    /**
    *编辑文章数据
    */
    public function editArticleAjax()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('editArticle',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/


        $array = array(
            'title' => trim(I('post.article_title')),
            'author' => trim(I('post.article_author')),
            'text' => I('post.article_content'),
            'article_type' => I('post.article_type'),
            'desc' => I('post.article_depict'),
            'last_edit_account_id' => session('account_id'),
            'last_edit_time' => date('Y-m-d H:i:s')
            );
        $saveId = $this->articleDB->data($array)->where(array('article_id' => I('post.article_id')))->save();
        
        $msg = ($saveId > 0) ? '编辑成功' : '编辑失败或未编辑';
        $status = ($saveId > 0) ? 1 : 0;
        
        A('System')->addLog(array('text' => '文章-'.trim(I('post.article_title')).$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    /**
    *删除文章
    */
    public function delArticle()
    {
        /*##############验证当前账户是否拥有模块访问权限##############**/
        A('Check')->isUse('delArticle',1); //模块关键词 //是否ajax 0 1
        /*##############验证当前账户是否拥有模块访问权限##############**/

        $info = $this->articleDB->field('title')->where(array('article_id' => I('get.id')))->find();

        $url = 'http://'.$_SERVER['HTTP_HOST'].'/Home/ArticleInfo/index/id/'.I('get.id').'/channel/'.session('organ_channel_id');
        $banner = $this->bannerDB->field('banner_id')->where(array('redirect_url' => $url,'type' => 1))->find();
        if($banner)
        {
            echo json_encode(array('msg' => '该文章在banner中展示,请取消','status' => 0));
            exit;
        }
        $id = $this->articleDB->where(array('article_id' => I('get.id')))->delete();
        $msg = ($id > 0) ? '删除成功' : '删除失败';
        $status = ($id > 0) ? '1' : '0';
        
        A('System')->addLog(array('text' => '文章-'.$info['title'].$msg,'platform_id' => 2,'account_id' => session('account_id')));

        echo json_encode(array('msg' => $msg,'status' => $status));
        exit;
    }
    
}