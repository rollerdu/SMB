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
</head>

<body>
    <div class="container info">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th colspan="2">
                        <div>SmartCode Report <br /><span class="time"> <?php echo ($data["ctime"]); ?></span></div>
                        <div><?php echo ($data["smartcode"]); ?><br /> SleepCube <?php echo ($data["titrationmode"]); ?></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="header">
                    <th></th>
                    <td class="last-day">Last Day</td>
                </tr>
                <tr>
                    <th>% Days of at least 4 hours(no contiinuous)<br /> 累计使用超过4个小时的天数</th>
                    <td class="least_4_hours"><?php echo ($data["percentdaysatleastxhours"]); ?></td>
                </tr>
                <tr>
                    <th>Day Count <br />总计天数</th>
                    <td class="all_day"><?php echo ($data["daycount"]); ?></td>
                </tr>
                <tr>
                    <th>Days at least 4 hours<br />使用超过4个小时的天数</th>
                    <td class="exceed_4_hours"><?php echo ($data["daysatleastxhours"]); ?></td>
                </tr>
                <tr>
                    <th>95th Percentile Pressure<br />95%的压力</th>
                    <td class="95th_pressure"><?php echo ($data["percentilepressure95th"]); ?></td>
                </tr>
                <tr>
                    <th>90th Percentile Pressure<br />90%的压力</th>
                    <td class="90th_pressure"><?php echo ($data["percentilepressure90th"]); ?></td>
                </tr>
                <tr>
                    <th>AHI<br />呼吸絮乱指数</th>
                    <td class="ahl"><?php echo ($data["ahi"]); ?></td>
                </tr>
                <tr>
                    <th>Pressure Plateau Time<br />达到压力最大值的时间占总使用时间的百分比</th>
                    <td class="plateau_time"><?php echo ($data["pressureplateautime"]); ?></td>
                </tr>
                <tr>
                    <th>Hight Leak Flow Time<br />高漏气时间</th>
                    <td class="flow_time"><?php echo ($data["leaktime"]); ?></td>
                </tr>
                <tr>
                    <th>NRI<br />未反应指数</th>
                    <td class="nrl"><?php echo ($data["noi"]); ?></td>
                </tr>
                <tr>
                    <th>EPI<br />张口呼吸指数</th>
                    <td class="epl"><?php echo ($data["epi"]); ?></td>
                </tr>
                <tr>
                    <th>SmartCode<br />智能编码</th>
                    <td class="smart_code"><?php echo ($data["smartcode"]); ?></td>
                </tr>
                <tr>
                    <th>While Breathing Hours<br />有效治疗时间</th>
                    <td class="breathing_hours"><?php echo ($data["whilebreathinghours"]); ?></td>
                </tr>
                <tr class="last-two">
                    <th>Titration Mode<br />治疗模式</th>
                    <td class="titration_mode"><?php echo ($data["titrationmode"]); ?></td>
                </tr>
                <tr>
                    <th>Minimum Usage Threshold<br />最少使用时间值</th>
                    <td class="mini_time"><?php echo ($data["minimumusethreshold"]); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>