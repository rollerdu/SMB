<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en-US">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,minimal-ui">
    <title>注册</title>
    <link href="/Public/weixin/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/Public/weixin/css/common.css">
</head>

<body>
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand back" onclick="history.back(-1)"></a>
                <a class="navbar-brand title">注册</a>
                <a class="navbar-brand home" href="<?php echo U('Weixin/Index/index');?>"></a>
            </div>
        </div>
    </nav>
    <div class="container login">
        <div class="alert alert-warning" role="alert"></div>
        <form>
            <div class="form-group">
                <input type="text" class="form-control username" placeholder="手机号码">
            </div>
            <div class="form-group">
                <input type="password" class="form-control password" placeholder="密码">
            </div>
            <div class="form-group">
                <input type="text" class="form-control numb" placeholder="呼吸机SN号">
            </div>
            <a type="button" id="button" class="btn btn-block login-btn">立即注册</a>
        </form>
    </div>
</body>
<script src="/Public/weixin/js/jquery-2.1.1.min.js" charset="utf-8"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var isCellphone = function(str) {
            var pattern = /^(13[0-9]|14[57]|15[012356789]|17[0678]|18[0-9])\d{8}$/;
            return pattern.test(str);
        }
        $('.numb').blur(function() {
            if ($(this).val() == '') {
                alert($(this).val());
                $('.alert').html('请输入呼吸机SN号').show(100)
                setTimeout(function() {
                    $('.alert').hide(100)
                }, 1500)
            }
        });
        $('.username').blur(function() {
            if ($(this).val() == '' || !isCellphone($('.username').val())) {
                $('.alert').html('请输入正确的手机号码').show(100)
                setTimeout(function() {
                    $('.alert').hide(100)
                }, 1500)
            }
        });

        $('#button').click(function(event) {
            if (isCellphone($('.username').val()) && $('.username').val() != '' && $('.password').val() != '' && $(".numb").val() != '') {
                $.ajax({
                    url: "<?php echo U('/Weixin/Login/register');?>",
                    type: 'POST',
                    data: {
                        mobile: $('.username').val(),
                        password: $('.password').val(),
                        sn      : $(".numb").val()
                    },
                    success: function(data) {
                        if (data.status == 0) {
                            $('.alert').html(data.info).show(100)
                            setTimeout(function() {
                                $('.alert').hide(100);
                            }, 1000)
                        } else {
                            $('.alert').html(data.info).show(100)
                            setTimeout(
                                "window.location.href='/Weixin/Login/login';"
                            , 1000)
                        }
                    }
                })
            }else{
                $('.alert').html('请填全信息').show(100)
                setTimeout(function() {
                    $('.alert').hide(100)
                }, 1500)
            }
        });
    });
</script>
</html>