<?php

$servername="127.0.0.1";  
$username="root";  
$userpassword="";  
  
$connent=new mysqli($servername,$username,$userpassword);  
if($connent->connect_error){  
    die("连接失败: " . $connent->connect_error);  
}
//创建数据库  
$createdatabase="CREATE DATABASE ciscn";

function generate( $length = 8 ) { 
// 密码字符集，可任意添加你需要的字符 
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; 
    $password = ''; 
    for ( $i = 0; $i < $length; $i++ ) 
    { 
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
    } 
    return $password; 
} 


if($connent->query($createdatabase)==true){  
    mysqli_select_db($connent, 'ciscn' );
    $createtables1 =  " 
        CREATE TABLE `commoditys`(
            `id` INT(11) unsigned NOT NULL auto_increment,
            `name` VARCHAR(200) NOT NULL DEFAULT '',
            `desc` VARCHAR(500) NOT NULL DEFAULT '',
            `amount` INT(11) unsigned NOT NULL DEFAULT 0,
            `price` FLOAT NOT NULL,
            KEY userid(`id`),
            UNIQUE KEY `name`(`name`) 
        )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
    $createtables2 = "
        CREATE TABLE `user`(
            `id` int(11) unsigned NOT NULL auto_increment,
            `username` VARCHAR(50) NOT NULL DEFAULT '',
            `mail` VARCHAR(50) NOT NULL DEFAULT '',
            `password` VARCHAR(60) NOT NULL DEFAULT '',
            `integral` FLOAT NOT NULL,
            `commodityid` int(11) unsigned NOT NULL DEFAULT 0,
            `invited` int(2) NOT NULL DEFAULT 0,
            `buy_count` int(2) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `username`(`username`)
        )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
    $connent->query($createtables1);
    $connent->query($createtables2);
    
    for ( $i = 0; $i < 50; $i++ ){
        $data['name'] = "hint".rand(0,100);
        $data['desc'] = generate(20);
        $data['amount'] = 99999999;
        $data['price'] = 300;

        $connent->query("INSERT INTO commoditys(`name`,`desc`,`amount`,`price`) VALUES ('".$data['name']."','".$data['desc']."','".$data['amount']."','".$data['price']."')");
        // var_dump($connent->error);
    }
   

    ;


}
