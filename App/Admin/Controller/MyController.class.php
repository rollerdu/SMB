<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/3/20
 * Time: 15:35
 */

namespace Admin\Controller;
use Think\Controller;


class MyController extends BaseController{

    public function index(){

        $this->display();
    }
    public function edit(){
        if(IS_POST){
            $model = M(get_admin_table(get_admin_role_id()));
            if (!$model->create()) {
                $this->error($model->getError());
            }else {
                //验证密码是否为空
                $data = I();
                $data['id'] = get_current_admin_id();
                //更新
                if(get_admin_role_id() == 4){
                    $data['username'] = $data['truename'];
                }
                if ($model->save($data)) {
                    $user = $model->alias('m')->where(array('m.id'=>$data['id']))
                        ->join(C('DB_PREFIX').'role r on r.id=m.roleid','LEFT')
                        ->field('m.*,r.name role_name')
                        ->find();
                    session('adminManage',$user);
                    write_system_log("修改个人信息");
                    $this->success("信息更新成功", U('My/index'));
                } else {
                    $this->error("未做任何修改,信息更新失败");
                }
            }
        }
        $this->display();
    }
    public function edit_password(){

        if(IS_POST){
            $pass1 = I("password1");
            $pass2 = I("password2");
            if($pass1 != $pass2){
                $this->error("两次密码不相同！");
            }
            $model = M(get_admin_table(get_admin_role_id()));
            $pass = $model->where(array('id'=>get_current_admin_id()))->getField('password');
            if($pass == md5(I('password0'))){
                $ret = $model->where(array('id'=>get_current_admin_id()))->setField('password',md5(I('password1')));
                if($ret){
                    write_system_log("修改密码");
                    $this->error("修改成功！");
                }else{
                    $this->error("修改失败！");
                }

            }else{
                $this->error("原密码输入错误！");
            }
        }
        $this->display();
    }
}