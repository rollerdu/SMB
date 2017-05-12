<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2016/11/30
 * Time: 10:23
 */

namespace Home\Controller;
use Home\Controller;

class IndexController extends BaseController{

    public function __construct()
    {
        parent::__construct();

    }
    public function login(){
        $mobile = I('mobile') ? I('mobile') : ""; //手机号
        $password   = I('password')   ? md5(trim(I('password'))) : '';//密码
        if(!$mobile || !$password){
            $this->ajaxReturn(array('msg'=>"参数不能为空!",'code'=>"1001"));
        }
        if(!preg_match('/^(1)[0-9]{10}$/',$mobile)) {
            $this->ajaxReturn(array('msg'=>"手机号格式错误!",'code'=>"1002"));
        }
        $user       = D('User');
        $userInfo   = $user->getUserInfo($mobile,2); //获得用户信息
        if($userInfo == -1){
            $this->ajaxReturn(array('msg'=>"用户不存在!",'code'=>"1003"));
        }
        if($userInfo['status'] != 1){
            $this->ajaxReturn(array('msg'=>"用户禁止登录!",'code'=>"1004"));
        }
        if($userInfo['password'] != $password){
            $this->ajaxReturn(array('msg'=>"密码错误!",'code'=>"1005"));
        }
        $ticket = $this->randString(15);
        $user_login = M("Userlogin")->where(array('userid'=>$userInfo['id']))->find();

        $data = array('ticket'=>$ticket,'userid'=>$userInfo['id']);
        if(empty($user_login)){ //入库票据和device
            M('Userlogin')->add($data);
        }else{
            M('Userlogin')->where(array('userid'=>$userInfo['id']))->save($data); //根据某字段更新数据
        }
        //更新用户登录日志，登录总数加1
        M("User")->where(array('id'=>$userInfo['id']))->setField('loginnum',$userInfo['loginnum'] + 1);
        $userInfo['ticket']      = $ticket; //票据
        unset($userInfo['password']);
        $this->ajaxReturn(array('msg'=>"登录成功!",'code'=>"0",'data'=>$userInfo));



    }
    public function register(){
        $mobile   = I('mobile') ? trim(I('mobile'))  : ''; //手机号
        $sn       = I('sn') ? trim(I('sn'))  : ''; //呼吸机SN号
        if (!preg_match('/^(1)[0-9]{10}$/',$mobile)){
            $this->ajaxReturn(array('msg'=>"手机号格式错误!",'code'=>"1006"));

        }
        $checkSn = M("Sn")->where(array('number'=>$sn))->find();
        if(!$checkSn){
            $this->ajaxReturn(array('msg'=>"SN号错误!",'code'=>"1010"));
        }
        if($checkSn['status'] != 0){
            $this->ajaxReturn(array('msg'=>"SN号已使用!",'code'=>"1011"));
        }
        $user       = D('User');
        $isUserInfo = $user->getUserInfo($mobile,$type = 2);
        if($isUserInfo != '-1') $this->ajaxReturn(array('msg'=>"手机号已存在!",'code'=>"1007"));
        $password       = I('password')        ? md5(trim(I('password'))) : '';//密码
        $device         = I('device')          ? trim(I('device'))         : '';//设备号
        $device_type    = I('device_type')      ? trim(I('device_type'))     : '2';//类型
        if(!$mobile || !$password || !$sn){
            $this->ajaxReturn(array('msg'=>"参数不能为空!",'code'=>"1008"));
        }
        $data = array('mobile'=>$mobile,'password'=>$password,'sn'=>$sn);
        if($id = M('User')->add($data)){
            $deviceInfo =  M("userlogin")->where("`userid` = '$id'")->find();
            $ticket  = $this->randString(15);
            $data1['device_type'] = $device_type; //这边类型，1安卓，2ios
            $data1['device']      = $device; //极光表示
            $data1['ticket']      = $ticket; //票据
            $data1['userid']      = $id; //用户id
            if(!$deviceInfo){
                M('userlogin')->add($data1); //添加设备
            }else{
                unset($data1['userid']);
                M('userlogin')->where("`userid` = '$id'")->save();
            }
            //胸带使用次数加默认
            $girdleInfo = M("GirdleTime")->where("`userid`=$id")->find();
            if(!$girdleInfo){
                M("GirdleTime")->add(array('userid'=>$id,'times'=>C('DEFAULT_GIRDLE_TIMES')));
            }
            //SN号改为已使用
            M("Sn")->where(array('number'=>$sn))->setField('status',1);
            $userInfo = $user->getUserInfo($id);
            $infos = array_merge($userInfo,array('ticket'=>$ticket));
            unset($infos['password']);
            $this->ajaxReturn(array('msg'=>"注册成功!",'code'=>"0",'data'=>$infos));
        }else{
            $this->ajaxReturn(array('msg'=>"注册失败，请重试!",'code'=>"1009"));
        }
    }
    /*
     * 获取胸带更新程序
     * */
    public function getNewsVersion()
    {
        $infos = array();
        if($infos = M('Setting')->find(1)){
            $infos['ufileurl'] =  $infos['ufileurl'] ?  SITE_URL.$infos['ufileurl'] : '';
        }
        $this->ajaxReturn(array('data'=>$infos));
    }
    /*
     * 获取三级地区
     * */
    public function getArea(){
        $data = M("Region")->where(array('layer'=>array('in',[1,2,3])))->field("region_id,parent_id,region_name,layer,spellname")->select();
        foreach($data as $k=>$v){
            $ret[$v['parent_id']][] = $v;
        }
        $this->ajaxReturn(array('data'=>$ret));
    }
    /*
     *获取公司信息接口
     * */
    public function getCompanyData(){
        $data = M("Company")->find(1);
        $message = json_decode($data['content'],true);
        if($message && is_array($message)){
            $this->ajaxReturn(array('msg'=>"获取成功!",'code'=>"0",'data'=>$message));
        }else{
            $this->ajaxReturn(array('msg'=>"获取失败!",'code'=>"1010"));
        }
    }


}