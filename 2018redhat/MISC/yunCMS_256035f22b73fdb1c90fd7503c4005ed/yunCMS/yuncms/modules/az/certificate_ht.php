<?php
/**
 * 企业会员后台操作类。完善公司信息，发布、修改信息等
 */

defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);

class certificate_ht extends admin {

	function __construct() {
		$this->yp_certificate = pc_base::load_model('yp_certificate_model');
		parent::__construct();
		pc_base::load_sys_class('form', '', 0);
	}


	/**
	 *
	 * 资质证书管理列表 ...
	 */
	public function init(){
 		$status = $_GET['status'];
 		if($status!=''){
 			$where = array("status"=>$status);
 		}
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->yp_certificate->listinfo($where,$order = 'id desc',$page, $pages = '9');
		$pages = $this->yp_certificate->pages;

		$need_pass = $this->count_pass();
		include $this->admin_tpl('certificate_list');
 	}

	/**
	 *
	 * 资质证书搜索 ...
	 */
	public function search(){
 		$username = $_POST['search']['username'];
 		$member = pc_base::load_app_class(member_interface,'member');
  		$user_arr = $member->get_member_info($username,2);
   		$userid = $user_arr['userid'];
  		if($userid!=''){
 			$where = array("userid"=>$userid);
 		}else {
 			showmessage(L('no_account_information'),'?m=yp&c=certificate_ht&a=init');
 		}

 		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->yp_certificate->listinfo($where,$order = 'id desc',$page, $pages = '9');
		$pages = $this->yp_certificate->pages;
 		$need_pass = $this->count_pass();
		include $this->admin_tpl('certificate_list');
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
			$_POST['info']['addtime'] = strtotime($_POST['info']['addtime']);
			$_POST['info']['endtime'] = strtotime($_POST['info']['endtime']);
 	  		$this->yp_certificate->update($_POST['info'],array('id'=>$id));
	  		showmessage(L('operation_success'),'?m=yp&c=certificate_ht&a=edit','', 'edit');
		}else{
			$id = $_GET['id'];
			$result = $this->yp_certificate->get_one(array('id'=>$id));
			extract($result);

			include $this->admin_tpl('certificate_edit');
		}

	}

	/**
	 *
	 * 通过待审证书申请 ...
	 */
	public function passed_check(){
		$ids =$_POST['id'];
		if(!is_array($ids)){
 			showmessage('请选择对应证书，再操作！',HTTP_REFERER);
		}
		$data = array('status'=>'1');
		foreach ($ids as $id){
			$this->yp_certificate->update($data,array('id'=>$id));
		}
  		showmessage(L("operation_success"),HTTP_REFERER);
	}

	/**
	 *
	 * 统计待审量 ...
	 */
	public function count_pass(){
		$where = array('status'=>0);
		$count = $this->yp_certificate->count($where);
		return $count;
	}

}
?>