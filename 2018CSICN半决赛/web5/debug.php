<?php
$salt = 'b3967a0e93';
$password = 'admin';
$dbpw = 'b8d68d1fbd44a51596662cb70f9edcb6';
echo md5(md5($password).$salt.md5($password));
// if(md5(md5($password).$salt.md5($password)) != $dbpw){
// 	echo '111';
// }
// else{
// 	echo 'xxxx';
// }