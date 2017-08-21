<?php
namespace Home\Model;
use Think\Model;
class LogModel extends Model
{
    protected $trueTableName  = 'tp_log';
    public function __construct() 
    {
        parent::__construct();
    }
    /**
     *获取日志信息数量
     * $where 条件
     */
    public function logCnt($where)
    {
        $sql ="SELECT count(1) as cnt from tp_log as log
         left join tp_account as account on log.account_id = account.account_id
         where {$where}";
        $row = $this->query($sql);
        return $row;
    }
    /**
     *获取日志信息
     * $where 条件
     * $firstRow 分页
     * $listRows 分页
    */
    public function logList($where,$firstRow,$listRows)
    {
        $sql ="SELECT log.id,log.text as log,log.create_time,account.real_name as display_name from tp_log as log
         left join tp_account as account on log.account_id = account.account_id
         where {$where} order by log.id desc limit {$firstRow},{$listRows}";
         $row = $this->query($sql);
         return $row;
    }

}