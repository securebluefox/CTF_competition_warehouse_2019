<?php
error_reporting(E_ALL);
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
define('SQL_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

$parentid = $menu_db->insert(array('name'=>'yp', 'parentid'=>2, 'm'=>'yp', 'c'=>'yp', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'yp_setting', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'yp', 'a'=>'setting', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$second_pid = $menu_db->insert(array('name'=>'yp_category_manage', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'category', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'yp_add_category', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'category', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'yp_edit_category', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'category', 'a'=>'edit', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'export_category', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'category', 'a'=>'export', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'del_category', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'category', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'listorder_category', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'category', 'a'=>'listorder', 'data'=>'', 'listorder'=>0, 'display'=>'0'));


$second_pid = $menu_db->insert(array('name'=>'yp_model_manage', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'ypmodel', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_yp_model', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'ypmodel', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_yp_model', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'ypmodel', 'a'=>'edit', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'del_yp_model', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'ypmodel', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'ypmodel_import', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'ypmodel', 'a'=>'import', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'disabled_ypmodel', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'ypmodel', 'a'=>'disabled', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'export_ypmodel', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'ypmodel', 'a'=>'export', 'data'=>'', 'listorder'=>0, 'display'=>'0'));

$three_pid = $menu_db->insert(array('name'=>'yp_fields_manage', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'ypmodel_field', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'0'), true);
$menu_db->insert(array('name'=>'add_yp_field', 'parentid'=>$three_pid, 'm'=>'yp', 'c'=>'ypmodel_field', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_yp_field', 'parentid'=>$three_pid, 'm'=>'yp', 'c'=>'ypmodel_field', 'a'=>'edit', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'disabled_yp_field', 'parentid'=>$three_pid, 'm'=>'yp', 'c'=>'ypmodel_field', 'a'=>'disabled', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_yp_field', 'parentid'=>$three_pid, 'm'=>'yp', 'c'=>'ypmodel_field', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'listorder_yp_field', 'parentid'=>$three_pid, 'm'=>'yp', 'c'=>'ypmodel_field', 'a'=>'listorder', 'data'=>'', 'listorder'=>0, 'display'=>'0'));

$second_pid = $menu_db->insert(array('name'=>'company_template_manage', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'template', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_new_template', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'template', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'setdefaulttpl', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'template', 'a'=>'tpl_default', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'priv_setting', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'template', 'a'=>'edit_priv', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'template_disabled', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'template', 'a'=>'disabled', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'template_delete', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'template', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));

$second_pid = $menu_db->insert(array('name'=>'yp_info_manage', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'content', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'edit_yp_content', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'content', 'a'=>'edit', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'listorder_yp_content', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'content', 'a'=>'listorder', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_yp_content', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'content', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'push_yp_content', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'push', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'pass_yp_content', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'content', 'a'=>'pass', 'data'=>'', 'listorder'=>0, 'display'=>'0'));


$second_pid = $menu_db->insert(array('name'=>'yp_company_manage', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'company', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'company_edit', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'company', 'a'=>'edit', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'company_delete', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'company', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'company_passed_check', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'company', 'a'=>'passed_check', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'company_elite', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'company', 'a'=>'elite', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$three_pid = $menu_db->insert(array('name'=>'certificate_manage', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'certificate_ht', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'certificate_ht_edit', 'parentid'=>$three_pid, 'm'=>'yp', 'c'=>'certificate_ht', 'a'=>'edit', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'certificate_delete', 'parentid'=>$three_pid, 'm'=>'yp', 'c'=>'certificate_ht', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'passed_check_certificate', 'parentid'=>$three_pid, 'm'=>'yp', 'c'=>'certificate_ht', 'a'=>'passed_check', 'data'=>'', 'listorder'=>0, 'display'=>'0'));

$second_pid = $menu_db->insert(array('name'=>'batch_update', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'update_urls', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'update_urls_menu', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'update_urls', 'a'=>'menu', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'update_urls_category', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'update_urls', 'a'=>'category', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'update_urls_content', 'parentid'=>$second_pid, 'm'=>'yp', 'c'=>'update_urls', 'a'=>'content', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
//附加字段菜单
$parentid = $menu_db->insert(array('name'=>'additional_field', 'parentid'=>977, 'm'=>'yp', 'c'=>'additional', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'additional_add', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'additional', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'additional_edit', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'additional', 'a'=>'edit', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'additional_disabled', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'additional', 'a'=>'disabled', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'additional_delete', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'additional', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'additional_listorder', 'parentid'=>$parentid, 'm'=>'yp', 'c'=>'additional', 'a'=>'listorder', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
//menu语言包
$language = array('yp'=>'企业黄页', 'yp_setting'=>'模块配置', 'yp_category_manage'=>'分类管理', 'yp_add_category'=>'添加分类', 'yp_edit_category'=>'修改分类', 'export_category'=>'导出分类', 'del_category'=>'删除分类', 'listorder_category'=>'分类排序', 'yp_model_manage'=>'黄页模型管理', 'add_yp_model'=>'添加模型', 'edit_yp_model'=>'修改模型', 'del_yp_model'=>'删除模型', 'ypmodel_import'=>'导入模型', 'disabled_ypmodel'=>'禁用模型', 'export_ypmodel'=>'导出模型', 'yp_fields_manage'=>'字段管理', 'add_yp_field'=>'添加字段', 'edit_yp_field'=>'修改字段', 'disabled_yp_field'=>'禁用字段', 'delete_yp_field'=>'删除字段', 'listorder_yp_field'=>'字段排序', 'company_template_manage'=>'企业模板管理', 'add_new_template'=>'添加新模板', 'setdefaulttpl'=>'设置默认模板', 'priv_setting'=>'权限设置', 'template_disabled'=>'模板启用关闭', 'template_delete'=>'模板删除', 'yp_info_manage'=>'信息管理', 'edit_yp_content'=>'修改信息', 'listorder_yp_content'=>'信息排序', 'delete_yp_content'=>'信息删除', 'push_yp_content'=>'信息推送',  'pass_yp_content'=>'信息审核/退稿', 'yp_company_manage'=>'企业库管理', 'company_edit'=>'企业修改', 'company_delete'=>'企业删除', 'company_passed_check'=>'企业审核/取消审核', 'company_elite'=>'企业推荐/取消推荐', 'certificate_manage'=>'资质证书管理', 'certificate_ht_edit'=>'资质证书修改', 'certificate_delete'=>'删除证书', 'passed_check_certificate'=>'审核证书', 'batch_update'=>'批量更新URL', 'update_urls_menu'=>'更新企业导航', 'update_urls_category'=>'更新分类url地址', 'update_urls_content'=>'更新内容url', 'additional_field'=>'附加字段管理', 'additional_add'=>'添加附加字段', 'additional_edit'=>'修改附加字段', 'additional_disabled'=>'禁用附件字段', 'additional_delete'=>'删除附加字段', 'additional_listorder'=>'附加字段排序');
//前台菜单管理
$member_menu_db = pc_base::load_model('member_menu_model');
$member_menu_db->insert(array('name'=>'business_centre', 'm'=>'yp', 'c'=>'business', 'a'=>'init', 'data'=>'t=3', 'listorder'=>'4', 'display'=>'1'));
$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'member_menu.lang.php';
if(file_exists($file)) {
	$content = file_get_contents($file);
	$content = substr($content,0,-2);
	$data = $content."\$LANG['business_centre'] = '商务中心';\r\n?>";
	file_put_contents($file,$data);
} else {
	$data = "\$LANG['business_centre'] = '商务中心';\r\n?>";
	file_put_contents($file,$data);
}
//添加category表两个新字段
$category_db = pc_base::load_model('category_model');
if (!$category_db->field_exists('additional')) {
	$category_db->query("ALTER TABLE `phpcms_category` ADD `additional` TEXT NOT NULL");
}
if (!$category_db->field_exists('commenttypeid')) {
	$category_db->query("ALTER TABLE `phpcms_category` ADD `commenttypeid` SMALLINT UNSIGNED NOT NULL DEFAULT '0'");
}
//向模型表添加企业库、新闻、产品、商机模型
$sitemodel = pc_base::load_model('sitemodel_model');
$sitemodel_field = pc_base::load_model('sitemodel_field_model');
$com_modelid = $sitemodel->insert(array('siteid'=>'1', 'name'=>'企业库', 'description'=>'企业会员', 'tablename'=>'yp_company', 'setting'=>'', 'addtime'=>SYS_TIME, 'type'=>4), true);
$sqls = file_get_contents(SQL_PATH.'company.sql');
$sqls = explode("\n", $sqls);
foreach ($sqls as $sql) {
	$sql = trim(str_replace('$modelid', $com_modelid, $sql));
	$sitemodel_field->query($sql);
}
$models = array();
$setting = array2string(array('ismenu'=>'0', 'meta_title'=>'中国最全的黄页信息', 'meta_keywords'=>'中国最全的黄页信息', 'meta_description'=>'中国最全的黄页信息'));
$models[72] = $sitemodel->insert(array('siteid'=>'1', 'name'=>'新闻', 'description'=>'', 'tablename'=>'yp_news', 'setting'=>$setting, 'addtime'=>SYS_TIME, 'default_style'=>'default', 'category_template'=>'', 'list_template'=>'', 'show_template'=>'', 'type'=>5), true);
$setting = array2string(array('ismenu'=>'1', 'meta_title'=>'品牌正品 商城保障', 'meta_keywords'=>'C2C,购物平台,正品保障', 'meta_description'=>'亚洲最大网上购物网站——打造的在线B2C购物平台（B2C，Business to Customer）。在商城购物，享受100%正品保障、7天退换货、提供发票的服务。加入商城，将为全新的B2C事业创造更多的奇迹。'));
$models[73] = $sitemodel->insert(array('siteid'=>'1', 'name'=>'产品', 'description'=>'', 'tablename'=>'yp_product', 'setting'=>$setting, 'addtime'=>SYS_TIME, 'default_style'=>'default', 'category_template'=>'model_product', 'list_template'=>'list_product', 'show_template'=>'show_product', 'type'=>5), true);
$models[76] = $sitemodel->insert(array('siteid'=>'1', 'name'=>'商机', 'description'=>'', 'tablename'=>'yp_buy', 'setting'=>$setting, 'addtime'=>SYS_TIME, 'default_style'=>'default', 'category_template'=>'model_buy', 'list_template'=>'list_buy', 'show_template'=>'show_buy', 'type'=>5), true);
foreach ($models as $_k => $_m) {
	$sqls = file_get_contents(SQL_PATH.$_k.'.sql');
	$sqls = explode("\n", $sqls);
	foreach ($sqls as $sql) {
		$sql = trim(str_replace('$modelid', $_m, $sql));
		$sitemodel_field->query($sql);
	}
	unset($sqls, $sql);
}
setcache('models', $models, 'yp');
//调用黄页内容数据模型创建新闻、产品、商机表
$yp_content = pc_base::load_model('yp_content_model');
if(file_exists(SQL_PATH.'table.sql')) {
	$sql = file_get_contents(SQL_PATH.'table.sql');
	_sql_execute($sql);
}

function _sql_execute($sql) {
	$r_tablepre = $s_tablepre = '';
	$yp_content = pc_base::load_model('yp_content_model');
    $sqls = _sql_split($sql,$r_tablepre,$s_tablepre);
	if(is_array($sqls)) {
		foreach($sqls as $sql) {
			if(trim($sql) != '') {
				$yp_content->query($sql);
			}
		}
	} else {
		$yp_content->query($sqls);
	}
	return true;
}

function _sql_split($sql) {
	$yp_content = pc_base::load_model('yp_content_model');
	$dbcharset = pc_base::load_config('database','default');
	$dbcharset = $dbcharset['charset'];
	if($yp_content->version() > '4.1' && $dbcharset) {
		$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=".$dbcharset, $sql);
	}

	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query) {
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		$queries = array_filter($queries);
		foreach($queries as $query) {
			$str1 = substr($query, 0, 1);
			if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
		}
		$num++;
	}
	return $ret;
}
?>