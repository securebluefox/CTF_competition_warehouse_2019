<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_app_func('global','dianping');//导入程序处理函数
class dianping extends admin {
	function __construct() {
		parent::__construct();
		$this->dianping_type = pc_base::load_model('dianping_type_model');
		$this->dianping_data = pc_base::load_model('dianping_data_model');
		$this->dianping = pc_base::load_model('dianping_model');
		$this->module = pc_base::load_model('module_model');
		pc_base::load_sys_class('form');
  	}
 	
	public function init() {
		//模块数组
		$module_arr = array();
		$modules = getcache('modules','commons');
 		foreach($modules as $module=>$m) $module_arr[$m['module']] = $m['name'];
 		
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->dianping_type->listinfo($where,$order = 'id DESC',$page, $pages = '4');
		$pages = $this->dianping_type->pages; 
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=dianping&c=dianping&a=add_type\', title:\'添加点评类型\', width:\'500\', height:\'350\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', '添加点评类型');
 		include $this->admin_tpl('dianping_type_list');
 	}
 	
	/**
 	 * 
 	 * 点评信息列表 ...
 	 */
 	public function dianping_data() {
 		$where = '';
 		$search = $_GET['search'];
	 	if(!empty($search)){
	 		extract($_GET['search']); 
	 		if($username){
				$where .= $where ?  " AND username='$username'" : " username='$username'";
			}
			if ($module){
				$where .= $where ?  " AND module='$module'" : " module='$module'";
			}
			if($start_time && $end_time) {
				$start = strtotime($start_time);
				$end = strtotime($end_time);
				$where .= "AND `addtime` >= '$start' AND `addtime` <= '$end' ";
			}
  		}
 		
		//按分类浏览
		$typeid = intval($_GET['typeid']);
		if($typeid){
			$where .= $where ?  " AND dianping_typeid='$typeid'" : " dianping_typeid='$typeid'";
		}
 		$default = $module ? $module : '不限模块';//未设定则显示 不限模块 ，设定则显示指定的
 		
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->dianping_data->listinfo($where,$order = 'id DESC',$page, $pages = '8');
		$pages = $this->dianping_data->pages; 
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=dianping&c=dianping&a=add_type\', title:\'添加点评类型\', width:\'500\', height:\'250\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', '添加点评类型');
 		pc_base::load_sys_class('format','', 0);
 		
 		//模块数组
		$module_arr = array();
		$modules = getcache('modules','commons');
 		foreach($modules as $module=>$m) $module_arr[$m['module']] = $m['name'];
		//点评类型数组
		$dianping_type_array = getcache('dianping_type','dianping');
		
 		include $this->admin_tpl('dianping_data_list');
 	}
 	
 	/**
 	 * 
 	 * 删除点评 ...
 	 */
 	public function ajax_checks(){
 		//获取数据
 		$id = intval($_GET['id']);
 		if($id<0){
 			return false;
 		}
 		$type = $_GET['type'];
 		$dianpingid = $_GET['dianpingid'];
 		$result = $this->delete_dianpingdata_update($id);
  		if($result == 1){
 			//同步更新v9_dianping 表，更新数据
 			$where = array('id'=>$id);
 			$dianping_data = $this->dianping_data->get_one($where);
 			$queryid = $this->dianping_data->delete($where);
 			
 			//如果有要扣会员积分
 			$dianping_type_array = getcache('dianping_type','dianping');
 			$setting = string2array($dianping_type_array[$dianping_data['dianping_typeid']]['setting']);
 			$member_user_db = pc_base::load_app_class('member_interface','member');
 	 		$member_user_db->add_point($dianping_data['userid'],'-'.$setting['del_point']);
 			exit('1');
 		}else {
 			exit('0');
 		}
  	}
  	
  	/**
  	 * 
  	 * 删除点评时更新v9_dianping 表 操作 ...
  	 */
  	public function delete_dianpingdata_update($id){
  		$where = array('id'=>$id);
  		$dianping_data = $this->dianping_data->get_one($where);
  		
  		//同步更新v9_dianping 表，更新数据
 		$update = array();
 		$result_data_info = string2array($dianping_data['data']);
 		$i = 1;
   		foreach ($result_data_info as $key=>$val){
	  		$update['data'.$i] = $val; 
	 		$i++;
 		}
  		//取出v9_dianping 表对应数据
 		$result = $this->dianping->get_one(array('dianpingid'=>$dianping_data['dianpingid']));
  		$result_update['dianping_nums'] = $result['dianping_nums'] -1;
  		for($k=1;$k<7;$k++){
 			$result_update['data'.$k] = $result['data'.$k] - $update['data'.$k];
 		}
    	$returnid = $this->dianping->update($result_update,array('id'=>$result['id']));
  		if($returnid){
  			return '1';
  		}
  	}
  	
 	/**
 	 * 
 	 * 点评信息列表 ...
 	 */
 	public function dianping_lists() {
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->dianping_type->listinfo($where,$order = 'id DESC',$page, $pages = '4');
		$pages = $this->dianping_type->pages; 
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=dianping&c=dianping&a=add_type\', title:\'添加点评类型\', width:\'500\', height:\'250\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', '添加点评类型');
 		include $this->admin_tpl('dianping_type_list');
 	}
 	
	/**
	 * 删除点评分类信息  
	 * @param	intval	$id	记录ID，递归删除
	 */
	public function delete_dianping_type() {
  		if(!isset($_POST['dianpingid']) || empty($_POST['dianpingid'])) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['dianpingid'])){
				foreach($_POST['dianpingid'] as $dianpingid_arr) {
					$this->dianping_type->delete(array('id'=>$dianpingid_arr));
				}
				showmessage(L('operation_success'),'?m=dianping&c=dianping');
			}
		} 
	}
	
	/**
	 * 添加点评类型
	 */
	public function add_type() {
		if(isset($_POST['dosubmit'])){ 
			if(empty($_POST['type']['name'])) {
				showmessage('类型名称不能为空',HTTP_REFERER);
			}
			if(empty($_POST['type']['data'])) {
				showmessage('类型数据不能为空',HTTP_REFERER);
			}
			if($_POST['setting']){
				$_POST['type']['setting'] = $_POST['setting'];
			}
			$typeid = $this->dianping_type->insert($_POST['type'],true);
			if(!$typeid) return FALSE;
			//查询配置表，并更新缓存
			$type_cache_array = array();
			$type_array = $this->dianping_type->select();
			if(is_array($type_array)){
				foreach ($type_array as $array){
					$type_cache_array[$array['id']]['type_name'] = $array['name'];
					$type_cache_array[$array['id']]['data'] = $array['data'];
					$type_cache_array[$array['id']]['setting'] = $array['setting'];
				}
			}
			setcache('dianping_type', $type_cache_array, 'dianping');
			
 			showmessage(L('operation_success'),HTTP_REFERER,'', 'add');
 		}else{
 			$show_validator = $show_scroll = $show_header = true; 
			include $this->admin_tpl('dianping_add_type');
		}
 	}
 	
	/**
	 * 生成类型缓存
	 */
	public function do_js() {
			//查询配置表，并更新缓存
			$type_cache_array = array();
			$type_array = $this->dianping_type->select();
			if(is_array($type_array)){
				foreach ($type_array as $array){
					$type_cache_array[$array['id']]['type_name'] = $array['name'];
					$type_cache_array[$array['id']]['data'] = $array['data'];
					$type_cache_array[$array['id']]['setting'] = $array['setting'];
 				}
			}
			setcache('dianping_type', $type_cache_array, 'dianping');
 			showmessage(L('operation_success'),HTTP_REFERER); 
 	}
	
	/**
	 * 修改点评类型
	 */
	public function edit_type() {
		if(isset($_POST['dosubmit'])){ 
			$typeid = intval($_GET['typeid']); 
			if($typeid < 1) return false;
 			if((!$_POST['type']['name']) || empty($_POST['type']['name'])) return false;
 			if((!$_POST['type']['data']) || empty($_POST['type']['data'])) return false;
  			if($_POST['setting']){
 				$_POST['type']['setting'] = array2string($_POST['setting']);
 			}
			$this->dianping_type->update($_POST['type'],array('id'=>$typeid));
			//更新缓存
			$type_cache_array = array();
			$type_array = $this->dianping_type->select();
			if(is_array($type_array)){
				foreach ($type_array as $array){
					$type_cache_array[$array['id']]['type_name'] = $array['name'];
					$type_cache_array[$array['id']]['data'] = $array['data'];
					$type_cache_array[$array['id']]['setting'] = $array['setting'];
 				}
			}
			setcache('dianping_type', $type_cache_array, 'dianping');
 			showmessage(L('operation_success'),'?m=dianping&c=dianping&a=init','', 'edit');
 		}else{
 			$show_validator = $show_scroll = $show_header = true;
			//解出分类内容
			$info = $this->dianping_type->get_one(array('id'=>$_GET['dianpingid']));
			if(!$info) showmessage('该点评类型不存在！');
			extract($info);
			$setting = string2array($info['setting']);
			include $this->admin_tpl('dianping_type_edit');
		}
 	}
 	
	
	/**
	 * 调取代码
	 * 
	 */ 
 	public function public_call() {
  		$_GET['typeid'] = intval($_GET['typeid']);
		if(!$_GET['typeid']) showmessage('请正确选择调取代码！', HTTP_REFERER, '', 'call');
		$r = $this->dianping_type->get_one(array('id'=>$_GET['typeid']));
		include $this->admin_tpl('dianping_call');
	}
	 
}
?>