<?php
namespace app\ctrl;
use app\model\userModel;
use app\model\commoditysModel;

class infoCtrl extends \core\mypro
{

    public function index()
	{
        $id = get('id',1,'int');
        $model = new commoditysModel();
        $commodity = $model->getOne($id);
        if($commodity){
            $this->assign('commodity',$commodity);
            $this->display('info.html');
        }else{
            dp('id error');
        }
        
	}


}