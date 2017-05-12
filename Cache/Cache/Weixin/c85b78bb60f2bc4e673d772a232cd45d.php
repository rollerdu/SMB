<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en-US">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,minimal-ui">
    <title>呼吸报告</title>
    <link href="/Public/weixin/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/Public/weixin/css/common.css">
    <script src="/Public/weixin/js/jquery-2.1.1.min.js" charset="utf-8"></script>
    <script charset="utf-8">
        var page = 2;
        var issend = false;
        $(document).ready(function() {
            $(window).scroll(function(){
                if(page == 0) return false;
                var scrollTop = $(document).scrollTop();
                if(scrollTop >= $(document).height() - $(window).height()){
                    addMore();
                }
            });
            function addMore(){
                if(issend) {
                    return false;
                }
                issend = true;
                $.ajax({
                    url: "<?php echo U('Weixin/Index/index');?>",
                    data:{page:page},
                    success: function(data) {
                        if (data.code == 0) {
                            var str = '';
//                            var tail = data.data;
//                            for(var i=0;i< tail.length; i++){
//                                str += "<tr onclick='viewDetail("+tail[i].id+")'><th>"+tail[i].smartcode+"</th><td class='time'>"+tail[i].ctime+"</td><td class='module'>"+tail[i].titrationmode+"</td></tr>";
//                            }
                            $(data.data).each(function(index, el) {
                                str += "<tr onclick='viewDetail("+el.id+")'><th>"+el.smartcode+"</th><td class='time'>"+el.ctime+"</td><td class='module'>"+el.titrationmode+"</td></tr>";
                            });
                            $('#bottom').before(str);
                            str = '';
                            page ++;
                        }else{
                            page = 0;
                        }
                        issend = false;
                    }
                })
            }
        });
        function viewDetail(id){
            window.location.href="<?php echo U('/Weixin/Index/smInfo');?>?id="+id;
        }
    </script>
</head>

<body>
    <div class="container wrap">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        smartcode
                    </th>
                    <th>
                        时间
                    </th>
                    <th>
                        治疗模式
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php if($data != null): if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$da): $mod = ($i % 2 );++$i;?><tr onclick="viewDetail('<?php echo ($da["id"]); ?>')"><th><?php echo ($da["smartcode"]); ?></th><td class="time"><?php echo ($da["ctime"]); ?></td><td class="module"><?php echo ($da["titrationmode"]); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    <?php else: ?>
                    <tr><th></th><td>暂无数据</td><td></td></tr><?php endif; ?>
            <tr id="bottom"></tr>
            </tbody>
        </table>
    </div>
</body>

</html>