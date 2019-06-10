<?php
chdir(dirname(__FILE__));
//mysql database address
define('DB_HOST','127.0.0.1');
//mysql database user
define('DB_USER','root');
//database password
define('DB_PASSWD','81c3b080da');
//database name
define('DB_NAME','ciscnweb233');
//dsn object
define('DB_DSN','mysql:dbname='.DB_NAME.';host='.DB_HOST);
$dbh = new PDO(DB_DSN, DB_USER, DB_PASSWD);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
function randstr($length){  
    $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    $randStr = str_shuffle($str);//打乱字符串  
    $rands= substr($randStr,0,$length);//substr(string,start,length);返回字符串的一部分  
    return $rands;  
}