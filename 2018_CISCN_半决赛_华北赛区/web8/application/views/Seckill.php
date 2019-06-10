<html lang="zh-CN"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>sshop</title>
    <!-- Bootstrap core CSS -->
    <link href="/static/css/bootstrap.min.css?v=ec3bb52a00e176a7181d454dffaea219" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/static/css/jumbotron-narrow.css?v=4c747ccfb71bf04495c664e4f54f452f" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="header clearfix">
        <nav>
            <ul class="nav nav-pills pull-right">
                <li role="presentation"><a href="/shop">商品列表</a></li>

                <?php
                if($this->session->userdata('login')){
                    $html = <<<crifan
                <li role="presentation"><a href="/user">个人中心</a></li>
                <li role="presentation"><a href="/seckill">！秒杀活动！</a></li>
                <li role="presentation"><a href="/shopcar">购物车</a></li>
                <li role="presentation"><a href="/user/change">修改密码</a></li>
                <li role="presentation"><a href="/logout">注销</a></li>
crifan;
                    echo $html;
                }else{
                    $html = <<<crifan
                <li role="presentation"><a href="/login">登录</a></li>
                <li role="presentation"><a href="/register">注册</a></li>
crifan;
                    echo $html;
                }?>

            </ul>
        </nav>
        <h3 class="text-muted">sshop</h3>
    </div>


    <?php if(isset($danger)){
        if($danger === 1){
            echo <<<crifan
            <div class="alert alert-danger alert-dismissable">
                操作失败。
            </div>
crifan;
        }
    }
    if(isset($success)){
        if($success === 1){
            echo <<<crifan
            <div class="alert alert-success alert-dismissable">
                操作成功。
            </div>
crifan;
        }
    }
    ?>




    <div class="jumbotron">
        <h1>秒杀活动</h1>
        <form action="" method="post">
            <input type="hidden" name="<?=$token_name;?>" value="<?=$token_hash;?>">
            <input class="hidden" name="id" value="1">
            <button class="btn btn-danger" type="submit">秒杀</button>
        </form>
    </div>

    <footer class="footer">
        <p>© 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->


</body></html>