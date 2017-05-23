<?php
namespace Home\Controller;
use Think\Controller;
class PayController extends BaseController {

       /** 
         * 支付宝异步通知
         * @author
         * @copyright
         */
	public function alipay() {
        //引入文件
        $alipay_config['partner']		= C('ALIPAY_PARTNER');
        $alipay_config['private_key_path']	= VENDOR_PATH.'Alipay/key/rsa_private_key.pem';
        $alipay_config['ali_public_key_path']= VENDOR_PATH.'Alipay/key/alipay_public_key.pem';
        $alipay_config['sign_type']    = strtoupper('RSA');
        $alipay_config['input_charset']= strtolower('utf-8');
        $alipay_config['cacert']    = VENDOR_PATH.'Alipay\\cacert.pem';
        $alipay_config['transport']    = 'http';

        vendor('Alipay.lib.alipay_core#function');
        vendor('Alipay.lib.alipay_rsa#function');
        vendor('Alipay.lib.alipay_notify#class');
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            $count = strlen($out_trade_no);
            //支付宝交易号
            $trade_no  = $_POST['trade_no'];
            $total_fee = $_POST['total_amount'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            if($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
                $order = M('Order');
                $s = $order->where(array('trade_sn'=>$out_trade_no))->find();
                if($s) {
                    //检测该订单是否已回调成功，防止同一订单多次回调
                    if($s['status'] == 'succ'){
                        echo true;
                        exit;
                    }
                    $s['transaction_id'] = $trade_no;
                    $s['pay_id']         = 1;
                    $s['pay_name']       = '支付宝';
                    if($s['payablemoney'] == $total_fee){ //验证篡改金额
                        $this->editUserOrderInfo($s);
                    }
                }
            }
            echo "success";
        }else {
            echo "fail";
        }
	}

    /** 
      * 微信支付
      * @author
      * @copyright
    */
    public function weixin(){
        require_once VENDOR_PATH.'/Weixin/WxPay.Api.php'; //引入微信Api
        require_once VENDOR_PATH.'/Weixin/WxPay.Config.php'; //引入微信配置文件
        require_once VENDOR_PATH.'/Weixin/WxPay.Data.php';
        require_once VENDOR_PATH.'/Weixin/WxPay.Notify.php';
        $payResult =  file_get_contents('php://input');
        $notify = new \WxPayNotify(); //返回值类
        $obj    = new \WxPayResults();
        $array  = $obj->FromXml($payResult);
        if($obj->checkSign() == TRUE){
            $order = M('Order');
            if($s  = $order->where(array('trade_sn'=>$array['out_trade_no']))->find()) {
                $s['transaction_id'] = $array['transaction_id'];
                $s['pay_id']         = 2;
                $s['pay_name']       = '微信支付';
                //检测该订单是否已回调成功，防止同一订单多次回调
                if($s['status'] == 'succ'){
                    echo $obj->ToResultWeixin(array('return_code'=>'SUCCESS','return_msg'=>'OK'));
                    exit;
                }
                $this->editUserOrderInfo($s);
                echo $obj->ToResultWeixin(array('return_code'=>'SUCCESS','return_msg'=>'OK'));
            }else{
                echo $obj->ToResultWeixin(array('return_code'=>'FAIL','return_msg'=>'参数格式校验错误'));
            }
        }else{
            echo $obj->ToResultWeixin(array('return_code'=>'FAIL','return_msg'=>'签名失败'));
        }
    }
    /**
     * 异步通知后操作...
     * @copyright
     */
    private function editUserOrderInfo($s)
    {
        if(empty($s) && !is_array($s)) return true;
        $order = M("Order");
        $arr  = array('status'=>'succ','pay_id'=>$s['pay_id'],'pay_name'=>$s['pay_name'],'paytime'=>time(),'etime'=>time(),'transaction_id'=>$s['transaction_id']);
        $ret = $order->where(array('trade_sn'=>$s['trade_sn']))->save($arr);
        if($ret){
            $detail = M("OrderDetail")->where(array('trade_sn'=>$s['trade_sn']))->select();
            $count = 0;
            $product_model = M("Product");
            foreach($detail as $val){
                if($val['cate_id'] == 3){
                    $count += $val['usage_count']*$val['num'];
                }
                $product_model->where(array('id'=>$val['productid']))->setInc('sale_count',$val['num']);
                if(in_array($val['cate_id'],[1,2,4]) ){
                    $product_model->where(array('id'=>$val['productid']))->setDec('inventory',$val['num']);
                }
            }
            M("GirdleTime")->where(array('userid'=>$s['userid']))->setInc('times',$count);

            return true;
        }else{
            return false;
        }
    }


	
}