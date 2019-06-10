<?php
require_once ('../config.php');
require_once ('./include/index.php');
$cid = $_GET['cid'];
$query = "SELECT `name`,`descr`,`amount`,`price` FROM `commoditys` WHERE `cid` = :cid";
$sth = $dbh->prepare($query);
$sth -> bindParam(":cid",$cid);
$sth -> execute();
$result = $sth -> fetch();
$name = $result['name'];
$descr = $result['descr'];
$amount = $result['amount'];
$price = $result['price'];
echo <<<EOT
	<div class="jumbotron commodity-info">
	   <h1>$name</h1>
	   <p class="lead" style="word-wrap:break-word">$descr</p>
	   <p>Price: $price</p>
	   <p>Amount: $amount</p>
	   <form action="/pay/" method="post">
	      <input type="hidden" name="price" value="$price">
	      <button class="btn btn-lg btn-success">Buy</button>
	   </form>
	</div>
EOT;
?>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>