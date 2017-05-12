<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
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
<![endif]--><title>订单详情</title>
</head>
<body>
<article class="page-container">
	<form class="form form-horizontal" id="form-article-add" action="<?php echo U('order/edit');?>" method="post">
        <div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">订单号：</label>
			<div class="formControls col-xs-8 col-sm-3">
                <?php echo ($data["trade_sn"]); ?>
			</div>
			<label class="form-label col-xs-4 col-sm-2">交易单号：</label>
			<div class="formControls col-xs-8 col-sm-3">
                <?php echo ($data["transaction_id"]); ?>
			</div>

		</div>
        <div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">用户名：</label>
			<div class="formControls col-xs-8 col-sm-3">
                <?php echo ($data["username"]); ?>
			</div>
			<label class="form-label col-xs-4 col-sm-2">用户手机号：</label>
			<div class="formControls col-xs-8 col-sm-3">
                <?php echo ($data["phone"]); ?>
			</div>
		</div>
        <div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">商品总数：</label>
			<div class="formControls col-xs-8 col-sm-3">
                <?php echo ($data["num"]); ?>
			</div>
			<label class="form-label col-xs-4 col-sm-2">订单总价：</label>
			<div class="formControls col-xs-8 col-sm-3">
                <?php echo ($data["payablemoney"]); ?>
			</div>
		</div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">订单状态：</label>
            <?php $status = array('succ'=>'支付成功','unpay' =>'等待支付','calcel'=>'支付取消','timeout'=>'支付超时','refund'=>'已退款');?>
            <div class="formControls col-xs-8 col-sm-3">
                <?php echo ($status[$data['status']]); ?>
            </div>
			<label class="form-label col-xs-4 col-sm-2">支付时间：</label>
			<div class="formControls col-xs-8 col-sm-3">
                <?php if(($data['status'] == 'succ')or ($data['status'] == 'refund')): echo (date("Y-m-d H:i:s",$data["paytime"])); endif; ?>
			</div>
		</div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">收货人：</label>
            <div class="formControls col-xs-8 col-sm-3">
                <?php echo ($data["user_name"]); ?>
            </div>
            <label class="form-label col-xs-4 col-sm-2">收货人手机号：</label>
            <div class="formControls col-xs-8 col-sm-3">
                <?php echo ($data["mobile"]); ?>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">收货地址：</label>
            <div class="formControls col-xs-8 col-sm-6">
                <?php echo ($data["address"]); ?>
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">物流状态：</label>
            <?php $lot = array('0'=>'未发货','1'=>'已发货','2'=>'已签收');?>
            <div class="formControls col-xs-8 col-sm-3">
                <?php echo ($lot[$data['logistics']]); ?>
            </div>
            <label class="form-label col-xs-4 col-sm-2">物流单号：</label>
            <div class="formControls col-xs-8 col-sm-3">
                <?php echo ($data["logistics_sn"]); ?>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">订单创建时间：</label>
            <div class="formControls col-xs-8 col-sm-3">
                <?php echo (date("Y-m-d H:i:s",$data["ctime"])); ?>
            </div>
            <label class="form-label col-xs-4 col-sm-2">ip地址：</label>
            <div class="formControls col-xs-8 col-sm-3">
                <?php echo ($data["ip"]); ?>
            </div>
        </div>
		<p class="mt-10">
			<input class="btn btn-block btn-primary radius size-MINI" value="" type="button">
		</p>
        <?php if(is_array($detail)): $key = 0; $__LIST__ = $detail;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$de): $mod = ($key % 2 );++$key;?><div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><h3><span class="label label-danger radius">商品<?php echo ($key); ?></span> </h3></label>
            <label class="form-label col-xs-4 col-sm-4"></label>
            <div class="formControls col-xs-8 col-sm-3">
                <img src="<?php echo ($de["thumb_img"]); ?>" width="80px"/>
            </div>
        </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">商品名：</label>
                <div class="formControls col-xs-8 col-sm-3">
                    <?php echo ($de["title"]); ?>
                </div>
                <label class="form-label col-xs-4 col-sm-2">商品ID：</label>
                <div class="formControls col-xs-8 col-sm-3">
                    <?php echo ($de["id"]); ?>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">数量：</label>
                <div class="formControls col-xs-8 col-sm-3">
                    <?php echo ($de["num"]); ?>
                </div>
                <label class="form-label col-xs-4 col-sm-2">单价：</label>
                <div class="formControls col-xs-8 col-sm-3">
                    <?php echo ($de["money"]); ?>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">商品简介：</label>
                <div class="formControls col-xs-8 col-sm-8">
                    <?php echo ($de["intro"]); ?>
                </div>
            </div>
            <div class="progress" style="width: 100%"><div class="progress-bar"><span class="sr-only" style="width:100%"></span></div></div><?php endforeach; endif; else: echo "" ;endif; ?>
        <?php if($data['logistics_sn'] == ''): ?><div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>选择快递公司：</label>
            <div class="formControls col-xs-8 col-sm-8">
                <select class="select" name="logistics_code">
                    <?php if(is_array($logistics)): $i = 0; $__LIST__ = $logistics;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$log): $mod = ($i % 2 );++$i;?><option value="<?php echo ($log["code"]); ?>"><?php echo ($log["logistics"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>添加物流单号：</label>
            <div class="formControls col-xs-8 col-sm-8">
                <input type="text" class="input-text" value="" placeholder="" name="logistics_sn">
            </div>
        </div><?php endif; ?>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">备注：</label>
			<div class="formControls col-xs-8 col-sm-8">
				<textarea class="textarea" placeholder="填写商品备注..." rows=""  id="remark" cols="" name="remark"><?php echo ($data["remark"]); ?></textarea>
			</div>
		</div>
		<input type="hidden" name="trade_sn" value="<?php echo ($data["trade_sn"]); ?>" />
		<div class="row cl">
			<div class="col-xs-8 col-sm-8 col-xs-offset-4 col-sm-offset-3">
				<button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 提交</button>
				<button onClick="removeIframe();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
			</div>
		</div>
	</form>
</article>

<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/Public/admin/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/Public/admin/lib/layer/2.1/layer.js"></script>
<script type="text/javascript" src="/Public/admin/lib/icheck/jquery.icheck.min.js"></script>
<script type="text/javascript" src="/Public/admin/lib/jquery.validation/1.14.0/jquery.validate.min.js"></script>
<script type="text/javascript" src="/Public/admin/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/Public/admin/lib/jquery.validation/1.14.0/messages_zh.min.js"></script>
<script type="text/javascript" src="/Public/admin/static/h-ui/js/H-ui.js"></script>
<script type="text/javascript" src="/Public/admin/static/h-ui.admin/js/H-ui.admin.js"></script>
<!--/_footer /作为公共模版分离出去-->

<script type="text/javascript">

$(function(){
	$('.skin-minimal input').iCheck({
		checkboxClass: 'icheckbox-blue',
		radioClass: 'iradio-blue',
		increaseArea: '20%'
	});
    $("#form-article-add").validate({
        onkeyup:false,
        focusCleanup:true,
        success:"valid",
        submitHandler:function(form){
            $(form).ajaxSubmit({
                success:function(data){
					layer.msg(data.info, {icon:6,time:1000});
                }
            });
        }
    });

});
</script>
<!--/请在上方写此页面业务相关的脚本-->

<!-- JavaScript -->
</body>
</html>