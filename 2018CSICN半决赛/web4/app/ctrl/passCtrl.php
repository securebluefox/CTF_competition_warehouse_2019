<?php
namespace app\ctrl;
use app\model\userModel;
use app\model\captchaModel;

class passCtrl extends \core\mypro
{

    public function reset()
	{
		$captcha = new captchaModel();
		if(empty($_POST)){
			$captcha->init();
			$src = $captcha->src;
			$ques = $captcha->ques;
			$this->assign('src',$src);
			$this->assign('ques',$ques);
			$this->display('reset.html');
			exit();
		}
		$mail = post('mail');
		$model = new userModel();
		
		if(preg_match('/.+@.+/',trim($mail))){
			jump('/login/');
		}else{
			$captcha->init();
			$src = $captcha->src;
			$ques = $captcha->ques;
			$this->assign('success',0);
			$this->assign('src',$src);
			$this->assign('ques',$ques);
			$this->display('reset.html');
		}
		
	}


}