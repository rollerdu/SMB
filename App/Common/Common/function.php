<?php


function set_user_uuid($uid,$key=1){
    return str_pad($key, 11-strlen($uid) , "0").$uid;
}

function rand_number(){
	return time().rand(10000,99999);
}

function upass($str){
	return md5(md5($str).'pYDk3QHZU6Yq0PFI6y5zXFOqAQsMPCUu');
}

function mkdir_by_date($date) {
	list($y, $m, $d) = explode('-', date('Y-m-d', $date));
	return "$y/$m$d";
}


//往期彩信接口
function get_mms_list($cid='584',$rid="19010000",$num=10,$page=1){

	$url = "http://192.100.7.15/~gateway/newcontent/index.php/wap/api/ls/?cid=$cid&rid=$rid&num=$num&page=$page";
	$ret = get_url_contents($url);
	$stu = json_decode($ret,true);
	if($stu['code'] == 0000){
		$set = $stu['list'];
	}else{
		$set = "";
	}
	return $set;
}

//往期彩信内容接口
function get_mms_view($cid='584',$rid="19010000",$mmsid=""){

	$url = "http://192.100.7.15/~gateway/newcontent/index.php/wap/api/view/?cid=$cid&rid=$rid&mmsid=$mmsid";
	$ret = get_url_contents($url);
	$stu = json_decode($ret,true);
	if($stu['code'] == 0000){
		$set = $stu['list'];
	}else{
		$set = "";
	}
	return $set;
}

//资讯缩略图
function img($id,$type='1'){
	$model = M("Image");
    if( $type==1 )
	$data = $model->where("article_id=$id")->find();
    else
    $data = $model->where("video_id=$id")->find();


    $thumb_img = "/Public/home/img/historyimg.png";
	if($data){
        $t = unserialize($data['imgpath']);
        $t_img = substr($t['thumb_img'],1);
        if (file_exists(ROOT_PATH . $t_img)) {
            $thumb_img = $t_img;
        }
    }
    return SITE_URL.$thumb_img;
}
function get_order_id(){
	return date('YmdHis', time()).rand(10000,99999);
}

function get_order_state($state){
	switch ($state) {
		case '0':
			return '未处理';
			break;
		case '1':
			return '已完成';
			break;
		case '2':
			return '已确认';
			break;
		case '3':
			return '已关闭';
			break;
		default:
			return '未知';
	}
}

function tp_avatar($img){
	if($img){
		return (substr($img,0,4) != 'http') ? SITE_URL.AVATAR_PATH.$img : $img;
	}
	return '/Public/images/noavatar.gif';
}
function tp_thumb($pic){
	$pic = explode('/', $pic);
	if(count($pic) > 0){
		$pic[4] = 'thumb_'.$pic[4];
		return implode('/', $pic);
	}

	return $pic;
}
function tp_time($t,$sort = false){
	$t = intval($t);
	if(!$t){
		return '无';
	}
	$cut = time();
	$diff = $cut - $t;
	$minute = ($diff/60)%60;
	$hour = ($diff/3600)%24;
	if($diff > 86400){
		if($sort ){
			return date('m月d日',$t);
		}else{
			return date('Y-m-d',$t);
		}
	}elseif($diff > 3600 && $diff < 86400){
		return $hour.'小时前';
	}elseif($diff > 60 && $diff < 3600) {
		return $minute.'分钟前';
	}else{
		return $diff.'秒前';
	}
}


function tp_ad($id, $target = '_blank'){
	$info = M('Ad')->find($id);
	if(! $info){
		return '';
	}

	switch ($info['type']) {
		case '1':
			return '<a href="'.$info['url'].'" target="'.$target.'"><img src="'.$info['path'].'" width="'.$info['width'].'" height="'.$info['height'].'" border="0" /></a>';
			break;

		case '2':
			return '<object type="application/x-shockwave-flash" data="'.$info['path'].'" width="'.$info['width'].'" height="'.$info['height'].'"><param name="movie" value="'.$info['path'].'" /><param value="transparent" name="wmode"></object>';
			break;

		default:
			return '';
			break;
	}
}

function hidtel($phone){
	$IsWhat = preg_match('/(0[0-9]{2,3}[-]?[2-9][0-9]{6,7}[-]?[0-9]?)/i',$phone);
	if($IsWhat == 1){
		return preg_replace('/(0[0-9]{2,3}[-]?[2-9])[0-9]{3,4}([0-9]{3}[-]?[0-9]?)/i','$1****$2',$phone);
	}else{
		return substr_replace($phone, '****', 3, 4);
	}
}

function returnJsonMsg($msg){
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($msg);
	exit;
}

function html2txt($html) {
	$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
		'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
		'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
		'@<![\s\S]*?--[ \t\n\r]*>@'        // Strip multi-line comments including CDATA
	);
	$text = preg_replace($search, '', $html);
	$text = strip_tags($text);
	return str_replace(array("\r","\n","\t","&nbsp;"), '', $text);
}

function str_cut($Str, $Length, $append = true) {
	$Str = html2txt($Str);
	$i = 0;
	$l = 0;
	$ll = strlen($Str);
	$s = $Str;
	$f = true;
	if($ll > $Length){
		while ($i < $ll) {
			if (ord($Str{$i}) < 0x80) {
				$l++; $i++;
			} else if (ord($Str{$i}) < 0xe0) {
				$l++; $i += 2;
			} else if (ord($Str{$i}) < 0xf0) {
				$l += 2; $i += 3;
			} else if (ord($Str{$i}) < 0xf8) {
				$l += 1; $i += 4;
			} else if (ord($Str{$i}) < 0xfc) {
				$l += 1; $i += 5;
			} else if (ord($Str{$i}) < 0xfe) {
				$l += 1; $i += 6;
			}

			if (($l >= $Length ) && $f) {
				$s = substr($Str, 0, $i);
				$f = false;
			}

			if (($l > $Length) && ($i < $ll)) {
				$isCut = true;
				break; //如果进行了截取退出
			}
			if (!$f) {
				break;//取出指定的长度后退出
			}
		}
		if(($i < $ll) && $append){
			if(is_string($append)){
				$s .= $append;
			}else{
				$s .= '...';
			}
		}
	}
	return $s;
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','&quot;',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);
	return $string;
}

/**
 * 过滤非法关键词
 */
function StrFilterCheck($Str,$Code_FilterStr) {
	$counts = count($Code_FilterStr);
	for ($i=0; $i<$counts; $i++) {
		if (eregi($Code_FilterStr[$i],$Str)) {
			return true;
		}
	}
	return false;
}

/**
 * 验证是否包含非法关键词，如果有将关键词换成**
 */
function StrFilterCheck2($Str, $Code_FilterStr) {
	$Code_FilterStr1 = array_combine($Code_FilterStr,array_fill(0,count($Code_FilterStr),'*'));
	return strtr($Str, $Code_FilterStr1);
}

/**
 * 用DES算法加密/解密字符串
 *
 * @param string $string 待加密的字符串
 * @param string $key 密匙，和管理后台需保持一致
 * @return string 返回经过加密/解密的字符串
 */

// 加密，注意，加密前需要把数组转换为json格式的字符串
function des_encrypt($string, $key) {
	$size = mcrypt_get_block_size('des', 'ecb');
	$string = mb_convert_encoding($string, 'GBK', 'UTF-8');
	$pad = $size - (strlen($string) % $size);
	$string = $string . str_repeat(chr($pad), $pad);
	$td = mcrypt_module_open('des', '', 'ecb', '');
	$iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	@mcrypt_generic_init($td, $key, $iv);
	$data = mcrypt_generic($td, $string);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	$data = base64_encode($data);
	return $data;
}

// 解密，解密后返回的是json格式的字符串
function des_decrypt($string, $key) {
	$string = base64_decode($string);
	$td = mcrypt_module_open('des', '', 'ecb', '');
	$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	$ks = mcrypt_enc_get_key_size($td);
	@mcrypt_generic_init($td, $key, $iv);
	$decrypted = mdecrypt_generic($td, $string);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	$pad = ord($decrypted{strlen($decrypted) - 1});
	if($pad > strlen($decrypted)) {
		return false;
	}
	if(strspn($decrypted, chr($pad), strlen($decrypted) - $pad) != $pad) {
		return false;
	}
	$result = substr($decrypted, 0, -1 * $pad);
	$result = mb_convert_encoding($result, 'UTF-8', 'GBK');
	return $result;
}


/**
 * 下拉选择框
 */
function select($array = array(), $id = 0, $str = '', $default_option = '') {
	$string = '<select '.$str.'>';
	$default_selected = (empty($id) && $default_option) ? 'selected' : '';
	if($default_option) $string .= "<option value='' $default_selected>$default_option</option>";
	if(!is_array($array) || count($array)== 0) return false;
	$ids = array();
	if(isset($id)) $ids = explode(',', $id);
	foreach($array as $key=>$value) {
		$selected = in_array($key, $ids) ? 'selected' : '';
		$string .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
	}
	$string .= '</select>';
	return $string;
}

/**
 * 复选框
 *
 * @param $array 选项 二维数组
 * @param $id 默认选中值，多个用 '逗号'分割
 * @param $str 属性
 * @param $defaultvalue 是否增加默认值 默认值为 -99
 * @param $width 宽度
 */
function checkbox($array = array(), $id = '', $str = '', $defaultvalue = '', $width = 0, $field = '') {
	$string = '';
	$id = trim($id);
	if($id != '') $id = strpos($id, ',') ? explode(',', $id) : array($id);
	if($defaultvalue) $string .= '<input type="hidden" '.$str.' value="-99">';
	$i = 1;
	foreach($array as $key=>$value) {
		$key = trim($key);
		$checked = ($id && in_array($key, $id)) ? 'checked' : '';
		if($width) $string .= '<label class="ib" style="width:'.$width.'px">';
		$string .= '<input type="checkbox" '.$str.' id="'.$field.'_'.$i.'" '.$checked.' value="'.htmlspecialchars($key).'"> '.htmlspecialchars($value);
		if($width) $string .= '</label>';
		$i++;
	}
	return $string;
}

/**
 * 单选框
 *
 * @param $array 选项 二维数组
 * @param $id 默认选中值
 * @param $str 属性
 */
function radio($array = array(), $id = 0, $str = '', $width = 0, $field = '') {
	$string = '';
	foreach($array as $key=>$value) {
		$checked = trim($id)==trim($key) ? 'checked' : '';
		if($width) $string .= '<label class="ib" style="width:'.$width.'px">';
		$string .= '<input type="radio" '.$str.' id="'.$field.'_'.htmlspecialchars($key).'" '.$checked.' value="'.$key.'"> '.$value;
		if($width) $string .= '</label>';
	}
	return $string;
}

/**
 * 日期时间控件
 *
 * @param $name 控件name，id
 * @param $value 选中值
 * @param $isdatetime 是否显示时间
 * @param $loadjs 是否重复加载js，防止页面程序加载不规则导致的控件无法显示
 * @param $showweek 是否显示周，使用，true | false
 */
function tp_date($name, $value = '', $isdatetime = 0, $loadjs = 0, $showweek = 'true', $timesystem = 1) {
	if($value == '0000-00-00 00:00:00') $value = '';
	$id = preg_match("/\[(.*)\]/", $name, $m) ? $m[1] : $name;
	if($isdatetime) {
		$size = 21;
		$format = '%Y-%m-%d %H:%M:%S';
		if($timesystem){
			$showsTime = 'true';
		} else {
			$showsTime = '12';
		}

	} else {
		$size = 10;
		$format = '%Y-%m-%d';
		$showsTime = 'false';
	}
	$str = '';
	if($loadjs || !defined('CALENDAR_INIT')) {
		define('CALENDAR_INIT', 1);
		$str .= '<link rel="stylesheet" type="text/css" href="/Public/calendar/jscal2.css"/>
		<link rel="stylesheet" type="text/css" href="/Public/calendar/border-radius.css"/>
		<link rel="stylesheet" type="text/css" href="/Public/calendar/win2k.css"/>
		<script type="text/javascript" src="/Public/calendar/calendar.js"></script>
		<script type="text/javascript" src="/Public/calendar/lang/en.js"></script>';
	}
	$str .= '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" size="'.$size.'" class="textinput" readonly>&nbsp;';
	$str .= '<script type="text/javascript">
		Calendar.setup({
		weekNumbers: '.$showweek.',
	    inputField : "'.$id.'",
	    trigger    : "'.$id.'",
	    dateFormat: "'.$format.'",
	    showTime: '.$showsTime.',
	    minuteStep: 1,
	    onSelect   : function() {this.hide();}
		});
    </script>';
	return $str;
}

function get_url_contents($url) {
	if(function_exists('curl_init')){
		$ch = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		$result = curl_exec($ch);
		$aStatus = curl_getinfo($ch);
		if(intval($aStatus["http_code"])==200){
			return $result;
		}else{
			echo "<pre>".$aStatus["http_code"].",请检查参数或者确实是服务器出错咯。</pre>";
			return FALSE;
		}
	}else{
		return file_get_contents($url);
	}
}

/**
 * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 */
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){
	$config = C('THINK_EMAIL');
	vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
	vendor('PHPMailer.class#smtp');
	$mail             = new PHPMailer(); //PHPMailer对象
	$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
	$mail->IsSMTP();  // 设定使用SMTP服务
	$mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
	// 1 = errors and messages
	// 2 = messages only
	$mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
	//$mail->SMTPSecure = 'tls';                 // 使用安全协议
	$mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
	$mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
	$mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
	$mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
	$mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
	$replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
	$replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
	$mail->AddReplyTo($replyEmail, $replyName);
	$mail->Subject    = $subject;
	$mail->MsgHTML($body);
	$mail->AddAddress($to, $name);
	if(is_array($attachment)){ // 添加附件
		foreach ($attachment as $file){
			is_file($file) && $mail->AddAttachment($file);
		}
	}
	$mail->Send();
	var_dump('errorinfoinfoinfoinfo',$mail->ErrorInfo,$config);
	exit;
	return $mail->Send() ? true : $mail->ErrorInfo;
}


function mySort(&$ary, $compareField, $seq = 'DESC', $sortFlag = SORT_NUMERIC) {
	$sortData = array();
	foreach($ary as $key => $value) {
		$sortData[$key] = $value[$compareField];
	}
	($seq == 'DESC') ? arsort($sortData, $sortFlag) : asort($sortData, $sortFlag);
	$ret = array();
	foreach($sortData as $key => $value)
	{
		$ret[$key] = $ary[$key];
	}
	$ary = $ret;
	return $ary;
}

function getRedis($host = 'main'){
	$_config = C('REDIS_CONFIG');
	if (! isset($_config[$host])) {
		return false;
	}

	$link = $_config[$host];

	$redisObj = new redis();
	$redisObj->connect($link['ip'], $link['port'], 1);
	$redisObj->auth($link['auth']);

	return $redisObj;
}

function return_msg($arr){
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($arr);
	exit;
}

function add_push_queue($branch, $title, $msg, $msg_type = 0, $waiter = 0){
	return true;
	$redis = getRedis();
	$qid = upload_file_name();
	$msg['qid'] = $qid;
	$data = json_encode(array('branch' => $branch, 'title'=> $title, 'msg' => $msg, 'msg_type' => $msg_type, 'waiter' => $waiter, 'qid' => $qid));
	$key = 'baidu_push_queue_' . $branch % 10;
	$state = $redis->lPush($key, $data);

	if(! $state){
		wlog("[".date('Y-m-d H:i:s', time())."] 队列错误".var_export(func_get_args(), true), '/data/logs/queue_push_add.log');
	}

	wlog("[".date('Y-m-d H:i:s', time())."] 添加队列 {$branch}||{$qid}||".var_export($msg, true), '/data/logs/queue_push_debug_'.date('Y_m_d', time()).'.log');
}

//模拟数据提交(POST)
function curl_post($url,$post_data){

    if(!empty($post_data)){
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL,$url);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch2, CURLOPT_POST, 1 );
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($post_data))
        );
        $val = curl_exec($ch2);
        curl_close($ch2);
    } else {
        $val = '';
    }
    return $val;
}
function curlPost($url,$fields){
    $ret = false;
    if(!empty($url) || !empty($fields)){
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL,$url);
        curl_setopt($ch2, CURLOPT_POST, 1 );
        curl_setopt($ch2, CURLOPT_TIMEOUT,5);
        curl_setopt($ch2, CURLOPT_POSTFIELDS,http_build_query($fields));
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch2, CURLOPT_HTTPHEADER,'Content-Type: application/x-www-form-urlencoded');
        $val = curl_exec($ch2);
        curl_close($ch2);
        $ret_val = array();
        $ret_val = json_decode($val,true);
    }
    return $ret_val ;
}

//模拟数据提交(POST)
function curl_post_xml($url,$post_data){


    if(!empty($post_data)){
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL,$url);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch2, CURLOPT_POST, 1 );
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        $val = curl_exec($ch2);
        curl_close($ch2);
    } else {
        $val = '';
    }
    return $val;
}



//模拟数据提交(GET)
function curl_get($url){
    $ret_val = array();
    if(!empty($url)){
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $url);
        curl_setopt($ch2, CURLOPT_HEADER, false);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        $val = curl_exec($ch2);
        curl_close($ch2);
    } else {
        $val = '';
    }
    return $val ;
}

function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

/**
 * 获取排序后的分类
 * @param  [type]  $data  [description]
 * @param  integer $pid   [description]
 * @param  string  $html  [description]
 * @param  integer $level [description]
 * @return [type]         [description]
 */
function getSortedCategory($data,$pid=0,$html="|---",$level=0)
{
    $temp = array();
    foreach ($data as $k => $v) {
        if($v['pid'] == $pid){

            $str = str_repeat($html, $level);
            $v['html'] = $str;
            $temp[] = $v;

            $temp = array_merge($temp,getSortedCategory($data,$v['id'],'|---',$level+1));
        }

    }
    return $temp;
}

/**
 * 根据key，返回当前行的所有数据
 * @param  string  $key  字段key
 * @return array         当前行的所有数据
 */
function getSettingValueDataByKey($key)
{
    return M('setting')->getByKey($key);
}

/**
 * 根据key返回field字段
 * @param  string $key   [description]
 * @param  string $field [description]
 * @return string        [description]
 */
function getSettingValueFieldByKey($key,$field)
{
    return M('setting')->getFieldByKey($key,$field);
}


function wlog($msg, $file){

	if (! $file) return false;

    $mkdir = substr($file,0,strrpos($file,'/')+1);
    if( !is_dir($mkdir) )  mkdir($mkdir,0777,true);

	return file_put_contents($file, $msg."\r\n",FILE_APPEND);
}

/**
 *+----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 *+----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 *+----------------------------------------------------------
 * @return string
 *+----------------------------------------------------------
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){

    if(function_exists("mb_substr")){

        if($suffix){

            return mb_substr($str, $start, $length, $charset)."...";

        }else{

            return mb_substr($str, $start, $length, $charset);

        }

    }elseif(function_exists('iconv_substr')) {

        if($suffix){

            return iconv_substr($str,$start,$length,$charset)."...";

        }else{

            return iconv_substr($str,$start,$length,$charset);

        }

    }

    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";

    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";

    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";

    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

    preg_match_all($re[$charset], $str, $match);

    $slice = join("",array_slice($match[0], $start, $length));

    if($suffix){

        return $slice."...";

    }else{

        return $slice;

    }

}