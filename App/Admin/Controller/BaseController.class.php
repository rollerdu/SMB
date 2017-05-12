<?php
namespace Admin\Controller;
use Think\Controller;

class BaseController extends Controller {

    public $user;
    public $admin_menu;
    public $menuUrl;

    public function _initialize(){
        if(isset($_SESSION['adminManage'])){
            if(!$this->check_access()){
                $this->error("您没有访问权限！");
            }
            $this->user = $_SESSION['adminManage'];
            $this->assign("user",$_SESSION['adminManage']);
        }else {
            if($_SERVER['PATH_INFO'] == 'doctor'){
                redirect("/Login/index?roleid=3");
            }elseif($_SERVER['PATH_INFO'] == 'agent'){
                redirect("/Login/index?roleid=2");
            }elseif($_SERVER['PATH_INFO'] == 'manager'){
                redirect("/Login/index?roleid=1");
            }
            $this->error("您还没有登录！",U('Login/index'));
        }

		if(!$_COOKIE['admin_menu']){
            $admin_menu = D("Menu")->menu_json();
            $_COOKIE['admin_menu'] = json_encode($admin_menu);
        }
        //echo CONTROLLER_NAME;
        $this->assign('action',CONTROLLER_NAME);
        $this->assign('SITE_URL',SITE_URL);
        $this->assign('menu',json_decode($_COOKIE['admin_menu'],true));
        
    }
    /**
     *  排序 排序字段为listorders数组 POST 排序字段为：listorder
     */
    protected function _listorders($model) {
        if (!is_object($model)) {
            return false;
        }
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['listorder'] = $r;
            $model->where(array('id' => $key))->save($data);
        }
        return true;
    }
    private function check_access(){
        //如果用户角色是超管，则无需判断
        $uid = get_admin_role_id();
        if($uid == 4){
            return true;
        }
        $rule=strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        if(in_array($rule,array('admin/index/index','admin/index/welcome','admin/my/index','admin/my/edit','admin/my/edit_password'))){
            return true;
        }
        if(empty($_SESSION['all_need_check_rules'])){
            $ret = M("AuthAccess")->where(array('role_id'=>$uid))->getField('rule_name',true);
            $_SESSION['all_need_check_rules'] = json_encode($ret);
        }
        if( !in_array($rule,json_decode($_SESSION['all_need_check_rules'],true))){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 空方法，不存在跳到首页
     */
    public function  _empty(){
        //$this->error(C('ERROR_404'),U('Index/index'));
    }
}