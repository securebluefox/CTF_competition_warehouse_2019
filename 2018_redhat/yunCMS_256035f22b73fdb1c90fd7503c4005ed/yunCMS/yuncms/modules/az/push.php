<?php
defined('IN_PHPCMS') or exit('No permission resources.');

pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('push_factory', '', 0);

class push extends admin {

	public function __construct() {
		parent::__construct();
		$this->siteid = $this->get_siteid();
		$module = (isset($_GET['module']) && !empty($_GET['module'])) ? $_GET['module'] : 'admin';
		if (in_array($module, array('admin', 'special','content'))) {
			$this->push = push_factory::get_instance()->get_api($module);
		} else {
			showmessage(L('not_exists_push'), 'blank');
		}
	}

	/**
	 * 推送选择界面
	 */
	public function init() {
		if ($_POST['dosubmit']) {
			$c = pc_base::load_model('yp_content_model');
            $modelid = intval($_POST['modelid']);
            $catid = intval($_POST['catid']);
			$c->set_model($modelid);
			$info = array();
			$ids = explode('|', $_POST['id']);

			if(is_array($ids)) {
				foreach($ids as $id) {
					$info[$id] = $c->get_content($catid, $id, $modelid);
				}
			}
			$_GET['add_action'] = $_GET['add_action'] ? $_GET['add_action'] : $_GET['action'];
			$this->push->$_GET['add_action']($info, $_POST, '', 'yp_content_model');
			showmessage(L('success'), '', '', 'push');
		} else {
			pc_base::load_app_func('global', 'template');
			if (method_exists($this->push, $_GET['action'])) {
				$html = $this->push->{$_GET['action']}(array('modelid'=>$_GET['modelid'], 'catid'=>$_GET['catid']));
				$tpl = isset($_GET['tpl']) ? 'push_to_category' : 'push_list';
				include $this->admin_tpl($tpl);
			} else {
				showmessage('CLASS METHOD NO EXISTS!', 'blank');
			}
		}
	}

	public function public_ajax_get() {
		if (method_exists($this->push, $_GET['action'])) {
			$html = $this->push->{$_GET['action']}($_GET['html']);
			echo $html;
		} else {
			echo 'CLASS METHOD NO EXISTS!';
		}
	}
}
?>