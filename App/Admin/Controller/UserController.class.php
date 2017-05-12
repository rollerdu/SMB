<?php
namespace Admin\Controller;
use Admin\Controller;
/**
 * 前台用户管理
 */
class UserController extends BaseController {

    /**
     * 用户列表
     * @return [type] [description]
     */
    public function index()
    {
        $model = M('user');
        $title = I("param.title");
        if($title){
            $where1['u.mobile'] = array('like',"%$title%");
            $where1['u.username'] = array('like',"%$title%");
            $where1['_logic'] = 'or';
            $where['_complex'] = $where1;
        }
        $roleid = get_admin_role_id();
        $adminid = get_current_admin_id();
        if($roleid == 1){
            $agent_ids = implode(',',M("Agent")->where(array('managerid'=>$adminid))->getField('id',true));
            $doctor_ids = M("Doctor")->where(array('agentid'=>array('in',$agent_ids)))->getField('id',true);
            $where['u.doctorid'] = array('in',implode(',',$doctor_ids));
        }elseif($roleid == 2){
            $doctor_ids = M("Doctor")->where(array('agentid'=>$adminid))->getField('id',true);
            $where['u.doctorid'] = array('in',implode(',',$doctor_ids));
        }elseif($roleid == 3){
            $where['u.doctorid'] = $adminid;
        }
        $count  = $model->alias('u')->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $user = $model->alias('u')->join(C("DB_PREFIX")."doctor d on d.id=u.doctorid",'LEFT')
            ->join(C("DB_PREFIX")."girdle_time g on g.userid=u.id",'LEFT')
            ->join(C("DB_PREFIX")."smartcode s on s.userid=u.id",'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)->where($where)
            ->field('u.*,d.truename doctor_truename,d.name doctor_name,g.times girdle_time,ifnull(s.id,0) smartcode_id')
            ->group("u.id")
            ->order('u.id DESC')->select();
        $this->assign('count', $count);
        $this->assign('user', $user);
        $this->assign('page',$show);
        $this->assign('title',$title);
        $this->display();
    }

    /**
     * 添加用户
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
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
        if (IS_POST) {
            //如果用户提交数据
            $model = D("user");

            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                $sn = I("post.sn");
                $data = M("Sn")->where(array('number'=>$sn,'status'=>0))->find();
                if(!$data){
                    $this->error('SN号已验证或不存在！');
                }
                if(get_admin_role_id() == 3){
                    $model->doctorid = get_current_admin_id();
                }
                M("Sn")->where(array('id'=>$data['id']))->setField('status',1);
                if ($insertid = $model->add()){
                    M("GirdleTime")->add(array('userid'=>$insertid,'times'=>C('DEFAULT_GIRDLE_TIMES')));
                    write_system_log();
                    $this->success("用户添加成功", U('user/index'));
                } else {
                    $this->error("用户添加失败");
                }
            }
        }
    }
    private function getChildren($arr,$id){
        foreach($arr as $k => $v){
            if($v['layer'] == 2 && $v['parent_id'] == $id){
                $data[] = $v;
            }
        }
        return $data;
    }
    /**
     * 更新用户信息
     * @param  [type] $id [用户ID]
     * @return [type]     [description]
     */
    public function edit()
    {
        //默认显示添加表单
        $model = M('user');
        $roleid = get_admin_role_id();
        $adminid = get_current_admin_id();
        if($roleid == 1){
            $agent_ids = implode(',',M("Agent")->where(array('managerid'=>$adminid))->getField('id',true));
            $doctor_ids = M("Doctor")->where(array('agentid'=>array('in',$agent_ids)))->getField('id',true);
            $where['doctor'] = array('in',implode(',',$doctor_ids));
        }elseif($roleid == 2){
            $doctor_ids = M("Doctor")->where(array('agentid'=>$adminid))->getField('id',true);
            $where['doctor'] = array('in',implode(',',$doctor_ids));
        }elseif($roleid == 3){
            $where['doctor'] = $adminid;
        }
        $data = $model->where($where)->find(I('id'));
        if(!$data){
            $this->error('请勿非法操作！');
        }
        if (IS_POST) {
            $model = D("user");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                //验证密码是否为空
                $data = I();
                unset($data['password']);
                if(I('password') != ""){
                    $data['password'] = md5(I('password'));
                }
                //强制更改超级用户用户类型
                if(C('SUPER_ADMIN_ID') == I('id')){
                    $data['type'] = 2;
                }
                //更新
                if ($model->save($data)) {
                    write_system_log();
                    $this->success("用户信息更新成功", U('user/index'));
                } else {
                    $this->error("未做任何修改,用户信息更新失败");
                }
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
        $this->assign('citys',json_encode($citys));
        $this->assign('current_citys',$current_citys);
        $this->assign('area',$area);
        $this->assign('data',$data);
        $this->display();
    }
    /**
     * 上下线用户
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function upstatus(){
        $id = intval(I('get.id'));
        if(!$id) $this->error('数据有误，请刷新重试！');
        $model = M('User');
        $where['id'] = $id;
        $ret = $model->where($where)->find();
        if(!$ret) $this->error('数据有误，请刷新重试！');
        $roleid = get_admin_role_id();
        if($roleid == 3){
            if($ret['doctorid'] != get_current_admin_id()){
                $this->error('请勿非法操作！');
            }
        }elseif($roleid == 1){
            $agent_ids = implode(',',M("Agent")->where(array('managerid'=>get_current_admin_id()))->getField('id',true));
            $doctor_ids = M("Doctor")->where(array('agentid'=>array('in',$agent_ids)))->getField('id',true);
            if(!in_array($ret['doctorid'],$doctor_ids)){
                $this->error('请勿非法操作！');
            }
        }elseif($roleid == 2){
            $doctor_ids = M("Doctor")->where(array('agentid'=>get_current_admin_id()))->getField('id',true);
            if(!in_array($ret['doctorid'],$doctor_ids)){
                $this->error('请勿非法操作！');
            }
        }
        $to = $ret['status'] ? 0 : 1;
        $message = $ret['status'] ? '已下架！' : '已上架！';
        $model->where(array('id'=>$id))->setField(array('status'=>$to));
        write_system_log();
        $this->success($message);
    }

    public function codelist(){
        $userid = I("param.userid",0,'intval');
        if(!$userid)$this->error("数据有误");
        $model = M('Smartcode');
        $where['userid'] = $userid;
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $data = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id DESC')->select();
//        var_dump($data);
        $this->assign('count', $count);
        $this->assign('data', $data);
        $this->assign('page',$show);
        $this->display();
    }
    public function codedetail(){
        $id = I("param.id",0,'intval');
        $detail = M("Smartcode")->where(array('id'=>$id))->find();
//        var_dump($detail);
        $this->assign('detail',$detail);
        $this->display();
    }


}
