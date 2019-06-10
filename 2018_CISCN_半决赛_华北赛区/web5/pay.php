<?php
require_once ('./config.php');
require_once ('./include/index.php');
if(!isset($_SESSION['uid'])){
	die('<meta http-equiv="Refresh" content="0;url=./"/>');
}
$flag = 'yes';
$price = $_POST['price'];
$uid = $_SESSION['uid'];
$jisuan = "UPDATE `user` SET `integral` = `integral` - :price WHERE `uid` = :uid";
$up_sth = $dbh ->prepare($jisuan);
$up_sth -> bindParam(":price",$price);
$up_sth -> bindParam(":uid",$uid);
try{
		$up_sth -> execute();
	}catch (Exception $e) {
        $flag = 'no';
}
if($flag == 'yes'){
    echo <<<EOT
        <div class="alert alert-success alert-dismissable">
                操作成功。
            </div>
EOT;
}
elseif($flag  == 'no'){
    echo <<<EOT
        <div class="alert alert-danger alert-dismissable">
                操作失败。
            </div>
EOT;
}
?>
<div class="jumbotron">
       <h1>结账</h1>
    </div>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>
