<?php
namespace app\ctrl;
use app\model\userModel;
use app\model\captchaModel;


class userCtrl extends \core\mypro
{
   	public function index()
	//个人信息页面
	{
		if(!loggedin()){
			jump('/login/');
		}else{
			//更新资料
			$id = $_SESSION['user']['id'];
			$model = new userModel();
			$_SESSION['user'] = $model->getById($id);
			$user = $_SESSION['user'];
			if($user['buy_count']>=5){
				$this->assign('hint',1);
			}else{
				$this->assign('hint',0);
			}
			$this->assign('user',$user);
			$this->display('user.html');
		}
		
	}
	
	// public function test()
	// {
	// 	$captcha = new captchaModel();
	// 	if(empty($_POST)){	
	// 		$captcha->init();
	// 		$src = $captcha->src;
	// 		$ques = $captcha->ques;
	// 		$this->assign('src',$src);
	// 		$this->assign('ques',$ques);
	// 		$this->display('captcha.html');
	// 	}else{
	// 		$captcha_x = post('captcha_x');
	// 		$captcha_y = post('captcha_y');
	// 		dp($captcha->check($captcha_x,$captcha_y));
	// 	}

	// }
    

    public function change()
    //修改密码
    {
		if(!isset($_SESSION)){
			session_start();
		}
		if(!loggedin()){
			jump('/login/');
		}
		if(empty($_POST))
		{
			$this->assign('user',$_SESSION['user']);
			$this->display('change.html');
			exit();
		}
		$old_password = post('old_password');
		$password = post('password');
		$password_confirm = post('password_confirm');
		$model = new userModel();
		$user = $_SESSION['user'];

		$data['username'] = $user['username'];
		$data['password'] = $old_password;
		if($password!==$password_confirm || !$model->getOne($data)){//验证密码是否正确
			$this->assign('success',0);
			$this->display("change.html");
			exit();
		}else{
			$data['id'] = $user['id'];
			$data['password'] = $password;
			if($model->setPass($data)){//更改密码
				$data['username'] = $user['username'];
				$data['password'] = $password;
				$_SESSION['user'] = $model->getOne($data);//更新session
				$this->assign('success',1);
				$this->display("change.html");
				exit();
			}else{
				$this->assign('success',0);
				$this->display("change.html");
				exit();
			}

		}
		

    }


}