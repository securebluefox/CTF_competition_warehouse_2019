<?php

pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);
pc_base::load_app_func('global');
pc_base::load_sys_class('format','',0);
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

class content extends admin {
	private $db,$siteid;
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('yp_content_model');
		$this->siteid = $this->get_siteid();
	}

	//黄页模型信息列表
	function init () {
		$yp_model = getcache('yp_model', 'model');
		if (!isset($_GET['modelid']) || empty($_GET['modelid'])) {
			$yp_modelids = array_keys($yp_model);
			$modelid = array_shift($yp_modelids);
		} else {
			$modelid = intval($_GET['modelid']);
		}
		if (!$modelid) showmessage(L('add_yp_model'), '?m=yp&c=ypmodel&a=init');
		$this->db->set_model($modelid);
		$categorys = getcache('category_yp_'.$modelid, 'yp');
		$status = $_GET['status'] ? intval($_GET['status']) : 1;
		$where = 'status = '.$status;
		if (isset($_GET['catid']) && !empty($_GET['catid'])) {
			$catid = intval($_GET['catid']);
			$where .= " AND catid = '$catid'";
		}
		if(isset($_GET['start_time']) && $_GET['start_time']) {
			$start_time = strtotime($_GET['start_time']);
			$where .= " AND `inputtime` > '$start_time'";
		}
		if(isset($_GET['end_time']) && $_GET['end_time']) {
			$end_time = strtotime($_GET['end_time']);
			$where .= " AND `inputtime` < '$end_time'";
		}
		if($start_time>$end_time) showmessage(L('starttime_than_endtime'));
		if(isset($_GET['keyword']) && !empty($_GET['keyword'])) {
			$type_array = array('title','description','username');
			$searchtype = intval($_GET['searchtype']);
			if($searchtype < 3) {
				$searchtype = $type_array[$searchtype];
				$keyword = strip_tags(trim($_GET['keyword']));
				$where .= " AND `$searchtype` like '%$keyword%'";
			} elseif($searchtype==3) {
				$keyword = intval($_GET['keyword']);
				$where .= " AND `id`='$keyword'";
			}
		}
		if(isset($_GET['posids']) && !empty($_GET['posids'])) {
			$posids = $_GET['posids']==1 ? intval($_GET['posids']) : 0;
			$where .= " AND `posids` = '$posids'";
		}
		$page = max(intval($_GET['page']),1);
		$datas = $this->db->listinfo($where,'id desc',$page);
		$pages = $this->db->pages;
		$show_header = $show_dialog = $show_validator = '';
		include $this->admin_tpl('content_list');
	}

	/**
	 * 删除
	 */
	public function delete() {
		if(isset($_GET['dosubmit'])) {
			$modelid = intval($_GET['modelid']);
			if(!$modelid) showmessage(L('missing_part_parameters'));

			$this->db->set_model($modelid);
			$this->hits_db = pc_base::load_model('hits_model');
			if(isset($_GET['ajax_preview'])) {
				$ids = intval($_GET['id']);
				$_POST['ids'] = array(0=>$ids);
			}
			if(empty($_POST['ids'])) showmessage(L('you_do_not_check'));
			//附件初始化
			$attachment = pc_base::load_model('attachment_model');
			$this->position_data_db = pc_base::load_model('position_data_model');
			$this->search_db = pc_base::load_model('search_model');
			$this->comment = pc_base::load_app_class('comment', 'comment');
			$this->company_db = pc_base::load_model('yp_company_model');
			$search_model = getcache('search_model_'.$this->siteid,'search');
			$typeid = $search_model[$modelid]['typeid'];

			foreach($_POST['ids'] as $id) {
				$r = $this->db->get_one(array('id'=>$id));
				$catid = intval($r['catid']);
				$userid = intval($userid);
				$fileurl = 0;
				//删除内容
				$this->db->delete_content($id,$fileurl,$catid);
				//删除统计表数据
				$this->hits_db->delete(array('hitsid'=>'c-'.$modelid.'-'.$id));
				//会员发布统计相应减少
				if ($userid) {
					$memberinfo = $this->company_db->get_one(array('userid'=>$userid), 'publish_total');
					$publish_total = string2array($memberinfo['publish_total']);
					if (isset($publish_total[$modelid])) {
						$publish_num = intval($publish_total[$modelid]);
						if ($publish_num>0) {
 							$publish_total[$modelid] = $publish_num-$minus_num;
 						} else {
 							$publish_total[$modelid] = 0;
 						}
					} else {
						$publish_total[$modelid] = 0;
					}
					$publish_total = array2string($publish_total);
					$this->company_db->update(array('publish_total'=>$publish_total), array('userid'=>$userid));
				}
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
			//更新栏目统计
			$this->db->cache_items();
			showmessage(L('operation_success'),HTTP_REFERER);
		} else {
			showmessage(L('operation_failure'));
		}
	}
	/**
	 * 过审内容
	 */
	public function pass() {
		$admin_username = param::get_cookie('admin_username');
		$modelid = intval($_GET['modelid']);

		if(!$modelid) showmessage(L('missing_part_parameters'));
		if ($_GET['reject']) $status = 0;
		$status = isset($status) ? $status : 99;
		if(isset($_GET['ajax_preview'])) {
			$_POST['ids'] = $_GET['id'];
		}
		$this->db->status($_POST['ids'],$status, $modelid);
		//只有存在工作流才需要审核

		showmessage(L('operation_success'),HTTP_REFERER);
	}
	/**
	 * 排序
	 */
	public function listorder() {
		if(isset($_GET['dosubmit'])) {
			$modelid = intval($_GET['modelid']);
			if(!$modelid) showmessage(L('missing_part_parameters'));
			$this->db->set_model($modelid);
			foreach($_POST['listorders'] as $id => $listorder) {
				$this->db->update(array('listorder'=>$listorder),array('id'=>$id));
			}
			showmessage(L('operation_success'));
		} else {
			showmessage(L('operation_failure'));
		}
	}

	//文章预览
	public function public_preview() {
		$catid = intval($_GET['catid']);
		$modelid = intval($_GET['modelid']);
		$id = intval($_GET['id']);

		if(!$catid || !$id || !$modelid) showmessage(L('missing_part_parameters'),'blank');
		$page = intval($_GET['page']);
		$page = max($page,1);
		$CATEGORYS = getcache('category_yp_'.$modelid,'yp');

		if(!isset($CATEGORYS[$catid])) showmessage(L('missing_part_parameters'),'blank');
		$CAT = $CATEGORYS[$catid];
		$MODEL = getcache('yp_model', 'model');

		$this->db->table_name = $this->db->db_tablepre.$MODEL[$modelid]['tablename'];
		$r = $this->db->get_one(array('id'=>$id));
		if(!$r) showmessage(L('information_does_not_exist'));
		$this->db->table_name = $this->db->table_name.'_data';
		$r2 = $this->db->get_one(array('id'=>$id));
		$rs = $r2 ? array_merge($r,$r2) : $r;
		$yp_setting = getcache('yp_setting', 'yp');

		//再次重新赋值，以数据库为准
		$catid = $CATEGORYS[$r['catid']]['catid'];
		$modelid = $CATEGORYS[$catid]['modelid'];

		require_once CACHE_MODEL_PATH.'yp_output.class.php';
		if ($rs['addition_field']) {
			$addition_field = $rs['addition_field'];
			unset($rs['addition_field']);
		}
		$yp_output = new yp_output($modelid,$catid,$CATEGORYS);
		$data = $yp_output->get($rs);
		if ($addition_field) {
			$addition_field = string2array($addition_field);
			$additional_fields = $yp_output->fields = get_additional_fields($catid, $CATEGORYS);
			$additional_data = $yp_output->get($addition_field);
			$additional_base = $additional_general = array();
			foreach ($additional_data as $k => $v) {
				if ($additional_fields[$k]['isbase ']) {
					$additional_base[$k] = $additional_data[$k];
				} else {
					$additional_general[$k] = $additional_data[$k];
				}
			}
			unset($additional_data, $addition_field, $additional_fields);
		}
		extract($data);
		$CAT['setting'] = string2array($CAT['setting']);
		$template = $template ? $template : $MODEL[$modelid]['show_template'];
		$allow_visitor = 1;
		//SEO
		$siteid = get_siteid();
		$model_setting = string2array($MODEL[$modelid]['setting']);
		$seo_keywords = $CAT['setting']['meta_keywords'] ? $CAT['setting']['meta_keywords'] : ($model_setting['meta_keywords'] ? $model_setting['meta_keywords'] : $yp_setting['meta_keywords']);
		$seo_description = $CAT['setting']['meta_description'] ? $CAT['setting']['meta_description'] : ($model_setting['meta_description'] ? $model_setting['meta_description'] : $yp_setting['meta_description']);
		$SEO = seo($siteid, '', $title, $seo_description, $seo_keywords);

		define('STYLE',$MODEL[$modelid]['default_style']);
		if(isset($rs['paginationtype'])) {
			$paginationtype = $rs['paginationtype'];
			$maxcharperpage = $rs['maxcharperpage'];
		}
		$pages = $titles = '';
		if($rs['paginationtype']==1) {
			//自动分页
			if($maxcharperpage < 10) $maxcharperpage = 500;
			$contentpage = pc_base::load_app_class('contentpage', 'content');
			$content = $contentpage->get_data($content,$maxcharperpage);
		}
		if($rs['paginationtype']!=0) {
			//手动分页
			$CONTENT_POS = strpos($content, '[page]');
			if($CONTENT_POS !== false) {
				$this->url = pc_base::load_app_class('url', 'content');
				$contents = array_filter(explode('[page]', $content));
				$pagenumber = count($contents);
				for($i=1; $i<=$pagenumber; $i++) {
					if ($yp_setting['enable_rewrite']) {
						$pageurls[$i] = APP_PATH.'yp-show-'.$catid.'-'.$id.'-'.$i.'.html';
					} else {
						$pageurls[$i] = APP_PATH.'index.php?m=yp&c=index&a=show&catid='.$catid.'&id='.$id.'&page='.$i;
					}
				}
				$END_POS = strpos($content, '[/page]');
				if($END_POS !== false) {
					if(preg_match_all("|\[page\](.*)\[/page\]|U", $content, $m, PREG_PATTERN_ORDER)) {
						foreach($m[1] as $k=>$v) {
							$p = $k+1;
							$titles[$p]['title'] = strip_tags($v);
							$titles[$p]['url'] = $pageurls[$p][0];
						}
					}
				} else {
					//当不存在 [/page]时，则使用下面分页
					$pages = content_pages($pagenumber,$page, $pageurls);
				}
				//判断[page]出现的位置是否在第一位
				if($CONTENT_POS<7) {
					$content = $contents[$page];
				} else {
					$content = $contents[$page-1];
				}
				if($titles) {
					list($title, $content) = explode('[/page]', $content);
					$content = trim($content);
					if(strpos($content,'</p>')===0) {
						$content = '<p>'.$content;
					}
					if(stripos($content,'<p>')===0) {
						$content = $content.'</p>';
					}
				}
			}
		}
		include template('yp',$template);
		$pc_hash = $_SESSION['pc_hash'];
		$steps = intval($_GET['steps']);
		echo "
		<link href=\"".CSS_PATH."dialog_simp.css\" rel=\"stylesheet\" type=\"text/css\" />
		<script language=\"javascript\" type=\"text/javascript\" src=\"".JS_PATH."dialog.js\"></script>
		<script type=\"text/javascript\">art.dialog({lock:false,title:'".L('operations_manage')."',mouse:true, id:'content_m', content:'<span id=cloading ><a href=\'javascript:ajax_manage(1)\'>".L('passed_checked')."</a> | <a href=\'javascript:ajax_manage(2)\'>".L('reject')."</a> |　<a href=\'javascript:ajax_manage(3)\'>".L('delete')."</a></span>',left:'right',width:'15em', top:'bottom', fixed:true});
		function ajax_manage(type) {
			if(type==1) {
				$.get('?m=yp&c=content&a=pass&ajax_preview=1&modelid=".$modelid."&id=".$id."&pc_hash=".$pc_hash."');
			} else if(type==2) {
				$.get('?m=yp&c=content&a=pass&ajax_preview=1&reject=1&modelid=".$modelid."&id=".$id."&pc_hash=".$pc_hash."');
			} else if(type==3) {
				$.get('?m=yp&c=content&a=delete&ajax_preview=1&dosubmit=1&modelid=".$modelid."&id=".$id."&pc_hash=".$pc_hash."');
			}
			$('#cloading').html('<font color=red>".L('operation_success')."<span id=\"secondid\">2</span>".L('after_a_few_seconds_left')."</font>');
			setInterval('set_time()', 1000);
			setInterval('window.close()', 2000);
		}
		function set_time() {
			$('#secondid').html(1);
		}
		</script>";
	}

	public function public_sub_categorys() {
		$catid = intval($_POST['root']);
		$modelid = intval($_POST['modelid']);
		$categorys = getcache('category_yp_'.$modelid, 'yp');
		$tree = pc_base::load_sys_class('tree');
		$tree->init($categorys);
		$strs = "<a href='?m=yp&c=content&a=\$type&modelid=".$modelid."&catid=\$catid&pc_hash=".$_SESSION['pc_hash']."' target='right' onclick='open_list(this)'>\$catname</a>";
		$data = $tree->creat_sub_json($catid,$strs);
		echo $data;
	}
	/**
	 * 显示分类菜单列表
	 */
	public function public_categorys() {
		$show_header = '';
		//$from = isset($_GET['from']) && in_array($_GET['from'],array('block')) ? $_GET['from'] : 'content';
		$tree = pc_base::load_sys_class('tree');
		$modelid = intval($_GET['modelid']);
		if (!$modelid) showmessage(L('add_yp_model'), '?m=yp&c=ypmodel&a=init');

		$CATEGORY = array();
		$categorys = getcache('category_yp_'.$modelid, 'yp');
		if(!empty($categorys)) {
			foreach($categorys as $r) {
				$r['icon_type'] = $r['vs_show'] = '';
				$r['type'] = 'init';
				if($r['parentid'] == 0) $r['folder'] = 'open';
				$CATEGORY[$r['catid']] = $r;
			}
		}
		if(!empty($CATEGORY)) {
			$tree->init($CATEGORY);
			$strs = "<span class='\$icon_type'><a href='?m=yp&c=content&a=\$type&modelid=".$modelid."&catid=\$catid' target='right' onclick='open_list(this)'>\$catname</a></span>";
			$strs2 = "<span class='folder'>\$catname</span>";
			$categorys = $tree->get_treeview(0,'category_tree',$strs,$strs2,2);
		} else {
			$categorys = L('please_add_category');
		}
        include $this->admin_tpl('category_tree');
		exit;
	}

    public function edit() {
        if (isset($_POST['dosubmit'])) {
            $catid = $_POST['info']['catid'] = intval($_POST['info']['catid']);
            $CATEGORYS = getcache('category_yp_'.$modelid, 'yp');
            $category = $CATEGORYS[$catid];
            $id = intval($_POST['id']);
            $modelid = intval($_GET['modelid']);
            $this->db->set_model($modelid);
            $catid = $_POST['info']['catid'] = intval($_POST['info']['catid']);
            if (isset($_POST['info']['addition_field'])) {
                $cat_db = pc_base::load_model('category_model');
                //判断最高级栏目是否设置了附加字段
                if ($CATEGORYS[$catid]['parentid']) {
                    $parentids = $CATEGORYS[$catid]['arrparentid'];
                    $pcatid = explode(',', $parentids);
                    $pcatid = $pcatid[1];
                    $r = $cat_db->get_one(array('catid'=>$pcatid), 'additional');
                } else {
                    $r = $cat_db->get_one(array('catid'=>$catid), 'additional');
                }
                $addition_field = $_POST['info']['addition_field'];
                unset($_POST['info']['addition_field']);
            }

            $this->db->edit_content($_POST['info'],$id);
            $forward = $_POST['forward'];
            //如果设置了附加字段，将附加字段添加到data表中
            if ($addition_field) {
                if ($r['additional']) {
                    $addition = string2array($r['additional']);
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
                        $this->db->update(array('addition_field'=>$addition_field), array('id'=>$id));
                    }
                }
            }

            showmessage(L('update_success').L('2s_close'),'blank','','','function set_time() {$("#secondid").html(1);}setTimeout("set_time()", 500);setTimeout("window.close()", 1200);');
        } else {
            $id = intval($_GET['id']);
            $modelid = intval($_GET['modelid']);
            $this->db->set_model($modelid);
            $r = $this->db->get_one(array('id'=>$id));
            if(!$r) showmessage(L('illegal_operation'));
            $this->db->table_name = $this->db->table_name.'_data';
            $r2 = $this->db->get_one(array('id'=>$id));
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
            include $this->admin_tpl('content_edit');
        }
    }

    public function public_get_addition() {
        //获取模块配置，检查该会员组是否有附加字段权限
        $setting = getcache('yp_setting', 'yp');
        //if (!$setting['priv'][$this->memberinfo['groupid']]['field_num']) exit;

        $catid = intval($_GET['catid']);
        $modelid = intval($_GET['modelid']);
        $this->db->set_model($modelid);
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
            $this->db->table_name = $this->db->table_name.'_data';
            $r = $this->db->get_one(array('id'=>$id), 'addition_field');
            $data = string2array($r['addition_field']);
        }
        $categorys = getcache('category_yp_'.$modelid, 'yp');
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
                    $formainfos_d = array_merge($forminfos_data['senior'], $forminfos_data['base']);

                    $forminfos = array();
                    foreach($formainfos_d as $_fk=>$_fv) {
                        $_fv['form'] = str_replace(array('name="info[', 'name=\'info['), array('name="info[addition_field][', 'name=\'info[addition_field]['), $_fv['form']);
                        if($_fv['isomnipotent']) continue;
                        if($_fv['formtype']=='omnipotent') {
                            foreach($formainfos_d as $_fm=>$_fm_value) {
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
    }
}
?>