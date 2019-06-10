<?php
session_start();
if(isset($_SESSION['uid'])){
    $uid = $_SESSION['uid'];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>sshop</title>

    <!-- Bootstrap core CSS -->
    <link href="/include/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/include/assets/css/jumbotron-narrow.css" rel="stylesheet">
</head>

<body>

<div class="container">
    <div class="header clearfix">
        <nav>
            <ul class="nav nav-pills pull-right">
                <li role="presentation"><a href="/shop">商品列表</a></li>
<?php
if(isset($_SESSION['uid'])){
    $uid = $_SESSION['uid'];
    if($uid == 1){
        echo '<li role="presentation"><a href="/admin/action.php">管理员中心</a></li>';
    }
    echo <<<Start
                <li role="presentation"><a href="/user">个人中心</a></li>
                <li role="presentation"><a href="/seckill">！秒杀活动！</a></li>
                <li role="presentation"><a href="/shopcar">购物车</a></li>
                <li role="presentation"><a href="/user/change">修改密码</a></li>
                <li role="presentation"><a href="/logout">注销</a></li>
Start;
}
else{
    echo <<<login
                <li role="presentation"><a href="/login/">登录</a></li>
                <li role="presentation"><a href="/register/">注册</a></li>
login;
}
?>
            </ul>
        </nav>
        <h3 class="text-muted">sshop</h3>
    </div>