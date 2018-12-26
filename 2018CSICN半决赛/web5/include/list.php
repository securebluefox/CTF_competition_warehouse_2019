<div class="row marketing">

    <div class="commodity-list">
        <table class="table">
            <tr>
                <th>商品名称</th>
                <th>商品描述</th>
                <th>商品价格</th>
                <th>操作</th>
            </tr>
            
<?php
$query = "SELECT `cid`,`name`,`descr`,`price` FROM `commoditys`";
$sth = $dbh->prepare($query);
$sth -> execute();
$num = $sth->rowCount();
for($i = 0;$i < $num;$i ++){
    $result = $sth->fetch();
    $cid = $result['cid'];
    $name = $result['name'];
    $descr = $result['descr'];
    $price = $result['price'];
    echo <<<EOT
            <tr>
            <td class="commodity-name"><a href="/info/info.php?cid=$cid">$name</a></td>
                <td>$descr</td>
                <td>$price</td>
                <td>
                    <form action="/shopcaradd.php" method="post">
                        <input type="hidden" name="id" value="$cid">
                        <button class="btn btn-success" type="submit">加入购物车</button>
                    </form>
                </td>
            </tr>
EOT;
}
?>            
            
        </table>

    </div>
<!--     <div class="pagination col-lg-12">
        {% if preview-1 >= 0 %}
        <a href="?page={{ preview }}">上一页</a>
        {% end %}
        {% if len(commoditys) < limit or not next %}
        {% else %}
        <a href="?page={{ next }}" class="pull-right">下一页</a>
        {% end %}
    </div> -->
</div>