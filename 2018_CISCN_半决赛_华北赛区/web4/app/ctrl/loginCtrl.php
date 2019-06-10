<?php
namespace app\ctrl;
use app\model\userModel;
use app\model\captchaModel;

class loginCtrl extends \core\mypro
{

    public function index()
	{	
		if(!isset($_SESSION)){
			session_start();
		}
		// if(loggedin()){
		// 	jump('/user/');
		// }
		$captcha = new captchaModel();
		if(empty($_POST)){
			$captcha->init();
			$src = $captcha->src;
			$ques = $captcha->ques;
			$this->assign('src',$src);
			$this->assign('ques',$ques);
			$this->display("login.html");
			exit();

		}

		$data['username'] = post('username');
		$data['password'] = post('password');
		$captcha_x = post('captcha_x');
		$captcha_y = post('captcha_y');
		if(!$captcha->check($captcha_x,$captcha_y)){
			$captcha->init();
			$src = $captcha->src;
			$ques = $captcha->ques;
			$this->assign('src',$src);
			$this->assign('ques',$ques);
			$this->assign('success',0);
			$this->display("login.html");
			exit();
		}
		$model = new userModel();
		$res = $model->getOne($data);
		if($res){
			@session_start();
			$_SESSION['user'] = $res; 
			jump('/user/');
			exit();
		}else{
			$captcha->init();
			$src = $captcha->src;
			$ques = $captcha->ques;
			$this->assign('src',$src);
			$this->assign('ques',$ques);
			$this->assign('success',0);
			$this->display("login.html");
			exit();
		}



	}


}