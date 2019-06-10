<?php
require_once ('./config.php');
require_once ('./include/index.php');
$key = md5(randstr(32));
if(!isset($_COOKIE['_xsrf'])){
    setcookie('_xsrf', $key, time()+3600);
    $xsrf = 'chushihua';
}
else{
    $xsrf = $_COOKIE['_xsrf'];
}
$flag = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['clean'])){
        if(!isset($_SESSION['uid'])){
            die('<meta http-equiv="Refresh" content="0;url=../login/"/>');
        }
        $uid = $_SESSION['uid'];
        $empty = "DELETE FROM `shopcar` WHERE `uid` = :uid";
        $sth = $dbh -> prepare($empty);
        $sth -> bindParam(":uid",$uid);
        try{
                $flag = 'yes';
                $sth -> execute();
            }catch (Exception $e) {
                $flag = 'no';
        }
    }
    elseif(isset($_POST['price'])){
        if(!isset($_SESSION['uid'])){
            die('<meta http-equiv="Refresh" content="0;url=../login/"/>');
        }
        $uid = $_SESSION['uid'];
        $total = $_POST['price'];
        $jisuan = "UPDATE `user` SET `integral` = `integral` - :qian WHERE `uid` = :uid";
        $up_sth = $dbh ->prepare($jisuan);
        $up_sth -> bindParam(":qian",$total,PDO::PARAM_INT);
        $up_sth -> bindParam(":uid",$uid);
        try{
                $flag = 'yes';
                $up_sth -> execute();
            }catch (Exception $e) {
                $flag = 'no';
        }


        $empty = "DELETE FROM `shopcar` WHERE `uid` = :uid";
        $sth = $dbh -> prepare($empty);
        $sth -> bindParam(":uid",$uid);
        try{
                $flag = 'yes';
                $sth -> execute();
            }catch (Exception $e) {
                $flag = 'no';
        }
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
if(!isset($_SESSION['uid'])){
	die('<meta http-equiv="Refresh" content="0;url=../login/"/>');
}
$uid = $_SESSION['uid'];
$cid = @$_GET['cid'];

$query = "SELECT `name`,`price` FROM `commoditys` WHERE `cid` = :cid";
$sth = $dbh->prepare($query);
$sth -> bindParam(":cid",$cid);
$sth -> execute();
$result = $sth -> fetch();
$name = $result['name'];
$price = $result['price'];

if(isset($_GET['cid'])){
	$insert = "INSERT INTO `shopcar` (`cid`,`uid`,`name`,`price`) VALUES (:cid,:uid,:name,:price)";
	$in_sth = $dbh->prepare($insert);
	$in_sth -> bindParam(":cid",$cid);
	$in_sth -> bindParam(":uid",$uid);
	$in_sth -> bindParam(":name",$name);
	$in_sth -> bindParam(":price",$price);
	try{
		$in_sth -> execute();
	}catch (Exception $e) {
        goto gogogo;
    }
	
}

gogogo:
$totalprice = 0;
$shopcar = "SELECT `name`,`price` FROM `shopcar` WHERE `uid` = :uid";
$query_sth = $dbh->prepare($shopcar);
$query_sth -> bindParam(":uid",$uid);
$query_sth -> execute();
$query_num = $query_sth->rowCount();
echo <<<EOT
	<div class="jumbotron">
       <h1>购物车</h1>
        <div class="shopcar_list">
            <ul class="list-group">
EOT;

for($i=0;$i<$query_num;$i++){
	$result = $query_sth->fetch();
	$cname = $result['name'];
	$cprice = $result['price'];
    $totalprice = $totalprice + $cprice;
	echo <<<EOT
		<li class="list-group-item">$cname / $cprice</li>
EOT;
}
echo <<<EOT
            </ul>
            <form action="/shopcar.php" method="post">
                <input class="hidden" name="clean" value="1">
                <button class="btn btn-danger" type="submit">清空购物车</button>
            </form>
            <form action="/shopcar.php" method="post">
                <input class="hidden" name="price" value="$totalprice">
                <input type="hidden" name="_xsrf" value="$xsrf">
                <button class="btn btn-success" type="submit">结算</button>
            </form>
        </div>
    </div>
EOT;
?>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>