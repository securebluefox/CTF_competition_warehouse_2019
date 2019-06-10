<?php
namespace app\ctrl;
use app\model\userModel;
use app\model\picUploadModel;

class logoutCtrl extends \core\mypro
{

    public function index()
	{
		session_start();
		session_destroy();
		jump('/');
	}


}