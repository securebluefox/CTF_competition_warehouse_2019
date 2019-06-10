<?php 
defined('IN_PHPCMS') or exit('Access Denied');
defined('UNINSTALL') or exit('Access Denied');

$yp_models = getcache('yp_model', 'model');
$yp_content_db = pc_base::load_model('yp_content_model');
$model_db = pc_base::load_model('sitemodel_model');
if (is_array($yp_models)) {
	foreach ($yp_models as $mid => $_m) {
		$yp_content_db->set_model($mid);
		$table_name = $yp_content_db->table_name;
		$yp_content_db->query("DROP TABLE IF EXISTS `$table_name`");
		$table_name_data = $table_name.'_data';
		$yp_content_db->query("DROP TABLE IF EXISTS `$table_name_data`");
		$model_db->delete(array('modelid'=>$mid));
	}
}
?>