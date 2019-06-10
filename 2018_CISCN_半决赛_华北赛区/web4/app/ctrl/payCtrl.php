<?php
namespace app\ctrl;
use app\model\userModel;
use app\model\commoditysModel;

class payCtrl extends \core\mypro
{

    public function index()
	{
        if(empty($_POST)){
            $this->assign('success',0);
            $this->display('pay.html');
        }
        if(!loggedin()){
            jump('/login');
        }
        $user = $_SESSION['user'];
        $price = post('price');
		if($price){
			$userid = $_SESSION['user']['id'];
			$model = new userModel();
			if($model->pay($user['id'],$price)){
				    $_SESSION['user'] = $usermodel->getById($user['id']);//更新session
                    $this->assign('success',1);
					$this->display('pay.html');
					exit();
			}
		}


        $commodityid = post('id',0,'int');
        if($commodityid){            
            $usermodel = new userModel();
            $userMoney = $usermodel->getById($user['id'])['integral'];
            $commoditymodel = new commoditysModel();
            $commodity = $commoditymodel->getOne($commodityid);
            $commodityprice = $commodity['price'];
            $commodityamount = $commodity['amount'];

            if($commodityamount>=1 && $userMoney>=$commodityprice){
                sleep(1);
                if($commoditymodel->reduceOne($commodityid) && $usermodel->pay($user['id'],$commodityprice))
                {//付费
                    $_SESSION['user'] = $usermodel->getById($user['id']);//更新session
                    $this->assign('success',1);
                    $this->display('pay.html');

                }else{
                $this->assign('success',0);
                $this->display('pay.html');
                }
            }else{
                $this->assign('success',0);
                $this->display('pay.html');
            }

        }
        
        
	}


}