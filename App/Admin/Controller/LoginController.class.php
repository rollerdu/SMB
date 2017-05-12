<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller {
    //登陆主页
    public function index(){
        $data = json_decode(M("Company")->getField('content'),true);
        $this->assign('copy',$data['copyright']);
        $this->assign('roleid',I("get.roleid",4,'intval'));
//        var_dump()
        $this->display();
    }
    //登陆验证
    public function login(){
        $roleid = I('post.roleid');
        $model = M(get_admin_table($roleid));
        $username =I('username');
        $password =I('password','','md5');
        $code = I('verify','','strtolower');
        //验证验证码是否正确
        if(!($this->check_verify($code))){
            $this->error('验证码错误');
        }
        //验证账号密码是否正确
        $user = $model->alias('m')->where(array('m.name'=>$username))
            ->join(C('DB_PREFIX').'role r on r.id=m.roleid','LEFT')
            ->field('m.*,r.name role_name')
            ->find();
        $user['name'] = $user['name'] ? $user['name'] : $user['username'];
        if(!$user || $user['password'] != $password) {
            $this->error('角色错误 或 账号密码错误 :(') ;
        }
        //验证账户是否被禁用
        if($user['status'] == 0){
            $this->error('账号被禁用，请联系超级管理员 :(') ;
        }
        //验证是否为管理员
        $data =array(
            'login_ip' => get_client_ip(),
            'login_times' => $user['login_times'] + 1,
        );
        //如果数据更新成功  跳转到后台主页
        $ret = $model->where(array('id' => $user['id']))->save($data);
        session('adminManage',$user);
        write_system_log('系统登录');
        $this->success("登陆成功",U('Index/index'));
        //定向之后台主页
        

    }
    //验证码
    public function verify(){
        $Verify = new \Think\Verify();
        $Verify->codeSet = '0123456789';
        $Verify->fontSize = 16;
        $Verify->length = 4;
        $Verify->useCurve = false;
        $Verify->fontttf = '2.ttf';
        $Verify->entry();
    }
    protected function check_verify($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }

    public function logout(){
        write_system_log('退出登录');
        $roleid = get_admin_role_id();
        session('adminManage',null);
        session('admin_menu',null);
        session('all_need_check_rules',null);
        redirect(U('Login/index',array('roleid'=>$roleid)));
    }

    /**
     * 空方法，不存在跳到首页
     */
    public function  _empty(){
        $this->error(C('ERROR_404'),U('Index/index'));
    }
}