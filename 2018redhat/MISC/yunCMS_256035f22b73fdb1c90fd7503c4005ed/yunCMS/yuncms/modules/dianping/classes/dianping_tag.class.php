<?php
/**
 * 
 * @param 点评模版标签
 */

defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
class dianping_tag {
	
	public function __construct() {
		$this->dianping_db = pc_base::load_model('dianping_model');
		$this->dianping_data_db = pc_base::load_model('dianping_data_model');
		$this->dianping_type_db = pc_base::load_model('dianping_type_model');
 	}
	
 	/**
 	 * 
 	 * 列表页 ...
 	 */
 	public function lists($data){
 		$siteid = intval($data['siteid']);
 		$dianpingid = $data['dianpingid'];
 		$status = $data['status'];
 		$is_useful = $data['is_useful'];
 		$order = $data['order'];
 		$where = " dianpingid='$dianpingid'";
  		if($siteid){
 			$where .= $where ?  " AND siteid='$siteid'" : " siteid='$siteid'";
 		}
 		if($status){
 			$where .= $where ?  " AND status='$status'" : " status='$status'";
 		}
 		if($is_useful){
 			$where .= $where ?  " AND is_useful='$is_useful'" : " is_useful='$is_useful'";
 		}
    	return $this->dianping_data_db->select($where, '*', $data['limit'], $order);
 	}
 	
 	/**
 	 * 
 	 * 获取点评总体信息 ...
 	 */
 	public function get_dianping($data){
 		$dianpingid = $data['dianpingid'];
 		$where = array('dianpingid'=>$dianpingid);
   		return $this->dianping_db->get_one($where, '*');
 	}
 	
	/**
 	 * 
 	 * 获取点评类型的信息 ...
 	 */
 	public function get_dianping_type($data){
 		$dianping_typeid = $data['typeid'];
  		$where = array('id'=>$dianping_typeid);
     	return $this->dianping_type_db->get_one($where, '*');
 	}
 	
	/**
 	 * 
 	 * 点评频道排行 ...
 	 */
 	public function hits($data){
 		$where = $data['where'] ? $data['where'] : '';
  		if($data['module']){
  			if($data['catid']){
 				$where .= $where ? " and dianpingid like '".$data['module']."_".$data['catid']."%'" : " dianpingid like '".$data[module]."_".$data[catid]."%'";
 			}else {
 				$where = " dianpingid like '".$data[module]."%'";
 			}
 		}
 		if($data['order']){
 			$where .= " order by ".$data['order'];
 		}
       	return $this->dianping_db->select($where, '*');
 	}
 	
	/**
	 * 计数
	 */
	public function count($data) {
		if(isset($data['where'])) {
			$sql = $data['where'];
		} else {
			$siteid = intval($data['siteid']);
	 		$dianpingid = $data['dianpingid'];
	 		$status = $data['status'];
	 		$is_useful = $data['is_useful'];
	 		$order = $data['order'];
	 		$where = " dianpingid='$dianpingid'";
	  		if($siteid){
	 			$where .= $where ?  " AND siteid='$siteid'" : " siteid='$siteid'";
	 		}
	 		if($status){
	 			$where .= $where ?  " AND status='$status'" : " status='$status'";
	 		}
	 		if($is_useful){
	 			$where .= $where ?  " AND is_useful='$is_useful'" : " is_useful='$is_useful'";
	 		}
	 		return $this->dianping_data_db->count($where); 
		}		 		
	} 
	
	/**
	 * pc 标签调用
	 */
	public function pc_tag() {
		$sites = pc_base::load_app_class('sites','admin');
		$sitelist = $sites->pc_tag_list();
		return array(
			'action'=>array('lists'=>L('list','', 'comment'),'get_vote'=>L('vote_overview','','vote')),
			'lists'=>array(
						'siteid'=>array('name'=>L('site_id', '', 'comment'),'htmltype'=>'input_select', 'data'=>$sitelist,'validator'=>array('min'=>1)),
 						'enabled'=>array('name'=>L('vote_status','','vote'), 'htmltype'=>'select', 'data'=>array('all'=>L('vote_Lockets','','vote'),'1'=>L('vote_use','','vote'), '0'=>L('vote_lock','','vote'))),
						'order'=>array('name'=>L('sort', '', 'comment'), 'htmltype'=>'select','data'=>array('subjectid desc'=>L('subjectid_desc', '', 'vote'), 'subjectid asc'=>L('subjectid_asc', '', 'vote'))),
					),
		    'get_vote'=>array(
						'subjectid'=>array('name'=>L('vote_voteid','','vote'),'htmltype'=>'input', 'validator'=>array('min'=>1)), 
					),
		);
	}
	
}
?>