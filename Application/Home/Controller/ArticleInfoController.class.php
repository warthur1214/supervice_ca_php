<?php
namespace Home\Controller;
use Think\Controller;
class ArticleInfoController extends Controller 
{
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $articleDB = new \Home\Model\ArticleModel('biz_'.I('get.channel'));
        $info = $articleDB->field('title as article_title,author as article_author,text,last_edit_time as edit_time')->where(array('article_id' => I('get.id')))->find();
        $info['article_content'] = htmlspecialchars_decode($info['text']);
        $this->assign('info',$info);
        $this->display('Article/info');
    }
}