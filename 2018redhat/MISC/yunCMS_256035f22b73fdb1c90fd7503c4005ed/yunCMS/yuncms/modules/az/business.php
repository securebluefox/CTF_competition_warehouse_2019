<?php
/**
 * 企业会员后台操作类。完善公司信息，发布、修改信息等
 */

defined('IN_PHPCMS') or exit('No permission resources.');
define('CACHE_MODEL_PATH', CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_class('common');
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('global','yp');
class business extends common {

	function __construct() {
		parent::__construct();
		$company = $this->memberinfo;
		define('COMPANY_URL', $company_memberinfo['url']);
   	}

	//商业用户中心首页
	public function init () {
		$memberinfo = $this->memberinfo;
		$setting = $this->setting;
		$groups = getcache('grouplist', 'member');
		$yp_models = getcache('yp_model', 'model');
		$groupid = param::get_cookie('_groupid');
		$memberinfo['groupname'] = $groups[$memberinfo[groupid]]['name'];

   		include template ('yp', 'company_init');
	}

	//注册为企业用户
	public function register() {
		if (isset($_POST['dosubmit'])) {
			$userid = $this->_userid;
			if (!isset($_POST['info']['catids']) || empty($_POST['info']['catids'])) {
				showmessage(L('please_select_com_type'), 'index.php?m=yp&c=business&a=register&t=3');
			} else {
				$catids = $_POST['info']['catids'];
			}
			$modelid = $_POST['modelid'];
			$info = new_addslashes($_POST['info']);
			//加载黄页模块配置
			$yp_setting = getcache('yp_setting', 'yp');
			require_once CACHE_MODEL_PATH.'yp_input.class.php';
			$yp_input = new yp_input($modelid);
			$inputinfo = $yp_input->get($info);
			$data = $inputinfo['system'];
			$data['status'] = $yp_setting['ischeck'] ? 0 : 1;
			$data['regtime'] = SYS_TIME;
			$data['userid'] = $userid;
			//将企业库字段添加到关系数据表
			$this->company_db->insert($data);
			$yp_relation_db = pc_base::load_model('yp_relation_model');
			foreach ($catids as $c) {
				$yp_relation_db->insert(array('userid'=>$userid, 'catid'=>$c));
			}
			//计算公司导航菜单及默认模板
			$yp_models = getcache('yp_model', 'model');
			$menu_user = array();
			$user_arr = array('about'=>L('company_profile'), 'certificate'=>L('certificate'), 'guestbook'=>L('guestbook'), 'contact'=>L('contact_us'));
			$n = 1;
			if ($this->setting['enable_rewrite']) {
				$menu_user['list'][$n] = $n;
				$menu_user['catname'][$n] = array('used'=>1, 'id'=>'index', 'is_system'=>1, 'catname'=>L('first'), 'linkurl'=>APP_PATH.'web-'.$userid.'.html');
				$n++;
				foreach ($yp_models as $mid => $ym) {
					if ($this->setting['priv'][$this->groupid][$mid]) {
						$menu_user['list'][$n] = $n;
						$menu_user['catname'][$n] = array('used'=>1, 'id'=>$mid, 'is_system'=>1, 'catname'=>$ym['name'], 'linkurl'=>APP_PATH.'web-model-'.$mid.'---'.$userid.'-1.html');
						$n++;
					}
				}
				foreach ($user_arr as $_k => $_v) {
					$menu_user['list'][$n] = $n;
					$menu_user['catname'][$n] = array('used'=>1, 'id'=>$_k, 'is_system'=>1, 'catname'=>$_v, 'linkurl'=>APP_PATH.'web-'.$_k.'----'.$userid.'-1.html');
					$n++;
				}
			} else {
				$menu_user['list'][$n] = $n;
				$menu_user['catname'][$n] = array('used'=>1, 'id'=>'index', 'is_system'=>1, 'catname'=>L('first'), 'linkurl'=>APP_PATH.'index.php?m=yp&c=com_index&userid='.$userid);
				$n++;
				foreach ($yp_models as $mid => $ym) {
					if ($this->setting['priv'][$this->groupid][$mid]) {
						$menu_user['list'][$n] = $n;
						$menu_user['catname'][$n] = array('used'=>1, 'id'=>$mid, 'is_system'=>1, 'catname'=>$ym['name'], 'linkurl'=>APP_PATH.'index.php?m=yp&c=com_index&a=model&modelid='.$mid.'&userid='.$userid);
						$n++;
					}
				}
				foreach ($user_arr as $_k => $_v) {
					$menu_user['list'][$n] = $n;
					$menu_user['catname'][$n] = array('used'=>1, 'id'=>$_k, 'is_system'=>1, 'catname'=>$_v, 'linkurl'=>APP_PATH.'index.php?m=yp&c=com_index&a='.$_k.'&userid='.$userid);
					$n++;
				}
			}
			//获取默认模板
			$template_config_file = PC_PATH.'templates'.DIRECTORY_SEPARATOR.pc_base::load_config('system', 'tpl_name').DIRECTORY_SEPARATOR.'yp'.DIRECTORY_SEPARATOR.'companytplnames.php';
			if (file_exists($template_config_file)) {
				$companytplnames = include $template_config_file;
				if (is_array($companytplnames) && !empty($companytplnames)) {
					foreach ($companytplnames as $k => $tpl) {
						if ($tpl['defaulttpl']) $default_tpl_dir = $tpl['dir'];
						break;
					}
					$memberinfo = $this->memberinfo;
				} else {
					$default_tpl_dir = 'com_default';
				}
			} else {
				$default_tpl_dir = 'com_default';
			}
			$menu_user = array2string($menu_user);
			if ($yp_setting['enable_rewrite']) {
				$url = APP_PATH.'web-'.$userid.'.html';
			} else {
				$url = APP_PATH.'index.php?m=yp&c=com_index&userid='.$userid;
			}
			$this->company_db->update(array('menu'=>$menu_user, 'tplname'=>$default_tpl_dir, 'url'=>$url), array('userid'=>$userid));
			if ($data['status']) {
				showmessage(L('registration_successful'), APP_PATH.'index.php?m=yp&c=business&a=init&t=3');
			} else {
				showmessage(L('registration_successful'), APP_PATH.'index.php?m=yp&c=business&a=init&t=3');
			}
		} else {
			$sitemodel_db = pc_base::load_model('sitemodel_model');
			$r = $sitemodel_db->get_one(array('type'=>4), 'modelid');
			$modelid = $r['modelid'];

			require CACHE_MODEL_PATH.'yp_form.class.php';
			$yp_form = new yp_form($modelid);
			$forminfos_data = $yp_form->get();
			$forminfos = array();
			foreach($forminfos_data as $_fk=>$_fv) {
				if($_fv['isomnipotent']) continue;
				if($_fv['formtype']=='omnipotent') {
					foreach($forminfos_data as $_fm=>$_fm_value) {
						if($_fm_value['isomnipotent']) {
							$_fv['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$_fv['form']);
						}
					}
				}
				$forminfos[$_fk] = $_fv;
			}
			$formValidator = $content_form->formValidator;
			$show_validator = true;
			header("Cache-control: private");
			include template('yp', 'register');
		}
	}

	//商家注册信息、修改资料、修改logo等
	public function company () {
		$action = isset($_GET['action']) ? $_GET['action'] : '';
		$memberinfo = $this->memberinfo;
		switch ($action) {
			//修改商家资料
			case 'info':
				//加载会员分类对应库数据模型
				$yp_relation_db = pc_base::load_model('yp_relation_model');
				if (isset($_POST['dosubmit'])) {
					$modelid = intval($_POST['modelid']);
		 			$info = $_POST['info'];
		 			$catids = $info['catids'];
		 			require_once CACHE_MODEL_PATH.'yp_input.class.php';
		 			$yp_input = new yp_input($modelid);
					$inputinfo = $yp_input->get($info);
					$data = $inputinfo['system'];
					$this->company_db->update($data, array('userid'=>$this->_userid));
					//删除以前的对应关系，重新记录对应关系
					$yp_relation_db->delete(array('userid'=>$this->_userid));
		 			foreach ($catids as $c) {
						$yp_relation_db->insert(array('userid'=>$this->_userid, 'catid'=>$c));
					}
					showmessage(L('operation_success'), APP_PATH.'index.php?m=yp&c=business&a=init&t=3');
				}
			break;

			//修改商家基本信息
			case 'logo':
				if (isset($_POST['dosubmit'])) {
					if (!$_POST['info']['logo']) showmessage(L('upload_logo'), APP_PATH.'index.php?m=yp&c=business&a=company&action=logo');
                    $info = array();
					$info['logo'] = $_POST['info']['logo'];
		 			$info['banner'] = $_POST['info']['banner'];
		 			$info['linkman'] = $_POST['info']['linkman'];
		 			$info['email'] = $_POST['info']['email'];
		 			$info['telephone'] = $_POST['info']['telephone'];
		 			$info['introduce'] = $_POST['info']['introduce'];
		 			$info['fax'] = $_POST['info']['fax'];
		 			$info['zip'] = $_POST['info']['zip'];
					$this->company_db->update($info, array('userid'=>$this->_userid));
					showmessage(L('operation_success'), APP_PATH.'index.php?m=yp&c=business&a=company&action=logo&t=3');
				} else {
					$memberinfo = $this->memberinfo;
					$show_validator = 1;
					include template('yp', 'company_info_logo');
				}
			break;

			default:
				$yp_relation_db = pc_base::load_model('yp_relation_model');
				$sitemodel_db = pc_base::load_model('sitemodel_model');
				$r = $sitemodel_db->get_one(array('type'=>4), 'modelid');
				$modelid = $r['modelid'];
				$catids = ',';

				$res = $yp_relation_db->select(array('userid'=>$this->_userid), 'catid');
				if (is_array($res) && !empty($res)) {
					foreach ($res as $c) {
						$catids .= $c['catid'].',';
					}
				}
				require CACHE_MODEL_PATH.'yp_form.class.php';
				$data = $this->company_db->get_one(array('userid'=>$this->_userid));
				$data['catids'] = $catids;
				$yp_form = new yp_form($modelid);
				$forminfos_data = $yp_form->get($data);
				$forminfos = array();
					foreach($forminfos_data as $_fk=>$_fv) {
						if($_fv['isomnipotent']) continue;
						if($_fv['formtype']=='omnipotent') {
							foreach($forminfos_data as $_fm=>$_fm_value) {
								if($_fm_value['isomnipotent']) {
									$_fv['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$_fv['form']);
								}
							}
						}
						$forminfos[$_fk] = $_fv;
					}
				$show_validator = 1;
				include template('yp', 'company_info');
			break;
		}
	}

	//商家模板选择
	public function template() {
		$template_config_file = PC_PATH.'templates'.DIRECTORY_SEPARATOR.pc_base::load_config('system', 'tpl_name').DIRECTORY_SEPARATOR.'yp'.DIRECTORY_SEPARATOR.'companytplnames.php';
		if (file_exists($template_config_file)) {
			$companytplnames = include $template_config_file;
			if (is_array($companytplnames) && !empty($companytplnames)) {
				/**
				foreach ($companytplnames as $k => $tpl) {
					$allow_group = explode(',', $tpl['groups']);
					if (!in_array($this->memberinfo['groupid'], $allow_group)) {
						unset($companytplnames[$k]);
					}
				}
				**/
				$memberinfo = $this->memberinfo;
				include template ('yp', 'company_template');
			} else {
				showmessage(L('system_error'), APP_PATH.'index.php?m=yp&c=business&a=init&t=3');
			}
		} else {
			showmessage(L('system_error'), APP_PATH.'index.php?m=yp&c=business&a=init&t=3');
		}
	}

	//设置默认模板
	public function set_default_tpl() {
		$tplname = $_GET['tplname'];
		$name = $_GET['name'];
		$template_config_file = PC_PATH.'templates'.DIRECTORY_SEPARATOR.pc_base::load_config('system', 'tpl_name').DIRECTORY_SEPARATOR.'yp'.DIRECTORY_SEPARATOR.'companytplnames.php';
		if (file_exists($template_config_file)) {
			$companytplnames = include $template_config_file;
			if (is_array($companytplnames) && !empty($companytplnames)) {

				foreach ($companytplnames as $k => $tpl) {
					if ($tpl['dir']!=$tplname) continue;
					$allow_group = explode(',', $tpl['groups']);
					if (!in_array($this->memberinfo['groupid'], $allow_group)) {
						showmessage(L('unlimited_use_template'), APP_PATH.'index.php?m=member&c=index&a=account_manage_upgrade&t=1');
					} else {
						$this->company_db->update(array('tplname'=>$tplname), array('userid'=>$this->_userid));
						showmessage(L('successfully_enabled').'\''.$name.'\'', APP_PATH.'index.php?m=yp&c=business&a=init&t=3');
					}
				}
			} else {
				showmessage(L('system_error'), APP_PATH.'index.php?m=yp&c=business&a=init&t=3');
			}
		} else {
			showmessage(L('system_error'), APP_PATH.'index.php?m=yp&c=business&a=init&t=3');
		}
	}

	//预览模板
	public function preview_template() {
		$tplname = $_GET['tplname'];
		$array = get_companyinfo($this->_userid);
		$this->default_tpl = 'yp/'.$array['tplname'];
		define('TEMPLATE_PATH', APP_PATH.'statics/'.$this->default_tpl.'/');
		$default_tpl = 'yp/'.$tplname;
		include template($default_tpl, 'index');
	}

	/**
	 *
	 * 前台菜单管理 ...
	 */
	public function menu(){
		$userid = $this->_userid;
		if($_POST['submit']){
			$new = array();
 			$new_list = $_POST['list'];
 			$list_catid = $_POST['list_catid'];
 			//检测是否重复排序值
			if (count($new_list) != count(array_unique($new_list))){
			 	showmessage(L('repeating_sort'),HTTP_REFERER);
			}
			foreach ($new_list as $k=>$list){
 				$new[$list] = $list_catid[$k];//新的排序数组
			}
			$array = array();
			$array['list'] = $new;
			$array['catname'] = $_POST['catname'];
			//如有新增加导航
  			if($_POST['new_catname']) {
  				//新的排序值，为现有排序最大值+1，这防止了删除一个中间自定义菜单，再新增加一个菜单，出现排序ID值相同的情况
  				$new_catname_num = max(array_keys($array['list'])) +1;
  				//取当前菜单catname，下标最大值，赋值给新加的菜单选项，以作对应
  				$new_catname_key = max(array_keys($array['catname'])) +1 ;
   				$array['list'][$new_catname_num] =  $new_catname_key;
   				$array['catname'][$new_catname_key]['catname'] = $_POST['new_catname'];
				$array['catname'][$new_catname_key]['linkurl'] = $_POST['new_linkurl'];
				$array['catname'][$new_catname_key]['used'] = 1;
				$array['catname'][$new_catname_key]['is_system'] = '0';
			}
 			$menustring = array2string($array);
 			$this->company_db->update(array('menu'=>$menustring),array('userid'=>$this->_userid));
			showmessage(L('operation_success'),HTTP_REFERER);
 		}else{
			$array = $this->company_db->get_one(array('userid'=>$userid));
			$menu = company_menu($array);
			$this->company_db->update(array('menu'=>$menu), array('userid'=>$userid)); //首先更新一下商户菜单

			$array = $this->company_db->get_one(array('userid'=>$userid));
			if($array['menu']){
				$menus = string2array($array['menu']);
				ksort($menus['list']);
 			}
 			$menu_num = count($menus['list']);//获取菜单数量
 	   		include template ('yp', 'menu_manage');
		}

 	}

	/**
	 *
	 * 收藏管理 ...
	 */
	public function collect() {
		//加载收藏数据库模型
		$yp_collect = pc_base::load_model('yp_collect_model');
		$action = isset($_GET['action']) ? $_GET['action'] : 'list';
		switch ($action) {
			//收藏列表
			case 'list':
				$where = array('userid'=>$this->_userid);
		 		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
				$infos = $yp_collect->listinfo($where,$order = 'id desc',$page, $pages = '9');
				$pages = $yp_collect->pages;
				include template('yp', 'company_collect_list');
			break;

			//删除收藏
			case 'delete':
				if(count($_POST['id'])==0){
					showmessage(L('illegal_parameters'), HTTP_REFERER);
				}
		  		if(is_array($_POST['id'])){
					foreach($_POST['id'] as $ids) {
			 			$yp_collect->delete(array('id'=>$ids));
					}
					showmessage(L('operation_success'),HTTP_REFERER);
				}
			break;
		}
	}



	/**
	 *
	 * 公司留言管理 ...
	 */
	public function guestbook() {
		//加载企业留言数据库模型
		$yp_guestbook = pc_base::load_model('yp_guestbook_model');
		$action = isset($_GET['action']) ? $_GET['action'] : 'list';
		switch ($action) {
			//留言列表
			case 'list':
 				$status = $_GET['status'];
 				$where = array('userid'=>$this->_userid,'status'=>$status);
				$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
				$infos = $yp_guestbook->listinfo($where,$order = 'gid desc',$page, $pages = '9');
				$pages = $yp_guestbook->pages;
				include template('yp', 'company_guestbook_list');
			break;

			//留言回复
			case 'edit':
				if($_POST['dosubmit']){
					$gid = intval($_GET['gid']);
					if(!$gid) {
						showmessage(L('illegal_parameters'), APP_PATH.'index.php?m=yp&c=business&a=guestbook&action=list');
					}
 					$where = array('status'=>1);
 					$yp_guestbook->update($where,array('gid'=>$gid,'userid'=>$this->_userid));
 			  		showmessage(L('operation_success'),HTTP_REFERER);
				}else{
					$gid = $_GET['gid'];
					$result = $yp_guestbook->get_one(array('gid'=>$gid,'userid'=>$this->_userid));
					if($result){
						extract($result);
					}else{
						showmessage(L('failed_properly_obtain'),HTTP_REFERER);
					}
 					include template('yp', 'company_guestbook_edit');
				}
			break;

			//删除留言
			case 'delete':
				if(count($_POST['gid'])==0){
					showmessage(L('operation_success'), HTTP_REFERER);
				}
		  		if(is_array($_POST['gid'])){
					foreach($_POST['gid'] as $ids) {
			 			$yp_guestbook->delete(array('gid'=>$ids,'userid'=>$this->_userid));
					}
					showmessage(L('operation_success'),HTTP_REFERER);
				}
			break;
		}
	}

	//公司资质证书管理：添加、修改、删除
	public function certificate() {
		//加载资质证书数据库模型
		$yp_certificate = pc_base::load_model('yp_certificate_model');
		$action = isset($_GET['action']) ? $_GET['action'] : 'list';
		switch ($action) {
			//资质列表
			case 'list':
				$where = array('userid'=>$this->_userid);
		 		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
				$infos = $yp_certificate->listinfo($where,$order = 'id desc',$page, $pages = '9');
				$pages = $yp_certificate->pages;
				include template('yp', 'company_certificate_list');
			break;

			//添加资质
			case 'add':
			if($_POST['dosubmit']){
				if(is_array($_POST['info'])) {
					$info = array();
					if(!$_POST['info']['name']) {
						showmessage(L('input_certificate_title'), APP_PATH.'index.php?m=yp&c=business&a=certificate&action=add&t=3');
					}
					if(!$_POST['info']['thumb']) {
						showmessage(L('certificate_thumb_empty'), APP_PATH.'index.php?m=yp&c=business&a=certificate&action=add&t=3');
					}
					if(!$_POST['info']['organization']) {
						showmessage(L('input_organization_title'), APP_PATH.'index.php?m=yp&c=business&a=certificate&action=add&t=3');
					}
					$info['name'] = $_POST['info']['name'];
					$info['thumb'] = $_POST['info']['thumb'];
					$info['organization'] = $_POST['info']['organization'];
					$info['userid'] = $this->_userid;
					$info['status'] = 1;
					$info['addtime'] = strtotime($_POST['info']['addtime']);
					$info['endtime'] = strtotime($_POST['info']['endtime']);
					//将资质证书信息入库
					$returnid = $yp_certificate->insert($info,true);
					if(!$returnid) showmessage(L('operation_failure'), APP_PATH.'index.php?m=yp&c=business&a=certificate&action=add&t=3');
					showmessage(L('operation_success'),HTTP_REFERER);
				}
			} else {
   				include template('yp', 'company_certificate_add');
			}
			break;

			//资质证书修改
			case 'edit':
				if($_POST['dosubmit']){
					$id = intval($_GET['id']);
					if(!$id) {
						showmessage(L('illegal_parameters'), APP_PATH.'index.php?m=yp&c=business&a=certificate&action=list&t=3');
					}
					if(!is_array($_POST['info'])) {
						showmessage(L('operation_failure'), APP_PATH.'index.php?m=yp&c=business&a=certificate&action=edit&id='.$id.'&t=3');
					}
					$info = array();
					if(!$_POST['info']['name']) {
						showmessage(L('input_certificate_title'), APP_PATH.'index.php?m=yp&c=business&a=certificate&action=add&t=3');
					}
					if(!$_POST['info']['thumb']) {
						showmessage(L('certificate_thumb_empty'), APP_PATH.'index.php?m=yp&c=business&a=certificate&action=add&t=3');
					}
					if(!$_POST['info']['organization']) {
						showmessage(L('input_organization_title'), APP_PATH.'index.php?m=yp&c=business&a=certificate&action=add&t=3');
					}
					$info['name'] = $_POST['info']['name'];
					$info['thumb'] = $_POST['info']['thumb'];
					$info['organization'] = $_POST['info']['organization'];
					$info['userid'] = $this->_userid;
					$info['status'] = 1;
					$info['addtime'] = strtotime($_POST['info']['addtime']);
					$info['endtime'] = strtotime($_POST['info']['endtime']);
		 	  		$yp_certificate->update($info,array('id'=>$id,'userid'=>$this->_userid));
			  		showmessage(L('operation_success'),HTTP_REFERER);
				}else{
					$id = $_GET['id'];
					$result = $yp_certificate->get_one(array('id'=>$id,'userid'=>$this->_userid));
					if($result){
						extract($result);
					}else {
						showmessage(L('failed_properly_certificate'),HTTP_REFERER);
					}
 					include template('yp', 'company_certificate_edit');
				}
			break;

			//修改资质证书信息
			case 'delete':
				if(count($_POST['id'])==0){
					showmessage(L('illegal_parameters'), HTTP_REFERER);
				}
		  		if(is_array($_POST['id'])){
					foreach($_POST['id'] as $ids) {
			 			$yp_certificate->delete(array('id'=>$ids,'userid'=>$this->_userid));
					}
					showmessage(L('operation_success'),HTTP_REFERER);
				}
			break;
		}
	}
	//地图标注
	public function map() {
		if (isset($_POST['dosubmit'])) {
			$maplever = $_POST['map'];
			$this->company_db->update(array('map'=>$maplever), array('userid'=>$this->_userid));
			showmessage(L('marked_complete'), APP_PATH.'index.php?m=yp&c=business&a=map&t=3');
		} else {
			$memberinfo = $this->memberinfo;
			$map = $memberinfo['map'];
			list($lng, $lat, $ZoomLevel) = explode('|', $map);
			include template('yp', 'company_map');
		}
	}
	//订单处理
	public function pay() {
		$o = pc_base::load_app_class('order');
		$status = isset($_GET['status']) ? intval($_GET['status']) : '';
		$data = $o->listinfo($this->_userid, $status);
		$pages = $o->pages;
		include template('yp', 'company_order');
	}
	//查看订单详情
	public function check_pay() {
        $id = intval($_GET['id']);
        if (!$id) {
            showmessage(L('illegal_parameters'));
        }
        $o = pc_base::load_app_class('order');
        if (isset($_POST['dosubmit'])) {
            $o->update($id, $_POST['info']);
            showmessage(L('operation_success'), APP_PATH.'index.php?m=yp&c=business&a=pay&t=3');
        } else {
            $info = $o->get($id);
            include template ('yp', 'company_check_order');
        }
	}

	//黄页模型信息列表、添加、修改、删除
	public function content() {
		$modelid = intval($_GET['modelid']);
		if (!$modelid) showmessage(L('illegal_parameters'), APP_PATH.'index.php?m=yp&c=business&a=init&t=3');
		$action = $_GET['action'] ? $_GET['action'] : 'list';
		$content_db = pc_base::load_model('yp_content_model');
		$content_db->set_model($modelid);
		$CATEGORYS = getcache('category_yp_'.$modelid, 'yp');
		switch ($action) {
			case 'list':
				$page = max(intval($_GET['page']), 1);
				$pagesize = 20;
				$where = '';
				if (isset($_GET['status']) && $_GET['status']==99) {
                    $where['userid'] = $this->_userid;
					$where['status'] = 99;
				} elseif (isset($_GET['posids']) && $_GET['posids']) {
                    $where['userid'] = $this->_userid;
					$where['posids'] = 1;
				} else {
                    $where = "`userid` = $this->_userid AND `status`!=99";
                }
				if (isset($_GET['catid']) && $CATEGORYS[$_GET['catid']]) {
					$catid = intval($_GET['catid']);
					if (is_array($where)) {
						$where['catid'] = $catid;
					} else {
						$where .= ' AND `catid`='.$catid;
					}
				}
				$datas = $content_db->listinfo($where, 'id DESC', $page, $pagesize);
				$pages = $content_db->pages;
				include template('yp', 'content_list');
			break;

			//信息添加
			case 'add':

				if (isset($_POST['dosubmit'])) {
					$siteid = get_siteid();

					$table_name = $content_db->table_name;
					$fields_sys = $content_db->get_fields();
					$content_db->table_name = $table_name.'_data';
					$fields_attr = $content_db->get_fields();
					$fields = array_merge($fields_sys,$fields_attr);
					$fields = array_keys($fields);
					$info = array();
					foreach($_POST['info'] as $_k=>$_v) {
						if(in_array($_k, $fields)) $info[$_k] = $_v;
					}
					if (isset($_POST['info']['addition_field'])) {
						$cat_db = pc_base::load_model('category_model');
						//判断最高级栏目是否设置了附加字段
						$addition = array();
						$catid = intval($_POST['info']['catid']);
						if ($CATEGORYS[$catid]['parentid']) {
							$parentids = substr($CATEGORYS[$catid]['arrparentid'], 2).','.$catid;
							$pcatid = explode(',', $parentids);
							foreach ($pcatid as $p) {
								$r = $cat_db->get_one(array('catid'=>$p), 'additional');
								if ($r['additional']) {
									$r1 = string2array($r['additional']);
									if (empty($addition)) {
										$addition = $r1;
									} else {
										$addition = array_merge($addition, $r1);
									}
								}
								unset($r, $r1);
							}
						} else {
							$r = $cat_db->get_one(array('catid'=>$catid), 'additional');
							$addition = string2array($r['additional']);
						}
						$addition_field = $_POST['info']['addition_field'];
						unset($_POST['info']['addition_field']);
					}
					//稿件为待审状态
                    $groupid = param::get_cookie('_groupid');
                    $setting = getcache('yp_setting', 'yp');
                    if ($setting['priv'][$groupid]['allowpostverify']) {
                        $info['status'] = 99;
                    } else {
                        $info['status'] = 1;
                    }
					$info['username'] = $this->memberinfo['username'];
					$info['userid'] = $this->memberinfo['userid'];
 					$id = $content_db->add_content($info);
 					//记录该会员在该模型添加信息总数
 					$publish_total = string2array($this->memberinfo['publish_total']);
 					if (isset($publish_total[$modelid])) {
 						$publish_total[$modelid] = intval($publish_total[$modelid])+1;
 					} else {
 						$publish_total[$modelid] = 1;
 					}
 					$publish_total = array2string($publish_total);
 					$this->company_db->update(array('publish_total'=>$publish_total), array('userid'=>$this->_userid));
 					//如果设置了附加字段，将附加字段添加到data表中
 					if ($addition_field && !empty($addition)) {
						if (is_array($addition) && !empty($addition)) {
							$afield = $flag =  '';
							foreach ($addition as $f) {
								$afield .= $flag.'\''.$f.'\'';
								$flag = ',';
							}
							$sitemodel_field = pc_base::load_model('sitemodel_field_model');
							$res = $sitemodel_field->query('SELECT * FROM `phpcms_model_field` WHERE `fieldid` IN ('.$afield.') ORDER BY `listorder` ASC');
							$fields = array();
							$field_arr = $sitemodel_field->fetch_array();
							if (is_array($field_arr) && !empty($field_arr)) {
								foreach ($field_arr as $f) {
									$setting = string2array($f['setting']);
									$fields[$f['field']] = array_merge($f, $setting);
								}
							}
							require_once CACHE_MODEL_PATH.'yp_input.class.php';
							require_once CACHE_MODEL_PATH.'yp_update.class.php';
							$yp_input = new yp_input($modelid);
							$yp_input->fields = $fields;
							$inputinfo = $yp_input->get($addition_field);
							$addition_field = array2string($inputinfo['model']);
							$content_db->update(array('addition_field'=>$addition_field), array('id'=>$id));
						}
 					}
                    if ($info['status']==99) {
                        showmessage(L('operation_success'), APP_PATH.'index.php?m=yp&c=business&a=content&action=list&modelid='.$modelid.'&status=99&t=3');
                    } else {
                        showmessage(L('contributors_checked'), APP_PATH.'index.php?m=yp&c=business&a=content&action=list&modelid='.$modelid.'&msg=1&t=3');
                    }
				} else {
					require CACHE_MODEL_PATH.'yp_form.class.php';
					$yp_form = new yp_form($modelid,'',$CATEGORYS);
					$forminfos_data = $yp_form->get();
					$forminfos = array();
					foreach($forminfos_data as $_fk=>$_fv) {
						if($_fv['isomnipotent']) continue;
						if($_fv['formtype']=='omnipotent') {
							foreach($forminfos_data as $_fm=>$_fm_value) {
								if($_fm_value['isomnipotent']) {
									$_fv['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$_fv['form']);
								}
							}
						}
						$forminfos[$_fk] = $_fv;
					}
					$formValidator = $yp_form->formValidator;
					$show_validator = 1;
					include template('yp', 'content_add');
				}
			break;

			//排序
			case 'listorder':
				if(isset($_GET['dosubmit'])) {
					foreach($_POST['listorders'] as $id => $listorder) {
						$content_db->update(array('listorder'=>$listorder),array('id'=>$id));
					}
					showmessage(L('operation_success'));
				} else {
					showmessage(L('operation_failure'));
				}
			break;

			//删除
			case 'delete':
				if(isset($_GET['dosubmit'])) {
					$this->hits_db = pc_base::load_model('hits_model');
					if(empty($_POST['ids'])) showmessage(L('you_do_not_check'));
					//附件初始化
					$attachment = pc_base::load_model('attachment_model');
					$this->position_data_db = pc_base::load_model('position_data_model');
					$this->search_db = pc_base::load_model('search_model');
					$this->comment = pc_base::load_app_class('comment', 'comment');
					$search_model = getcache('search_model_'.get_siteid(),'search');
					$typeid = $search_model[$modelid]['typeid'];

					$minus_num = 0;
					foreach($_POST['ids'] as $id) {
						$r = $content_db->get_one(array('id'=>$id));
						$catid = intval($r['catid']);
						$fileurl = 0;
						//删除内容
						$content_db->delete_content($id,$fileurl,$catid);
						//删除统计表数据
						$this->hits_db->delete(array('hitsid'=>'c-'.$modelid.'-'.$id));
						//统计删除的数量
						if ($catid) $minus_num++;
						//删除附件
						$attachment->api_delete('c-'.$catid.'-'.$id);
						//删除推荐位数据
						$this->position_data_db->delete(array('id'=>$id,'catid'=>$catid,'module'=>'content'));
						//删除全站搜索中数据
						$this->search_db->delete_search($typeid,$id);
						//删除相关的评论
						$commentid = id_encode('yp_'.$catid, $id, $siteid);
						$this->comment->del($commentid, $siteid, $id, $catid);
					}
					//在会员统计字段中减去相应数量
					$publish_total = string2array($this->memberinfo['publish_total']);
 					if (isset($publish_total[$modelid])) {
 						$publish_num = intval($publish_total[$modelid]);
 						if ($publish_num>$minus_num) {
 							$publish_total[$modelid] = $publish_num-$minus_num;
 						} else {
 							$publish_total[$modelid] = 0;
 						}
 					} else {
 						$publish_total[$modelid] = 0;
 					}
 					$publish_total = array2string($publish_total);
 					$this->company_db->update(array('publish_total'=>$publish_total), array('userid'=>$this->_userid));
					//更新栏目统计
					$content_db->cache_items();
					showmessage(L('operation_success'),HTTP_REFERER);
				} else {
					showmessage(L('operation_failure'));
				}
			break;

			//更新修改日期
			case 'update':
				if(isset($_GET['dosubmit'])) {
					if(empty($_POST['ids'])) showmessage(L('you_do_not_check'));
					foreach($_POST['ids'] as $id) {
						$updatetime = SYS_TIME;
						$content_db->update(array('updatetime'=>$updatetime),array('id'=>$id));
					}
					showmessage(L('operation_success'));
				} else {
					showmessage(L('operation_failure'));
				}
			break;

			//修改信息
			case 'edit':
				if (isset($_POST['dosubmit'])) {
					$catid = $_POST['info']['catid'] = intval($_POST['info']['catid']);
					$CATEGORYS = getcache('category_yp_'.$modelid, 'yp');
					$category = $CATEGORYS[$catid];
					$id = intval($_POST['id']);
					$catid = $_POST['info']['catid'] = intval($_POST['info']['catid']);
					if (isset($_POST['info']['addition_field'])) {
						$cat_db = pc_base::load_model('category_model');
						//判断最高级栏目是否设置了附加字段
						$addition = array();
						if ($CATEGORYS[$catid]['parentid']) {
							$parentids = substr($CATEGORYS[$catid]['arrparentid'], 2).','.$catid;
							$pcatid = explode(',', $parentids);
							foreach ($pcatid as $p) {
								$r = $cat_db->get_one(array('catid'=>$p), 'additional');
								if ($r['additional']) {
									$r1 = string2array($r['additional']);
									if (empty($addition)) {
										$addition = $r1;
									} else {
										$addition = array_merge($addition, $r1);
									}
								}
								unset($r, $r1);
							}
						} else {
							$r = $cat_db->get_one(array('catid'=>$catid), 'additional');
							$addition = string2array($r['additional']);
						}
						$addition_field = $_POST['info']['addition_field'];
						unset($_POST['info']['addition_field']);
					}

					$groupid = param::get_cookie('_groupid');
                    $setting = getcache('yp_setting', 'yp');
                    if ($setting['priv'][$groupid]['allowpostverify']) {
                        $_POST['info']['status'] = 99;
                    } else {
                        $_POST['info']['status'] = 1;
                    }
					$content_db->edit_content($_POST['info'],$id);
					$forward = $_POST['forward'];
					//如果设置了附加字段，将附加字段添加到data表中
 					if ($addition_field && !empty($addition)) {
						if (is_array($addition) && !empty($addition)) {
							$afield = $flag =  '';
							foreach ($addition as $f) {
								$afield .= $flag.'\''.$f.'\'';
								$flag = ',';
							}
							$sitemodel_field = pc_base::load_model('sitemodel_field_model');
							$res = $sitemodel_field->query('SELECT * FROM `phpcms_model_field` WHERE `fieldid` IN ('.$afield.') ORDER BY `listorder` ASC');
							$fields = array();
							$field_arr = $sitemodel_field->fetch_array();
							if (is_array($field_arr) && !empty($field_arr)) {
								foreach ($field_arr as $f) {
									$setting = string2array($f['setting']);
									$fields[$f['field']] = array_merge($f,$setting);
								}
							}
							require_once CACHE_MODEL_PATH.'yp_input.class.php';
							require_once CACHE_MODEL_PATH.'yp_update.class.php';
							$yp_input = new yp_input($modelid);
							$yp_input->fields = $fields;
							$inputinfo = $yp_input->get($addition_field);
							$addition_field = array2string($inputinfo['model']);
							$content_db->update(array('addition_field'=>$addition_field), array('id'=>$id));
						}
 					}
                    if ($_POST['info']['status']==99) {
                        showmessage(L('operation_success'), APP_PATH.'index.php?m=yp&c=business&a=content&action=list&modelid='.$modelid.'&status=99&t=3');
                    } else {
                        showmessage(L('contributors_checked'), APP_PATH.'index.php?m=yp&c=business&a=content&action=list&modelid='.$modelid.'&msg=1&t=3');
                    }
				} else {
					$id = intval($_GET['id']);
					$r = $content_db->get_one(array('id'=>$id,'userid'=>$this->_userid));
					if(!$r) showmessage(L('illegal_operation'));
					$content_db->table_name = $content_db->table_name.'_data';
					$r2 = $content_db->get_one(array('id'=>$id));
					unset($r2['addition_field']);
					$data = array_merge($r,$r2);

					require CACHE_MODEL_PATH.'yp_form.class.php';
					$yp_form = new yp_form($modelid);
					$forminfos_data = $yp_form->get($data);
					$forminfos = array();
					foreach($forminfos_data as $_fk=>$_fv) {
						if($_fv['isomnipotent']) continue;
						if($_fv['formtype']=='omnipotent') {
							foreach($forminfos_data as $_fm=>$_fm_value) {
								if($_fm_value['isomnipotent']) {
									$_fv['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$_fv['form']);
								}
							}
						}
						$forminfos[$_fk] = $_fv;
					}
					$formValidator = $yp_form->formValidator;
					$show_validator = 1;
					include template('yp', 'content_edit');
				}
			break;

			//推荐信息
			case 'info_top':
				$setting = getcache('yp_setting', 'yp');
				$position = $setting['position'];
				$pos_data = pc_base::load_model('position_data_model');
				$now = SYS_TIME;
				$id = intval($_GET['id']);
				foreach ($position as $k => $pos) {
					$r = $pos_data->get_one("`posid`=$pos[posid] AND `expiration`>$now", 'COUNT(id) AS num');
					if ($r['num']>$pos['num']) {
						$position[$k]['disabled'] = 1;
					} else {
						$position[$k]['disabled'] = 0;
					}
				}
				$r = $content_db->get_one(array('id'=>$id), 'catid');
				$catid = $r['catid'];
				foreach($position as $_k => $_v) {
					if($pos_data->get_one(array('id'=>$id,'catid'=>$catid,'posid'=>$_v['posid']))) {
						$exist_posids[$_v['posid']] = 1;
					}
				}
				$memberinfo = $this->memberinfo;
				include template('yp', 'top_info');
			break;

			case 'info_top_cost':
				$amount = $msg = '';
				$memberinfo = $this->memberinfo;
				$_username = $this->memberinfo['username'];
				$_userid = $this->memberinfo['userid'];
				$setting = getcache('yp_setting','yp');
				$infos = $setting['position'];

				foreach ($infos as $k => $info) {
					$toptype_arr[] = $info['posid'];
					//置顶积分数组
					$toptype_price[$info['posid']] = $info['point'];
					//置顶推荐位数组
					$toptype_posid[$info['posid']] = $info['posid'];
				}
				if (isset($_POST['dosubmit'])) {
					$posids = array();
					$push_api = pc_base::load_app_class('push_api','admin');
					$pos_data = pc_base::load_model('position_data_model');
					$modelid = intval($_POST['modelid']);
					$id = intval($_POST['id']);
					$flag = $modelid.'_'.$id;
					$toptime = intval($_POST['toptime']);
					if($toptime == 0 || empty($_POST['toptype'])) showmessage(L('info_top_not_setting_toptime'));
					//计算置顶扣费积分，时间
					if(is_array($_POST['toptype']) && !empty($_POST['toptype'])) {
						foreach($_POST['toptype'] as $r) {
							if(is_numeric($r) && in_array($r, $toptype_arr)) {
								$posids[] = $toptype_posid[$r];
								$amount += $toptype_price[$r];
								$msg .= $r.'-';
							}
						}
					}
					//应付总积分
					$amount = $amount * $toptime;
					//扣除置顶点数
					pc_base::load_app_class('spend','pay',0);
					$pay_status = spend::point($amount, L('info_top').$msg, $_userid, $_username, '', '', $flag);
					if($pay_status == false) {
						$msg = spend::get_msg();
						showmessage($msg);
					}
					//置顶过期时间
					//TODO
					$expiration = SYS_TIME + $toptime * 3600;

					//获取置顶文章信息内容
					if(isset($modelid) && $modelid) {
						$siteid = get_siteid();
						$r = $content_db->get_one(array('id'=>$id,'username'=>$_username,'sysadd'=>0));
					}
					if(!$r) showmessage(L('illegal_operation'));
					$catid = intval($r['catid']);
					$push_api->position_update($id, $modelid, $catid, $posids, $r, $expiration, 1, 'yp_content_model');
					showmessage(L('ding_success'), '', '', 'top');
				} else {
					$toptype = trim($_POST['toptype']);
					$toptime = trim($_POST['toptime']);
					$types = explode('_', $toptype);
					if(is_array($types) && !empty($types)) {
						foreach($types as $r) {
							if(is_numeric($r) && in_array($r, $toptype_arr)) {
								$amount += $toptype_price[$r];
							}
						}
					}
					$amount = $amount * $toptime;
					echo $amount;
				}
			break;

			//附加字段
			case 'get_addition':

				$catid = intval($_GET['catid']);
				if (isset($_GET['id']) && !empty($_GET['id'])) {
					$id = intval($_GET['id']);
					$content_db->table_name = $content_db->table_name.'_data';
					$r = $content_db->get_one(array('id'=>$id), 'addition_field');
					$data = string2array($r['addition_field']);
				}
				$categorys = $CATEGORYS;
				$cat_db = pc_base::load_model('category_model');

				//判断各级栏目是否设置了附加字段
				$addition = array();
				if ($categorys[$catid]['parentid']) {
					$parentids = substr($categorys[$catid]['arrparentid'], 2).','.$catid;
					$pcatid = explode(',', $parentids);
					foreach ($pcatid as $p) {
						$r = $cat_db->get_one(array('catid'=>$p), 'additional');
						if ($r['additional']) {
							$r1 = string2array($r['additional']);
							if (empty($addition)) {
								$addition = $r1;
							} else {
								$addition = array_merge($addition, $r1);
							}
						}
						unset($r, $r1);
					}
				} else {
					$r = $cat_db->get_one(array('catid'=>$catid), 'additional');
					$addition = string2array($r['additional']);
				}
				//如果存在附件字段，则将其将其转换成form格式输出
				if (is_array($addition) && !empty($addition)) {
						$sitemodel_field = pc_base::load_model('sitemodel_field_model');
						$afield = $flag =  '';
						foreach ($addition as $f) {
							$afield .= $flag.'\''.$f.'\'';
							$flag = ',';
						}
						$fields = $sitemodel_field->select('`fieldid` IN ('.$afield.')', '*', '', '`listorder` ASC', '', 'field');
						if (is_array($fields) && !empty($fields)) {
							foreach ($fields as $field => $f) {
								$setting = string2array($f['setting']);
								$fields[$field] = array_merge($fields[$field],$setting);
							}
							require CACHE_MODEL_PATH.'yp_form.class.php';
							$yp_form = new yp_form($modelid);
							$yp_form->fields = $fields;
							$forminfos_data = $yp_form->get($data);
							$forminfos = array();
							foreach($forminfos_data as $_fk=>$_fv) {
								$_fv['form'] = str_replace(array('name="info[', 'name=\'info['), array('name="info[addition_field][', 'name=\'info[addition_field]['), $_fv['form']);
								if($_fv['isomnipotent']) continue;
								if($_fv['formtype']=='omnipotent') {
									foreach($forminfos_data as $_fm=>$_fm_value) {
										if($_fm_value['isomnipotent']) {
											$_fv['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$_fv['form']);
										}
									}
								}
								$forminfos[$_fk] = $_fv;
							}
							$forminfos = array_iconv($forminfos, CHARSET, 'utf-8');
							exit(json_encode($forminfos));
						}
				}
			break;
		}
	}
}
?>