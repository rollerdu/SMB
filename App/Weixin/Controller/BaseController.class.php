<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/4/26
 * Time: 13:30
 */

namespace Weixin\Controller;
use Think\Controller;


class BaseController extends Controller{
    public function __construct(){
        parent::__construct();
        //过滤参数
    }
    public function _initialize(){
        if(!empty(session('UserInfo'))){
            $this->assign('userInfo',session('UserInfo'));
        }else {
            $this->error('请先登录', U('/Weixin/Login/login'));
        }
    }
}