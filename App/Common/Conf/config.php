<?php

//微信授权接口
define('WX_OAUTH2_ACCESSTOKEN_URL',"https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code");

return array (
'DB_CHARSET' => "utf8",
'DB_TYPE' => "mysql",
'DB_HOST' => '127.0.0.1',
'DB_PORT' => '3306',
'DB_NAME' => 'cloudsystem',
'DB_USER' => 'root',
'DB_PWD' => '123456',
'DB_PREFIX' => 'j_',

//    'DB_CHARSET' => "utf8",
//    'DB_TYPE' => "mysql",
//    'DB_HOST' => '127.0.0.1',
//    'DB_PORT' => '3306',
//    'DB_NAME' => 'cloudsystem',
//    'DB_USER' => 'cloud',
//    'DB_PWD' => '123456',
//    'DB_PREFIX' => 'j_',



'APP_GROUP_LIST' => 'Admin,Home,Weixin', //项目分组设定
'DEFAULT_GROUP'  => 'Admin', //默认分组

'MODULE_ALLOW_LIST' => array('Home','Admin','Weixin'),
'DEFAULT_MODULE' => 'Admin',
'DEFAULT_CONTROLLER'    =>  'Login', // 默认控制器名称
'DEFAULT_ACTION'        =>  'index', // 默认操作名称


//二级域名配置
//'APP_SUB_DOMAIN_DEPLOY' => 1,
//'APP_DOMAIN_SUFFIX'=>'com.cn',
/*'APP_SUB_DOMAIN_RULES' => array(
		'www' => array('Home/'),
		'admin' => array('Admin/'),
		'manage' => array('Manage/'),
		'custom' => array('Custom/'),
		'my' => array('My/'),
	),
*/

'TMPL_TEMPLATE_SUFFIX' => ".html",
'TMPL_ACTION_ERROR' => "Public:error",
'TMPL_ACTION_SUCCESS' => "Public:success",

'SHOW_ERROR_MSG' => true,   //// 显示错误信息
'SHOW_PAGE_TRACE'=> false,
'APP_DEBUG' => true,
'TMPL_CACHE_ON' =>  false,




//开启缓存
//'HTML_CACHE_ON'     =>    true, // 开启静态缓存
//'HTML_CACHE_TIME'   =>    3600,   // 全局静态缓存有效期（秒）
//'HTML_FILE_SUFFIX'  =>    '.html', // 设置静态缓存文件后缀
//'HTML_CACHE_RULES'  =>     array(  // 定义静态缓存规则
// // 定义格式1 数组方式
// 'Weather:city_opt'    =>   array('Weather/city_opt','3600'),
//  ),


'URL_MODEL' => 2,   //// URL访问模式,可选参数0、1、2、3,代表以下四种模式：// 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
'URL_CASE_INSENSITIVE' => true, // 默认false 表示URL区分大小写 true则表示不区分大小写
'URL_HTML_SUFFIX' => ".html",
'URL_404_REDIRECT' => "/404.html",

'TOKEN_ON' => FALSE,

'LOAD_EXT_CONFIG' => array(
    //'CITY' => 'city',

),
    //支付宝合作者身份ID
    'ALIPAY_PARTNER' => '2088121483432802',
    'ADMIN_TABLE' => array(
        '1' => 'Manager',
        '2' => 'Agent',
        '3' => 'Doctor',
        '4' => 'Admin'
    ),
    'ROLE_NAME' => array(
      '1'   => '区域经理',
      '2'   => '代理商',
      '3'   => '医生',
      '4'   => '超级管理员',
    ),
    'PRODUCT_CATEGORY' => array(
        '1' => '设备',
        '2' => '配件',
        '3' => '胸带服务',
        '4' => '其他服务'
    ),
    'ESS_QUESTIONS' => array(
        '1' =>'坐着看电视时',
        '2' =>'坐着阅读书刊、报纸时',
        '3' =>'在会议中',
        '4' =>'看电影或听讲座时',
        '5' =>'搭乘交通工具',
        '6' =>'坐着和别人交谈时',
        '7' =>'驾驶时或停车等候中',
        '8' =>'餐后坐下休息时',
    ),
    'ESS_ANSWER' => array(
      '0'   => '从未发生',
      '1'   => '偶尔发生',
      '2'   => '发生较多',
      '3'   => '频繁发生',
    ),
    //未支付订单有效时间
    'ORDER_EFFECTIVE_TIME' => 45*60,
    //默认胸带使用次数为10次
    'DEFAULT_GIRDLE_TIMES' => 10,
    'COOKIE_PREFIX'=>"FLBK_",
    'ERROR_404'=>'页面跑了,返回首页',

);
