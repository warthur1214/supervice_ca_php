<?php
namespace Home\Model;
use Think\Model;
class AccountModel extends Model
{
    protected $dbName;
    protected $trueTableName;
    public function __construct() 
    {
        parent::__construct();
    }
}