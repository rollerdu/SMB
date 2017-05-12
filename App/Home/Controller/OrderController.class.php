<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/4/17
 * Time: 16:51
 */

namespace Home\Controller;
use Think\Controller;


class OrderController extends BaseController{


    public $alipay_notify_url = "http://app.jfr.cn/Home/Pay/alipay";
    public $weixin_notify_url = "http://app.jfr.cn/Home/Pay/weixin";
    /*
     * 商品直接购买接口
     * */
    public function productAddOrder(){
        $user_id    = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $address_id     = I("param.address_id",0,'intval');
            $product_id     = I("param.product_id",0,'intval');
            $num            = abs(I("param.num",1,'intval'));
            $address = M("Address")->where(array('userid'=>$user_id,'id'=>$address_id))->find();
            if(!$address) $this->ajaxReturn(array('code'=>'5001','msg'=>'收货地址有误，请重新选择'));
            $data = M("Product")->where(array('id'=>$product_id,'status'=>1,'isdel'=>1))->find();
            if(!$data || !is_array($data)){
                $this->ajaxReturn(array('code'=>'5002','msg'=>'该商品已下架！'));
            }
            if(in_array($data['cate_id'],[1,2])) {
                if ($data['inventory'] < $num) {
                    $this->ajaxReturn(array('code' => '5003', 'msg' => '该商品库存不足！'));
                }
            }
            $detail['trade_sn']     = $this->createOrderMergeNo();
            $detail['productid']    = $product_id;
            $detail['title']        = $data['title'];
            $detail['intro']        = $data['intro'];
            $detail['cate_id']      = $data['cate_id'];
            $detail['content']      = $data['content'];
            $detail['img']          = $data['img'];
            $detail['thumb_img']    = $data['thumb_img'];
            $detail['money']        = $data['price'];
            $detail['num']          = $num;
            $detail['usage_count']  = $data['usage_count'];
            $detail['ctime']        = time();
            $detail['etime']        = time();
            $ins = M("OrderDetail")->add($detail);
            if($ins){
                $order['heading']       = $detail['title'].'等'.$detail['num'].'个商品';
                $order['trade_sn']      = $detail['trade_sn'];
                $order['userid']        = $user_id;
                $order['num']           = $num;
                $order['payablemoney']  = $num * $detail['money'];
                $order['user_name']     = $address['truename'];
                $order['mobile']        = $address['mobile'];
                $order['address']       = $address['provname'].$address['cityname'].$address['townname'].$address['address'];
                $order['status']        = 'unpay';
                $order['logistics']     = 0;
                $order['ip']            = get_client_ip();
                $order['isdel']         = 1;
                $order['ctime']         = $order['etime']  = time();
                $ret = M("Order")->add($order);
                if($ret){
                    $this->ajaxReturn(array('code'=>'0','msg'=>'订单提交成功,45分钟有效，请及时付款！','data'=>$order));
                }else{
                    M("OrderDetail")->where(array('trade_sn'=>$detail['trade_sn']))->delete();
                    $this->ajaxReturn(array('code'=>'5004','msg'=>'订单生成失败！'));
                }

            }else{
                $this->ajaxReturn(array('code'=>'5005','msg'=>'数据有误！'));
            }
        }

    }
    /*
     * 购物车提交接口
     * */
    public function cartAddOrder(){
        $user_id    = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $address_id     = I("param.address_id",0,'intval');
            $product_ids     = explode(',',I("param.product_id"));
            if(!$product_ids || !is_array($product_ids)){
                $this->ajaxReturn(array('code'=>'5000','msg'=>'数据有误!'));
            }
            $address = M("Address")->where(array('userid'=>$user_id,'id'=>$address_id))->find();
            if(!$address) $this->ajaxReturn(array('code'=>'5001','msg'=>'收货地址有误，请重新选择'));

            $cart_model = M("Cart");
            $product_model = M("Product");

            $cart = $cart_model->where(array('userid'=>$user_id,'productid'=>array('in',$product_ids)))->field('productid,num')->select();
            if(!$cart){
                $this->ajaxReturn(array('code'=>'5022','msg'=>'购物车没数据'));
            }
            foreach($cart as $vv){
                $cart_data[$vv['productid']] = $vv['num'] ? $vv['num'] : 1;
            }

            $data = $product_model->where(array('id'=>array('in',$product_ids)))->select();
            if(!$data){
                $this->ajaxReturn(array('code'=>'5021','msg'=>'数据有误'));
            }
            $trade_sn = $this->createOrderMergeNo();
            $all_count = 0;
            $all_money = 0;
            $product_model->startTrans();
            foreach($data as $val){
                if($val['status'] == 0 || $val['isdel'] == 0){
                    $product_model->rollback();
                    $cart_model->where(array('userid'=>$user_id,'productid'=>$val['id']))->delete();
                    $this->ajaxReturn(array('code'=>'5006','msg'=>"商品$val[title]已下架！"));
                }
                if(in_array($val['cate_id'],[1,2])){
                    $product_model->rollback();
                    if($val['inventory'] < $cart_data[$val['id']]){
                        $this->ajaxReturn(array('code'=>'5007','msg'=>"商品$val[title]库存不足！"));
                    }
                }
                $detail['trade_sn']     = $trade_sn;
                $detail['productid']    = $val['id'];
                $detail['cate_id']      = $val['cate_id'];
                $detail['title']        = $val['title'];
                $detail['intro']        = $val['intro'];
                $detail['content']      = $val['content'];
                $detail['img']          = $val['img'];
                $detail['thumb_img']    = $val['thumb_img'];
                $detail['money']        = $val['price'];
                $detail['num']          = $cart_data[$val['id']];
                $detail['usage_count']  = $val['usage_count'];
                $detail['ctime']        = time();
                $detail['etime']        = time();
                $all_count             += $cart_data[$val['id']];
                $all_money             += $val['price'] * $cart_data[$val['id']];
                $ins = M("OrderDetail")->add($detail);
                if(!$ins){
                    $product_model->rollback();
                    $this->ajaxReturn(array('code'=>'5008','msg'=>"订单添加失败"));
                }
            }
        }
        $order['heading']       = $data[0]['title'].'等'.count($data).'个商品';
        $order['trade_sn']      = $trade_sn;
        $order['userid']        = $user_id;
        $order['num']           = $all_count;
        $order['payablemoney']  = $all_money;
        $order['user_name']     = $address['truename'];
        $order['mobile']        = $address['mobile'];
        $order['address']       = $address['provname'].$address['cityname'].$address['townname'].$address['address'];
        $order['status']        = 'unpay';
        $order['logistics']     = 0;
        $order['ip']            = get_client_ip();
        $order['isdel']         = 1;
        $order['ctime']         = $order['etime']  = time();
        $ret = M("Order")->add($order);
        if($ret){
            $cart_model->where(array('userid'=>$user_id,'productid'=>array("in",$product_ids)))->delete();
            $product_model->commit();
            $this->ajaxReturn(array('code'=>'0','msg'=>'订单提交成功,45分钟有效，请及时付款！','data'=>$order));
        }else{
            $product_model->rollback();
            $this->ajaxReturn(array('code'=>'5004','msg'=>'订单生成失败！'));
        }

    }
    /*
     * 支付
     * */
    public function goToPay(){
        $user_id    = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)) {
            $trade_sn = I("param.trade_sn", '', 'trim');
            $pay_id   = I("param.pay_id",0,'intval');
            if(!in_array($pay_id,[1,2])){
                $this->ajaxReturn(array('code'=>'5006','msg'=>'付款信息有误！'));
            }
            if(!$trade_sn){
                $this->ajaxReturn(array('code'=>'5007','msg'=>'数据有误！'));
            }
            $order_model = M("Order");
            $detail_model = M("OrderDetail");
            $order = $order_model->where(array('trade_sn'=>$trade_sn,'userid'=>$user_id))->find();
            if(!$order){
                $this->ajaxReturn(array('code'=>'5008','msg'=>'订单信息有误！'));
            }
            if(time() - $order['ctime'] > C("ORDER_EFFECTIVE_TIME")){
                $order_model->where(array('trade_sn'=>$trade_sn))->delete();
                $detail_model->where(array('trade_sn'=>$trade_sn))->delete();
                $this->ajaxReturn(array('code'=>'5009','msg'=>'该订单已失效！'));
            }
            if ($pay_id == 1) { //支付宝
                $infos['partner']       = C('ALIPAY_PARTNER');
                $infos['out_trade_no'] = strval($order['trade_sn']);
                $infos['subject'] = $order['heading'];
                $infos['total_fee'] = strval($order['payablemoney']);
                $infos['body'] = "买家(订单:$order[trade_sn])支付订单(￥：$order[payablemoney])";
                $infos['notify_url'] = $this->alipay_notify_url;
                $order_model->where(array('trade_sn', $order['trade_sn']))->save( array( 'pay_id'=>1,'pay_name'=>'支付宝', 'etime' => time()));
                $this->ajaxReturn(array('code'=>'0','msg'=>'请求成功！','data'=>$infos));

            }elseif ($pay_id == 2) { //微信支付
                $param = array(
                    'notify_url'    => $this->weixin_notify_url,
                    'trade_sn'      => $order['trade_sn'],
                    'body'          => $order['heading'],
                    'money'         => $order['payablemoney'],
                    'reqreserved'   => "买家(订单:$order[trade_sn])支付订单(￥：$order[payablemoney])"
                );
                if (count(array_filter($param)) < 4){
                    $this->ajaxReturn(array('code'=>'5010','msg'=>'缺少参数！'));
                }
                $infos = $this->weixinPay($param);
                if ($infos != false) {
                    $order_model->where(array('trade_sn', $order['trade_sn']))->save( array( 'pay_id'=>2,'pay_name'=>'微信支付', 'etime' => time()));
                    $this->ajaxReturn(array('code'=>'0','msg'=>'请求成功！','data'=>$infos));
                } else {
                    $this->ajaxReturn(array('code'=>'5011','msg'=>'微信生成失败,请稍后重试!！'));

                }
            }

        }

    }
    /**
     * 微信支付
     * @Param  $arr['body'] 产品名称
     * @Param  $arr['attach'] 产品附加信息
     * @Param  $arr['money'] 总价
     * @Param: $arr['trade_sn] 订单号
     * @Param: $arr['trade_type] 交易类型(JSAPI，NATIVE，APP，WAP)
     * @Param: $arr['notify_url'] 异步通知url
     * @copyright 2015
     */
    private function weixinPay($param){
        require_once VENDOR_PATH.'/Weixin/WxPay.Api.php'; //引入微信Api
        require_once VENDOR_PATH.'/Weixin/WxPay.Config.php'; //引入微信配置文件
        require_once VENDOR_PATH.'/Weixin/WxPay.Data.php';

        //产品信息
//        extract($param);
        if(!$param['body'] || !$param['trade_sn'] || !$param['money'] || !$param['notify_url']) return array();

        $money = $param['money']*100;
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($param['body']); //产品名称
//        $input->SetAttach($type);
        $input->SetOut_trade_no($param['trade_sn']);
        $input->SetTotal_fee($money);
        $input->SetTime_start(date("YmdHis"));
        $input->SetNotify_url($param['notify_url']);
        $input->SetTrade_type("APP");
//        $input->SetOpenid($openId);
        $order =  \WxPayApi::unifiedOrder($input);
        if(!empty($order) && is_array($order)){
            $obj = new \WxPayResults();
            if($obj->CheckSign()){
                $arr = array('prepay_id'=>$order['prepay_id'],'nonce_str'=>$order['nonce_str']);
                return $obj->returenAppFromArray($arr);
            }else{
                return false; //签名错误
            }
        }else{
            return false; //订单生成失败
        }
    }
    /*
     * 删除我的订单
     * */
    public function deleteUserOrder(){
        $user_id    = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)) {
            $trade_sn = I("param.trade_sn", '', 'trim');
            if(!$trade_sn){
                $this->ajaxReturn(array('code'=>'5012','msg'=>'数据有误！'));
            }
            $model = M("Order");
            $data = $model->where(array('userid'=>$user_id,'trade_sn'=>$trade_sn))->find();
            if(!$data){
                $this->ajaxReturn(array('code'=>'5013','msg'=>'订单信息有误'));
            }
            if($data['status'] == 'unpay' || $data['status'] == 'timeout'){
                $ret = $model->where(array('userid'=>$user_id,'trade_sn'=>$trade_sn))->delete();
                if($ret){
                    M("OrderDetail")->where(array('trade_sn'=>$trade_sn))->delete();
                    $this->ajaxReturn(array('code'=>'0','msg'=>'订单删除成功'));
                }else{
                    $this->ajaxReturn(array('code'=>'5014','msg'=>'订单删除失败'));
                }
            }elseif($data['status'] == 'succ' && $data['logistics'] == 2){
                $ret = $model->where(array('userid'=>$user_id,'trade_sn'=>$trade_sn))->setField('isdel',0);
                if($ret){
                    $this->ajaxReturn(array('code'=>'0','msg'=>'订单删除成功'));
                }else{
                    $this->ajaxReturn(array('code'=>'5015','msg'=>'订单删除失败'));
                }
            }else{
                $this->ajaxReturn(array('code'=>'5016','msg'=>'该订单暂时不能删除'));
            }
        }
    }
    /*
     * 确认收货
     * */
    public function confirmGetOrder(){
        $user_id    = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)) {
            $trade_sn = I("param.trade_sn", '', 'trim');
            if (!$trade_sn) {
                $this->ajaxReturn(array('code' => '5012', 'msg' => '数据有误！'));
            }
            $model = M("Order");
            $data = $model->where(array('userid' => $user_id, 'trade_sn' => $trade_sn))->find();
            if (!$data || $data['status'] == 'unpay' || $data['status'] == 'timeout') {
                $this->ajaxReturn(array('code' => '5017', 'msg' => '订单信息有误'));
            }elseif($data['status'] == 'succ' && $data['logistics'] == 2){
                $this->ajaxReturn(array('code' => '5020', 'msg' => '该订单已确认收货'));
            }elseif ($data['status'] == 'succ') {
                $ret = $model->where(array('userid'=>$user_id,'trade_sn'=>$trade_sn))->save(array(
                    'logistics' => 2,
                    'etime'     => time(),
                ));
                if($ret){
                    $this->ajaxReturn(array('code' => '0', 'msg' => '修改成功'));
                }else{
                    $this->ajaxReturn(array('code' => '5018', 'msg' => '修改失败'));
                }
            }else{
                $this->ajaxReturn(array('code' => '5019', 'msg' => '订单信息有误'));
            }
        }
    }
}