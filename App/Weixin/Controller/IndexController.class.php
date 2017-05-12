<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/4/26
 * Time: 13:27
 */
namespace Weixin\Controller;
use Think\Controller;
class IndexController extends BaseController{

    public $pageSize = 20;
    public function index(){
        $userid = $_SESSION['UserInfo']['userid'];
        $page = I("param.page",1,'intval');
        $limit = ($page-1)*$this->pageSize;
        $data = M("Smartcode")->where(array('userid'=>$userid))
            ->limit("$limit,$this->pageSize")
            ->field("id,smartcode,ctime,titrationmode")
            ->select();
        if($page > 1){
            if($data && is_array($data)){
                $this->ajaxReturn(array('code'=>0,'msg'=>'查询成功','data'=>$data));
            }else{
                $this->ajaxReturn(array('code'=>1000,'msg'=>'没有更多数据'));
            }
        }
        $this->assign('data',$data);
        $this->display();
    }
    public function smInfo(){
        $id = I("param.id",0,'intval');
        if(!$id) $this->error('数据有误');
        $userid = $_SESSION['UserInfo']['userid'];
        $data = M("Smartcode")->where(array('userid'=>$userid,'id'=>$id))->find();
        if($data){
            $this->assign('data',$data);
            $this->display();
        }else{
            $this->error('数据有误');
        }
    }

}