<?php
function getrandstr($length){  
    $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    $randStr = str_shuffle($str);//打乱字符串  
    $rands= substr($randStr,0,$length);//substr(string,start,length);返回字符串的一部分  
    return $rands;  
}
require_once ('../config.php');
require_once ('./include/adminheader.php');
require_once ('./include/function/captcha.php');
if(!isset($_SESSION['username'])){
	die('<meta http-equiv="Refresh" content="0;url=../login/"/>');
}
else if($_SESSION['username'] != 'admin'){
	die('<meta http-equiv="Refresh" content="0;url=../login/"/>');
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $getid = $_SESSION['checkid'];
    unset($_SESSION['checkid']);
    $getstr = $_POST['str'];
    $substr = substr(md5($getstr), 0, 8);
    $codecheck = "SELECT `value` FROM `checkc` WHERE `id` = :id";
    $checksth = $dbh -> prepare($codecheck);
    $checksth -> bindParam(":id",$getid);
    $checksth -> execute();
    $checkresult = $checksth ->fetch();
    $checkcode = $checkresult['value'];
    if($substr === $checkcode){
        require_once 'upload.class.php';
        $upload=new upload('myFile1','uploads');
        $dest=$upload->uploadFile();
        echo $dest;
    }else{
        echo '验证码错误';
    }
}
$id = rand(1,10000);
$_SESSION['checkid'] = $id;
$codeselect = "SELECT `value` FROM `checkc` WHERE `id` = :id";
$sth = $dbh -> prepare($codeselect);
$sth -> bindParam(":id",$id);
$sth -> execute();
$result = $sth ->fetch();
$code = $result['value'];
?>
<div class="jumbotron">
       <h1>欢迎你</h1>
       <form action="upload.php" method="post" enctype="multipart/form-data">请选择您要上传的图片：
            <div class="input-wrapper">
                <input type="file" name='myFile1' />
            </div>
            <div class="input-wrapper">
                <label for="male" rel="<?php echo $code?>">请输入验证码:substr(md5($str), 0, 8) === '<?php echo $code?>'</label>
                <input type="text" name='str' />
            </div>
            <input type="submit" value="上传文件" />
        </form>
    </div>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>