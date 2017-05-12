<?php
namespace Extend;
	/**
	* 短信、彩信下发类
	*
	* @author lijj <lijj@umessage.com.cn>
	* @date 2013-5-22
	* @version 1.0.0
	* @copyright  Copyright 2013 12580.com
	*/
	class SmsMms {

		const USERNAME = 'wuxian_qianxiang';

		const PASSWORD = 'ed1f30042gNV2iUl0WLinSfdRMLEIbTP';

		static private $result = '';

	    static private $smssingle = "http://192.100.8.242:7000/sms/single";

        static private $smsgroup  = "http://192.100.8.242:7000/sms/group";

        static private $mmssingle = "http://192.100.8.242:7000/mms/single";

        static private $mmsgroup  = "http://192.100.8.242:7000/mms/group";


		/**
		* 短信发送
		*
		* @param string $sendType 单发(single)|群发(group)
		* @param int $ReceiveType 0.普通信息 1.自动拆分长短信 2.长短信 4.wap push 6.闪信
		* @param AppCode 业务代码
		* @param SourceTermID 长号码
		* @param DestTermID   目的号码
		* @param FeeTermID    计费号码
		* @param GateWay	群发手机号归属地省份
		* @param Tag  业务渠道
		* @param $MessageContent  短信内容
		* @param ValidTime  有效时间(短信生存期)
		* @return string
		*/
		static  public function sms($DestTermID,$FeeTermID,$MessageContent,$SendType = 'single', $ReceiveType = 2,$AppCode = '10101000',$SourceTermID = '10658880',$GateWay = '0531',$Tag = '',$ValidTime = '') {

			$http = $SendType == 'group' ? self::$smsgroup : self::$smssingle;
	        $url = $http."?UserName=".self::USERNAME."&Password=".self::PASSWORD."&AppCode=".$AppCode."&SourceTermID=".$SourceTermID."&ReceiveType=".$ReceiveType."&DestTermID=".$DestTermID."&FeeTermID=".$FeeTermID."&MessageContent=".urlencode($MessageContent)."&GateWay=".$GateWay."&Tag=".$Tag."&validTime=".$validTime;
			try {
	            self::$result = self::curlSms($url);
	        } catch (Exception $e) {
	        	throw new Exception( "SMS发送失败".$e, 100001);
	        }
	        return self::$result;
		}


		/*
		 * 下发彩信接口
		 *
		 * @param string $sendType 单发(single)|群发(group)
		 * @param AppCode 业务代码
		 * @param SourceTermID 发送号码
		 * @param DestTermID   目的号码
		 * @param FeeTermID    计费号码
		 * @param GateWay	群发手机号归属地省份
		 * @param Tag  业务渠道 [可选]
		 * @param Resource  彩信资源
		 * @return string
		*/
		 static public function  mms($DestTermID,$FeeTermID,$Resource,$SendType = 'single',$AppCode = '10611000',$SourceTermID = '10658880',$GateWay = '025',$Tag = '0') {
			$url = $SendType == 'group' ? self::$mmsgroup : self::$mmssingle;
	        $fields = "UserName=".self::USERNAME."&Password=".self::PASSWORD."&AppCode=".$AppCode."&SourceTermID=".$SourceTermID."&DestTermID=".$DestTermID."&FeeTermID=".$FeeTermID."&GateWay=".$GateWay."&Tag=".$Tag."&Resource=".$Resource;
			try {
	            self::$result = self::curlMms($url,$fields);
	        } catch (Exception $e) {
	        	throw new Exception( "MMS发送失败".$e, 100002);
	        }
	        return self::$result;
		}

		/**
		* curl模拟提交
		*
		* @param $url string 发送url
		* @return string
		*/
		static public function curlSms($url){
			$ret = '';
			if(!empty($url)){
				$ch2 = curl_init();
				curl_setopt($ch2, CURLOPT_URL, $url);
				curl_setopt($ch2, CURLOPT_HEADER, false);
				curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
				$val = curl_exec($ch2);
				curl_close($ch2);
				$ret_val = array();
				$ret_val = explode("\n",$val);
				$ret = $ret_val[0]."##".$ret_val[1];
			}
			return $ret ;
		}

	/**
	* curl模拟提交  POST
	*
	* @param $url string 发送url
	* @param $fields string 发送字段
	* @return string
	*/
	static public function curlMms($url,$fields){
		if(!empty($url) || !empty($fields)){
			$ch2 = curl_init();
			curl_setopt($ch2, CURLOPT_URL,$url);
			curl_setopt($ch2, CURLOPT_POST, 1 );
			curl_setopt($ch2, CURLOPT_POSTFIELDS,$fields);
			curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
			$val = curl_exec($ch2);
			curl_close($ch2);
			$ret_val = array();
			$ret_val = explode("\n",$val);
			$ret = $ret_val[0]."##".$ret_val[1];
		}else{
			$ret = '';
		}
		return $ret ;
	}

	}


?>
