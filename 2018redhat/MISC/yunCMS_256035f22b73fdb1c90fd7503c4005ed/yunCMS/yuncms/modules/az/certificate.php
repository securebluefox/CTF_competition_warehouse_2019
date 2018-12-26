<?php
/**
 * 企业会员后台操作类。完善公司信息，发布、修改信息等
 */

defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('common');
//pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);

class certificate extends common {

	function __construct() {
		$this->yp_certificate = pc_base::load_model('yp_certificate_model');
		parent::__construct();
		pc_base::load_sys_class('form', '', 0);
		//$this->_userid = param::get_cookie('_userid');
	}

	/**
	 *
	 * 资质证书管理列表 ...
	 */
	public function init(){
		$where = array('userid'=>$this->_userid);
 		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->yp_certificate->listinfo($where,$order = 'id desc',$page, $pages = '9');
		$pages = $this->yp_certificate->pages;
		include template('yp', 'certificate_manage');
	}

	/**
	 *
	 * 添加资质证书 ...
	 */
	public function add() {
		if($_POST['dosubmit']){
			if(is_array($_POST['info'])){
 				if(!$_POST['info']['name']){
					return false;
				}
				if(!$_POST['info']['thumb']){
					return false;
				}
				$_POST['info']['userid'] = $this->_userid;
				$_POST['info']['status'] = 1;
				$_POST['info']['addtime'] = strtotime($_POST['info']['addtime']);
				$_POST['info']['endtime'] = strtotime($_POST['info']['endtime']);
 				$returnid = $this->yp_certificate->insert($_POST['info'],true);
 				if(!$returnid) return FALSE;
 				showmessage(L('operation_success'),HTTP_REFERER);
			}
		}else{
 			include template('yp', 'certificate_add');
		}

	}


	/**
	 * 删除资质证书信息
	 * @param	intval	$sid	记录ID，递归删除
	 */
	public function delete() {
		if(count($_POST['id'])==0){
			showmessage(L('illegal_parameters'),HTTP_REFERER);
		}
  		if(is_array($_POST['id'])){
			foreach($_POST['id'] as $ids) {
	 			$this->yp_certificate->delete(array('id'=>$ids));
			}
			showmessage(L('operation_success'),HTTP_REFERER);
		}
	}

	/**
	 * 修改资质证书信息
	 * @param	intval	$sid	记录ID，递归删除
	 */
	public function edit() {
		if($_POST['dosubmit']){
			$id = intval($_GET['id']);
			if(!$id){return false;}
			if(!is_array($_POST['info'])){ return false;}
			$_POST['info']['status'] = '0';
			$_POST['info']['addtime'] = strtotime($_POST['info']['addtime']);
			$_POST['info']['endtime'] = strtotime($_POST['info']['endtime']);
 	  		$this->yp_certificate->update($_POST['info'],array('id'=>$id));
	  		showmessage(L('operation_success'),HTTP_REFERER);
		}else{
			$id = $_GET['id'];
			$result = $this->yp_certificate->get_one(array('id'=>$id));
			extract($result);
			include template('yp', 'certificate_edit');
		}

	}

}
?>