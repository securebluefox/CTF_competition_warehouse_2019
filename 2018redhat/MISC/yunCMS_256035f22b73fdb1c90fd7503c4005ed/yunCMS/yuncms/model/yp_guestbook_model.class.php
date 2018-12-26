<?php
/**
 * 黄页企业与分类关系库操作类
 */

defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class yp_guestbook_model extends model {

	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'yp_guestbook';
		parent::__construct();
	}
}
?>