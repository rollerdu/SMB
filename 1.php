<?php
$soap = new SoapClient('https://dcwebservices.smartlinkwarehouse.com/smartcodeservice.asmx?WSDL');
//var_dump($soap);
//var_dump($soap->__getFunctions());
//var_dump($soap->__getTypes());

try {

    $parseSmartCode = new stdClass();
    $parseSmartCode->codeType = 3;
    $parseSmartCode->code = '7MTJT47D97DT7L';
    $parseSmartCode->serialCheck = true;
    $parseSmartCode->serialNumber = 'HD000005';

    $scTransport = $soap->ParseSmartCode($parseSmartCode);
    var_dump($scTransport->ParseSmartCodeResult);

} catch(Exception $e) {
    print_r($e);
}

/*
 *  接入地址http://www.ytx.net/ytx.htm?type=Console
 *  云讯科技
 *  @date 2017/03/09
 */

class ytx {

	const accountSID = "22e2aaa008384674bd5eb18740eda5f7";
	const authToken = "6565bef09aef4a2aae8b5e1b1f2ee2fd";
	const version = "201612";
	const appID = "2fc433f7240b4b1e8661aa18646d3a5e";
	const serverUrl = "http://api.ytx.net/";
	/*const appID = "179d299cdb66457cb439dcfafd3c9301";
	const serverUrl = "http://sandbox.ytx.net/";*/


    private $arr_post = array();
    public  $http_post = array();
    private $ytx_url = self::serverUrl . self::version . "/sid/";

	function __construct(){
		echo $this->ytx_url;
	}

}

//$obj = new ytx();
