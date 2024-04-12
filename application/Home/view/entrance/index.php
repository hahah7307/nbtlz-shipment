<!DOCTYPE html>
<html>
<style>
    body {background-image: url(/static/images/mu-market.jpg); background-repeat: no-repeat; background-size: cover;}
    .main-container {display: flex; justify-content: flex-end; padding: 200px 300px}
    .main-container li {line-height: 72px; font-size: 32px}
</style>
<head>
    <meta charset="utf-8">
    <title>网站入口</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/layuiadmin/style/admin.css" media="all">
    <link rel="stylesheet" href="/static/layuiadmin/style/login.css" media="all">
    <link rel="stylesheet" href="/static/manage/css/manage.css" media="all">
</head>
<body>

<div class="main-container">
    <div class="main-list">
        <ul>
            <li>网站入口：</li>
            <li><a href="http://139.224.106.228/Manage/index/index.html" target="_blank">数据统计系统</a></li>
            <li><a href="http://47.100.200.11/Manage/Index/index.html" target="_blank">运营管理系统</a></li>
            <li><a href="http://47.101.47.220/Manage/Index/index.html" target="_blank">采购跟单系统</a></li>
            <li><a href="http://139.196.102.61/Manage/Index/index.html" target="_blank">文件管理系统</a></li>
        </ul>
    </div>
</div>
<script src="/static/layuiadmin/layui/layui.js"></script>
<script src="/static/js/axios.min.js"></script>
<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'user'], function(){

    });
</script>
</body>
</html>