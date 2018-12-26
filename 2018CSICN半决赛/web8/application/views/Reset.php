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



    <div class="row">
        <form action="../pass/reset" method="post" class="col-lg-6 col-lg-offset-3">
            <input type="hidden" name="<?=$token_name;?>" value="<?=$token_hash;?>">
            <div class="form-group">
                <label>邮件地址：</label>
                <input type="email" class="form-control" name="mail" placeholder="" required="">
            </div>
            <div>
                <label>验证码 ( <?=$answer['vtt_ques']?> )：</label><br>
                <canvas id="vtt_captcha" width="680" height="460" rel="<?=$uuid?>"></canvas>
                <input type="hidden" id="captcha_x" name="captcha_x" value="">
                <input type="hidden" id="captcha_y" name="captcha_y" value="">
            </div>
            <button class="btn btn-primary pull-right" type="submit">发送重置链接</button>
        </form>
    </div>
    <br>
    <script>
        window.onload = function () {
            var c = document.getElementById("vtt_captcha");
            var ctx = c.getContext("2d");
            var img = new Image();
            img.onload = function () {
                ctx.drawImage(img, 0, 0);
            };
            img.src = '../captcha';
            c.onmousedown = function (event) {
                var rect = this.getBoundingClientRect();
                var x = (event.x - rect.left) * (this.width / rect.width);
                var y = (event.y - rect.top) * (this.height / rect.height);
                ctx = c.getContext("2d");
                ctx.beginPath();
                ctx.arc(x,y,10,0,360,false);
                ctx.fillStyle="red";
                ctx.fill();
                ctx.closePath();
                document.getElementById('captcha_x').value = x;
                document.getElementById('captcha_y').value = y;
            }
        };
    </script>

    <footer class="footer">
        <p>© 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->


</body></html>