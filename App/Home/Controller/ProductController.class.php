<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/4/18
 * Time: 14:19
 */

namespace Home\Controller;


class ProductController extends BaseController{

    public $pageSize = 15;
    /*
     * 产品列表
     * */
    public function getProductList(){
        $page = I("param.page",1,'intval');
        $cate_id = I("param.cate_id",0,'intval');
        $model = M("Product");
        if(!in_array($cate_id,[1,2,3])){
            $this->ajaxReturn(array('msg'=>'产品分类错误！','code'=>'4001'));
        }
        if($cate_id == 3){
            //如果商品为服务，则包括胸带服务和其他服务
            $where['cate_id'] = array('in',[3,4]);
        }else{
            $where['cate_id'] = $cate_id;
        }
        $where['isdel']   = 1;
        $where['status']  = 1;
        $limit = ($page-1)*$this->pageSize;
        $data = $model->where($where)->limit("$limit,$this->pageSize")
            ->order("etime desc")
            ->field("id,title,intro,price,img,thumb_img,inventory,usage_count,sale_count,cate_id")
            ->select();
        if($data && is_array($data)){
            $this->ajaxReturn(array('msg'=>'查询成功！','code'=>'0','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'没有更多数据！','code'=>'4002'));
        }
    }
    /*
     * 商品详情
     * */
    public function getProductDetail(){
        $product_id = I("param.product_id",0,'intval');
        if(!$product_id) $this->ajaxReturn(array('msg'=>'数据有误！','code'=>'4003'));
        $data = M("Product")->where(array('id'=>$product_id,'status'=>1,'isdel'=>1))
            ->field("id,title,intro,content,price,img,thumb_img,inventory,usage_count,sale_count,cate_id")
            ->find();
        $data['content'] = html_entity_decode(htmlspecialchars_decode($data['content']));
        if($data && is_array($data)){
            $this->ajaxReturn(array('msg'=>'查询成功！','code'=>'0','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'该商品已下架！','code'=>'4003'));
        }
    }
    /*
 * 购物车列表
 * */
    public function getUserCartList(){
        $page = I("param.page",1,'intval');
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $data = M("Cart")->alias('c')->where(array('c.userid'=>$user_id,'p.status'=>1,'p.isdel'=>1))
                ->order('p.id desc')
                ->limit("($page-1)*$this->pageSize,$this->pageSize")
                ->join(C("DB_PREFIX")."product p on p.id=c.productid",'INNER')
                ->field("c.num cart_num,p.id,p.title,p.intro,p.price,p.thumb_img,p.img,p.inventory,p.usage_count,p.cate_id")
                ->select();
            if($data && is_array($data)){
                $this->ajaxReturn(array('msg'=>"查询成功!",'code'=>"0",'data'=>$data));
            }else{
                $this->ajaxReturn(array('msg'=>"没有更多数据!",'code'=>"3010"));
            }
        }
    }
    /*
     * 加入购物车
     * */
    public function cartAddProduct(){
        $user_id = I("param.user_id",0,'intval');
        $count   = I("param.count",1,'intval');
        if($this->Checkticket($user_id)){
            $product_id = I("param.product_id",0,'intval');
            $data = M("Product")->where(array('id'=>$product_id,'status'=>1,'isdel'=>1))->find();
            if(!$data)$this->ajaxReturn(array('msg'=>'该商品已下架！','code'=>'4004'));
            $model = M("Cart");
            $check = $model->where(array('userid'=>$user_id,'productid'=>$product_id))->find();
            if($check){
                $ret = $model->where(array('id'=>$check['id']))->save(array('num'=>$check['num']+$count));
            }else{
                $ret = $model->add(array('userid'=>$user_id,'productid'=>$product_id,'num'=>$count));
            }
            if($ret){
                $this->ajaxReturn(array('msg'=>'加入成功！','code'=>'0'));
            }else{
                $this->ajaxReturn(array('msg'=>'加入失败！','code'=>'4005'));
            }
        }
    }
    /*
 * 删除购物车商品
 * */
    public function delUserCartProduct(){
        $product_id = I("param.product_id",0,'intval');
        if(!$product_id) $this->ajaxReturn(array('msg'=>"数据有误!",'code'=>"4006"));
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $ret = M("Cart")->where(array('userid'=>$user_id,'productid'=>$product_id))->delete();
            if($ret){
                $this->ajaxReturn(array('msg'=>"删除成功!",'code'=>"0"));
            }else{
                $this->ajaxReturn(array('msg'=>"删除失败!",'code'=>"4007"));
            }
        }
    }
    /*
     * 获取四张广告图
     * */
    public function getProductBanner(){
        $type = I("param.type",1,'intval');//type为1弹出式，2设备广告图，3配件广告图，4服务广告图
        $data = M("Advertise")->where(array('status'=>1,'type'=>$type))->field("id,title,description,picurl,type")->find();
        $this->ajaxReturn(array('msg'=>"查询成功!",'code'=>"0",'data'=>$data));
    }

}