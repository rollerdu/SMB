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
<link rel="stylesheet" type="text/css" href="/Public/admin/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="/Public/admin/static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/Public/admin/static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="/Public/admin/lib/Hui-iconfont/1.0.7/iconfont.css" />
<link rel="stylesheet" type="text/css" href="/Public/admin/lib/icheck/icheck.css" />
<link rel="stylesheet" type="text/css" href="/Public/admin/lib/icheck/jquery.icheck.min.js" />
<link rel="stylesheet" type="text/css" href="/Public/admin/static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="/Public/admin/static/h-ui.admin/css/style.css" />
<!--[if IE 6]>
<script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]--><title>睡眠宝后台管理</title>
</head>
<body>
<header class="navbar-wrapper">
	<div class="navbar navbar-fixed-top">
		<div class="container-fluid cl"><img src="/Public/admin/images/logo.jpg" class="logo navbar-logo f-l" /> <a class="logo navbar-logo f-l mr-10 hidden-xs" href="<?php echo U(Index/index);?>">睡眠宝后台管理</a><span class="logo navbar-slogan f-l mr-10 hidden-xs">v 1.0</span> <a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
			<nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
				<ul class="cl">
					<li><?php echo ($user["role_name"]); ?></li>
					<li class="dropDown dropDown_hover"> <a href="#" class="dropDown_A"><?php echo ((isset($user["truename"]) && ($user["truename"] !== ""))?($user["truename"]):$user['name']); ?> <i class="Hui-iconfont">&#xe6d5;</i></a>
						<ul class="dropDown-menu menu radius box-shadow">
							<li><a href="<?php echo U('Login/logout');?>">退出</a></li>
						</ul>
					</li>
					<!--<li id="Hui-msg"> <a href="#" title="消息"><span class="badge badge-danger">1</span><i class="Hui-iconfont" style="font-size:18px">&#xe68a;</i></a> </li>-->
					<li id="Hui-skin" class="dropDown right dropDown_hover"> <a href="javascript:;" class="dropDown_A" title="换肤"><i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i></a>
						<ul class="dropDown-menu menu radius box-shadow">
							<li><a href="javascript:;" data-val="default" title="默认（黑色）">默认（黑色）</a></li>
							<li><a href="javascript:;" data-val="blue" title="蓝色">蓝色</a></li>
							<li><a href="javascript:;" data-val="green" title="绿色">绿色</a></li>
							<li><a href="javascript:;" data-val="red" title="红色">红色</a></li>
							<li><a href="javascript:;" data-val="yellow" title="黄色">黄色</a></li>
							<li><a href="javascript:;" data-val="orange" title="绿色">橙色</a></li>
						</ul>
					</li>
				</ul>
			</nav>
		</div>
	</div>
</header>
<aside class="Hui-aside">
	<input runat="server" id="divScrollValue" type="hidden" value="" />
	<div class="menu_dropdown bk_2">
		<dl>
			<dt><i class="Hui-iconfont">&#xe705;</i> 个人资料<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a _href="<?php echo U('My/index');?>" data-title="我的信息" href="javascript:void(0)">我的信息</a></li>
					<li><a _href="<?php echo U('My/edit_password');?>" data-title="修改密码" href="javascript:void(0)">修改密码</a></li>
				</ul>
			</dd>
		</dl>
		<?php if(is_array($menu)): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dl>
			<dt><i class="Hui-iconfont <?php echo ($vo["icon"]); ?>"></i> <?php echo ($vo["name"]); ?><i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<?php if(is_array($vo['items'])): $i = 0; $__LIST__ = $vo['items'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$it): $mod = ($i % 2 );++$i;?><li><a _href="<?php echo ($it["url"]); ?>" data-title="<?php echo ($it["name"]); ?>" href="javascript:void(0)"><?php echo ($it["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</dd>
		</dl><?php endforeach; endif; else: echo "" ;endif; ?>
		<!--<dl id="menu-picture">-->
			<!--<dt><i class="Hui-iconfont">&#xe613;</i> 应用管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>-->
			<!--<dd>-->
				<!--<ul>-->
					<!--<li><a _href="<?php echo U('Article/index');?>" data-title="文章列表" href="javascript:void(0)">文章列表</a></li>-->
					<!--<li><a _href="<?php echo U('Category/index');?>" data-title="文章栏目" href="javascript:void(0)">文章栏目</a></li>-->
					<!--<li><a _href="<?php echo U('Message/index');?>" data-title="留言列表" href="javascript:void(0)">留言列表</a></li>-->
				<!--</ul>-->
			<!--</dd>-->
		<!--</dl>-->
		<!--<dl id="menu-admin">-->
			<!--<dt><i class="Hui-iconfont">&#xe62d;</i> 管理员管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>-->
			<!--<dd>-->
				<!--<ul>-->
					<!--<li><a _href="<?php echo U('Role/index');?>" data-title="角色管理" href="javascript:void(0)">角色管理</a></li>-->
					<!--<li><a _href="<?php echo U('Member/index');?>" data-title="管理员列表" href="javascript:void(0)">管理员列表</a></li>-->
				<!--</ul>-->
			<!--</dd>-->
		<!--</dl>-->
		<!--<dl id="menu-tongji">-->
			<!--<dt><i class="Hui-iconfont">&#xe61a;</i> 系统统计<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>-->
			<!--<dd>-->
				<!--<ul>-->
					<!--<li><a _href="<?php echo U('Highcharts/lineChart');?>" data-title="折线图" href="javascript:void(0)">折线图</a></li>-->
					<!--<li><a _href="<?php echo U('Highcharts/timeChart');?>" data-title="时间轴折线图" href="javascript:void(0)">时间轴折线图</a></li>-->
					<!--<li><a _href="<?php echo U('Highcharts/regionalMap');?>" data-title="区域图" href="javascript:void(0)">区域图</a></li>-->
					<!--<li><a _href="<?php echo U('Highcharts/histogram');?>" data-title="柱状图" href="javascript:void(0)">柱状图</a></li>-->
					<!--<li><a _href="<?php echo U('Highcharts/pieChart');?>" data-title="饼状图" href="javascript:void(0)">饼状图</a></li>-->
					<!--<li><a _href="<?php echo U('Highcharts/histogram3D');?>" data-title="3D柱状图" href="javascript:void(0)">3D柱状图</a></li>-->
					<!--<li><a _href="<?php echo U('Highcharts/pieChart3D');?>" data-title="3D饼状图" href="javascript:void(0)">3D饼状图</a></li>-->
				<!--</ul>-->
			<!--</dd>-->
		<!--</dl>-->
		<!--<dl id="menu-system">-->
			<!--<dt><i class="Hui-iconfont">&#xe62e;</i> 系统管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>-->
			<!--<dd>-->
				<!--<ul>-->
					<!--<li><a _href="<?php echo U('System/index');?>" data-title="系统设置" href="javascript:void(0)">系统设置</a></li>-->
					<!--<li><a _href="<?php echo U('Menu/index');?>" data-title="菜单管理" href="javascript:void(0)">菜单管理</a></li>-->
					<!--<li><a _href="<?php echo U('System/keyword');?>" data-title="屏蔽词" href="javascript:void(0)">屏蔽词</a></li>-->
					<!--<li><a _href="<?php echo U('System/log');?>" data-title="系统日志" href="javascript:void(0)">系统日志</a></li>-->
				<!--</ul>-->
			<!--</dd>-->
		<!--</dl>-->
		<!--<dl id="menu-award">-->
			<!--<dt><i class="Hui-iconfont">&#xe6bb;</i> 抽奖管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>-->
			<!--<dd>-->
				<!--<ul>-->
					<!--<li><a _href="<?php echo U('Award/setting');?>" data-title="抽奖设置" href="javascript:void(0)">抽奖设置</a></li>-->
					<!--<li><a _href="<?php echo U('Award/index');?>" data-title="中奖名单" href="javascript:void(0)">中奖名单</a></li>-->
				<!--</ul>-->
			<!--</dd>-->
		<!--</dl>-->
	</div>
</aside>
<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
<section class="Hui-article-box">
	<div id="Hui-tabNav" class="Hui-tabNav hidden-xs">
		<div class="Hui-tabNav-wp">
			<ul id="min_title_list" class="acrossTab cl">
				<li class="active"><span title="我的桌面" data-href="welcome.html">我的桌面</span><em></em></li>
			</ul>
		</div>
		<div class="Hui-tabNav-more btn-group"><a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d4;</i></a><a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d7;</i></a></div>
	</div>
	<div id="iframe_box" class="Hui-article">
		<div class="show_iframe">
			<div style="display:none" class="loading"></div>
			<iframe scrolling="yes" frameborder="0" src="<?php echo U('/Admin/Index/welcome');?>"></iframe>
		</div>
	</div>
</section>
<script type="text/javascript" src="/Public/admin/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/Public/admin/lib/layer/2.1/layer.js"></script> 
<script type="text/javascript" src="/Public/admin/static/h-ui/js/H-ui.js"></script> 
<script type="text/javascript" src="/Public/admin/static/h-ui.admin/js/H-ui.admin.js"></script> 
<script type="text/javascript">
/*资讯-添加*/
function article_add(title,url){
	var index = layer.open({
		type: 2,
		title: title,
		content: url
	});
	layer.full(index);
}
/*图片-添加*/
function picture_add(title,url){
	var index = layer.open({
		type: 2,
		title: title,
		content: url
	});
	layer.full(index);
}
/*产品-添加*/
function product_add(title,url){
	var index = layer.open({
		type: 2,
		title: title,
		content: url
	});
	layer.full(index);
}
/*用户-添加*/
function member_add(title,url,w,h){
	layer_show(title,url,w,h);
}
</script> 
<script type="text/javascript">

</script>
</body>
</html>