<?php
namespace Home\Model;
use Think\Model;
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/4/6
 * Time: 16:01
 */
class UserModel extends HomeModel{
    protected $domainUrl = '';
    public function __construct() {
        $this->domainUrl = C('SITE_URL');
    }
    public function getUserInfo($mix,$type = 1) {
        $mix = safe_replace($mix);
        $where = "WHERE 1 = '1' ";
        if($type == 1) {
            $where .= " AND a.id = '$mix' ";
        } elseif($type == 2) {
            $where .= " AND a.mobile = '$mix' ";
        }else{
            return -1; //非法操作
        }
        $sql  = "select a.*,b.ticket,b.device,g.times girdle_times
                    from ".C('DB_PREFIX')."user a left join ".C('DB_PREFIX')."userlogin b on a.id = b.userid LEFT JOIN ".C('DB_PREFIX')."girdle_time g
                    on g.userid=a.id {$where} ";//IFNULL(concat('$this->domainUrl',a.headimg),'') headimg,
        $data = $this->getone($sql);
        if(!empty($data) && is_array($data)){
            return $data;
        }else{
            return -1;
        }
    }
}