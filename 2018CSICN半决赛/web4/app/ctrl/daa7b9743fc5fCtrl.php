<?php
namespace app\ctrl;
use app\model\captchaModel;

class daa7b9743fc5fCtrl extends \core\mypro
{

    public function d624ee33c0de4a46e1e()
    {
        $captcha = new captchaModel();
        if(empty($_POST)){
            $captcha->init();
			$src = $captcha->src;
			$ques = $captcha->ques;
			$this->assign('src',$src);
			$this->assign('ques',$ques);
            $this->display('phpinfo_2.html');
            exit();
        }
        $captcha_x = post('captcha_x');
        $captcha_y = post('captcha_y');
        
        if(!$captcha->check($captcha_x,$captcha_y)){
			$captcha->init();
			$src = $captcha->src;
			$ques = $captcha->ques;
			$this->assign('src',$src);
			$this->assign('ques',$ques);
			$this->assign('success',0);
			$this->display("phpinfo_2.html");
		}else{
            $captcha->init();
			$src = $captcha->src;
			$ques = $captcha->ques;
			$this->assign('src',$src);
			$this->assign('ques',$ques);
			$this->assign('success',1);
			$this->display("phpinfo_2.html");
        }
        
    }

    public function a13963a2e27dd80a770()
    {
        $this->display('lfi.html');
    } 
}