<?php
require_once ('./config.php');
require_once ('./include/index.php');
$flag = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $cid = $_POST['id'];
    $qeury = "UPDATE `commoditys` SET `amount` = `amount` - 1 WHERE `cid` = :cid";
    $up_sth = $dbh ->prepare($qeury);
    $up_sth -> bindParam(":cid",$cid);
    try{
        $flag = 'yes';
        $up_sth -> execute();
    }catch (Exception $e) {
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
<div class="jumbotron">
       <h1>秒杀活动</h1>
        <form action="/seckill.php" method="post">
            <input class="hidden" name="id" value="2">
            <button class="btn btn-danger" type="submit">秒杀</button>
        </form>
    </div>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>