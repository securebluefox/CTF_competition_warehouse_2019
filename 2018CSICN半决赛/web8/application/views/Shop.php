<html lang="zh-CN"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>sshop</title>
    <!-- Bootstrap core CSS -->
    <link href="static/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="static/css/jumbotron-narrow.css" rel="stylesheet">
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
    <div class="row marketing">
        <div class="commodity-list">
            <table class="table">
                <tbody><tr>
                    <th>商品名称</th>
                    <th>商品价格</th>
                    <th>操作</th>
                </tr>

                <?php
//                $csrf = array(
//                    'name' => $this->security->get_csrf_token_name(),
//                    'hash' => $this->security->get_csrf_hash()
//                );
                foreach($commodity_list as $commodity){
                    //var_dump($commodity);
                    echo <<< CONTENT
                <tr>
                    <td class="commodity-name"><a href="/info/{$commodity['id']}">{$commodity['name']}</a></td>
                    <td>{$commodity['price']}</td>
                    <td>
                        <a href="javascript:;" onclick="document.getElementById('{$commodity['name']}').submit();">加入购物车</a>
                        <form action="/shopcar/add" method="post" id="{$commodity['name']}">
                            <input type="hidden" name="{$token_name}" value="{$token_hash}">
                            <input type="hidden" name="id" value="{$commodity['id']}">
                        </form>
                    </td>
                </tr>
CONTENT;
                }?>

                </tbody></table>
        </div>
        <div class="pagination col-lg-12">

            <?php
            //var_dump($page);
            if($page > 1){

                echo "<a href=\"?page=$page-1\">上一页</a>";
            }
            //var_dump($page);
            //var_dump($page_num);
            if($page+1<=$page_num){
                echo "<a href=\"?page=$page-1\" class=\"pull-right\">下一页</a>";
            }?>

        </div>
    </div>

    <footer class="footer">
        <p>© 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->


</body></html>