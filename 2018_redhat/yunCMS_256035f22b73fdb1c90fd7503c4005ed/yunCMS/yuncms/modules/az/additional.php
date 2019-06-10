<?php
defined('IN_PHPCMS') or exit('No permission resources.');
//模型原型存储路径
define('MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'yp'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
pc_base::load_app_class('admin','admin',0);
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('util');
class additional extends admin {
	private $db,$model_db;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('sitemodel_field_model');
		$this->model_db = pc_base::load_model('sitemodel_model');
		$this->siteid = $this->get_siteid();
	}
	
	public function init() {
		$show_header = '';
		$modelid = $_GET['modelid'];
		$this->cache_field($modelid);
		$datas = $this->db->select(array('modelid'=>'0'),'*',100,'listorder ASC');
		require MODEL_PATH.'fields.inc.php';
		include $this->admin_tpl('additional_field_manage');
	}
	public function add() {
		if(isset($_POST['dosubmit'])) {

			$field = $_POST['info']['field'];
			$minlength = $_POST['info']['minlength'] ? $_POST['info']['minlength'] : 0;
			$maxlength = $_POST['info']['maxlength'] ? $_POST['info']['maxlength'] : 0;
			
			//附加属性值
			$_POST['info']['setting'] = array2string($_POST['setting']);
			$_POST['info']['siteid'] = $this->siteid;
			$_POST['info']['unsetgroupids'] = isset($_POST['unsetgroupids']) ? implode(',',$_POST['unsetgroupids']) : '';
			$_POST['info']['unsetroleids'] = isset($_POST['unsetroleids']) ? implode(',',$_POST['unsetroleids']) : '';
			$this->db->insert($_POST['info']);
			$this->cache_field();
			showmessage(L('add_success'),'?m=yp&c=additional&a=init&menuid='.$_GET['menuid']);
		} else {
			$show_header = $show_validator = $show_dialog = '';
			pc_base::load_sys_class('form','',0);
			require MODEL_PATH.'fields.inc.php';

			$f_datas = $this->db->select(array('modelid'=>0),'field,name',100,'listorder ASC');
			foreach($f_datas as $_k=>$_v) {
				$exists_field[] = $_v['field'];
			}

			$all_field = array();
			foreach($fields as $_k=>$_v) {
				if(in_array($_k,$not_allow_fields) || in_array($_k,$exists_field) && in_array($_k,$unique_fields)) continue;
				$all_field[$_k] = $_v;
			}

			$modelid = $_GET['modelid'];
			//角色缓存
			$roles = getcache('role','commons');
			$grouplist = array();
			//会员组缓存
			$group_cache = getcache('grouplist','member');
			foreach($group_cache as $_key=>$_value) {
				$grouplist[$_key] = $_value['name'];
			}
			header("Cache-control: private");
			include $this->admin_tpl('additional_field_add');
		}
	}
	public function edit() {
		if(isset($_POST['dosubmit'])) {

			//附加属性值
			$_POST['info']['setting'] = array2string($_POST['setting']);
			$fieldid = intval($_POST['fieldid']);
			
			$_POST['info']['unsetgroupids'] = isset($_POST['unsetgroupids']) ? implode(',',$_POST['unsetgroupids']) : '';
			$_POST['info']['unsetroleids'] = isset($_POST['unsetroleids']) ? implode(',',$_POST['unsetroleids']) : '';
			$this->db->update($_POST['info'],array('fieldid'=>$fieldid,'siteid'=>$this->siteid));
			$this->cache_field($modelid);
			showmessage(L('update_success'),'?m=yp&c=additional&a=init&modelid='.$modelid);
		} else {
			$show_header = $show_validator = $show_dialog = '';
			pc_base::load_sys_class('form','',0);
			require MODEL_PATH.'fields.inc.php'; 
			$modelid = 0;
			$fieldid = intval($_GET['fieldid']);

			
			$m_r = $this->model_db->get_one(array('modelid'=>0));
			$r = $this->db->get_one(array('fieldid'=>$fieldid));
			extract($r);
			require MODEL_PATH.$formtype.DIRECTORY_SEPARATOR.'config.inc.php';
			
			$setting = string2array($setting);
			ob_start();
			include MODEL_PATH.$formtype.DIRECTORY_SEPARATOR.'field_edit_form.inc.php';
			$form_data = ob_get_contents();
			ob_end_clean();
			//角色缓存
			$roles = getcache('role','commons');
			$grouplist = array();
			//会员组缓存
			$group_cache = getcache('grouplist','member');
			foreach($group_cache as $_key=>$_value) {
				$grouplist[$_key] = $_value['name'];
			}
			header("Cache-control: private");
			include $this->admin_tpl('additional_field_edit');
		}
	}
	public function disabled() {
		$fieldid = intval($_GET['fieldid']);
		$disabled = $_GET['disabled'] ? 0 : 1;
		$this->db->update(array('disabled'=>$disabled),array('fieldid'=>$fieldid,'siteid'=>$this->siteid));
		$modelid = $_GET['modelid'];
		$this->cache_field($modelid);
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	public function delete() {
		$fieldid = intval($_GET['fieldid']);
		$r = $this->db->get_one(array('fieldid'=>$_GET['fieldid'],'siteid'=>$this->siteid));
		//必须放在删除字段前、在删除字段部分，重置了 tablename
		$this->db->delete(array('fieldid'=>$_GET['fieldid'],'siteid'=>$this->siteid));

		$modelid = intval($_GET['modelid']);
		$this->cache_field($modelid);
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	/**
	 * 排序
	 */
	public function listorder() {
		if(isset($_POST['dosubmit'])) {
			foreach($_POST['listorders'] as $id => $listorder) {
				$this->db->update(array('listorder'=>$listorder),array('fieldid'=>$id));
			}
			showmessage(L('operation_success'),HTTP_REFERER);
		} else {
			showmessage(L('operation_failure'));
		}
	}
	/**
	 * 检查字段是否存在
	 */
	public function public_checkfield() {
		$field = strtolower($_GET['field']);
		$oldfield = strtolower($_GET['oldfield']);
		if($field==$oldfield) exit('1');
		
		if ($this->db->get_one(array('modelid'=>0, 'field'=>$field))) {
			exit('0');
		} else {
			exit('1');
		}
	}
	/**
	 * 字段属性设置
	 */
	public function public_field_setting() {
		$fieldtype = $_GET['fieldtype'];
		require MODEL_PATH.$fieldtype.DIRECTORY_SEPARATOR.'config.inc.php';
		ob_start();
		include MODEL_PATH.$fieldtype.DIRECTORY_SEPARATOR.'field_add_form.inc.php';
		$data_setting = ob_get_contents();
		//$data_setting = iconv('gbk','utf-8',$data_setting);
		ob_end_clean();
		$settings = array('field_basic_table'=>$field_basic_table,'field_minlength'=>$field_minlength,'field_maxlength'=>$field_maxlength,'field_allow_search'=>$field_allow_search,'field_allow_fulltext'=>$field_allow_fulltext,'field_allow_isunique'=>$field_allow_isunique,'setting'=>$data_setting);
		echo json_encode($settings);
		return true;
	}
	/**
	 * 更新指定模型字段缓存
	 * 
	 * @param $modelid 模型id
	 */
	public function cache_field($modelid = 0) {
		$field_array = array();
		$fields = $this->db->select(array('modelid'=>0,'disabled'=>$disabled),'*',100,'listorder ASC');
		foreach($fields as $_value) {
			$setting = string2array($_value['setting']);
			$_value = array_merge($_value,$setting);
			$field_array[$_value['field']] = $_value;
		}
		setcache('additional_field',$field_array,'model');
		return true;
	}
}
?>