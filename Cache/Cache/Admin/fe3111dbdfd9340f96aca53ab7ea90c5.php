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
<![endif]--><title>订单列表</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 商城管理 <span class="c-gray en">&gt;</span> 订单列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
	<div class="text-c">
		<input type="text" class="input-text" style="width:250px" placeholder="输入订单号" value="<?php echo ($title); ?>" id="title">
		<button type="button" class="btn btn-success" onclick="btn_search()" name=""><i class="Hui-iconfont">&#xe665;</i> 搜订单</button>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="r">共有数据：<strong><?php echo ((isset($count ) && ($count !== ""))?($count ):0); ?></strong> 条</span> </div>
	<table class="table table-border table-bordered table-bg">
		<thead>
			<tr class="text-c">
				<th width="40">ID</th>
				<th width="150">订单号</th>
				<th width="60">购买人ID</th>
				<th width="60">支付方式</th>
				<th width="80">总价</th>
				<th width="80">物流状态</th>
                <th width="120">创建时间</th>
                <th width="120">支付时间</th>
				<th width="100">IP地址</th>
				<th width="60">状态</th>
				<th width="60">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php if(is_array($data)): foreach($data as $key=>$v): ?><tr class="text-c">
				<td><?php echo ($v["id"]); ?></td>
				<td><?php echo ($v["trade_sn"]); ?></td>
				<td><?php echo ($v["userid"]); ?></td>
				<td><?php echo ($v["pay_name"]); ?></td>
				<td><?php echo ($v["payablemoney"]); ?></td>
				<?php $lot = array('0'=>'未发货','1'=>'已发货','2'=>'已签收');?>
				<td><?php echo ($lot[$v['logistics']]); ?></td>
				<td><?php echo (date("Y-m-d H:i:s",$v["ctime"])); ?></td>
                <td><?php if($v['paytime'] == ''): ?>#<?php else: echo (date("Y-m-d H:i:s",$v["paytime"])); endif; ?></td>
                <td><?php echo ($v["ip"]); ?></td>
                <td class="td-status<?php echo ($v["id"]); ?>">
                    <?php if($v["status"] == 'succ'): ?><span class="label label-success radius">已付款</span>
                        <?php elseif($v["status"] == 'timeout'): ?>
                            <span class="label radius">支付超时</span>
                        <?php elseif($v["status"] == 'unpay'): ?>
                            <span class="label radius">未付款</span>
                        <?php elseif($v["status"] == 'calcel'): ?>
                            <span class="label radius">支付取消</span>
                        <?php elseif($v["status"] == 'refund'): ?>
                            <span class="label radius">已退款</span><?php endif; ?>
                </td>
				<td class="td-manage<?php echo ($v["id"]); ?>">
                    <a title="订单详情" href="javascript:;" onclick="admin_edit('订单详情','<?php echo U("Order/detail",array("id"=>$v["id"]));?>','1000','500')" class="ml-5" style="text-decoration:none">
                        <i class="Hui-iconfont">&#xe620;</i>
                    </a>
                </td>
			</tr><?php endforeach; endif; ?>
		</tbody>
	</table>
</div>
<div class="clearfix"></div>
<?php echo ($page); ?>
<script type="text/javascript" src="/Public/admin/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/Public/admin/lib/layer/2.1/layer.js"></script> 
<script type="text/javascript" src="/Public/admin/lib/laypage/1.2/laypage.js"></script> 
<script type="text/javascript" src="/Public/admin/lib/My97DatePicker/WdatePicker.js"></script> 
<script type="text/javascript" src="/Public/admin/static/h-ui/js/H-ui.js"></script> 
<script type="text/javascript" src="/Public/admin/static/h-ui.admin/js/H-ui.admin.js"></script> 
<script type="text/javascript">
function btn_search(){
    var title = $("#title").val();
    window.location.href="<?php echo U('Order/index');?>?title="+title;
}
/*-编辑*/
function admin_edit(title,url,w,h){
    var index = layer.open({
        type: 2,
        title: title,
        content: url
    });
    layer.full(index);
}

</script>

<!-- JavaScript -->
</body>
</html>