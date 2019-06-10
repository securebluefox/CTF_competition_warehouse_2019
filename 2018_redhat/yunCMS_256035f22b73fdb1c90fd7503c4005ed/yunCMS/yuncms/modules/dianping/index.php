<?php
defined('IN_PHPCMS') or exit('No permission resources.');
class index {
	protected  $reviewsid, $modules, $siteid, $format;
	function __construct() {
		pc_base::load_app_func('global');
		pc_base::load_sys_class('format', '', 0);
		$this->dianping = pc_base::load_model('dianping_model');
		$this->dianping_data = pc_base::load_model('dianping_data_model');
		//list($this->modules, $contentid, $this->siteid) = decode_reviewsid($this->reviewsid);
		$this->username = param::get_cookie('_username');
		$this->userid = param::get_cookie('_userid');
		$this->siteid = get_siteid();
		define('SITEID', $this->siteid);
	}
	
	/**
	 * 
	 * 默认前台显示 ...
	 */
	public function init() {
		$hot = isset($_GET['hot']) && intval($_GET['hot']) ? intval($_GET['hot']) : 0;
		$siteid =& $this->siteid; 
		$dianpingid = $_GET['dianpingid'];
		//读取类型缓存，供前台调用 
		$dianping_type = $_GET['dianping_type'];
		$type_array = getcache('dianping_type','dianping');
		$module = $_GET['module'];
		$modelid = $_GET['modelid'];
 		$page = $_GET[page];
 		
 		
 		$contentid = $_GET['contentid'];
		if(empty($type_array[$dianping_type])){
			showmessage('请检查后台配置，是否有此项分类！');exit;
		}

		//读取当前点评配置，查看是否允许点评等选项
		$setting = string2array($type_array[$dianping_type]['setting']); 
		$is_checkuserid = $setting['is_checkuserid'];
 		//不允许游客点评，接下来还要再根据传递的参数，判断是否要检测该用户允许点评
 		if($is_checkuserid=='1'){
	 			$comment_relation = pc_base::load_model('comment_relation_model');
				$sql = array("userid"=>$this->userid,'module'=>$module,'contentid'=>$contentid);
	 			$allowdianping_array = $comment_relation->get_one($sql);
	 			if($allowdianping_array){
	 				$is_allowdianping = '1';
	 				$del_id = $allowdianping_array['id'];
	 			}else{
	 				$is_allowdianping = '0';
	 				$dianping_info = '你已经点评此信息，或尚未购买此产品，无法点评！';
	 			}
 	  	}else {
 	  			if($setting['guest']=='1'){
 	  				$is_allowdianping = '1';
  	  			}else{
 	  				if($this->userid){
 	  					$is_allowdianping = '1';
 	  				}else{
 	  					$is_allowdianping = '0';
 	  					$dianping_info ='对不起，不允许游客点评！';
 	  				}
 	  			}
 	  	} 
		
  		pc_base::load_sys_class('form', '', 0);
   		if (isset($_GET['iframe'])) { 
			if ($_GET['iframe'] ==1) {
  				include template('dianping', 'show_list');
			}elseif($_GET['iframe'] =='2') {
				include template('dianping', 'show_milist');
			}
 		}else {
			include template('dianping', 'list');
		}
	}
	
	/**
	 * 
	 * 点评列表页 ...
	 */
	public function dianping_data_list(){
		//获取点评diapinID
		$page = intval($_GET['page']);
 		if($page<=0){
 			$page = 1;
 		}
		$dianpingid = $_GET['dianpingid'];
 		include template('dianping', 'dianping_data_list');
	}

	/**
	 * 
	 * 提交点评 ...
	 */
	public function post(){
   		//点评各项详情
 		if(!is_array($_POST['data'])){
			showmessage('数据来源错误，请检查！',HTTP_REFERER);return false;
 		}
   		
   		$module = $_POST['module'];
 		$modelid = $_POST['modelid'];
 		$dianping_type = intval($_POST['dianping_type']);
 		$dianpingid = $_GET['dianpingid'];
 		$content = new_html_special_chars(iconv('UTF-8',CHARSET,$_POST['content']));
 		$addtime = SYS_TIME;
		
 		$new_array = array();
 		$dianping_type_array = getcache('dianping_type','dianping');
 		
 		//先根据TYPEID，判断是否正常点评
  		$type_setting = string2array($dianping_type_array[$dianping_type]['setting']); 
		$is_checkuserid = $type_setting['is_checkuserid'];
		$is_guest = $type_setting['guest'];
		if(!$is_guest){
			//不允许游客点评
			if(!$this->userid){
				showmessage('此信息必须登录过才能点评！',HTTP_REFERER);return false;
			}
		}
		if($is_checkuserid){
			//要检查会员信息
			$contentid = intval($_GET['contentid']);
			$comment_relation = pc_base::load_model('comment_relation_model');
			$sql = array("userid"=>$this->userid,'module'=>$module,'contentid'=>$contentid);
 			$allowdianping_array = $comment_relation->get_one($sql);
 			if(!$allowdianping_array){
 				showmessage('此信息需检查身份才能点评，请核查你是否被允许点评此信息！',HTTP_REFERER);return false;
 			}
		}
 		
 		//需要重新组合点评详情内容数组
		$post_nums = '0';
 		$dianping_type_data = explode('&&', $dianping_type_array[$dianping_type]['data']);
 		foreach ($_POST['data'] as $key=>$val){
 			$new_array[$dianping_type_data[$key-1]] = $val; 
			$post_nums +=$val;
 		}
		//计算综合得分
		$all_points = count($dianping_type_data)*5;
		$new_array['平均得分'] = round(($post_nums/$all_points)*100);
		
 		$data_array = array2string($new_array); 
  		
		//把各项点评数值，组成数组存入数据库中
  		$insert_data = array('userid'=>$this->userid, 'username'=>$this->username, 'dianpingid'=>$dianpingid,'module'=>$module,'modelid'=>$modelid,'catid'=>$catid,'siteid'=>SITEID,'content'=>$content,'dianping_typeid'=>$dianping_type, 'status'=>'1','is_useful'=>'','data'=>$data_array, 'addtime'=>$addtime);
 		
 		$return_dianpingid = $this->dianping_data->insert($insert_data);
		if(!$return_dianpingid){
			showmessage('点评失败！请检查！',HTTP_REFERER);
		}else {
 			//为注册会员点评，加积分
 			$setting = string2array($dianping_type_array[$dianping_type]['setting']);
 			if($this->userid && $setting['add_point']>0){
 				//点评成功，根据配置，为会员加分
 	 			$member_user_db = pc_base::load_app_class('member_interface','member');
 	 			$member_user_db->add_point($this->userid,$setting['add_point']);
 			}
  			if(intval($_GET['is_checkuserid']) == '1' && intval($_GET['del_id'])){
				//如有检测用户名参数传递过来，提交成功后，要删除对应记录项。comment_relation 表
				$coment_relation = pc_base::load_model('comment_relation_model');
				$sql = array('id'=>intval($_GET['del_id']));
				$coment_relation->delete($sql);
			}
 			
			//入库成功更新v9_dianping 表
			$dianping_data = array();
			$dianping_data['dianpingid'] = $dianpingid;
			$dianping_data['siteid'] = SITEID;
 	 		
			//先查询是否已经存在此数据，如有则更新，无则添加
			$dianping_sql = array('dianpingid'=>$dianpingid);
			$dianping_result = $this->dianping->get_one($dianping_sql);
			if($dianping_result){
				//存在数据，更新之
				$update_data = array();
				foreach ($_POST['data'] as $key=>$val){
	 			$dianping_data['data'.$key] = $val+$dianping_result['data'.$key]; 
	 			}
	 			$dianping_data['dianping_nums'] = $dianping_result['dianping_nums'] + 1;
				$update_where = array('dianpingid'=>$dianpingid);
 				$return_id = $this->dianping->update($dianping_data,$update_where);
 			}else {
				//无数据，新添加之
				foreach ($_POST['data'] as $key=>$val){
		 			$dianping_data['data'.$key] = $val; 
		 		}
		 		$dianping_data['dianping_typeid'] = $dianping_type;
		 		$dianping_data['dianping_nums'] = 1;
 		 		$dianping_data['addtime'] = SYS_TIME;
				$return_id = $this->dianping->insert($dianping_data);
			}
 			if($return_id){
				echo 1;
			}else{
				echo 0;
			}
		}
 	}

}
?>
