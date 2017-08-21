<?php
namespace Home\Model;
use Think\Model;
class ArticleModel extends Model
{
    protected $dbName;
    protected $trueTableName  = 'tp_article';
    public function __construct($dbName) 
    {
        $this->dbName = $dbName;
        parent::__construct();
    }
    /**
    * 获取所有文章信息
    * $where 查询条件 string
    */
    public function articleList($where)
    {
        $sql = "SELECT art.article_id,art.article_type,art.title as article_title,art.`desc` as article_depict,art.last_edit_time as edit_time,acc.real_name as edit_name,acc_c.real_name as create_name from {$this->dbName}.tp_article as art
         left join auth.tp_account as acc on art.last_edit_account_id = acc.account_id
         left join auth.tp_account as acc_c on art.create_account_id = acc_c.account_id
         where {$where} order by art.article_id desc";
        $row = $this->query($sql);
        return $row;

    }
}