<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/3/28
 * Time: 13:29
 */

namespace Admin\Controller;


class DoctorController extends BaseController{

    public function index(){
        $model = M("Doctor");
        $title = trim(I("param.title"));
        $roleid = get_admin_role_id();
        $adminid = get_current_admin_id();
        if($roleid == 1){
            $all_agent = M("Agent")->where(array('managerid'=>$adminid))->getField('id',true);
            $str_agent = implode(',',$all_agent);

            $where['a.agentid'] = array('in',$str_agent);
        }elseif($roleid == 2 ){
            $where['a.agentid'] = $adminid;
        }elseif( $roleid == 3){
            $this->error('您无权限操作！');
        }
        if($title){
            $where1['a.name'] = array('like',"%$title%");
            $where1['a.email'] = array('like',"%$title%");
            $where1['_logic'] = 'or';
            $where['_complex'] = $where1;
        }
        $count  = $model->alias('a')->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $member = $model->alias('a')->join(C('DB_PREFIX')."agent m on m.id=a.agentid",'LEFT')
            ->join(C('DB_PREFIX')."hospital r on r.id=a.hospitalid",'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('a.id DESC')
            ->field('a.*,m.name manager_name,m.truename manager_truename,r.title')
            ->select();
        $array = M("User")->alias('d')->where(array('d.status'=>1))
            ->join(C('DB_PREFIX')."region h on h.region_id=d.provid",'LEFT')
            ->join(C('DB_PREFIX')."region r on r.region_id=d.cityid",'LEFT')
            ->field('d.id,d.doctorid,d.username,d.mobile,h.region_name provname,r.region_name cityname')->select();
        foreach($array as $ag){
            $user[$ag['doctorid']][] = $ag;
        }
        foreach($member as $key => $me){
            foreach($user as $k => $a){
                if($me['id'] == $k){
                    $member[$key]['user'] = $a;
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
            $model = D("Doctor");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                $roleid = get_admin_role_id();
                if($roleid == 2){
                    $model->agentid = get_current_admin_id();
                }
                if ($model->add()) {
                    write_system_log();
                    $this->success("添加成功");
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $ret = M("Region")->where(array('layer'=>array('in',[1,2])))->field('region_id,parent_id,region_name,layer')->select();
        foreach($ret as $key => $val){
            if($val['layer'] == 1){
                $citys[$val['region_id']] = $this->getChildren($ret,$val['region_id']);
                $area[] = $val;
            }
        }
        $this->assign('area',$area);
        $this->assign('citys',json_encode($citys));
        $this->display();
    }
    private function getChildren($arr,$id){
        foreach($arr as $k => $v){
            if($v['layer'] == 2 && $v['parent_id'] == $id){
                $data[] = $v;
            }
        }
        return $data;
    }
    public function select_hospital(){
        $cityid = intval(I("param.cityid"));
        $data = M("Hospital")->where(array('cityid'=>$cityid))->field('title,id')->select();
        if(!empty($data)){
            return $this->ajaxReturn(array('code'=>0,'data'=>$data));
        }else{
            return $this->ajaxReturn(array('code'=>1,'msg'=>'没有数据'));
        }
    }
    public function edit()
    {
        $id = intval(I("param.id"));
        $roleid = get_admin_role_id();
        $adminid = get_current_admin_id();
        if($roleid == 1){
            $all_agent = M("Agent")->where(array('managerid'=>$adminid))->getField('id',true);
            $str_agent = implode(',',$all_agent);

            $where['agentid'] = array('in',$str_agent);
        }elseif($roleid == 2 ){
            $where['agentid'] = $adminid;
        }elseif( $roleid == 3){
            $this->error('您无权限操作！');
        }
        $where['id'] = $id;
        $data = M("Doctor")->where($where)->find();
        if(empty($data)){
            $this->error('您无权限操作！');
        }
        if (IS_POST) {
            $model = D("Doctor");
            $data = I();
            //更新
            if ($model->where("id=$data[id]")->save($data)) {
                write_system_log();
                $this->success("更新成功");
            } else {
                $this->error("未做任何修改,更新失败");
            }
        }

        $ret = M("Region")->where(array('layer'=>array('in',[1,2])))->field('region_id,parent_id,region_name,layer')->select();
        foreach($ret as $key => $val){
            if($val['layer'] == 1){
                $child = $this->getChildren($ret,$val['region_id']);
                $citys[$val['region_id']] = $child;
                if($val['region_id'] == $data['provid']){
                    $current_citys = $child;
                }
                $area[] = $val;
            }
        }
        $current_hospital = M("Hospital")->where(array('cityid'=>$data['cityid']))->field('title,id')->select();

        $this->assign('current_hospital',$current_hospital);
        $this->assign('current_citys',$current_citys);
        $this->assign('area',$area);
        $this->assign('citys',json_encode($citys));
        $this->assign('data',$data);
        $this->display();
    }
    public function upstatus(){
        $id = intval(I('get.id'));
        if(!$id) $this->error('数据有误，请刷新重试！');
        $model = M('Doctor');
        $where['id'] = $id;
        $ret = $model->where($where)->find();
        if(!$ret) $this->error('数据有误，请刷新重试！');
        $roleid = get_admin_role_id();
        if($roleid == 2){
            if($ret['agentid'] != get_current_admin_id()){
                $this->error('请勿非法操作！');
            }
        }elseif($roleid == 1){
            $agent_ids = M("Agent")->where(array('managerid'=>get_current_admin_id()))->getField('id',true);
            if(!in_array($ret['agentid'],$agent_ids)){
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
        $doctor_id = I("param.id",0,'intval');
        if(!$doctor_id){
            $this->error("请选择一个账号操作");
        }

        $model = M('User');

        $data = $model->alias('a')->where(array('a.status'=>1,'doctorid'=>array('in',[0,$doctor_id])))->join(C('DB_PREFIX').'doctor r on r.id=a.doctorid','LEFT')
            ->field("a.id,a.username,a.mobile,a.doctorid,a.mobile,r.name doctor_name")->select();
        $roleid = get_admin_role_id();
        $adminid = get_current_admin_id();
        if($roleid == 1){
            $agent_ids = M('Agent')->where("managerid=$adminid")->getField('id',true);
            $doctorids = M("Doctor")->where(array('agentid'=>array('in',$agent_ids)))->getField('id',true);
            if(!in_array($doctor_id,$doctorids)){
                $this->error('您无权限操作！');
            }
        }elseif($roleid == 2){
            $d_id = M("Doctor")->where(array('agentid'=>$adminid))->getField('id');
            if($doctor_id != $d_id){
                $this->error('您无权限操作！');
            }
        }

        if(IS_POST){
            $user_id_arr = I("post.user");
            $model->where(array('doctorid'=>$doctor_id))->save(array('doctorid'=>0));
            if($user_id_arr){
                $user_str = implode(',',$user_id_arr);
                $where['id'] = array('in',$user_str);

                $ret = $model->where($where)->save(array('doctorid'=>$doctor_id));
                if(!$ret) {
                    $this->error('分配失败');
                }
            }
            $this->success('分配成功');
        }

        $this->assign('data',$data);
        $this->assign('doctorid',$doctor_id);
        $this->display();
    }


}