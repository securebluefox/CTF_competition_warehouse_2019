<?php
namespace app\ctrl;
use app\model\userModel;
use app\model\commoditysModel;

class seckillCtrl extends \core\mypro
{

    public function index()
	{
        if(empty($_POST)){
            $seckill_id = rand(0,50);
            $commoditymodel = new commoditysModel();
            $commodity = $commoditymodel->getOne($seckill_id);
            $this->assign('commodity',$commodity);
            $this->display('seckill.html');
            exit();
        }
        if(!loggedin()){
            jump('/login');
        }
        $id = post('id',0,'int');
        
        if($id){            
            $usermodel = new userModel();
            $user = $usermodel->getById($_SESSION['user']['id']);
            $userMoney = $user['integral'];

            $commoditymodel = new commoditysModel();
            $commodity = $commoditymodel->getOne($id);
            $commodityamount = $commodity['amount'];
            $commodityprice = $commodity['price'];

            if($commodityamount>=1 && $userMoney>=$commodityprice && $commoditymodel->reduceOne($id) && $usermodel->pay($user['id'],$commodityprice)){
                $_SESSION['user'] = $usermodel->getById($user['id']);//更新session
                $this->assign('success',1);
                $this->display('seckill.html');
            }else{
            $this->assign('success',0);
            $this->display('seckill.html');
            }
        }else{
            $this->assign('success',0);
            $this->display('seckill.html');
        }
        
	}


}