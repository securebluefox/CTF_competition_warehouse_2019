<?php
defined('IN_PHPCMS') or exit('No permission resources.');
class yp_tag {
 	private $category_db,$type_db;

	public function __construct() {
		$this->db = pc_base::load_model('yp_content_model');
		$this->position = pc_base::load_model('position_data_model');
		$this->category_db = pc_base::load_model('category_model');
		$this->type_db = pc_base::load_model('type_model');
 	}

	/**
	 * 初始化模型
	 * @param $catid
	 */
	public function set_modelid($modelid) {
		$modelid = intval($modelid);
		if (!$modelid) return false;
		$models = getcache('yp_'model');//读取黄页模型缓存
		$models = array_keys($models);
		if (!in_array($modelid, $models)) return false;
		$this->modelid = $modelid;
		$this->category = getcache('category_yp_'.$modelid,'yp');
		$this->db->set_model($modelid);
		return true;
	}

	/**
	 * 分页统计
	 * @param $data
	 */
	public function count($data) {
		pc_base::load_app_func('global');
		if($data['action'] == 'lists') {
			$modelid = intval($data['modelid']);
			if(!$this->set_modelid($modelid)) return false;
			if(isset($data['where'])) {
				$sql = $data['where'];
			} elseif ($data['catid']) {
				$catid = intval($data['catid']);
				$thumb = intval($data['thumb']) ? " AND thumb != ''" : '';
				if($this->category[$catid]['child']) {
					$catids_str = $this->category[$catid]['arrchildid'];
					$pos = strpos($catids_str,',')+1;
					$catids_str = substr($catids_str, $pos);
					$sql = "status=99 AND catid IN ($catids_str)".$thumb;
				} else {
					$sql = "status=99 AND catid='$catid'".$thumb;
				}
			} else {
				$thumb = intval($data['thumb']) ? " AND thumb != ''" : '';
				$sql = "status=99".$thumb;
			}
			if ($data['userid']) {
				$sql .= ' AND `userid`='.$data['userid'];
			}
			return $this->db->count($sql);
		}
		if($data['action'] == 'get_datas') {
			$modelid = intval($data['modelid']);
			$userid = intval($data['userid']);
  			if(!$this->set_modelid($modelid)) return false;
 			if(!$userid) return false;
 			$sql = " status=99 and userid='".$userid."'";
   			return  $this->db->count($sql);
		}
	   if($data['action'] == 'get_certificate') {
	  	 	$this->yp_certificate_db = pc_base::load_model('yp_certificate_model');
			$userid = intval($data['userid']);
 			if(!$userid) return false;
 			$sql = " status=1 and userid='".$userid."'";
   			return  $this->yp_certificate_db->count($sql);
		}
		if($data['action'] == 'get_guestbook') {
	  	 	$this->yp_guestbook_db = pc_base::load_model('yp_guestbook_model');
			$userid = intval($data['userid']);
 			if(!$userid) return false;
 			$sql = " status=1 and userid='".$userid."'";
   			return  $this->yp_guestbook_db->count($sql);
		}
		if($data['action'] == 'get_company_byfenlei') {
			$modelid = get_company_model();
    		$catid = intval($data['catid']);
 	 		$order = $data['order'];
			$this->yp_relation_db = pc_base::load_model('yp_relation_model');
	 		$sql = get_sql_catid("category_yp_".$modelid,$catid,'yp');
	  		$sql .= " group by userid";
 	  		$array = $this->yp_relation_db->select($sql,'userid');
   	 		return count($array);
		}

	}

	/**
 	 * 取出该分类的详细 信息
  	 * @param $typeid 分类ID
 	 */
 	public function get_fenlei($data){
 		$catid = intval($data['catid']);
 		$order = $data['order'];
 		$this->category_db = pc_base::load_model('category_model');
 		$sql = " " ;
 		$modelid = get_company_model();
   		if(!$catid){
  			$sql = " module='yp' and parentid='0' and modelid='$modelid'";
  		}else {
   			$array = $this->category_db->get_one(array('catid'=>$catid));
 			$sql = " module='yp' and child='0' and modelid='$modelid' and catid in(".$array['arrchildid'].")";
 		}
 		return $this->category_db->select($sql, '*', $data['limit'], $order, '');

 	}


	/**
 	 * 取出该分类下所有公司信息
  	 * @param $typeid 分类ID
 	 */
 	public function get_company_byfenlei($data){
 		pc_base::load_app_func('global');
 		$modelid = get_company_model();
 		$catid = intval($data['catid']);
 		$order = $data['order'];
 		$yp_company_db = pc_base::load_model('yp_company_model');
		$yp_relation_db = pc_base::load_model('yp_relation_model');
 		$sql = get_sql_catid("category_yp_".$modelid,$catid,'yp');
  		$sql .= " group by userid";
 		return $yp_relation_db->select($sql, '*', $data['limit'], $order, '');
 	}

	/**
	 * 列表页标签
	 * @param $data
	 */
	public function lists($data) {
		$modelid = intval($data['modelid']);
		if(!$this->set_modelid($modelid)) return false;
		if(isset($data['where'])) {
			$sql = $data['where'];
		} elseif($data['catid']) {
			$catid = intval($data['catid']);
			$thumb = intval($data['thumb']) ? " AND thumb != ''" : '';
			if($this->category[$catid]['child']) {
				$catids_str = $this->category[$catid]['arrchildid'];
				$pos = strpos($catids_str,',')+1;
				$catids_str = substr($catids_str, $pos);
				$sql = "status=99 AND catid IN ($catids_str)".$thumb;
			} else {
				$sql = "status=99 AND catid='$catid'".$thumb;
			}
		} else {
			$thumb = intval($data['thumb']) ? " AND thumb != ''" : '';
			$sql = "status=99".$thumb;
		}
		if ($data['userid']) {
			$sql .= ' AND `userid`='.$data['userid'];
		}
		$order = $data['order'];

		$return = $this->db->select($sql, '*', $data['limit'], $order, '', 'id');
		//调用副表的数据
		if (isset($data['moreinfo']) && intval($data['moreinfo']) == 1) {
			$ids = array();
			foreach ($return as $v) {
				if (isset($v['id']) && !empty($v['id'])) {
					$ids[] = $v['id'];
				} else {
					continue;
				}
			}
			if (!empty($ids)) {
				$this->db->table_name = $this->db->table_name.'_data';
				$ids = implode('\',\'', $ids);
				$r = $this->db->select("`id` IN ('$ids')", '*', '', '', '', 'id');
				if (!empty($r)) {
					foreach ($r as $k=>$v) {
						if (isset($return[$k])) $return[$k] = array_merge($v, $return[$k]);
					}
				}
			}
		}
		return $return;
	}

	/**
	 * 推荐位
	 * @param $data
	 */
	public function position($data) {
		$sql = '';
		$array = array();
		$posid = intval($data['posid']);
		$order = $data['order'];
		$yp_setting = getcache('yp_setting', 'yp');
		$thumb = (empty($data['thumb']) || intval($data['thumb']) == 0) ? 0 : 1;
		$siteid = $GLOBALS['siteid'] ? $GLOBALS['siteid'] : 1;
		$catid = (empty($data['catid']) || $data['catid'] == 0) ? '' : intval($data['catid']);
		if (!$data['modelid'] && $catid) {
			$r = $this->category_db->get_one(array('catid'=>$catid), 'modelid');
			$modelid = intval($r['modelid']);
			$this->category = getcache('category_yp_'.$modelid, 'yp');
		} elseif ($modelid && !$catid) {
			$this->category = getcache('category_yp_'.$modelid, 'yp');
			if (is_array($this->category) && !empty($this->category)) {
				$catids_str = $t = '';
				foreach ($this->category as $c) {
					if (!$c['child']) {
						$catids_str .= $t.'\''.$c['catid'].'\'';
						$t = ',';
					}
				}
				$sql = "`catid` IN ($catids_str) AND ";
			}
		}

		if($catid && $this->category[$catid]['child']) {
			$catids_str = $this->category[$catid]['arrchildid'];
			$pos = strpos($catids_str,',')+1;
			$catids_str = substr($catids_str, $pos);
			$sql = "`catid` IN ($catids_str) AND ";
		}  elseif($catid && !$this->category[$catid]['child']) {
				$sql = "`catid` = '$catid' AND ";
		}
		if($thumb) $sql .= "`thumb` = '1' AND ";
		if(isset($data['where'])) $sql .= $data['where'].' AND ';
		if(isset($data['expiration']) && $data['expiration']==1) $sql .= '(`expiration` >= \''.SYS_TIME.'\' OR `expiration` = \'0\' ) AND ';
		$sql .= "`posid` = '$posid' AND `siteid` = '".$siteid."'";
		$pos_arr = $this->position->select($sql, '*', $data['limit'],$order);
		if(!empty($pos_arr)) {
			foreach ($pos_arr as $info) {
				$key = $info['catid'].'-'.$info['id'];
				$array[$key] = string2array($info['data']);
				if ($yp_setting['enable_rewrite']) {
					$array[$key]['url'] = APP_PATH.'yp-show-'.$info['catid'].'-'.$info['id'].'.html';
				} else {
					$array[$key]['url'] = APP_PATH.'index.php?m=yp&c=index&a=show&catid='.$info['catid'].'&id='.$info['id'];
				}
				$array[$key]['id'] = $info['id'];
				$array[$key]['catid'] = $info['catid'];
				$array[$key]['listorder'] = $info['listorder'];
			}
		}
		return $array;
	}

	/**
 	 * 获取企业对应模型的数据
  	 * @param $useid 企业urserID
 	 */
 	public function get_datas($data){
 		$userid = intval($data['userid']);
 		if(!$userid){return false;}
  		$order = $data['order'];
  		//设置模型ID
  		$modelid = intval($data['modelid']);
		if(!$this->set_modelid($modelid)) return false;

  		$sql =" userid='".$userid."'";
    	return $this->db->select($sql, '*', $data['limit'], $order, '','id');
 	}

 	/**
 	 *
 	 * 获取企业库公司信息 ...
 	 * @param unknown_type $data
 	 */
	public function get_company($data){
  		$order = $data['order'];
  		$elite = $data['elite'];
 		$this->company_db = pc_base::load_model('yp_company_model');
 		$sql = array('status'=>1);
 		if($elite){
  			$sql = array('status'=>1,'elite'=>$elite);
 		}
  		return $this->company_db->select($sql, '*', $data['limit'], $order, '');
  	}

	/**
 	 *
 	 * 获取企业的证书 ...
 	 * @param unknown_type $data
 	 */
	public function get_certificate($data){
 		$userid = $data['userid'];
		$status = $data['status'];
  		$order = $data['order'];
  		$this->certificate_db = pc_base::load_model('yp_certificate_model');
 		$sql = array('status'=>$status,'userid'=>$userid);
   		return $this->certificate_db->select($sql, '*', $data['limit'], $order, '');
  	}

	/**
 	 *
 	 * 获取企业的留言信息 ...
 	 * @param 配置数组 $data
 	 */
	public function get_guestbook($data){
 		$userid = $data['userid'];
		$status = $data['status'];
		if(!$userid)return false;
  		$order = $data['order'];
 		$this->yp_guestbook_db = pc_base::load_model('yp_guestbook_model');
 		$sql = array('status'=>$status,'userid'=>$userid);
  		return $this->yp_guestbook_db->select($sql, '*', $data['limit'], $order, '');
  	}

    /**
	 * 相关商品标签
	 * @param $data
	 */
	public function relation($data) {
		$modelid = intval($data['modelid']);
		if(!$this->set_modelid($modelid)) return false;
		$order = $data['order'];
		$sql = "`status`=99";
		$limit = $data['id'] ? $data['limit']+1 : $data['limit'];
		if($data['keywords']) {
			$keywords = str_replace('%', '',$data['keywords']);
			$keywords_arr = explode(' ',$keywords);
			$key_array = array();
			$number = 0;
			$i =1;
			foreach ($keywords_arr as $_k) {
				$sql2 = $sql." AND `keywords` LIKE '%$_k%'".(isset($data['id']) && intval($data['id']) ? " AND `id` != '".abs(intval($data['id']))."'" : '');
				$r = $this->db->select($sql2, '*', $limit, '','','id');
				$number += count($r);
				foreach ($r as $id=>$v) {
					if($i<= $data['limit'] && !in_array($id, $key_array)) $key_array[$id] = $v;
					$i++;
				}
				if($data['limit']<$number) break;
			}
		}
		if($data['id']) unset($key_array[$data['id']]);
		return $key_array;
	}

	/**
	 * 排行榜标签
	 * @param $data
	 */
	public function hits($data) {
		$modelid = intval($data['modelid']);
		if(!$this->set_modelid($modelid)) return false;

		$this->hits_db = pc_base::load_model('hits_model');
		$sql = $desc = $ids = '';

		$array = $ids_array = array();
		$order = $data['order'];
		$hitsid = 'c-'.$this->modelid.'-%';
		$sql = "hitsid LIKE '$hitsid'";
		if(isset($data['day'])) {
			$updatetime = SYS_TIME-intval($data['day'])*86400;
			$sql .= " AND updatetime>'$updatetime'";
		}
		if ($catid) {
			if($this->category[$catid]['child']) {
				$catids_str = $this->category[$catid]['arrchildid'];
				$pos = strpos($catids_str,',')+1;
				$catids_str = substr($catids_str, $pos);
				$sql .= " AND catid IN ($catids_str)";
			} else {
				$sql .= " AND catid='$catid'";
			}
		}
		$hits = array();
 		$result = $this->hits_db->select($sql, '*', $data['limit'], $order);
 		foreach ($result as $r) {
			$pos = strpos($r['hitsid'],'-',2) + 1;
			$ids_array[] = $id = substr($r['hitsid'],$pos);
			$hits[$id] = $r;
 		}
		$ids = implode(',', $ids_array);
		if($ids) {
			$sql = "status=99 AND id IN ($ids)";
		} else {
			$sql = '';
		}
 		$result = $this->db->select($sql, '*', $data['limit'],'','','id');
		foreach ($ids_array as $id) {
			if($result[$id]['title']!='') {
				$array[$id] = $result[$id];
				$array[$id] = array_merge($array[$id], $hits[$id]);
			}
		}
		return $array;
	}

	/**
	 * 获取模型分类方法
	 * @param intval $modelid 模型ID
	 * @param string $value 默认选中值
	 * @param intval $id onchange影响HTML的ID
	 *
	 */
	public function get_catids($modelid = 0, $value = '', $id = '') {
		if (!$modelid) return '';
		pc_base::load_app_func('global', 'yp');
		$html = $id ? ' id="catid" onchange="$(\'#'.$id.'\').val(this.value);"' : 'name="catid", id="catid"';
		$cache_field = 'category_yp_'.$modelid;
		return form::select_category($cache_field, $value, $html, L('select_category_id', '', 'yp'));
	}

	/**
	 * pc 标签调用
	 */
	public function pc_tag() {
		$positionlist = getcache('position','commons');
		$sites = pc_base::load_app_class('sites','admin');
		$sitelist = $sites->pc_tag_list();
		foreach ($positionlist as $_v) if($_v['siteid'] == get_siteid() || $_v['siteid'] == 0) $poslist[$_v['posid']] = $_v['name'];

		return array(
			'action'=>array('lists'=>L('list', '', 'yp'),'position'=>L('position'), 'hits'=>L('top', '', 'yp')),
			'lists'=>array(
				'modelid'=>array('name'=>L('model_id', '', 'yp'),'htmltype'=>'select_yp_model','validator'=>array('min'=>1),'ajax'=>array('name'=>L('category_yp', '', 'yp'), 'action'=>'get_catids', 'id'=>'catid')),
				'order'=>array('name'=>L('listorder'), 'htmltype'=>'select','data'=>array('id DESC'=>L('add_time_descending', '', 'yp'), 'updatetime DESC'=>L('edit_time_descending', '', 'yp'), 'listorder ASC'=>L('listorder_ascending', '', 'yp'))),
				'thumb'=>array('name'=>L('thumb_template', '', 'yp'), 'htmltype'=>'radio','data'=>array('0'=>L('unlimited', '', 'yp'), '1'=>L('have_thumb', '', 'yp'))),
				'moreinfo'=>array('name'=>L('used_side_tables', '', 'yp'), 'htmltype'=>'radio', 'data'=>array('1'=>L('yes'), '0'=>L('no'))),
			),
		 	'position'=>array(
				'posid'=>array('name'=>L('position_t_id', '', 'yp'),'htmltype'=>'input_select','data'=>$poslist,'validator'=>array('min'=>1)),
				'modelid'=>array('name'=>L('model_id', '', 'yp'),'htmltype'=>'select_yp_model','validator'=>array('min'=>1),'ajax'=>array('name'=>L('category_yp', '', 'yp'), 'action'=>'get_catids', 'id'=>'catid')),
				'thumb'=>array('name'=>L('thumb_template', '', 'yp'), 'htmltype'=>'radio','data'=>array('0'=>L('unlimited', '', 'yp'), '1'=>L('have_thumb', '', 'yp'))),
				'order'=>array('name'=>L('listorder'), 'htmltype'=>'select','data'=>array('listorder DESC'=>L('listorder_descending', '', 'yp'),'listorder ASC'=>L('listorder_ascending', '', 'yp'),'id DESC'=>L('add_time_descending', '', 'yp'))),
			),
			'hits'=>array(
				'modelid'=>array('name'=>L('position_t_id', '', 'yp'),'htmltype'=>'select_yp_model','validator'=>array('min'=>1),'ajax'=>array('name'=>L('category_yp', '', 'yp'), 'action'=>'get_catids', 'id'=>'catid')),
				'day'=>array('name'=>L('day_select', '', 'content'), 'htmltype'=>'input', 'data'=>array('type'=>0)),
				'order'=>array('name'=>L('listorder'), 'htmltype'=>'select','data'=>array('views DESC'=>L('total_view','','yp'),'yesterdayviews ASC'=>L('yesterday_view', '', 'yp'),'weekviews DESC'=>L('week_view'), 'monthviews DESC'=>L('month_view'))),
			),
		);
	}

}