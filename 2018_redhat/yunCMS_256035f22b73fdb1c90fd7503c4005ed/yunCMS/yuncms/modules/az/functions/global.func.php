<?php
/**
 *  global.func.php 黄页函数库
 *
 * @copyright			(C) 2005-2010 PHPCMS
 * @license				http://www.phpcms.cn/license/
 * @lastmodify			2011-06-09
 */

	/**
	 *
	 * 统计登录企业会员的新留言提醒  ...
	 */
	function count_new_guestbook($userid){
		if(intval($userid)){
			//查询是否有新留言
			$guestbook_db = pc_base::load_model('yp_guestbook_model');
			$sql = array('userid'=>$userid,'status'=>'0');
	 		$nums = $guestbook_db->count($sql);
			echo $nums;
		}
  	}

  	/**
  	 *
  	 * 获取产品的点评总统计...
  	 * @param $proid
  	 */
  	function get_pro_dainpingall($dianpingid){
  		if(!$dianpingid){
  			return false;
  		}
  		//获取该点评的统计详情
  		$dianping_db = pc_base::load_model('dianping_model');
  		$dianping_array = $dianping_db->get_one(array('dianpingid'=>$dianpingid));
   		if($dianping_array){
  			//有点评总数统计，点评分类ID
  			$dianping_typeid = $dianping_array['dianping_typeid'];
  			$dianping_type_all = getcache('dianping_type','dianping');
   			$dianping_data = $dianping_type_all[$dianping_typeid]['data'];
  			//分隔数据
  			$type_array  = explode('&&',$dianping_data);
  			$i = 1;
  			$str = '';
  			foreach ($type_array as $type){
  				//计算百分比
    			$percent = ($dianping_array['data'.$i]/(5*$dianping_array['dianping_nums']))*100;
  				$str .= $type.'：<dl class="lite-rate ib"><dd style="width:'.$percent.'%"></dd></dl><br/>';
  				$i++;
  			}
  			echo $str;
  		}else {
  			echo $str = '';
  		}
  	}


/**
 *
 * 通过企业会员 userid，获取企业等级 ...
 * @param $userid 会员userid值
 */
function get_company_rank($userid){
	$userid = intval($userid);
	if(!$userid){
		return false;
	}
	$company_db = pc_base::load_model('yp_company_model');
	$company_array = $company_db->get_one(array('userid'=>$userid));
	if(!$company_array){
		return false;
	}
	$company_rank = $company_array['points'];
  	//计算等级，确定显示的图片样式及数量
 	if(!$company_rank || $company_rank<4){
 		return L('total_credit').$company_rank;
 	}else {
 		//定义配置数组
 		$rank_cache = array (
			  1 =>
			  array (
			    'images' => 'yp/b_red_.gif',
			 	'rank' =>
				  array (
					'4-10' => '1',
					'11-40' => '2',
					'41-90' => '3',
					'91-150' => '4',
					'151-250' => '5',
				  ),
			  ),
			  2 =>
			  array (
			    'images' => 'yp/s_blue_.gif',
				'rank' =>
				  array (
					'251-500' => '1',
					'501-1000' => '2',
					'1001-2000' => '3',
					'2001-5000' => '4',
					'5001-10000' => '5',
				  ),
			  ),
			  3 =>
			  array (
			    'images' => 'yp/s_cap_.gif',
				'rank' =>
				  array (
					'10001-20000' => '1',
					'20001-50000' => '2',
					'50001-100000' => '3',
					'100001-200000' => '4',
					'200001-500000' => '5',
				  ),
			  ),
			  4 =>
			  array (
			    'images' => 'yp/b_4_.gif',
				'rank' =>
				  array (
					'500001-1000000' => '1',
					'1000001-2000000' => '2',
					'2000001-5000000' => '3',
					'5000001-10000000' => '4',
					'10000001-90000000' => '5',
				  ),
			  ),
		);
 		if($company_rank >= 4 && $company_rank<= '250'){
 			$now_rank = $rank_cache[1];
		}
		if($company_rank>='251' && $company_rank<='10000'){
			$now_rank = $rank_cache[2];
		}
		if($company_rank>='10001' && $company_rank<= '500000'){
			$now_rank = $rank_cache[3];
		}
		if($company_rank>='500001' && $company_rank<= '90000000'){
			$now_rank = $rank_cache[4];
		}
	 	$images = $now_rank['images'];
	 	foreach ($now_rank['rank'] as $key=>$rank){
	 		$rank_poins = explode('-', $key);
			if($company_rank<=max($rank_poins) && $company_rank>=min($rank_poins)){
				$rank_num = $rank;
			}
		}
		$images_array = explode('.', $images);
		$str = "<img src=".IMG_PATH.$images_array[0].$rank_num.'.'.$images_array[1].">";
		echo $str;
 	}
}

/**
 * 企业库专用 构造筛选URL
 */
function new_structure_filters_url($fieldname,$array=array(),$type = 1,$modelid, $isphp = 0) {
	if(empty($array)) {
 		$array = $_GET;
	} else {
		$array = array_merge($_GET,$array);
	}
	$setting = getcache('yp_setting', 'yp');
	//TODO
	if($setting['enable_rewrite'] == 0) $urlpars .= '&catid={$catid}';
	else $urlpars .= '-{$catid}';

	//伪静态url规则管理，apache伪静态支持9个参数
	if($setting['enable_rewrite'] == 0 || $isphp) $urlrule =APP_PATH.'index.php?m=yp&c=index&a=list_company'.$urlpars.'&page={$page}';
	else $urlrule =APP_PATH.'company-'.$urlpars.'-{$page}.html';

 	//根据get传值构造URL
	if (is_array($array)) foreach ($array as $_k=>$_v) {
		if($_k=='page') $_v=1;
		if($type == 1) if($_k==$fieldname) continue;
		$_findme[] = '/{\$'.$_k.'}/';
		$_replaceme[] = $_v;
	}
     //type 模式的时候，构造排除该字段名称的正则
	if($type==1) $filter = '(?!'.$fieldname.'.)';
	$_findme[] = '/{\$'.$filter.'([a-z0-9_]+)}/';
	$_replaceme[] = '';
	$urlrule = preg_replace($_findme, $_replaceme, $urlrule);
 	return 	$urlrule;
}

/**
 * 构造筛选URL
 */
function yp_filters_url($fieldname,$array=array(),$type = 1,$modelid, $isphp = 0) {
	if(empty($array)) {
		$array = $_GET;
	} else {
		$array = array_merge($_GET,$array);
	}
	$setting = getcache('yp_setting', 'yp');
	//TODO
	$fields = getcache('model_field_'.$modelid,'model');
	if(is_array($fields) && !empty($fields)) {
		ksort($fields);
		foreach ($fields as $_v=>$_k) {
			if($_k['filtertype'] || $_k['rangetype']) {
				if($setting['enable_rewrite'] == 0) $urlpars .= '&'.$_v.'={$'.$_v.'}';
				else $urlpars .= '-{$'.$_v.'}';
			}
		}
	}
	//伪静态url规则管理，apache伪静态支持9个参数
	if($setting['enable_rewrite'] == 0 || $isphp) $urlrule =APP_PATH.'index.php?m=yp&c=index&a=lists&modelid='.$modelid.$urlpars.'&page={$page}';
	else $urlrule =APP_PATH.'yp-list-'.$modelid.$urlpars.'-{$page}.html';
	//根据get传值构造URL
	if (is_array($array)) foreach ($array as $_k=>$_v) {
		if($_k=='page') $_v=1;
		if($type == 1) if($_k==$fieldname) continue;
		$_findme[] = '/{\$'.$_k.'}/';
		$_replaceme[] = $_v;
	}
     //type 模式的时候，构造排除该字段名称的正则
	if($type==1) $filter = '(?!'.$fieldname.'.)';
	$_findme[] = '/{\$'.$filter.'([a-z0-9_]+)}/';
	$_replaceme[] = '';
	$urlrule = preg_replace($_findme, $_replaceme, $urlrule);

	return 	$urlrule;
}

/**
 * 构造筛选时候的sql语句
 */
function yp_filters_sql($modelid) {
	$sql = $fieldname = $min = $max = '';
	$fieldvalue = array();
	$modelid = intval($modelid);
	$model =  getcache('yp_model','model');
	$fields = getcache('model_field_'.$modelid,'model');
	$fields_key = array_keys($fields);
	//TODO

	$sql = '`status` = \'99\'';
	foreach ($_GET as $k=>$r) {
		if(in_array($k,$fields_key) && intval($r)!=0 && ($fields[$k]['filtertype'] || $fields[$k]['rangetype'])) {
			if($fields[$k]['formtype'] == 'linkage') {
				$datas = getcache($fields[$k]['linkageid'],'linkage');
				$infos = $datas['data'];
				if($infos[$r]['arrchildid']) {
					$sql .=  ' AND `'.$k.'` in('.$infos[$r]['arrchildid'].')';
				}
			} elseif($fields[$k]['formtype'] == 'catids') {
				$datas = getcache('category_yp_'.$modelid, 'yp');
				if ($datas[$r]['child']) {
					$sql .= ' AND `'.$k.'` IN('.$datas[$r]['arrchildid'].')';
				} else {
					$sql .= ' AND `'.$k.'`=\''.$r.'\'';
				}
			} elseif($fields[$k]['rangetype']) {
				if(is_numeric($r)) {
					$sql .=" AND `$k` = '$r'";
				} else {
					$fieldvalue = explode('_',$r);
					$min = intval($fieldvalue[0]);
					$max = $fieldvalue[1] ? intval($fieldvalue[1]) : 999999;
					$sql .=" AND `$k` >= '$min' AND  `$k` < '$max'";
				}
			} else {
				$sql .=" AND `$k` = '$r'";
			}
		}
	}
	return $sql;
}

/**
 * 生成分类信息中的筛选菜单
 * @param $field   字段名称
 * @param $modelid  模型ID
 * @param $diyarr 数据包
 * @param $isall 是否显示全部
 */
function yp_filters($field = '',$modelid,$diyarr = array(),$isall = 1) {
	$fields = getcache('model_field_'.$modelid,'model');
	$options = empty($diyarr) ?  explode("\n",$fields[$field]['options']) : $diyarr;
	$field_value = intval($_GET[$field]);
	foreach($options as $_k) {
		$v = explode("|",$_k);
		$k = trim($v[1]);
		$option[$k]['name'] = $v[0];
		$option[$k]['value'] = $k;
		$option[$k]['url'] = yp_filters_url($field,array($field=>$k),2,$modelid);
		$option[$k]['menu'] = $field_value == $k ? '<em>'.$v[0].'</em>' : '<a href='.$option[$k]['url'].'>'.$v[0].'</a>';
	}
	if ($isall) {
		$all['name'] = L('all');
		$all['url'] = yp_filters_url($field,array($field=>''),2,$modelid);
		$all['menu'] = $field_value == '' ? '<em>'.$all['name'].'</em>' : '<a href='.$all['url'].'>'.$all['name'].'</a>';
		array_unshift($option,$all);
	}
	return $option;
}

/**
 * 获取子分类
 * @param $parentid 父级id
 * @param $modelid 模型id
 * @param $self 是否包含本身 0为不包含
 * @param $siteid 站点id
 */
function yp_subcat($parentid = 0, $modelid = 0,$self = '0', $siteid = '') {
	if ($modelid==0) return '';
	static $cat, $categorys;
	if ($cat[$parentid]) {
		$subcat = $cat[$parentid];
	} else {
		if ($categorys[$modelid]) {
			$category = $categorys['$modelid'];
		} else {
			$categorys['$modelid'] = $category = getcache('category_yp_'.$modelid, 'yp');
		}
		if (is_array($category)) {
			foreach($category as $id=>$cat) {
				if($cat['parentid'] == $parentid) $subcat[$id] = $cat;
				if($self == 1 && $cat['catid'] == $parentid && !$cat['child'])  $subcat[$id] = $cat;
			}
		}
		$cat[$parentid] = $subcat;
	}
	return $subcat;
}

/**
 * 将数组转换成筛选数组格式
 * @param array $data 要转换的数组
 * @param string $k 键值
 * @param string $v 值
 */
function array2filter($data = array(), $k, $v) {
	if (empty($data)) return '';
	$result = array();
	foreach ($data as $r) {
		$result[] = trim($r[$k]).'|'.$r[$v];
	}
	return $result;
}

/**
 * 获取附件字段的fields
 * @param intval $catid 分类id
 * @param array $categorys
 */
function get_additional_fields($catid, $categorys = array()) {
	if (empty($categorys) || !$catid) return false;
	$category_db = pc_base::load_model('category_model');
	if ($categorys[$catid]['parentid']) {
		$parent_arr = $parent_addition = array();
		if ($categorys[$catid]['parentid']) {
			$parent_arr = substr($categorys[$catid]['arrparentid'], 2).','.$catid;
			$parent_arr = explode(',', $parent_arr);
		} else {
			$parent_arr[] = $parentid;
		}
		foreach ($parent_arr as $par) {

			$r = $category_db->get_one(array('catid'=>$par), 'additional');
			$r1 = string2array($r['additional']);
			if (!empty($r1)) {
				if (empty($parent_addition)) {
					$parent_addition = $r1;
				} else {
					$parent_addition = array_merge($parent_addition, $r1);
				}
			}
			unset($r, $r1);
		}
	} else {
		$r = $category_db->get_one(array('catid'=>$catid), 'additional');
		$parent_addition = string2array($r['additional']);
	}
	$fields = array();
	if (is_array($parent_addition) && !empty($parent_addition)) {
		$sitemodel_field = pc_base::load_model('sitemodel_field_model');
		$afield = $flag =  '';
		foreach ($parent_addition as $f) {
			$afield .= $flag.'\''.$f.'\'';
			$flag = ',';
		}
		$res = $sitemodel_field->query('SELECT * FROM `phpcms_model_field` WHERE `fieldid` IN ('.$afield.') ORDER BY `listorder` ASC');
        $fields = array();
        $field_arr = $sitemodel_field->fetch_array();
		if (is_array($field_arr) && !empty($field_arr)) {
			foreach ($field_arr as $f) {
				$setting = string2array($f['setting']);
				$fields[$f['field']] = array_merge($f, $setting);
			}
		}
	}
	return $fields;
}
/**
 * get_parent_url 获取当前分类父分类的url地址及当前分类路径
 * $modelid intval 所属模型ID
 * $catid intval 当前分类ID
 * $parentid intval 当前分类的父分类ID
 */
function get_parent_url($modelid, $catid = 0, $parentid = 0) {
	if (!modelid || !$catid) return false;
	$categorys = getcache('category_yp_'.$modelid, 'yp');
	if ($categorys[$catid]['parentid']) {
		$arrparentid = substr($categorys[$catid]['arrparentid'], 2).','.$catid;
		$arrparentid = explode(',', $arrparentid);
	} else {
		$arrparentid[] = $catid;
	}
	$data['title'] = $t = '';
	foreach ($arrparentid as $pc) {
		$data['title'] .= $t.$categorys[$pc]['catname'];
		$t = ' - ';
	}
	$data['url'] = yp_filters_url('catid', array('catid'=>$parentid), 2, $modelid);
	return $data;
}

	/**
	 * new_get_parent_url 企业库专用 获取当前分类父分类的url地址及当前分类路径
	 * $modelid intval 所属模型ID
	 * $catid intval 当前分类ID
	 * $parentid intval 当前分类的父分类ID
	 */
	function new_get_parent_url($modelid, $catid = 0, $parentid = 0) {
		if (!modelid || !$catid) return false;
		$categorys = getcache('category_yp_'.$modelid, 'yp');
		if ($categorys[$catid]['parentid']) {
			$arrparentid = substr($categorys[$catid]['arrparentid'], 2).','.$catid;
			$arrparentid = explode(',', $arrparentid);
		} else {
			$arrparentid[] = $catid;
		}
		$data['title'] = $t = '';
		foreach ($arrparentid as $pc) {
			$data['title'] .= $t.$categorys[$pc]['catname'];
			$t = ' - ';
		}
 		$data['url'] = new_structure_filters_url('catid', array('catid'=>$parentid), 2, $modelid);
 		return $data;
	}

/**
 * yp_show_linkage 获取子联动菜单
 * @param $linkageid 联动菜单ID
 * @param $fieldid 字段名称
 * @param $lid 当前菜单ID
 * @param $modelid 当前模型ID
 */
function yp_show_linkage($linkageid = 0, $fieldid = '', $lid = 0, $modelid = 0) {
	$linkage_db = pc_base::load_model('linkage_model');
	$r = $linkage_db->select(array('keyid'=>$linkageid, 'parentid'=>$lid), 'linkageid, name');
	$data = array();
	if (is_array($r) && !empty($r)) {
		foreach ($r as $d) {
			$data[$d['linkageid']]['title'] = $d['name'];
			$data[$d['linkageid']]['url'] = yp_filters_url($fieldid, array($fieldid=>$d['linkageid']), 2, $modelid);
		}
	}
	return $data;
}

/**
 * linkage_parent_url 获取当前菜单父菜单的url及完成菜单名
 * @param $linkageid 联动菜单ID
 * @param $lid 当前菜单ID
 * @param $filedname 字段名称
 * @param $modelid 当前模型ID
 */
function linkage_parent_url($linkageid = 0, $lid = 0, $fieldname = '', $modelid = 0) {
	$data = array();
	$data['title'] = get_linkage($lid, $linkageid, ' - ', 1);
	if ($data['title']) {
		$datas = getcache($linkageid,'linkage');
		$linkage_parentid = $datas['data'][$lid]['parentid'];
		$data['url'] = yp_filters_url($filedname, array($fieldname=>$linkage_parentid), 2, $modelid);
		return $data;
	} else {
		return '';
	}

}

/**
 * 通过userid取得企业用户信息
 * @param intval $userid 用户ID
 * @param string $fields 搜索的字段
 */
function get_companyinfo($userid, $fields = '*') {
	static $usersinfo;
	$markid = md5($userid.$fields);
	if (!isset($usersinfo[$markid])) {
		$yp_company_db = pc_base::load_model('yp_company_model');
		$usersinfo[$markid] = $yp_company_db->get_one(array('userid'=>$userid), $fields.',status');
	}
	return $usersinfo[$markid];
}

//取得企业库的modelid
function get_company_model() {
	static $modelid;
	if (!isset($modelid)) {
		$sitemodel_db = pc_base::load_model('sitemodel_model');
		$r = $sitemodel_db->get_one(array('tablename'=>'yp_company', 'type'=>4), 'modelid');
		$modelid = intval($r['modelid']);
	}
	return $modelid;
}

function yp_makeurlrule() {
	$setting = getcache('yp_setting', 'yp');
	if($setting['enable_rewrite'] == 0) {
		return url_par('page={$'.'page}');
	}
	else {
		$url = preg_replace('/-[0-9]+.html$/','-{$page}.html',get_url());
		return $url;
	}
}

function yp_makecaturl($url, $city, $multi_city = '1') {
	if($multi_city) {
		if(strpos($url,'.html') === FALSE) {
			return $url.'&city='.$city;
		} else {
			return preg_replace('/(-[0-9]+).html$/i', '-'.$city.'$0', $url);
		}
	} else {
		return $url;
	}
}

//推算商家页面url地址
function compute_company_url($action = '', $array = array()) {
	static $company_db, $com_url;
	if(empty($array)) {
		$array = $_GET;
	} else {
		$array = array_merge($_GET,$array);
	}
	if (!$array['page']) {
		$array['page'] = 1;
	}
	if (!$array['catid']) {
		$array['catid'] = 0;
	}
	if (!$array['modelid']) {
		$array['modelid'] = 0;
	}
	if (!$array['id']) {
		$array['id'] = 0;
	}
	$setting = getcache('yp_setting', 'yp');
	$userid = intval($_GET['userid']);
	if (!$com_url[$userid]) {
		if (!is_object($company_db)) {
			$company_db = pc_base::load_model('yp_company_model');
		}
		$r = $company_db->get_one(array('userid'=>$userid), 'url');
		$user_url = $r['url'];
		if (strpos($user_url, APP_PATH)===false && $user_url) {
			if (substr($user_url, -1, 1)!='/') {
				$com_url[$userid] = $user_url = $user_url.'/';
			} else {
				$com_url[$userid] = $user_url;
			}
		} else {
			$com_url[$userid] = $user_url = APP_PATH;
		}
	} else {
		$user_url = $com_url[$userid];
	}
	//伪静态url规则管理，apache伪静态支持9个参数
	if($setting['enable_rewrite'] == 0) {
		if ($action == 'model') {
			$urlrule =$user_url.'index.php?m=yp&c=com_index&a=model&modelid={$modelid}&catid={$catid}&userid={$userid}&page={$page}';
		} else if ($action == 'show') {
			$urlrule =$user_url.'index.php?m=yp&c=com_index&a=show&modelid={$modelid}&catid={$catid}&id={$id}&userid={$userid}&page={$page}';
		} else {
			$urlrule =$user_url.'index.php?m=yp&c=com_index&a='.$action.'&userid={$userid}&page={$page}';
		}
		if ($array['tid']) {
			$urlrule .= '&tid={$tid}';
		}
	} else {
		$urlrule =$user_url.'web-'.$action.'-{$modelid}-{$catid}-{$id}-{$userid}-{$page}.html';
		if ($array['tid']) $urlrule = $user_url.'web-'.$action.'-{$modelid}-{$catid}-{$id}-{$tid}-{$userid}-{$page}.html';
	}

	//根据get传值构造URL
	if (is_array($array)) foreach ($array as $_k=>$_v) {
		//if($_k=='page') $_v=1;
		if($type == 1) if($_k==$fieldname) continue;
		$_findme[] = '/{\$'.$_k.'}/';
		$_replaceme[] = $_v;
	}
	$urlrule = preg_replace($_findme, $_replaceme, $urlrule);
	return $urlrule;
}

//取得商家的常用分类
function get_compay_catid($modelid, $userid, $limit = 10) {
	$content_db = pc_base::load_model('yp_content_model');
	$content_db->set_model($modelid);
	$data = $content_db->select(array('userid'=>$userid, 'status'=>99), 'COUNT(id) as num, catid', $limit, 'num DESC', 'catid', 'catid');
	return $data;
}

/**
 * 生成流水号
 */
function create_transaction_code(){
	mt_srand((double )microtime() * 1000000 );
	return date("YmdHis" ).str_pad( mt_rand( 1, 99999 ), 5, "0", STR_PAD_LEFT );
}
//获取url
function get_yp_url($type = 'index', $modelid = 0) {
	$setting = getcache('yp_setting', 'yp');
	if ($setting['enable_rewrite']) {
		$enable_rewrite = 1;
	} else {
		$enable_rewrite = 0;
	}

	switch ($type) {
		case 'index':
			if ($enable_rewrite) $url = APP_PATH.'yp.html';
			else $url = APP_PATH.'index.php?m=yp&c=index&a=init';
		break;

		case 'model':
			if ($enable_rewrite) $url = APP_PATH.'yp-model-'.$modelid.'.html';
			else $url = APP_PATH.'index.php?m=yp&c=index&a=model&modelid='.$modelid;
		break;

		case 'company':
			if ($enable_rewrite) $url = APP_PATH.'yp-company-1.html';
			else $url = APP_PATH.'index.php?m=yp&c=index&a=company';
		break;
	}
	return $url;
}

//更新企业主页导航
function company_menu ($companyinfo) {
	static $member_db, $setting, $yp_models, $user_arr;
	if (!$member_db) {
		$member_db = pc_base::load_model('member_model');
	}
	if (!$setting){
		$setting = getcache('yp_setting', 'yp');
	}
	if (!$yp_models) {
		$yp_models = getcache('yp_model', 'model');
	}
	if (!$user_arr) {
		$user_arr = array('about'=>L('company_profile'), 'certificate'=>L('certificate'), 'guestbook'=>L('guestbook'), 'contact'=>L('contact_us'));
	}
	$memberinfo = $member_db->get_one(array('userid'=>$companyinfo['userid']), 'groupid');
	$groupid = $memberinfo['groupid'];
	$update_user = array_flip($user_arr);
	$update_models = $yp_models;
	//计算公司导航菜单及默认模板
	$company_meun = string2array($companyinfo['menu']);
	$userid = $companyinfo['userid'];
	$companyurl = $companyinfo['url'];
	if (strpos($companyurl, APP_PATH)===false && $companyurl) {
		if(substr(trim($companyurl), -1, 1)!='/') {
			$pre_url = $companyurl.'/';
		} else {
			$pre_url = $companyurl;
		}
	}
	foreach ($company_meun['catname'] as $k => $p) {
		if ($p['is_system']) {
			if (in_array($p['id'], $user_arr)) {

				if ($pre_url) {
					if ($setting['enable_rewrite']) {
						$company_meun['catname'][$k]['linkurl'] = $pre_url.'web-'.$update_user[$p['catname']].'----1.html';
					} else {
						$company_meun['catname'][$k]['linkurl'] = $pre_url.'index.php?m=yp&c=com_index&a='.$update_user[$p['catname']].'&userid='.$userid;
					}
				} else {
					if ($setting['enable_rewrite']) {
						$company_meun['catname'][$k]['linkurl'] = APP_PATH.'web-'.$update_user[$p['catname']].'----'.$userid.'-1.html';
					} else {
						$company_meun['catname'][$k]['linkurl'] = APP_PATH.'index.php?m=yp&c=com_index&a='.$update_user[$p['catname']].'&userid='.$userid;
					}
				}
			} elseif ($p['id'] == 'index') {
				if ($pre_url) {
					if ($setting['enable_rewrite']) {
						$company_meun['catname'][$k]['linkurl'] = $pre_url.'web-'.$userid.'.html';
					} else {
						$company_meun['catname'][$k]['linkurl'] = $pre_url.'index.php?m=yp&c=com_index&userid='.$userid;
					}
				} else {
					if ($setting['enable_rewrite']) {
						$company_meun['catname'][$k]['linkurl'] = APP_PATH.'web-'.$userid.'.html';
					} else {
						$company_meun['catname'][$k]['linkurl'] = APP_PATH.'index.php?m=yp&c=com_index&userid='.$userid;
					}
				}
			} else {
				foreach ($yp_models as $mid => $m) {
					if ($mid==$p['id']) {
						if ($pre_url) {
							if ($setting['enable_rewrite']) {
								$company_meun['catname'][$k]['linkurl'] = $pre_url.'web-model-'.$mid.'---1.html';
							} else {
								$company_meun['catname'][$k]['linkurl'] = $pre_url.'index.php?m=yp&c=com_index&a=model&modelid='.$mid.'&userid='.$userid;
							}
						} else {
							if ($setting['enable_rewrite']) {
								$company_meun['catname'][$k]['linkurl'] = APP_PATH.'web-model-'.$mid.'---'.$userid.'-1.html';
							} else {
								$company_meun['catname'][$k]['linkurl'] = APP_PATH.'index.php?m=yp&c=com_index&a=model&modelid='.$mid.'&userid='.$userid;
							}
						}
						unset($update_models[$mid]);
						break;
					}
				}
			}
		}
	}
	if (is_array($update_models) && !empty($update_models)) {
		$vid = max($company_meun['list'])+1;
		$kid = max(array_keys($company_meun['list']))+1;
		foreach ($update_models as $mid =>$m) {
			if ($setting['priv'][$groupid][$mid]) {
				$company_meun['list'][$kid] = $vid;
				if ($pre_url) {
					if ($setting['enable_rewrite']) {
						$company_meun['catname'][$vid] = array('used'=>0, 'id'=>$mid, 'is_system'=>1, 'catname'=>$m['name'], 'linkurl'=>$pre_url.'web-model-'.$mid.'---1.html');
					} else {
						$company_meun['catname'][$vid] = array('used'=>0, 'id'=>$mid, 'is_system'=>1, 'catname'=>$m['name'], 'linkurl'=>$pre_url.'index.php?m=yp&c=com_index&a=model&modelid='.$mid.'&userid='.$userid);
					}
				} else {
					if ($setting['enable_rewrite']) {
						$company_meun['catname'][$vid] = array('used'=>0, 'id'=>$mid, 'is_system'=>1, 'catname'=>$m['name'], 'linkurl'=>APP_PATH.'web-model-'.$mid.'---'.$userid.'-1.html');
					} else {
						$company_meun['catname'][$vid] = array('used'=>0, 'id'=>$mid, 'is_system'=>1, 'catname'=>$m['name'], 'linkurl'=>APP_PATH.'index.php?m=yp&c=com_index&a=model&modelid='.$mid.'&userid='.$userid);
					}
				}
				$kid++;
				$vid++;
			}
		}
	}
	$company_meun = array2string($company_meun);
	return $company_meun;
}

	/**
	 * 分类选择
	 * @param string $file 栏目缓存文件名
	 * @param intval/array $catid 别选中的ID，多选是可以是数组
	 * @param string $str 属性
	 * @param string $default_option 默认选项
	 * @param intval $modelid 按所属模型筛选
	 * @param intval $type 栏目类型
	 * @param intval $onlysub 只可选择子栏目
	 * @param intval $siteid 如果设置了siteid 那么则按照siteid取
	 */
	function select_category($file = '',$catid = 0, $str = '', $default_option = '', $modelid = 0, $type = -1, $onlysub = 0,$siteid = 0) {
		$tree = pc_base::load_sys_class('tree');
		if(!$siteid) $siteid = param::get_cookie('siteid');
		if (!$file) {
			$file = 'category_content_'.$siteid;
		}
		$result = getcache($file,'yp');
		$string = '<select '.$str.'>';
		if($default_option) $string .= "<option value='0'>$default_option</option>";
		if (is_array($result)) {
			foreach($result as $r) {
				if($siteid != $r['siteid'] || ($type >= 0 && $r['type'] != $type)) continue;
				$r['selected'] = '';
				if(is_array($catid)) {
					$r['selected'] = in_array($r['catid'], $catid) ? 'selected' : '';
				} elseif(is_numeric($catid)) {
					$r['selected'] = $catid==$r['catid'] ? 'selected' : '';
				}
				$r['html_disabled'] = "0";
				if (!empty($onlysub) && $r['child'] != 0) {
					$r['html_disabled'] = "1";
				}
				$categorys[$r['catid']] = $r;
				if($modelid && $r['modelid']!= $modelid ) unset($categorys[$r['catid']]);
			}
		}
		$str  = "<option value='\$catid' \$selected>\$spacer \$catname</option>;";
		$str2 = "<optgroup label='\$spacer \$catname'></optgroup>";

		$tree->init($categorys);
		$string .= $tree->get_tree_category(0, $str, $str2);
			
		$string .= '</select>';
		return $string;
	}

//获取待处理订单数量
function get_orders () {
	$userid = param::get_cookie('_userid');
	$order_db = pc_base::load_model('order_model');
	$r = $order_db->get_one(array('uid'=>$userid, 'status'=>0), 'COUNT(*) AS num');
	return $r['num'];
}
?>