<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<!--[if lt IE 9]>
<script type="text/javascript" src="/Public/admin/lib/html5.js"></script>
<script type="text/javascript" src="/Public/admin/lib/respond.min.js"></script>
<script type="text/javascript" src="/Public/admin/lib/PIE_IE678.js"></script>
<![endif]-->
<link href="/Public/admin/static/h-ui/css/H-ui.min.css" rel="stylesheet" type="text/css" />
<link href="/Public/admin/static/h-ui.admin/css/H-ui.login.css" rel="stylesheet" type="text/css" />
<link href="/Public/admin/static/h-ui.admin/css/style.css" rel="stylesheet" type="text/css" />
<link href="/Public/admin/lib/Hui-iconfont/1.0.7/iconfont.css" rel="stylesheet" type="text/css" />
<!--[if IE 6]>
<script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]-->
<title>睡眠宝 - 后台登录 </title>
</head>
<body>
<input type="hidden" id="TenantId" name="TenantId" value="" />
<div class="header"></div>
<div class="loginWraper">
  <div id="loginform" class="loginBox">
    <form class="form form-horizontal" action="<?php echo U('login/login');?>" method="post">

      <div class="row cl">
        <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe62d;</i></label>
        <div class="formControls col-xs-8" style="margin-top: 10px;">
          <?php if(in_array($roleid,[1,2,3])): ?><span style="font-size: 16px"><input type="hidden" name="roleid" value="<?php echo ($roleid); ?>" /><?php echo C('ROLE_NAME')[$roleid];?></span>
              <?php else: ?>
            <select id="roleid" name="roleid"class="select-box size-L col-xs-8">
              <option value="4">管理员</option>
              <option value="1">区域经理</option>
              <option value="2">代理商</option>
              <option value="3">医生</option>
            </select><?php endif; ?>
        </div>
      </div>
      <div class="row cl">
        <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60d;</i></label>
        <div class="formControls col-xs-8">
          <input id="username" name="username" type="text" placeholder="账户" class="input-text size-L">
        </div>
      </div>
      <div class="row cl">
        <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60e;</i></label>
        <div class="formControls col-xs-8">
          <input id="password" name="password" type="password" placeholder="密码" class="input-text size-L">
        </div>
      </div>
      <div class="row cl">
        <div class="formControls col-xs-8 col-xs-offset-3">
          <input class="input-text size-L" name="verify" type="text" id="exampleInputCode" placeholder="验证码" value="" style="width:150px;">

		  <a href="javascript:void(0)"><img class="verify" src="<?php echo U('login/verify');?>" alt="点击刷新"/>看不清，换一张</a>
			</div>
      </div>
      <div class="row cl">
        <div class="formControls col-xs-8 col-xs-offset-3">
          <label for="online">
            <input type="checkbox" name="online" id="online" value="">
            使我保持登录状态</label>
        </div>
      </div>
      <div class="row cl">
        <div class="formControls col-xs-8 col-xs-offset-3">
          <input name="" type="submit" class="btn btn-success radius size-L" value="&nbsp;登&nbsp;&nbsp;&nbsp;&nbsp;录&nbsp;">
          <input name="" type="reset" class="btn btn-default radius size-L" value="&nbsp;取&nbsp;&nbsp;&nbsp;&nbsp;消&nbsp;">
        </div>
      </div>
    </form>
  </div>
</div>
<div class="footer"><?php echo ($copy); ?></div>
<script type="text/javascript" src="/Public/admin/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/Public/admin/static/h-ui/js/H-ui.js"></script> 
<script>
    $(function(){
        $(".verify").click(function(){
            var src = "<?php echo U('login/verify/',$vars='',$suffix=false);?>";
            var random = Math.floor(Math.random()*(1000+1));
            $(this).attr("src",src+"/random/"+random);

        });
    })
</script>
</body>
</html>