<?php
namespace Admin\Model;
use Think\Model;
class UserModel extends Model{
    protected $_validate = array(
        array('mobile','require','请填写手机号！'), //默认情况下用正则进行验证
        array('username','require','请填写用户名！'),
        array('sn','require','请填写呼吸机SN号！','','',1),
        array('password','require','请填写密码！','','',1), //默认情况下用正则进行验证
     	array('repassword','password','确认密码不正确',0,'confirm',1), // 验证确认密码是否和密码一致
        array('mobile','','手机号已存在！',0,'unique',3), // 在新增的时候验证name字段是否唯一
        array('status',array(0,1),'请勿恶意修改字段',3,'in'), // 当值不为空的时候判断是否在一个范围内
        array('gender',array('男','女','未知'),'请勿恶意修改字段',3,'in'), // 当值不为空的时候判断是否在一个范围内

    );

    protected $_auto = array(
    	array('password','md5',1,'function') , //添加时用md5函数处理
      //  array('password','',2,'ignore')   //怎么不能用？
    );


}