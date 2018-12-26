<html lang="zh-CN"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>sshop</title>
    <!-- Bootstrap core CSS -->
    <link href="static/css/bootstrap.min.css?v=ec3bb52a00e176a7181d454dffaea219" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="static/css/jumbotron-narrow.css?v=4c747ccfb71bf04495c664e4f54f452f" rel="stylesheet">
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







    <div class="jumbotron user-info">
        <h1 class="user-username"><?=$user->username?></h1>
        <h3 class="user-email">邮箱地址：<?=$user->mail?></h3>
        <h3 class="user-integral">剩余积分：<?=$user->integral?></h3>
        <?php if($user->username === "admin1strat0r"){
//                $html = <<<crifan
//        <h3 class="user-integral">现在你可以薅薅薅，凑够买magic box的积分，~~~</h3>
//crifan;
//            echo $html;
            if($user->integral>=800){
                $html = <<<crifan
        <h3 class="user-integral">有趣的路径 /sdlsaflholhpnklnvlk</h3>
crifan;
                echo $html;

            }else{
                $html = <<<crifan
        <h3 class="user-integral">你的积分还未满800哦，满啦告诉你个小秘密</h3>
crifan;
                echo $html;
            }
        }

//        $html = <<<crifan
//        <img src=api/hoihodolaniosyhoidgnalsmdl?url=/static/timg.jpg></img>
//crifan;
//        echo $html;
        ?>
    </div>

    <footer class="footer">
        <p>© 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->


</body></html>