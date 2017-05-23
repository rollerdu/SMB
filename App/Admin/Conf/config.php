cd<?php
return array (
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016/8/8
 * Time: 15:44
 */


//'URL_HTML_SUFFIX' => "",
//'URL_MODEL' =>0,


//图片配置
'UploadImgConfig'=>array(

    'maxSize'    =>  3145728,
    'rootPath'   =>  UPLOAD_PATH,
    'subName'       =>  array('mkdir_by_date', time()), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
    'hash'        =>  true, //是否生成hash编码
    'savePath'   =>    '',  //业务/用户id/年月/日/图片名
    'exts'        =>  array('jpg', 'gif', 'png', 'jpeg'),

    'width'      =>'180',
    'height'     =>'120',

    'width_v'      =>'350',
    'height_v'     =>'220',
),






);