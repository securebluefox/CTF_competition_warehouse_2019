<?php
function getrandstr($length){  
    $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    $randStr = str_shuffle($str);//打乱字符串  
    $rands= substr($randStr,0,$length);//substr(string,start,length);返回字符串的一部分  
    return $rands;  
}
require_once ('../config.php');
require_once ('./include/index.php');
require_once ('./include/function/captcha.php');
$key = md5(randstr(32));
if(!isset($_COOKIE['_xsrf'])){
    setcookie('_xsrf', $key, time()+3600);
    $xsrf = 'chushihua';
}
else{
    $xsrf = $_COOKIE['_xsrf'];
}
$flag = '';
$admin = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'];
    if($username != 'admin'){
    	$admin = 'no';
    }
    $password = $_POST['password'];
    $captcha_x = $_POST['captcha_x'];
    $captcha_y = $_POST['captcha_y'];

    $ans_pos_x_1 = $_SESSION['captcha']['ans_pos_x_1'];
    $ans_width_x_1 = $_SESSION['captcha']['ans_width_x_1'];
    $ans_pos_y_1 = $_SESSION['captcha']['ans_pos_y_1'];
    $ans_height_y_1 = $_SESSION['captcha']['ans_height_y_1'];
    unset($_SESSION['captcha']);
    if($ans_pos_x_1 > $captcha_x || $captcha_x > ($ans_pos_x_1 + $ans_width_x_1)){
        $flag = 'no';
    }elseif($ans_pos_y_1 > $captcha_y || $captcha_y > ($ans_pos_y_1 + $ans_height_y_1)){
        $flag = 'no';
    }
    // 周末想出去玩啊打打篮球也行啊
    // 1.登录
    if($flag != 'no'){
        $login_select = "SELECT `uid`,`salt`,`password` FROM `user` WHERE `username` = :username";
        $sth = $dbh -> prepare($login_select);
        $sth -> bindParam(":username",$username);
        $sth -> execute();
        $result = $sth->fetch();
        $salt = $result['salt'];
        $hashpw = $result['password'];
        $uid = $result['uid'];
    }
    if(hash('sha256',$password.$salt) == $hashpw){
        $_SESSION['uid'] = $uid;
        $flag = 'yes';
    }
    else{
        $flag = 'no';
    }
}
if($admin == 'no'){
	echo '对不起，你不是管理员';
	die ('<meta http-equiv="Refresh" content="1;url=../"/>');
}
if($flag == 'yes'){
    echo <<<EOT
        <div class="alert alert-success alert-dismissable">
                操作成功。
            </div>
EOT;
    die ('<meta http-equiv="Refresh" content="0;url=../"/>');
}
elseif($flag  == 'no'){
    echo <<<EOT
        <div class="alert alert-danger alert-dismissable">
                操作失败。
            </div>
EOT;
}
$dir = "./captcha/ans/";
$files = my_dir("./captcha/ans");
$filesNum = count($files);
$randNum = mt_rand(0, $filesNum - 1);
$captchaId = str_replace('ans','',(explode(".",$files[$randNum])[0]));
$captchajpg = search_file("./captcha/jpgs",$captchaId);
$answer = @file_read($dir.($files[$randNum]));
// 验证码存入session
$_SESSION['captcha'] = $answer;
?>
        <div class="row">
            <form action="./index.php" method="post" class="col-lg-6 col-lg-offset-3">
                <div class="form-group">
                    <label>管理员用户名：</label>
                    <input type="text" class="form-control" name="username" placeholder="" required>
                </div>
                <div class="form-group">
                    <label>密码：</label>
                    <input type="password" class="form-control" name="password" placeholder="" required>
                </div>

                <div>
                    <label><?php echo $answer['vtt_ques']?></label><br/>
                    <canvas id="vtt_captcha" width="680" height="460" rel="<?php echo $captchaId?>"></canvas>
                    <input type="hidden" id="captcha_x" name="captcha_x" value="">
                    <input type="hidden" id="captcha_y" name="captcha_y" value="">
                </div>
                <p></p>
                <a class="btn btn-warning" href="/pass/reset">找回密码</a>
                <input type="hidden" name="_xsrf" value="<?php echo $xsrf?>">
                <button class="btn btn-primary pull-right" type="submit">登录</button>
            </form>
            </div>
        <br/>
<?php
echo <<<EOT
<script>
    window.onload = function () {
        var c = document.getElementById("vtt_captcha");
        var ctx = c.getContext("2d");
        var img = new Image();
        img.onload = function () {
            ctx.drawImage(img, 0, 0);
        };
        img.src = '/captcha/jpgs/$captchajpg';

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
EOT;
?>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>