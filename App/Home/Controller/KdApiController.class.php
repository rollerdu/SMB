<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/5/2
 * Time: 14:22
 */

namespace Home\Controller;
use Think\Controller;

class KdApiController extends BaseController{

    public $EBusinessID = '1286845';
    public $AppKey      = 'f9ed5b9a-6fb7-4b47-b7ac-58f033c7f1e5';
    public $ReqURL      = 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';
    /**
     * Json方式  物流信息订阅
     */
    public function orderTracesSubByJson(){
        $trade_sn = I("param.trade_sn",'','trim');
        if(!$trade_sn){
            $this->ajaxReturn(array('code'=>'6001','msg'=>'信息有误'));
        }
        $data = M("Order")->where(array('trade_sn'=>$trade_sn))->field('logistics,logistics_sn,logistics_code')->find();
        if(!in_array($data['logistics'],[1,2]) || !$data['logistics_sn'] || !$data['logistics_code']){
            $this->ajaxReturn(array('code'=>'6002','msg'=>'该订单暂无发货'));
        }

        $requestData= "{'OrderCode':'','ShipperCode':'".$data['logistics_code']."','LogisticCode':'".$data['logistics_sn']."'}";

        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->AppKey);
        $result=$this->sendPost($this->ReqURL, $datas);

        //根据公司业务处理返回的信息......
        $ret = json_decode($result,true);
        if($ret['Success'] == false){
            $this->ajaxReturn(array('code'=>'6003','msg'=>'暂无物流信息'));
        }
        $this->ajaxReturn(array('code'=>'0','msg'=>'查询成功',
            'logistics_code'=>$ret['LogisticCode'],
            'state'=>$ret['State'],
            'data'  => $ret['Traces']
        ));
    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    private function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    private function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }
}