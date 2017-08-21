<?php

namespace Home\Model;

use Think\Model;

class IntegralModel extends Model
{
    protected $dbName;
    protected $trueTableName = 'tp_point';

    public function __construct($dbName)
    {
        $this->dbName = $dbName;
        parent::__construct();
    }

    /**
     * 获取所有积分数量信息
     * $where 查询条件 string
     */
    public function inteCnt($where, $searchWhere=null)
    {

        $sql = "SELECT count(1) as cnt 
         from {$this->dbName}.tp_user as user
         left join {$this->dbName}.tp_point as inte on inte.user_id = user.user_id
         where {$where}
         and user.user_id in (
            select pr.user_id 
            from {$this->dbName}.tp_point_record pr 
            where 1=1
            {$searchWhere}
         )
         ";
        $row = $this->query($sql);
        return $row;
    }

    /**
     * 获取所有积分信息
     * $where 查询条件 string
     * $firstRow 分页
     * $listRows 分页
     * $order 排序
     */
    public function integralList($where, $firstRow, $listRows, $order = "occur_time desc ", $searchWhere=null)
    {

        $sql = "SELECT 
                    user.user_id,user.tel,inte.usable_point as usable_integral,
                    inte.total_point as total_integral, (
                        SELECT from_unixtime(pr1.occur_time, '%Y-%m-%d %H:%i:%s') FROM {$this->dbName}.tp_point_record pr1
                        WHERE pr1.user_id = user.user_id
                        order by occur_time desc
                        limit 1
                    ) as occur_time
                from {$this->dbName}.tp_user as user
                left join {$this->dbName}.tp_point as inte on inte.user_id = user.user_id
                where {$where}
                and user.user_id in (
                    select pr.user_id 
                    from {$this->dbName}.tp_point_record pr 
                    where 1=1
                    {$searchWhere}
                )
                order by {$order} 
                limit {$firstRow},{$listRows}";
        $row = $this->query($sql);
        return $row;
    }

    /**
     * 获取行驶积分详情数量
     * $db 行驶积分数据库
     * $id 用户id
     */
    public function getUbiInteCnt($db, $id)
    {
        $sql = "SELECT count(1) as cnt from (
                    SELECT user_id,integral,create_time from {$db} WHERE integral <> 0
                union 
                    SELECT pr.user_id,pr.point as integral,pr.create_time 
                    from {$this->dbName}.tp_point_record as pr
                    inner join biz.tp_point_type as pt on pr.point_type_id = pt.point_type_id
                    where pr.point <> 0
                ) as s
         where s.user_id = '{$id}'";
        $row = $this->query($sql);
        return $row;
    }

    /**
     * 获取行驶积分详情
     * $db 行驶积分数据库
     * $id 用户id
     * $firstRow 分页
     * $listRows 分页
     */
    public function getUbiInte($db, $id, $firstRow, $listRows)
    {
        $sql = "SELECT s.user_id,s.integral,s.comment,s.create_time 
                from (
                    SELECT user_id,integral,
                    case when risk_score is not null then '驾驶积分' else '驾驶积分' end as comment,create_time 
                    from {$db}
                    WHERE integral <> 0
                union 
                    SELECT pr.user_id,pr.point as integral,pt.point_type as comment,pr.create_time 
                    from {$this->dbName}.tp_point_record as pr
                    inner join biz.tp_point_type as pt on pr.point_type_id = pt.point_type_id
                    where pr.point <> 0
                ) as s
                where s.user_id = '{$id}' 
                order by s.create_time desc 
                limit {$firstRow},{$listRows}";
        $row = $this->query($sql);
        return $row;
    }

    /**
     * 获取ubi积分信息
     * $nowDB 当前数据库
     * $ubiDB ubi数据库 array
     * $where 查询条件 string
     */
    public function fromUbiInte($nowDB, $ubiDB, $where)
    {
        $sql = " SELECT s.id,s.user_id,s.integral FROM ((";
        $sql .= "select id,user_id, sum(integral) AS integral from {$nowDB}.tp_sgl_jny_analysis_0000";
        if (!empty($ubiDB)) {
            foreach ($ubiDB as $key => $val) {
                $sql .= " union select id,user_id,sum(integral) AS integral 
                    from {$val['table_schema']}.{$val['table_name']}
                    where {$where}
                    group by user_id
                    ";
            }
        }
        $sql .= ")) as s group by user_id";
        $row = $this->query($sql);
        return $row;
    }

    /**
     * 获取其他积分信息
     * $where 查询条件 string
     */
    public function recordInte($where)
    {
        $sql = "SELECT s.id,s.user_id,sum(s.point) as integral 
                from {$this->dbName}.tp_point_record as s 
                where {$where}
                group by s.user_id
                ";
        $row = $this->query($sql);
        return $row;
    }

}