<?php
namespace Home\Controller;
//use Home\Common\MyController;
use Think\Controller;
class MyUploadController extends Controller 
{
	function __construct()
	{
		parent::__construct();
	}	
	public function index()
	{
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     1048576 ;// 设置附件上传大小
        $upload->exts      =     array('jpg','png','jpeg');// 设置附件上传类型
        $upload->rootPath  =     SITE_PATH.'/Public/upload/'.I('get.path').'/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        $upload->autoSub = true; // 开启子目录保存 并以日期为子目录
        $upload->subName = array('date','Ymd');
        $upload->saveName = time().'_'.mt_rand();
        //上传文件 
        if (!is_dir($upload->rootPath)) mkdir($upload->rootPath, 0777);
        $info = $upload->upload();
        $img = '/Public/upload/'.I('get.path').'/'.$info['file']['savepath'].$info['file']['savename'];
        echo json_encode(array('img' => $img));
        exit;
	}
}