<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/3/28
 * Time: 13:29
 */

namespace Admin\Controller;


class AgentController extends BaseController{

    public function index(){
        $model = M("Agent");
        $title = trim(I("param.title"));
        if($title){
            $where1['a.name'] = array('like',"%$title%");
            $where1['a.email'] = array('like',"%$title%");
            $where1['_logic'] = 'or';
            $where['_complex'] = $where1;
        }
        $roleid = get_admin_role_id();
        if($roleid == 1){
            $adminid = get_current_admin_id();
            $where['a.managerid'] = $adminid;
        }elseif($roleid == 2 || $roleid == 3){
            $this->error('您无权限操作！');
        }

        $count  = $model->alias('a')->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $member = $model->alias('a')->join(C('DB_PREFIX')."manager m on m.id=a.managerid",'LEFT')
            ->join(C('DB_PREFIX')."region r on r.region_id=a.provid",'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('a.id DESC')
            ->field('a.*,m.name manager_name,m.truename manager_truename,r.region_name')
            ->select();
        $array = M("Doctor")->alias('d')->where(array('d.status'=>1))
            ->join(C('DB_PREFIX')."hospital h on h.id=d.hospitalid",'LEFT')
            ->field('d.id,d.agentid,d.name,d.truename,h.title')->select();
        foreach($array as $ag){
            $agent[$ag['agentid']][] = $ag;
        }
        foreach($member as $key => $me){
            foreach($agent as $k => $a){
                if($me['id'] == $k){
                    $member[$key]['doctor'] = $a;
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
            $model = D("Agent");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                $roleid = get_admin_role_id();
                if($roleid == 1){
                    $model->managerid = get_current_admin_id();
                }
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
            $model = D("Agent");
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
        $data = M("Agent")->where(array('id'=>$id))->find();
        $roleid = get_admin_role_id();
        if($roleid == 1){
            if($data['managerid'] != get_current_admin_id()){
                $this->error('请勿非法操作！');
            }
        }
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
        $model = M('Agent');
        $where['id'] = $id;
        $ret = $model->where($where)->find();
        if(!$ret) $this->error('数据有误，请刷新重试！');
        $roleid = get_admin_role_id();
        if($roleid == 1){
            if($ret['managerid'] != get_current_admin_id()){
                $this->error('请勿非法操作！');
            }
        }
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
        $roleid = get_admin_role_id();
        if($roleid == 1){
            $managerid = M('Agent')->where("id=$id")->getField('managerid');
            if($managerid != get_current_admin_id()){
                $this->error('请勿非法操作！');
            }
        }

        $model = M('Doctor');
        if(IS_POST){
            $agentid = I("post.agent");
            $model->where(array('agentid'=>$id))->save(array('agentid'=>0));
            if($agentid){
                $agent_str = implode(',',$agentid);
                $where['id'] = array('in',$agent_str);

                $ret = $model->where($where)->save(array('agentid'=>$id));
                if(!$ret) {
                    $this->error('分配失败');
                }
            }
            $this->success('分配成功');
        }
        $data = $model->alias('a')->where(array('a.status'=>1,'agentid'=>array('in',[0,$id])))->join(C('DB_PREFIX').'hospital r on r.id=a.hospitalid','LEFT')
            ->field("a.id,a.name,a.truename,a.agentid,a.mobile,r.title")->select();
        $this->assign('data',$data);
        $this->assign('agentid',$id);
        $this->display();
    }


}