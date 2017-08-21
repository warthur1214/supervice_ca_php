<?php
namespace Home\Model;
use Think\Model;
class ServiceModel extends Model
{
    protected $dbName = 'biz';
    protected $trueTableName  = 'tp_service_provider';
    public function __construct() 
    {
        parent::__construct();
    }

}