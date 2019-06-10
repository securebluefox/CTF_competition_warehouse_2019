<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('global');
class update_urls extends admin {

    function __construct() {
		parent::__construct();
        $this->db = pc_base::load_model('yp_content_model');
        if (isset($_GET['modelid'])) {
            $modelid = intval($_GET['modelid']);
        }
		$this->categorys = getcache('category_yp_'.$modelid,'yp');
		foreach($_GET as $k=>$v) {
			$_POST[$k] = $v;
		}
	}

    public function init() {
		$show_header = $show_dialog  = '';
        $admin_username = param::get_cookie('admin_username');
        $modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : 0;

        $company_modelid = get_company_model();
        $tree = pc_base::load_sys_class('tree');
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $categorys = array();
        if(!empty($this->categorys)) {
            foreach($this->categorys as $catid=>$r) {
                $r['disabled'] = $r['child'] ? 'disabled' : '';
                $categorys[$catid] = $r;
            }
        }
        $str  = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";

        $tree->init($categorys);
        $string .= $tree->get_tree(0, $str);
        include $this->admin_tpl('update_urls');
	}
    //更新分类URL
    public function category() {
        $this->modelid = intval($_POST['modelid']);
        $this->category_db = pc_base::load_model('category_model');
        $company_modelid = get_company_model();
        $models = getcache('yp_model', 'model');
        if ($this->modelid) {
            if ($this->modelid == $company_modelid) {
                $is_company_model = 1;
            } else {
                $is_company_model = 0;
            }
            $categorys = getcache('category_yp_'.$this->modelid, 'yp');
            if (is_array($categorys) && !empty($categorys)) {
                foreach ($categorys as $catid => $c) {
                    $url = $this->update_url($catid, $is_company_model);
                    if($c['url']!=$url) $this->category_db->update(array('url'=>$url), array('catid'=>$catid));
                }
            }
            $this->cache($this->modelid);
            showmessage(L('operation_success'), APP_PATH.'index.php?m=yp&c=update_urls');
        } else {
            $page = intval($_GET['page']);
            if (!$page) {
                $modelids = array_keys($models);
                $modelids[] = $company_modelid;
            } else {
                $modelids = getcache('update_url_modelid'.$_SESSION['userid'], 'yp');
            }
            if (empty($modelids)) {
                delcache('update_url_modelid'.$_SESSION['userid'], 'yp');
                showmessage(L('operation_success'), APP_PATH.'index.php?m=yp&c=update_urls');
            }
            $this->modelid = array_shift($modelids);
            setcache('update_url_modelid'.$_SESSION['userid'], $modelids, 'yp');
            if ($this->modelid == $company_modelid) {
                $is_company_model = 1;
            } else {
                $is_company_model = 0;
            }
            $categorys = getcache('category_yp_'.$this->modelid, 'yp');
            if (is_array($categorys) && !empty($categorys)) {
                foreach ($categorys as $catid => $c) {
                    $url = $this->update_url($catid, $is_company_model);
                    if($c['url']!=$url) $this->category_db->update(array('url'=>$url), array('catid'=>$catid));
                }
            }
            $this->cache($this->modelid);
            if ($models[$this->modelid]) {
                showmessage(L('updates').$models[$this->modelid]['name'].L('category_success'), APP_PATH.'index.php?m=yp&c=update_urls&a=category&page=1');
            } else {
                showmessage(L('update_company_category_success'), APP_PATH.'index.php?m=yp&c=update_urls&a=category&page=1');
            }
        }
    }

    //更新内容
    public function content() {
        $modelid = $_POST['modelid'] ? intval($_POST['modelid']) : intval($_GET['modelid']);
        $company_modelid = get_company_model();
        $pagesize = $_POST['pagesize'] ? intval($_POST['pagesize']) : intval($_GET['pagesize']);
        if ($pagesize<1) $pagesize = 100;
        $page = max(intval($_GET['page']), 1);
        $pages = intval($_GET['pages']);
        $setting = $this->get_setting();
        $models = getcache('yp_model', 'model');
        $model_setting = string2array($models[$modelid]['setting']);
        if ($modelid == $company_modelid) {
            $company_db = pc_base::load_model('yp_company_model');
            if (!$pages) {
                $r = $company_db->get_one(array(), 'COUNT(userid) AS num');
                $total = $r['num'];
                $pages = ceil($total/$pagesize);
            }
            $offset = ($page-1)*$pagesize;
            $data = $company_db->select(array(), 'url, userid', $offset.','.$pagesize, '`userid` ASC');
            if (is_array($data) && !empty($data)) {
                foreach ($data as $com) {
                    if (strpos($com['url'], APP_PATH)===false) {
                        continue;
                    } else {
                        if ($setting['enable_rewrite']) {
                            $url = APP_PATH.'web-'.$com['userid'].'.html';
                        } else {
                            $url = APP_PATH.'index.php?m=yp&c=com_index&userid='.$com['userid'];
                        }
                    }
                    $company_db->update(array('url'=>$url), array('userid'=>$com['userid']));
                }
            }
            if ($pages>$page) {
                $page++;
                showmessage(L('business_page').($page-1).'/'.$pages.L('updates_successful'), APP_PATH.'index.php?m=yp&c=update_urls&a=content&modelid='.$modelid.'&page='.$page.'&pages='.$pages.'&pagesize='.$pagesize);
            } else {
                showmessage(L('updates_business_page_successful'), APP_PATH.'index.php?m=yp&c=update_urls');
            }
        } else {
            //设置模型数据表名
            $this->db->set_model($modelid);
            $table_name = $this->db->table_name;
            extract($_POST,EXTR_SKIP);
            $where = ' WHERE status=99 ';
			$order = 'ASC';

            if($type == 'lastinput') {
                $offset = 0;
            } else {
                $page = max(intval($page), 1);
                $offset = $pagesize*($page-1);
            }

            if (!isset($first) && is_array($catids) && $catids[0] > 0) {
                setcache('yp_url_show_'.$_SESSION['userid'], $catids,'yp');
                $catids = implode(',',$catids);
                $where .= " AND catid IN($catids) ";
                $first = 1;
            } elseif($first) {
                $catids = getcache('yp_url_show_'.$_SESSION['userid'], 'yp');
                $catids = implode(',',$catids);
                $where .= " AND catid IN($catids) ";
            } else {
                $first = 0;
            }

            if($type == 'lastinput' && $number) {
                $offset = 0;
                $pagesize = $number;
                $order = 'DESC';
            }

            if(!isset($total) && $type != 'lastinput') {
                $rs = $this->db->query("SELECT COUNT(*) AS `count` FROM `$table_name` $where");
                $result = $this->db->fetch_array($rs);

                $total = $result[0]['count'];
                $pages = ceil($total/$pagesize);
                $start = 1;
            }

            $rs = $this->db->query("SELECT * FROM `$table_name` $where ORDER BY `id` $order LIMIT $offset,$pagesize");
            $data = $this->db->fetch_array($rs);
            foreach($data as $r) {
                if (!$model_setting['ismenu']) {
                    $url = compute_company_url('show', array('catid'=>$r['catid'], 'id'=>$r['id'], 'page'=>1, 'userid'
                    =>$r['userid']));
                } else {
                    if ($setting['enable_rewrite']) {
                        $url = APP_PATH.'yp-show-'.$r['catid'].'-'.$r['id'].'.html';
                    } else {
                        $url = APP_PATH.'index.php?m=yp&c=index&a=show&catid='.$r['catid'].'&id='.$r['id'];
                    }
                }
                $this->db->update(array('url'=>$url), array('id'=>$r['id']));
            }

            if($pages > $page) {
                $page++;
                $http_url = get_url();
                $creatednum = $offset + count($data);
                $percent = round($creatednum/$total, 2)*100;

                $message = L('need_update_items',array('total'=>$total,'creatednum'=>$creatednum,'percent'=>$percent));
                $forward = $start ? "?m=yp&c=update_urls&a=content&type=$type&dosubmit=1&first=$first&pagesize=$pagesize&page=$page&pages=$pages&total=$total&modelid=$modelid" : preg_replace("/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url);
            } else {
                delcache('yp_url_show_'.$_SESSION['userid'],'content');
                $message = L('create_update_success');
                $forward = '?m=yp&c=update_urls&a=init';
            }
            showmessage($message,$forward,200);
        }
    }

    //更新商家主页导航
    public function menu() {
    	$company_db = pc_base::load_model('yp_company_model');
    	$pagesize = $_POST['pagesize'] ? intval($_POST['pagesize']) : intval($_GET['pagesize']);
    	if ($pagesize<1) $pagesize = 100;
    	$pages = intval($_GET['pages']);
    	$page = max(intval($_GET['page']), 1);
    	if (!$pages) {
    		$r = $company_db->get_one(array(), 'COUNT(*) AS num');
    		$total = $r['num'];
    		$pages = ceil($total/$pagesize);
    	}
    	$offset = ($page-1)*$pagesize;
    	$data = $company_db->select(array(), 'userid, menu, url', $offset.','.$pagesize, 'userid ASC');
    	if (is_array($data) && !empty($data)) {
    		foreach ($data as $m) {
	    		$menu = company_menu($m);
	    		$company_db->update(array('menu'=>$menu), array('userid'=>$m['userid']));
    		}
    	}
    	if ($pages>$page) {
    		$page++;
    		showmessage(L('updates_business_menu').($page-1).'/'.$pages.L('updates_successful'), APP_PATH.'index.php?m=yp&c=update_urls&a=menu&page='.$page.'&pages='.$pages.'&pagesize='.$pagesize);
    	} else {
    		showmessage(L('updates_business_menu_successful'), APP_PATH.'index.php?m=yp&c=update_urls');
    	}
     } 
    
	/**
	 * 更新模型缓存
	 */
	public function model() {
		//模型原型存储路径
		define('MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'yp'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
		define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
		require MODEL_PATH.'fields.inc.php';
		//更新企业库模型字段
		$com_modelid = get_company_model();
		$this->cache_field($com_modelid);
		//更新内容模型类：表单生成、入库、更新、输出
		$classtypes = array('form','input','update','output');
		foreach($classtypes as $classtype) {
			$cache_data = file_get_contents(MODEL_PATH.'yp_'.$classtype.'.class.php');
			$cache_data = str_replace('}?>','',$cache_data);
			foreach($fields as $field=>$fieldvalue) {
				if(file_exists(MODEL_PATH.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php')) {
					$cache_data .= file_get_contents(MODEL_PATH.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php');
				}
			}
			$cache_data .= "\r\n } \r\n?>";
			file_put_contents(CACHE_MODEL_PATH.'yp_'.$classtype.'.class.php',$cache_data);
			@chmod(CACHE_MODEL_PATH.'yp_'.$classtype.'.class.php',0777);
		}
		//更新模型数据缓存
		$model_array = array();
		$this->sitemodel_db = pc_base::load_model('sitemodel_model');
		$datas = $this->sitemodel_db->select(array('type'=>5));
		foreach ($datas as $r) {
			if(!$r['disabled']) $model_array[$r['modelid']] = $r;
			$this->cache_field($r['modelid']);
		}
		setcache('yp_model', $model_array, 'model');
		showmessage(L('operation_success'), APP_PATH.'index.php?m=yp&c=update_urls');
	}
	
	/**
	 * 更新指定模型字段缓存
	 * 
	 * @param $modelid 模型id
	 */
	public function cache_field($modelid = 0) {
		$this->field_db = pc_base::load_model('sitemodel_field_model');
		$field_array = array();
		$fields = $this->field_db->select(array('modelid'=>$modelid,'disabled'=>$disabled),'*',100,'listorder ASC');
		foreach($fields as $_value) {
			$setting = string2array($_value['setting']);
			$_value = array_merge($_value,$setting);
			$field_array[$_value['field']] = $_value;
		}
		setcache('model_field_'.$modelid,$field_array,'model');
		return true;
	}
  
    /**
	* 更新栏目链接地址
	*/
	private function update_url($catid, $is_company_model) {
		$catid = intval($catid);
		if (!$catid) return false;
		pc_base::load_app_func('global');

        //获取模块缓存，查看是否启用伪静态
        $setting = $this->get_setting();
        if ($is_company_model) {
        	if ($setting['enable_rewrite']) {
        		$url = yp_filters_url('catid', array('catid'=>$catid), 2, $this->modelid);
        	} else {
        		$url = APP_PATH.'index.php?m=yp&c=index&a=list_company&catid='.$catid;
        	}
        } else {
       		if ($setting['enable_rewrite']) {
            	$url = yp_filters_url('catid', array('catid'=>$catid, 'page'=>1), 2, $this->modelid);
	        } else {
	            $url = APP_PATH.'index.php?m=yp&c=index&a=lists&catid='.$catid;
	        }
        }
		return $url;
	}

    private function get_setting() {
        static $setting;
        if (!$setting) {
            $setting = getcache('yp_setting', 'yp');
        }
        return $setting;
    }

    private function cache($modelid) {
        $modelid = intval($modelid);
        $datas = $this->category_db->select(array('modelid'=>$modelid),'catid,items',10000);
        $array = array();
        foreach ($datas as $r) {
            $array[$r['catid']] = $r['items'];
        }
        setcache('category_yp_items_'.$modelid, $array,'yp');
        $result = $this->category_db->select(array('module'=>'yp', 'modelid'=>$modelid),'*',20000,'listorder ASC');
        foreach ($result as $r) {
            unset($r['module'],$r['catdir']);
            $setting = string2array($r['setting']);
            $r['meta_title'] = $setting['meta_title'];
            $r['meta_keywords'] = $setting['meta_keywords'];
            $r['meta_description'] = $setting['meta_description'];
            $categorys[$r['catid']] = $r;
        }
        $this->categorys = $categorys;
        setcache('category_yp_'.$modelid,$categorys,'yp');
        return true;
    }
}
?>