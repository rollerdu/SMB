<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/3/23
 * Time: 14:35
 */

namespace Admin\Controller;


class OrderController extends BaseController{
    public $listRows = 20;
    public function index(){
        $title = I('get.title');
        if($title){
            $where['trade_sn'] = array('like','%'.$title.'%');
        }
        $where['isdel'] = 1;
        $model = M("Order");
        $count = $model->where($where)->count();
        $Page = new \Extend\Page($count,$this->listRows);
        $data = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('ctime desc')->select();
        $this->assign('page',$Page->show());
        $this->assign('data',$data);
        $this->assign('title',$title);
        $this->assign('count',$count);
        $this->display();
    }
    public function detail(){

        $id = I("param.id");
        $model = M("Order");
        $data = $model->alias('o')->where(array('o.id'=>$id))->join(C('DB_PREFIX').'user u on u.id = o.userid','LEFT')
            ->join(C('DB_PREFIX')."express e on e.code=o.logistics_code",'LEFT')
            ->field("o.*,u.mobile phone,u.username,e.logistics logist_name")->find();
        $detail = M("order_detail")->alias('a')->where(array('a.trade_sn'=>$data['trade_sn']))
            ->join(C('DB_PREFIX').'product p on p.id=a.productid','LEFT')
            ->field('a.*,p.title,p.intro,p.price,p.img,p.thumb_img')
            ->select();
        $logistics = M("Express")->order('sort desc')->select();

        $this->assign('logistics',$logistics);
        $this->assign('detail',$detail);
        $this->assign('data',$data);
        $this->display();
    }

    public function edit(){
        $trade_sn = I("post.trade_sn");
        $info = M("order")->where(array('trade_sn'=>$trade_sn))->find();
        if(!$info) $this->error("信息有误！");
        if(!$info['logistics_sn'] && trim(I("post.logistics_sn"))){
            if($info['status'] != 'succ'){
                $this->error("订单未支付，不能发货！");
            }
            $data['logistics_code'] = trim(I("post.logistics_code"));
            $data['logistics_sn'] = trim(I("post.logistics_sn"));
            if(!$data['logistics_code'] || !$data['logistics_sn']){
                $this->error("信息有误！");
            }
            $data['ltime'] = time();
            $data['logistics'] = 1;
        }
        $data['remark'] = I("post.remark");
        $data = array_filter($data);
        $ret = M("Order")->where(array('trade_sn'=>$trade_sn))->save($data);
        if($ret){
            write_system_log();
            $this->success('修改成功！');
        }else{
            $this->error("修改失败！");
        }
    }

}