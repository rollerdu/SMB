<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/3/28
 * Time: 13:29
 */

namespace Admin\Controller;


class HospitalController extends BaseController{

    public function index(){
        $model = M("Hospital");
        $title = trim(I("param.title"));
        if($title){
            $where['h.title'] = array('like',"%$title%");
        }
        $count  = $model->alias('h')->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $data = $model->alias('h')->limit($Page->firstRow.','.$Page->listRows)
            ->join(C('DB_PREFIX').'region r on r.region_id=h.provid','LEFT')
            ->join(C('DB_PREFIX').'region g on g.region_id=h.cityid','LEFT')
            ->where($where)->order('h.id DESC')->field('h.*,r.region_name provname,g.region_name cityname')->select();
        $this->assign('data', $data);
        $this->assign('count', $count);
        $this->assign('page',$show);
        $this->assign('title',$title);
        $this->display();
    }
    public function add(){
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Hospital");
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
    public function edit()
    {
        $id = intval(I("param.id"));
        if (IS_POST) {
            $model = D("Hospital");
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
        $data = M("Hospital")->where(array('id'=>$id))->find();
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
        $this->assign('data',$data);
        $this->assign('current_citys',$current_citys);
        $this->assign('area',$area);
        $this->assign('citys',json_encode($citys));
        $this->display();
    }
    public function upstatus(){
        $id = intval(I('get.id'));
        if(!$id) $this->error('数据有误，请刷新重试！');
        $model = M('Hospital');
        $where['id'] = $id;
        $ret = $model->where($where)->find();
        if(!$ret) $this->error('数据有误，请刷新重试！');
        $doctor = M("Doctor")->where(array('hospitalid'=>$id))->select();
        if($doctor){
            $this->error('该医院下有附属医生，不可下线');
        }
        $to = $ret['status'] ? 0 : 1;
        $message = $ret['status'] ? '已停用！' : '已启用！';
        $model->where(array('id'=>$id))->setField(array('status'=>$to));
        write_system_log();
        $this->success($message);
    }
    public function delete($id)
    {
        $doctor = M("Doctor")->where(array('hospitalid'=>$id))->select();
        if($doctor){
            $this->error('该医院下有附属医生，不可删除');
        }
        $model = M('Hospital');
        $result = $model->where('id='.$id)->delete();

        if($result){
            write_system_log();
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }


}