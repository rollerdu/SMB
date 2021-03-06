<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>loading</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,minimal-ui">
    <link rel="stylesheet" href="/Public/weixin/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Public/weixin/css/common.css">
    <script src="/Public/weixin/js/jquery-2.1.1.min.js" charset="utf-8"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.loading-wrap').height($(window).height());
            var time = 3;// 倒计时时间控制
            var href = document.getElementById('href').href;
            $('time').text(time);
            var timer = window.setInterval(function() {
                time--;
                $('time').text(time);
                if (time === 0) {
                    window.location.href = href;
                }
            }, 1000);
        });
    </script>
</head>

<body>
<div class="loading-wrap bg-info">
    <div class="loading panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo (nl2br(htmlspecialchars($error))); ?></h3>
        </div>
        <div class="panel-body text-primary">
            <div>页面自动<a id="href" href="<?php echo ($jumpUrl); ?>">跳转</a></div><span class="waiting">等待<time></time>秒</span>
        </div>
    </div>
</div>
</body>

</html>