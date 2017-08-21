<?php
namespace Home\Model;
use Think\Model;
class BannerModel extends Model
{
    protected $dbName;
    protected $trueTableName  = 'tp_banner';
    public function __construct($dbName) 
    {
        $this->dbName = $dbName;
        parent::__construct();
    }
    /**
    *获取banner信息
    * $where 条件
    */
    public function bannerList($where)
    {
        $sql = "SELECT banner_id as id,case when type = 0 then '活动' else '文章' end as new_banner_type,name as active_name,redirect_url as active_url,image_url as picture_url,begin_date,end_date,create_time,is_sticked as top_type,is_available as status,redirect_url_type as active_url_type,redirect_item_id as native_name from {$this->dbName}.tp_banner
         where {$where} order by is_sticked = '0',top_type desc,banner_id desc";
        $row = $this->query($sql);
        return $row;
    }

}