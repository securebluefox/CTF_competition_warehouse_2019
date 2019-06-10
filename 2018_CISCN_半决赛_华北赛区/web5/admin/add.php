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
$con = @mysql_connect("localhost","ciscn","ciscnweb233");
error_reporting(0);
$agent = $_SERVER['HTTP_USER_AGENT'];
if(isset($_POST['name'])){
  $sqlmap_AG = "/sqlmap/i";
  if(true == preg_match($sqlmap_AG, $agent)){  
    die('ERROR');
  }
	$name = $_POST['name'];
	$desc = $_POST['desc'];
	$amount = $_POST['amount'];
	$price = $_POST['price'];
	$sql="INSERT INTO `commoditys` (`name`,`descr`,`amount`,`price`) VALUES ('$name','$desc',$amount,$price)";
	$db_selected = mysql_select_db("ciscnweb233",$con);
	$result=mysql_query($sql,$con);
	$row = mysql_fetch_array($result);
	if($result)
	{
  	echo '成功';
  	}
	else 
	{
	  echo '失败了';
	}
}
?>
<div class="jumbotron">
       <h1>增加商品</h1>
       <form action="./add.php" method="post">
            <input class="text" name="name" placeholder="商品名">
            <input class="text" name="desc" placeholder="商品描述">
            <input class="text" name="amount" placeholder="商品数量">
            <input class="text" name="price" placeholder="商品价格">
            <button class="btn btn-danger" type="submit">增加</button>
        </form>
    </div>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>