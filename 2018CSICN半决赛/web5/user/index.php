<?php
require_once ('../config.php');
require_once ('./include/index.php');
$uid = $_SESSION['uid'];
$query = "SELECT `username`,`mail`,`integral` FROM `user` WHERE `uid` = :uid";
$sth = $dbh->prepare($query);
$sth -> bindParam(":uid",$uid);
$sth -> execute();
$result = $sth -> fetch();
$username = $result['username'];
$mail = $result['mail'];
$integral = $result['integral'];
echo <<<EOT
	<div class="jumbotron user-info">
	    <h1 class="user-username">$username</h1>
	    <h3 class="user-email">邮箱地址：$mail</h3>
	    <h3 class="user-integral">剩余积分：$integral</h3>
	</div>
EOT;
?>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>