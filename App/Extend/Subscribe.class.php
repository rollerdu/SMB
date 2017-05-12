<?php
namespace Extend;
	/**
	* 订购库类
	*
	* @author lijj <lijj@umessage.com.cn>
	* @date 2016-9-13
	* @version 1.0.0
	* @copyright  Copyright 2007 12580.com
	*/
	class Subscribe {

		static private $result = '';

        static private $SUBSCRIBE = 'http://192.100.8.243:8888/subscribe?id=$id&servcode=$servcode&appcode=$appcode';

        static private $CANCEL = 'http://192.100.8.243:8888/cancel?id=$id&servcode=$servcode';

	    static private $EXISTS = 'http://192.100.8.243:8888/exists?id=$id';

        static private $SERVICES = 'http://192.100.8.243:8888/services?id=$id';


		/**
		 * 查询一个用户订阅的服务详细列表
         *
         * http://HOST/services?id=12312345678 或者 http://HOST/services?id=12312345678&servcode=SHBB
        *
        * @ param string id 手机号
        * @ param string servcode 业务代码用于标识一个完整的内容、计费单元，须统一编码，采用拼音首字母风格，如：全能助理短信版(QNZL_SMS)，生活播报(SHBB)
        * @ return string
		*/
		static  public function services($id,$servcode='') {
            $ext = '';
            if ($id) {
                if ($servcode) {
                    $ext = "&servcode=".$servcode;
                }
                $url = self::$SERVICES.$ext;
                eval("\$url = \"$url\";");
                try {
                    self::$result = self::curlGet($url);
                } catch (Exception $e) {
                    throw new Exception( "services请求失败".$e->getMessage(), 100001);
                }
            }

	        return self::$result;
		}

        /**
         * 判断一个用户是否订阅了某项业务
         *
         * http://HOST/exists?id=12312345678&servcode=SHBB
         *
         * @ param string id 手机号
         * @ param string servcode 业务代码用于标识一个完整的内容、计费单元，须统一编码，采用拼音首字母风格，如：全能助理短信版(QNZL_SMS)，生活播报(SHBB)
         * @ return string
         */
        static  public function exists($id,$servcode='') {
            $ext = '';
            if ($id) {
                if ($servcode) {
                    $ext = "&servcode=".$servcode;
                }
                $url = self::$EXISTS.$ext;
                eval("\$url = \"$url\";");
                try {
                    self::$result = self::curlGet($url);
                } catch (Exception $e) {
                    throw new Exception( "exists请求失败".$e->getMessage(), 100002);
                }
            }

            return self::$result;
        }
		/**
		 *  用户订阅服务
		 *
		 * http://HOST/booking_service?id=12312345678&servcode=FLBK_MMS&appcode=$appcode   或者
		 * http://HOST/booking_service?id=12312345678&servcode=FLBK_MMS&appcode=$appcode&subcodes=$subcodes
		 * @ param string id 手机号
		 * @ param string servcode 业务代码用于标识一个完整的内容、计费单元，须统一编码，采用拼音首字母风格，如：全能助理短信版(QNZL_SMS)，生活播报(SHBB)
		 * @ param string appcode
		 * @ param string subcodes
		 * @ return string
		 */
		static  public function subscribe($id,$servcode,$appcode,$subcodes='') {
			$ext = '';
			if ($id && $servcode && $appcode) {
                if($subcodes){
                    $ext = "&subcodes=".$subcodes;
                }

				$url = self::$SUBSCRIBE.$ext;
				eval("\$url = \"$url\";");
				try {
					self::$result = self::curlGet($url);
				} catch (Exception $e) {
					throw new Exception( "services请求失败".$e->getMessage(), 100001);
				}
			}

			return self::$result;
		}

		/**
		* curl模拟提交
		*
		* @param $url string 发送url
		* @return string
		*/
		static public function curlGet($url){
			$ret = false;
			if(!empty($url)){
				$ch2 = curl_init();
				curl_setopt($ch2, CURLOPT_URL, $url);
				curl_setopt($ch2, CURLOPT_HEADER, false);
                curl_setopt($ch2, CURLOPT_TIMEOUT,5);
				curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
				$val = curl_exec($ch2);
				curl_close($ch2);
				$ret_val = array();
				$ret_val = json_decode($val,true);
                if ($ret_val['result']) {
                    $ret = $ret_val;
                }
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
	static public function curlPost($url,$fields){
        $ret = false;
		if(!empty($url) || !empty($fields)){
			$ch2 = curl_init();
			curl_setopt($ch2, CURLOPT_URL,$url);
			curl_setopt($ch2, CURLOPT_POST, 1 );
            curl_setopt($ch2, CURLOPT_TIMEOUT,5);
			curl_setopt($ch2, CURLOPT_POSTFIELDS,$fields);
			curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
			$val = curl_exec($ch2);
			curl_close($ch2);
			$ret_val = array();
            $ret_val = json_decode($val,true);
            if ($ret_val['result']) {
                $ret = $ret_val;
            }
		}
		return $ret ;
	}

}

?>
