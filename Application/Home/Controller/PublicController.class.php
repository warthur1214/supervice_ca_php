<?php
namespace Home\Controller;
use Home\Common\MyController;

class PublicController extends MyController 
{
    function __construct()
    {
        parent::__construct();
    }
    /**
    *获取企业机构车组递归数据
    */
    public function organTree()
    {
        $group = $this->groupDB->select();
        foreach ($group as $key => $val) 
        {
            $new_group[$val['organ_id']][] = $val;
        }
        $info = $this->getOrganGroup(session('parent_organ_id'),$new_group," and organ.organ_id = '".session('organ_id')."'");
        if(empty($info))
        {
            $info = array();
        }
        echo json_encode($info);
        exit;
    }
    /**
    *无限极递归
    */
    public function getOrganGroup($pid = '0',$new_group,$otherWhere = '')
    {
        $where = "1=1 and organ.parent_organ_id = '{$pid}' and organ.is_available = '1'".$otherWhere;

        if($this->login_role_organ())
        {
            $organ_id = $this->belong_organ().",".$this->login_role_organ();
        }
        else
        {
            $organ_id = $this->belong_organ();
        }
        switch (session('organ_bs')) 
        {
            case false:
                $where .= " and organ.organ_bs != 2";
                break;
            case '1':
                $where .= " and organ.organ_bs != 2";
                $where .= " and organ.organ_id in (".$organ_id.")";
                break;
            case '2':
                $where .= " and organ.organ_bs != 1";
                $where .= " and organ.organ_id in (".$organ_id.")";
                break;
            default:
                break;
        }
        $list = $this->organDB->organList($where,'organ.organ_id,organ.organ_name');
        if($list)
        {
            foreach ($list as $key => $val) 
            {
                if ($val['organ_id']) 
                {
                    $val['group'] = $new_group[$val['organ_id']];
                    $val['son'] = $this->getOrganGroup($val['organ_id'],$new_group,$otherWhere = '');
                }
                $array[] = $val;
            }
        }
        return $array;
    }
    /**
    *车系数据
    */
    // public function getCarName()
    // {
    //     $series = I('get.id') ? I('get.id') : '-1';
    //     $arr = $this->brandDB->field('car_name')->where(array('car_series' => $series))->select();
    //     foreach ($arr as $key => $val) 
    //     {
    //         $data[] = $val['car_name'];
    //     }
    //     echo json_encode($data);
    //     exit;
    // }
    /**
    *发送短信
    *$tel 手机号
    *$type 提示内容
    */
    public function sms($tel,$type)
    {
        $data = array();
        $data['un'] = C('SMS_ACCOUNT');
        $data['pw'] = C('SMS_PWD');
        $data['msg'] = '欧尚评驾用户您好,您的'.$type.'已经充值成功,使用有效期为360天。';
        $data['phone'] = $tel;
        $data['rd'] = 1;

        $url = C('SMS_URL'); 
        $res = $this->http_request($url,http_build_query($data));
        return $res;
    }
    /**
    *发送短信接口处理
    *$url 短信接口地址
    *$data 接口数据
    */
    public function http_request($url,$data = null)
    {
    
        $RemindMsg  = array(
             '0' =>'发送成功',
            '101'=>'无此账号',
            '102'=>'密码错',
            '103'=>'提交过快',
            '104'=>'系统忙',
            '105'=>'敏感短信',
            '106'=>'消息长度错',
            '107'=>'错误的手机号码',
            '108'=>'手机号码个数错',
            '109'=>'无发送额度',
            '110'=>'不在发送时间内',
            '111'=>'超出该账号当月发送额度限制',
            '112'=>'无此产品',
            '113'=>'extno格式错',
            '115'=>'自动审核驳回',
            '116'=>'签名不合法，未带签名',
            '117'=>'IP地址认证错',
            '118'=>'账号没有相应的发送权限',
            '119'=>'账号已过期',
            '120'=>'内容不是白名单',
        );
        if(function_exists('curl_init')){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
           
            if (!empty($data)){
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);
            
        
            $result=preg_split("/[,\r\n]/",$output);

            if($result[1] == 0){
                return '短信发送成功!';
            }else{
                return '短信发送失败,可能导致的原因是'.$RemindMsg[$result[1]];
            }
        }elseif(function_exists('file_get_contents')){
            
            $output=file_get_contents($url.$data);
            $result=preg_split("/[,\r\n]/",$output);
        
            if($result[1] == 0){
                return '短信发送成功!';
            }else{
                return '短信发送失败,可能导致的原因是'.$RemindMsg[$result[1]];
            }
        }else{
            return '短信发送失败!';
        } 
        
    }
}