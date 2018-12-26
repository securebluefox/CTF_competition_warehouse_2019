<?php
namespace app\model;

use core\lib\model;

class shopcarModel extends model
{
    public $table = 'shopcar';

    public function addOne($userid,$commodityid)
    {
        $sql = $this->prepare("INSERT INTO ".$this->table."(`userid`,`commodityid`) VALUES (?,?)");
        return $sql->execute(array($userid,$commodityid));
        
    }
}