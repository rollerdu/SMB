<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/3/28
 * Time: 13:29
 */

namespace Admin\Controller;


class ManagerController extends BaseController{

    public function index(){
        $model = M("Manager");
        $title = trim(I("param.title"));
        if($title){
            $where['name'] = array('like',"%$title%");
            $where['email'] = array('like',"%$title%");
            $where['_logic'] = 'or';
        }
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $member = $model->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('id DESC')->select();
        $array = M("Agent")->where(array('status'=>1))->field('id,managerid,name,truename')->select();
        foreach($array as $ag){
            $agent[$ag['managerid']][] = $ag;
        }
        foreach($member as $key => $me){
            foreach($agent as $k => $a){
                if($me['id'] == $k){
                    $member[$key]['agents'] = $a;
                    break;
                }
            }
        }
        $this->assign('member', $member);
        $this->assign('count', $count);
        $this->assign('page',$show);
        $this->assign('title',$title);
        $this->display();
    }
    public function add(){
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Manager");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($model->add()) {
                    write_system_log();
                    $this->success("添加成功");
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $area = M("Region")->where(array('layer'=>1))->field('region_id,region_name')->select();
        $this->assign('area',$area);
        $this->display();
    }
    public function edit()
    {
        $id = intval(I("param.id"));
        if (IS_POST) {
            $model = D("Manager");
            $data = I();
            $data['provids'] = implode(',',$data['provids']);
            //更新
            if ($model->where("id=$data[id]")->save($data)) {
                write_system_log();
                $this->success("更新成功");
            } else {
                $this->error("未做任何修改,更新失败");
            }
        }
        $data = M("Manager")->where(array('id'=>$id))->find();
        $provids = explode(',',$data['provids']);
        $area = M("Region")->where(array('layer'=>1))->field('region_id,region_name')->select();
        $this->assign('data',$data);
        $this->assign('provids',$provids);
        $this->assign('area',$area);
        $this->display();
    }
    public function upstatus(){
        $id = intval(I('get.id'));
        if(!$id) $this->error('数据有误，请刷新重试！');
        $model = M('Manager');
        $where['id'] = $id;
        $ret = $model->where($where)->find();
        if(!$ret) $this->error('数据有误，请刷新重试！');
        $to = $ret['status'] ? 0 : 1;
        $message = $ret['status'] ? '已下架！' : '已上架！';
        $model->where(array('id'=>$id))->setField(array('status'=>$to));
        write_system_log();
        $this->success($message);
    }
    public function attach(){
        $id = I("param.id",0,'intval');
        if(!$id){
            $this->error("请选择一个账号操作");
        }
        $model = M('Agent');
        if(IS_POST){
            $agentid = I("post.agent");
            $model->where(array('managerid'=>$id))->save(array('managerid'=>0));
            if($agentid){
                $agent_str = implode(',',$agentid);
                $where['id'] = array('in',$agent_str);

                $ret = $model->where($where)->save(array('managerid'=>$id));
                if(!$ret) {
                    $this->error('分配失败');
                }
            }
            $this->success('分配成功');
        }
        $data = $model->alias('a')->where(array('a.status'=>1,'managerid'=>array('in',[0,$id])))->join(C('DB_PREFIX').'region r on r.region_id=a.provid','LEFT')
            ->field("a.id,a.name,a.truename,a.managerid,a.mobile,r.region_name")->select();
        $this->assign('data',$data);
        $this->assign('managerid',$id);
        $this->display();
    }


}