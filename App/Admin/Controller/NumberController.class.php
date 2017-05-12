<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/3/26
 * Time: 23:01
 */

namespace Admin\Controller;


class NumberController  extends BaseController{

    public function index(){
        $model = M('Sn');
        $sn = I("param.sn");
        if($sn){
            $where['number'] = array('like',"%$sn%");
        }
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $data = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
        $this->assign('sn', $sn);
        $this->assign('count', $count);
        $this->assign('data', $data);
        $this->assign('page',$show);
        $this->display();
    }
    public function add(){

        if(IS_POST){
            $sn = trim(I("post.sn"));
            if(!$sn){
                $this->error('请输入SN号');
            }
            $ret = M("Sn")->add(array('number'=>$sn));
            if($ret){
                write_system_log();
                $this->success('添加成功！');
            }else{
                $this->error('添加失败！');
            }
        }
        $this->display();
    }
    public function update(){
        $id = intval(I("get.id"));
        if(!$id){
            $this->error('数据有误！');
        }
        $ret = M("Sn")->where(array('id'=>$id))->setField('status',1);
        if($ret){
            write_system_log();
            $this->success('修改成功！');
        }else{
            $this->error('修改失败！');
        }

    }
    public function delete() {
        $id = intval(I("get.id"));
        $model = M("Sn");
        $data = $model->where("id=$id")->find();
        if($data['status'] != 0){
            $this->error("该SN号已使用，不可删除！");
        }else{
            $status = $model->delete($id);
            if ($status!==false) {
                write_system_log();
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }

    }
}