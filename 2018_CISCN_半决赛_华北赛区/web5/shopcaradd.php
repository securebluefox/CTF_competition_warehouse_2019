<?php
require_once ('./config.php');
require_once ('./include/index.php');
$uid = $_SESSION['uid'];
$cid = $_POST['id'];
//  查询商品
$query = "SELECT `name`,`descr`,`amount`,`price` FROM `commoditys` WHERE `cid` = :cid";
$sth = $dbh->prepare($query);
$sth -> bindParam(":cid",$cid);
$sth -> execute();
$result = $sth -> fetch();
$name = $result['name'];
$descr = $result['descr'];
$amount = $result['amount'];
$price = $result['price'];

//  插入购物车
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

gogogo:
echo <<<EOT
    <div class="jumbotron">
       <h1>购物车</h1>
        <div class="shopcar_list">
            <ul class="list-group">
                <li class="list-group-item">{{ commodity.name }} / {{ commodity.price }}</li>
            </ul>
            <form action="" method="post">
                {% raw xsrf_form_html() %}
                <input type="hidden" name="price" value="{{ commodity.price }}">
                <button class="btn btn-danger" type="submit">结算</button>
            </form>
        </div>
    </div>
EOT;
// die('<meta http-equiv="Refresh" content="0;url=/shopcar.php"/>');
