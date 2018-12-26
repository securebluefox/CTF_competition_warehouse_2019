<?php
require_once ('../config.php');
require_once ('./include/index.php');
define("SECRET_KEY", "ciscnwebxxx");
define("METHOD", "aes-128-cbc");
$key = md5(randstr(32));
if(!isset($_COOKIE['_xsrf'])){
    setcookie('_xsrf', $key, time()+3600);
}
else{
    $xsrf = 'suibianla';
}
$flag = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $uid = $_SESSION['uid'];
    $old_password = $_POST['old_password'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    if($password !== $password_confirm){
        $flag = 'no';
    }
    if(isset($_COOKIE['cipher']) && isset($_COOKIE['iv'])){
        $cipher = base64_decode($_COOKIE['cipher']);
        $iv = base64_decode($_COOKIE["iv"]);
        if($plain = openssl_decrypt($cipher, METHOD, SECRET_KEY, OPENSSL_RAW_DATA, $iv)){
            $username = unserialize($plain) or die("<p>base64_decode('".base64_encode($plain)."') can't unserialize</p>");
            $_SESSION['username'] = $username;
        }else{
            die("ERROR!");
        }
    }
    if($flag != 'no'){
        $login_select = "SELECT `salt`,`password` FROM `user` WHERE `uid` = :uid";
        $sth = $dbh -> prepare($login_select);
        $sth -> bindParam(":uid",$uid);
        $sth -> execute();
        $result = $sth->fetch();
        $salt = $result['salt'];
        $hashpw = $result['password'];
    }
    if(hash('sha256',$old_password.$salt) == $hashpw){
        $newpassword = hash('sha256',$password.$salt);
        $update = "UPDATE `user` SET `salt` = :salt,`password` = :newpassword WHERE `username` = :username";
        $sth = $dbh ->prepare($update);
        $sth ->bindParam(":newpassword",$newpassword);
        $sth ->bindParam(":salt",$salt);
        $sth ->bindParam(":username",$_SESSION['username']);
        $sth -> execute();
        $flag = 'yes';
    }
    else{
        $flag = 'no';
    }
}
if($flag == 'yes'){
	echo <<<EOT
		<div class="alert alert-success alert-dismissable">
                操作成功。
            </div>
EOT;
}
elseif($flag == 'no'){
	echo <<<EOT
		<div class="alert alert-danger alert-dismissable">
                操作失败。
            </div>
EOT;
}
?>
<div class="row">
        <form action="/user/userchange.php" method="post" class="col-lg-6 col-lg-offset-3">
            <div class="form-group">
                <label>原密码：</label>
                <input type="password" class="form-control" name="old_password" placeholder="" required>
            </div>
            <div class="form-group">
                <label>新密码：</label>
                <input type="password" class="form-control" name="password" placeholder="" required>
            </div>
            <div class="form-group">
                <label>确认密码：</label>
                <input type="password" class="form-control" name="password_confirm" placeholder="" required>
            </div>
            <input type="hidden" name="_xsrf" value="<?php echo $xsrf?>">
            <button class="btn btn-primary pull-right" type="submit">修改</button>
        </form>
        </div>
    <br/>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>