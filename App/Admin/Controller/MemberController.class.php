<?php
namespace Admin\Controller;
use Admin\Controller;
/**
 * 用户管理
 */
class MemberController extends BaseController
{
    /**
     * 用户列表
     * @return [type] [description]
     */
    public function index($key="")
    {
        if($key == ""){
            $model = M('admin');
        }else{
            $where['name'] = array('like',"%$key%");
            $where['email'] = array('like',"%$key%");
            $where['_logic'] = 'or';
            $model = M('Admin')->where($where);
        } 
        
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $member = $model->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('id DESC')->select();
        $role = M("Role")->where(array('stauts'=>1))->select();
        foreach ($role as $it) {
            $new_role[$it['id']] = $it;

        }
        $this->assign('role',$new_role);
        $this->assign('member', $member);
        $this->assign('count', $count);
        $this->assign('page',$show);
        $this->assign('key',$key);
        $this->display();
    }

    /**
     * 添加用户
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $role = M("Role")->where(array('status'=>1,'id'=>array('gt',3)))->select();
            $this->assign('role',$role);
            $this->display();
        }
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Admin");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($model->add()) {
                    write_system_log();
                    $this->success("用户添加成功", U('member/index'));
                } else {
                    $this->error("用户添加失败");
                }
            }
        }
    }
    /**
     * 更新管理员信息
     * @param  [type] $id [管理员ID]
     * @return [type]     [description]
     */
    public function update()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $model = M('Admin')->find(I('id'));
            $role = M("Role")->where(array('status'=>1))->select();
            $this->assign('role',$role);
            $this->assign('model',$model);
            $this->display();
        }
        if (IS_POST) {
            $model = D("Admin");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                //验证密码是否为空   
                $data = I();
                unset($data['password']);
                if(I('password') != ""){
                    $data['password'] = md5(I('password'));
                }
                //更新
                if ($model->save($data)) {
                    write_system_log();
                    $this->success("用户信息更新成功", U('member/index'));
                } else {
                    $this->error("未做任何修改,用户信息更新失败");
                }        
            }
        }
    }
    /**
     * 删除管理员
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delete($id)
    {
    	if(1 == $id) $this->error("超级管理员不可禁用!");
        $model = M('Admin');
        //查询status字段值
        $result = $model->find($id);
        //更新字段
        $data['id']=$id;
        if($result['status'] == 1){
        	$data['status']=0;
        }
        if($result['status'] == 0){
        	$data['status']=1;
        }
        if($model->save($data)){
            write_system_log();
            $this->success("状态更新成功", U('member/index'));
        }else{
            $this->error("状态更新失败");
        }
    }


    /**
     * 修改管理员信息
     * @param  [type] $id [管理员ID]
     * @return [type]     [description]
     */
    public function pw() {

        $data['id'] = $this->user['id'];
        if (IS_POST) {
            $model = D("Admin");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                $password = I('password');
                $password1 = I('password1');
                $password2 = I('password2');
                $oldpassword = $this->user['password'];
                if ($oldpassword != md5($password)) {
                    $this->error("原始密码错误！");
                } else {
                    if (!empty($password1) || !empty($password2)) {
                        if ($password1 != $password2) {
                            $this->error("两次密码输入不一致！");
                        } else {
                            $data['password'] = md5($password1);
                        }
                    } else {
                        $this->error("新密码不能为空！");

                    }

                }

                //更新
                if ($model->save($data)) {
                    session('adminManage',null);
                    write_system_log();
                    $this->success("用户信息更新成功", U('member/pw'));
                } else {
                    $this->error("未做任何修改,用户信息更新失败");
                }
            }
        } else {
            $this->display();

        }
    }

}
