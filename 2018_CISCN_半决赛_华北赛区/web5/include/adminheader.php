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
<?php
if(isset($_SESSION['uid'])){
    $uid = $_SESSION['uid'];
    if($uid == 1){
        echo '<li role="presentation"><a href="/admin/action.php">管理员中心</a></li>';
    }
    echo <<<Start
                <li role="presentation"><a href="/admin/add.php">商品添加</a></li>
                <li role="presentation"><a href="/admin/upload.php">商品图片上传</a></li>
Start;
}
?>
            </ul>
        </nav>
        <h3 class="text-muted">sshop</h3>
    </div>