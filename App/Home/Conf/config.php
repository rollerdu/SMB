<?php
return array (


//'APP_GROUP_LIST' => 'Admin,Home,Coupon', //项目分组设定
//'DEFAULT_GROUP'  => 'Home', //默认分组
//
//'MODULE_ALLOW_LIST' => array('Home','Admin','Coupon'),
//'DEFAULT_MODULE' => 'Home',


'TMPL_TEMPLATE_SUFFIX' => ".html",
'TMPL_ACTION_ERROR' => "Public:error",
'TMPL_ACTION_SUCCESS' => "Public:success",

//'SHOW_ERROR_MSG' => true,   //// 显示错误信息
//'SHOW_PAGE_TRACE'=> true,
//'APP_DEBUG' => true,
//'TMPL_CACHE_ON' =>  false,

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
    'CITY' => 'city',

),


'COOKIE_PREFIX'=>"FLBK_",
'ERROR_404'=>'页面跑了,返回首页',

);
