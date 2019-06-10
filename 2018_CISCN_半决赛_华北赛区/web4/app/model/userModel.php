<?php
namespace app\model;

use core\lib\model;

class userModel extends model
{
    public $table = 'user';

	public function addOne($data)
    {
        $sql = $this->prepare("INSERT INTO ".$this->table."(`username`,`password`,`mail`,`integral`) VALUES (?,?,?,?)");
        return $sql->execute(array($data['username'],md5($data['password']),$data['mail'],$data['integral']));
        
    }

    public function addCommodity($userid,$commodityid)
    {
        $sql = $this->prepare("UPDATE ".$this->table." SET commodityid=? WHERE `id`=? ");
        return $sql->execute(array($commodityid,$userid));
        
    }



	public function getOne($data)
    {
        $sql = $this->prepare("SELECT * FROM ".$this->table." WHERE `username`= ? AND `password`= ? limit 0,1");
        $sql->execute(array($data['username'],md5($data['password'])));
        $res = $sql->fetchAll();
        
        foreach ($res as $r) {
                return $r;
        }	
        
    }

    public function getById($id)
    {
        $sql = $this->prepare("SELECT * FROM ".$this->table." WHERE `id`= ? ");
        $sql->execute(array($id));
        $res = $sql->fetchAll();
        
        foreach ($res as $r) {
                return $r;
        }	
        
    }

    public function getByName($name)
    {
        $sql = $this->prepare("SELECT * FROM ".$this->table." WHERE `username`= ? ");
        $sql->execute(array($name));
        $res = $sql->fetchAll();
        
        foreach ($res as $r) {
                return $r;
        }	
        
    }

    public function setPass($data)
    {
        $sql = $this->prepare("UPDATE ".$this->table." SET `password` = ? where `id` = ?");
        //dp($sql);
        return $sql->execute(array(md5($data['password']),$data['id']));
        
    }

    public function addIntegral($username,$integral)
    {
        $sql = $this->prepare("UPDATE ".$this->table." SET `integral` = integral+? , `invited`=invited+1 where `username` = ?");
        return $sql->execute(array($integral,$username));
    }

    public function pay($userid,$price)
    {
        $sql = $this->prepare("UPDATE ".$this->table." SET integral= integral-?,commodityid=0,buy_count=buy_count+1 WHERE `id`=? ");
        $sql->bindValue(1, $price, \PDO::PARAM_STR);
        $sql->bindValue(2, $userid, \PDO::PARAM_STR);
        $sql->execute();
        return $sql;

    }



}