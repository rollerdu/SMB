<?php
namespace Extend\PhpRsa;
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2016/9/23
 * Time: 9:21
 */
class Rsa{
    //测试环境的pem文件
    const PRIVATE_KEY_ALPHA   = '/pem/serverPrivateKey_Alpha.pem';
    //生产环境的pem文件
    const PRIVATE_KEY_RELEASE = '/pem/serverPrivateKey_release.pem';

    public function get_private_encrypt(){
        $pem_path = 1 ? dirname(__FILE__).Rsa::PRIVATE_KEY_ALPHA : dirname(__FILE__).Rsa::PRIVATE_KEY_RELEASE;
        (file_exists($pem_path)) or die('秘钥路径不正确！');
        $pi_key =  openssl_pkey_get_private(file_get_contents($pem_path));
        $data = $this->getRandChar();//原始数据
        $encrypted = "";
        openssl_private_encrypt($data,$encrypted,$pi_key);//私钥加密

        $encrypted = base64_encode($encrypted);
        return  array('encrypted'=>$encrypted,'rand_code'=>$data);
    }
    //生成随机字符串
    function getRandChar($length=16){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }

}