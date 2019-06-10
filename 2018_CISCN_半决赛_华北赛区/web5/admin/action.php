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
?>
<div class="jumbotron">
       <h1>欢迎你</h1>
    </div>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>