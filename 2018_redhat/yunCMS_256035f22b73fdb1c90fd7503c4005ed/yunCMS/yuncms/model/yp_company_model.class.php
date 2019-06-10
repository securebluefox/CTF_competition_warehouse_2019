<?php
/**
 * 黄页企业库数据库操作类
 */

defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 1);
class yp_company_model extends model {

	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'yp_company';
		parent::__construct();
	}
}
?>