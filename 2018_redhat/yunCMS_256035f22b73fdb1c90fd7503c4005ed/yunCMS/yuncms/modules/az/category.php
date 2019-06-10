<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

pc_base::load_app_func('global');
class category extends admin {
	private $db;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('category_model');
		$this->siteid = $this->get_siteid();
	}
	/**
	 * 管理栏目
	 */
	public function init () {
		$show_pc_hash = '';
		$tree = pc_base::load_sys_class('tree');
		$category_items = array();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$categorys = array();
		//如果没有传递模型ID，取企业库的模型ID
		$modelid = $yp_company_modelid = '';
		if (isset($_GET['modelid']) && $_GET['modelid']) {
			$modelid = intval($_GET['modelid']);
		}
		$category_items = getcache('category_yp_items_'.$modelid,'yp');
		$sitemodel_db = pc_base::load_model('sitemodel_model');
		$yp_company_model = $sitemodel_db->get_one(array('tablename'=>'yp_company', 'type'=>'4'), 'modelid');
		$yp_company_modelid = $yp_company_model['modelid'];
		if (!$modelid || $modelid == $yp_company_modelid) {
			$modelid = $yp_company_modelid;
			$modename = L('business_model');
		}
		//获取黄页的模型
		$yp_models = getcache('yp_model', 'model');

		$result = getcache('category_yp_'.$modelid,'yp');
		$show_detail = count($result) < 500 ? 1 : 0;
		$parentid = $_GET['parentid'] ? $_GET['parentid'] : 0;

		if(!empty($result)) {
			foreach($result as $r) {
				$r['str_manage'] = '';
				if(!$show_detail) {
					if($r['parentid']!=$parentid) continue;
					$r['parentid'] = 0;
					$r['str_manage'] .= '<a href="?m=yp&c=category&a=init&parentid='.$r['catid'].'&modelid='.$modelid.'&menuid='.$_GET['menuid'].'&s='.$r['type'].'&pc_hash='.$_SESSION['pc_hash'].'">'.L('manage_sub_category').'</a> | ';
				}
				$r['str_manage'] .= '<a href="javascript:add_sub(\''.$r['catid'].'\', \''.new_addslashes($r['catname']).'\');void(0);">'.L('add_sub_category').'</a> | ';

				$r['str_manage'] .= '<a href="javascript:edit(\''.$r['catid'].'\', \''.new_addslashes($r['catname']).'\');void(0);">'.L('edit').'</a> | <a href="javascript:confirmurl(\'?m=yp&c=category&a=delete&catid='.$r['catid'].'&modelid='.$r['modelid'].'&menuid='.$_GET['menuid'].'\',\''.L('confirm',array('message'=>addslashes($r['catname']))).'\')">'.L('delete').'</a> ';
				if($r['child'] || !isset($category_items[$r['catid']])) {
					$r['items'] = '';
				} else {
					$r['items'] = $category_items[$r['catid']];
				}
				$setting = string2array($r['setting']);
				if($r['url']) {
					$r['url'] = "<a href='$r[url]' target='_blank'>".L('vistor')."</a>";
				} else {
					$r['url'] = "<a href='?m=yp&c=category&a=public_cache&menuid=".$_GET['menuid']."&module=yp&modelid=".$modelid."'><font color='red'>".L('update_backup')."</font></a>";
				}
				$categorys[$r['catid']] = $r;
			}
		}
		$str  = "<tr>
					<td align='center'><input name='listorders[\$id]' type='text' size='10' value='\$listorder' class='input-text-c'></td>
					<td align='center'>\$id</td>
					<td >\$spacer\$catname</td>
					<td align='center'>\$items</td>
					<td align='center'>\$url</td>
					<td align='center' >\$str_manage</td>
				</tr>";
		$tree->init($categorys);
		$categorys = $tree->get_tree(0, $str);
		$show_header = true;
		if ($modename) {
			$title = L('business_model');
		} else {
			$title = $yp_models[$modelid]['name'];
		}
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=yp&c=category&a=add&modelid='.$modelid.'\', title:\''.L('yp_add').$title.L('yp_category').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('yp_add').$title.L('yp_category'));
		include $this->admin_tpl('category_manage');
	}
	/**
	 * 添加栏目
	 */
	public function add() {
		if(isset($_POST['dosubmit'])) {
			pc_base::load_sys_func('iconv');
			$_POST['info']['type'] = intval($_POST['type']);

			if(isset($_POST['batch_add']) && empty($_POST['batch_add'])) {
				if($_POST['info']['catname']=='') showmessage(L('input_catname'));
			}

			$_POST['info']['siteid'] = $this->siteid;
			$_POST['info']['module'] = 'yp';
			$setting = $_POST['setting'];
			$_POST['info']['setting'] = array2string($setting);
            if ($_POST['additional']) {
                $_POST['info']['additional'] = array2string($_POST['additional']);
            } else {
                $_POST['info']['additional'] = '';
            }

			if(!isset($_POST['batch_add']) || empty($_POST['batch_add'])) {
				$catname = CHARSET == 'gbk' ? $_POST['info']['catname'] : iconv('utf-8','gbk',$_POST['info']['catname']);
				$letters = new_addslashes(gbk_to_pinyin($catname));
				$_POST['info']['letter'] = strtolower(implode('', $letters));
				if (isset($_POST['info']['modelid']) && !empty($_POST['info']['modelid'])) {
					$modelids = $_POST['info']['modelid'];
					unset($_POST['info']['modleid']);
				}
				$data = $_POST['info'];
				$catid = $this->db->insert($data, true);
			} else {
				$end_str = '';
				$batch_adds = explode("\n", $_POST['batch_add']);
				if ($_POST['info']['parentid']) {
					$parentid = $_POST['info']['parentid'];
				}
				foreach ($batch_adds as $_v) {
					if(trim($_v)=='') continue;
					$_v = str_replace(array(' ', "\n", "\r"), '', $_v);
					$_POST['info']['catname'] = $_v;
					$level = substr_count($_v, '-')+1;
					if ($level>1) {
						$_POST['info']['catname'] = $_v = str_replace('-', '', $_v);
					}
					$letters = new_addslashes(gbk_to_pinyin($_v));
					if ($level>1) {
						$plevel = intval($level-1);
						$_POST['info']['parentid'] = ${'parentid_'.$plevel};
					} else {
						$_POST['info']['parentid'] = $parentid;
					}
					$_POST['info']['letter'] = strtolower(implode('', $letters));
					$data = $_POST['info'];
					${'parentid_'.$level} = $this->db->insert($_POST['info'], true);
				}
			}
			$modelid = $_POST['modelid'];
			$this->cache($modelid);
			showmessage(L('add_success'), HTTP_REFERER, '', 'add');
		} else {

			$show_validator = $select_modelid = '';

			$modelid = intval($_GET['modelid']);
			if (!$modelid) showmessage(L('select_catgory_for_model'));
			$categorys = getcache('category_yp_'.$modelid, 'yp');
            $commenttypeid = '';
			if(isset($_GET['parentid'])) {
				$parentid = $_GET['parentid'];
				$parent_arr = $parent_addition = array();
				if ($categorys[$parentid]['parentid']) {
					$parent_arr = substr($categorys[$parentid]['arrparentid'], 2).','.$parentid;
					$parent_arr = explode(',', $parent_arr);
				} else {
					$parent_arr[] = $parentid;
				}
				foreach ($parent_arr as $par) {
					$r = $this->db->get_one(array('catid'=>$par), 'additional, commenttypeid');
					$r1 = string2array($r['additional']);
                    if ($r['commenttypeid']) {
                        $commenttypeid = $r['commenttypeid'];
                    }
					if (!empty($r1)) {
						if (empty($parent_addition)) {
							$parent_addition = $r1;
						} else {
							$parent_addition = array_merge($parent_addition, $r1);
						}
					}
					unset($r, $r1);
				}
			}
			$additional_field = getcache('additional_field', 'model');
            $dianping_types = getcache('dianping_type', 'dianping');
            if (is_array($dianping_types) && !empty($dianping_types)) {
                foreach ($dianping_types as $did => $dp) {
                    $dianping_arr[$did] = $dp['type_name'];
                }
            }
			pc_base::load_sys_class('form','',0);

			$show_header = $show_validator = $show_scroll = 1;
			include $this->admin_tpl('category_add');
		}
	}
	/**
	 * 修改栏目
	 */
	public function edit() {
		if(isset($_POST['dosubmit'])) {
			pc_base::load_sys_func('iconv');
			$catid = 0;
			$catid = intval($_POST['catid']);
			$setting = $_POST['setting'];
			//栏目生成静态配置

			$_POST['info']['setting'] = array2string($setting);
			$_POST['info']['module'] = 'yp';
			$modelid = $_POST['modelid'] ? intval($_POST['modelid']) : 0;
			unset($_POST['modelid']);
			$catname = CHARSET == 'gbk' ? $_POST['info']['catname'] : iconv('utf-8','gbk',$_POST['info']['catname']);
			$letters = new_addslashes(gbk_to_pinyin($catname));
			$_POST['info']['letter'] = strtolower(implode('', $letters));
            if ($_POST['additional']) {
                $_POST['info']['additional'] = array2string($_POST['additional']);
            } else {
                $_POST['info']['additional'] = '';
            }
			$this->db->update($_POST['info'],array('catid'=>$catid,'siteid'=>$this->siteid));
			$usechild = intval($_POST['usechild']);
			if ($usechild) {
				$arrchildid = $this->db->get_one(array('catid'=>$catid), 'arrchildid');
				if(!empty($arrchildid['arrchildid'])) {
					$arrchildid_arr = explode(',', $arrchildid['arrchildid']);
					if(!empty($arrchildid_arr)) {
						$commenttypeid = $_POST['info']['commenttypeid'];
						foreach ($arrchildid_arr as $arr_v) {
							$this->db->update(array('commenttypeid'=>$commenttypeid), array('catid'=>$arr_v));
						}
					}
				}
			}
			$this->cache($modelid);
			//更新附件状态
			if($_POST['info']['image'] && pc_base::load_config('system','attachment_stat')) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($_POST['info']['image'],'catid-'.$catid,1);
			}
			showmessage(L('operation_success'),HTTP_REFERER, '', 'edit');
		} else {

			$show_validator = $catid = $r = '';
			$catid = intval($_GET['catid']);
			$modelid = intval($_GET['modelid']);
			if (!$modelid) showmessage(L('select_catgory_for_model'));
			pc_base::load_sys_class('form','',0);
			$r = $this->db->get_one(array('catid'=>$catid));
            $self_commenttypeid = $r['commenttypeid'];
			if($r) extract($r);
            $categorys = getcache('category_yp_'.$modelid, 'yp');
			$parent_arr = $parent_addition = array();
			if($parentid) {
				if ($categorys[$parentid]['parentid']) {
					$parent_arr = substr($categorys[$parentid]['arrparentid'], 2).','.$parentid;
					$parent_arr = explode(',', $parent_arr);
				} else {
					$parent_arr[] = $parentid;
				}
				foreach ($parent_arr as $par) {
					$r = $this->db->get_one(array('catid'=>$par), 'additional, commenttypeid');
                    if ($r['commenttypeid']) {
                        $commenttypeid = $r['commenttypeid'];
                    }
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
			}
            if ($self_commenttypeid) {
                $commenttypeid = $self_commenttypeid;
            }
			$additional_field = getcache('additional_field', 'model');
			$setting = string2array($setting);
			$additional = string2array($additional);
            $dianping_types = getcache('dianping_type', 'dianping');
            if (is_array($dianping_types) && !empty($dianping_types)) {
                foreach ($dianping_types as $did => $dp) {
                    $dianping_arr[$did] = $dp['type_name'];
                }
            }

			$show_header = $show_validator = $show_scroll = 1;
			include $this->admin_tpl('category_edit');
		}
	}

    /**
     * 获取附件字段
     */
    public function public_get_additional() {
        $catid = intval($_GET['catid']);
        $parentid = intval($_GET['parentid']);
        $modelid = intval($_GET['modelid']);
        $categorys = getcache('category_yp_'.$modelid, 'yp');
        if ($parentid) {
        	$parent_arr = $parent_addition = array();
			if ($categorys[$parentid]['parentid']) {
				$parent_arr = substr($categorys[$parentid]['arrparentid'], 2).','.$parentid;
				$parent_arr = explode(',', $parent_arr);
			} else {
				$parent_arr[] = $parentid;
			}
			foreach ($parent_arr as $par) {
				$r = $this->db->get_one(array('catid'=>$par), 'additional');
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
        }
        if ($catid) {
            $r = $this->db->get_one(array('catid'=>$catid), 'additional');
			$additional = string2array($r['additional']);
        }
        $additional_field = getcache('additional_field', 'model');
        $data = '';
        if (is_array($additional_field)) {
            foreach ($additional_field as $a) {
                $data .= '<label class="ib" style="width:125px;">';
                if (in_array($a['fieldid'], $parent_addition)) {
                    $data .= '<input type="checkbox" name="additional[]" disabled checked="checked" value="'.$a['fieldid'].'">';
                } else {
	                $data .= '<input type="checkbox" name="additional[]" ';
	                if (in_array($a['fieldid'], $additional)) {
	                    $data .= 'checked ';
	                }
	                $data .= 'value="'.$a['fieldid'].'">';
                }
                $data .= ' '.$a['name'].'</label>';
            }
        }
        exit($data);
    }
	/**
	 * 排序
	 */
	public function listorder() {
		if(isset($_POST['dosubmit'])) {
			$modelid = intval($_GET['modelid']);
			foreach($_POST['listorders'] as $id => $listorder) {
				$this->db->update(array('listorder'=>$listorder),array('catid'=>$id));
			}
			$this->cache($modelid);
			showmessage(L('operation_success'),HTTP_REFERER);
		} else {
			showmessage(L('operation_failure'));
		}
	}
	/**
	 * 删除分类
	 */
	public function delete() {
		$catid = intval($_GET['catid']);
		$modelid = intval($_GET['modelid']);
		$this->delete_child($catid);
		$this->db->delete(array('catid'=>$catid));
		$this->cache($modelid);
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	/**
	 * 递归删除栏目
	 * @param $catid 要删除的栏目id
	 */
	private function delete_child($catid) {
		$catid = intval($catid);
		if (empty($catid)) return false;
		$r = $this->db->get_one(array('parentid'=>$catid));
		if($r) {
			$this->delete_child($r['catid']);
			$this->db->delete(array('catid'=>$r['catid']));
		}
		return true;
	}
	/**
	 * 更新缓存
	 */
	public function cache($modelid = 0) {
		$categorys = $yp_models = $this->categorys = array();

		if (!$modelid) {
			//获取企业库模型ID
			$sitemodel_db = pc_base::load_model('sitemodel_model');
			$yp_company_model = $sitemodel_db->get_one(array('tablename'=>'yp_company', 'type'=>'4'), 'modelid');
			$yp_company_modelid = $yp_company_model['modelid'];
			$yp_models = getcache('yp_model', 'model');
			if (is_array($yp_models)) {
				$yp_models = array_keys($yp_models);
			}
			$yp_models[] = $yp_company_modelid;
			if (is_array($yp_models)) {
				foreach ($yp_models as $mid) {
					$datas = $this->db->select(array('modelid'=>$mid),'catid,items',10000);
					$array = array();
					foreach ($datas as $r) {
						$array[$r['catid']] = $r['items'];
					}
					setcache('category_yp_items_'.$modelid, $array,'yp');
					$result = $this->db->select(array('module'=>'yp', 'modelid'=>$mid),'*',20000,'listorder ASC');
					foreach ($result as $r) {
						unset($r['module'],$r['catdir']);
						$setting = string2array($r['setting']);
						$r['meta_title'] = $setting['meta_title'];
						$r['meta_keywords'] = $setting['meta_keywords'];
						$r['meta_description'] = $setting['meta_description'];
						$categorys[$r['catid']] = $r;
					}
					setcache('category_yp_'.$mid,$categorys,'yp');
					$categorys = array();
				}
			}
		} else {
			$modelid = intval($modelid);
			$datas = $this->db->select(array('modelid'=>$modelid),'catid,items',10000);
			$array = array();
			foreach ($datas as $r) {
				$array[$r['catid']] = $r['items'];
			}
			setcache('category_yp_items_'.$modelid, $array,'yp');
			$result = $this->db->select(array('module'=>'yp', 'modelid'=>$modelid),'*',20000,'listorder ASC');
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
		}
		return true;
	}
	/**
	 * 更新缓存并修复栏目
	 */
	public function public_cache() {
		$this->modelid = intval($_GET['modelid']);
		$this->repair($this->modelid);
		$this->cache($this->modelid);
		$yp_models = getcache('yp_model', 'model');
		if ($yp_models[$this->modelid]) {
			$modelname = $yp_models[$this->modelid]['name'];
		} else {
			$modelname = L('business_model');
		}
		showmessage(L('updates').$modelname.L('category_successfull'),'?m=yp&c=category&a=init&module=yp&modelid='.$this->modelid);
	}
	/**
	* 修复栏目数据
	* @param intval $modelid 模型ID
	*/
	private function repair($modelid) {
		pc_base::load_sys_func('iconv');
		@set_time_limit(600);
		$html_root = pc_base::load_config('system','html_root');
		$this->categorys = $categorys = array();
		$this->categorys = $categorys = $this->db->select(array('module'=>'yp', 'modelid'=>$modelid), '*', '', 'listorder ASC, catid ASC', '', 'catid');
		$yp_models = getcache('yp_model', 'model');
		$MODEL = $yp_models[$modelid];
		//判断是不是企业库模型的分类，企业库模型分类的url地址特殊
		$is_company_model = false;
		if (!$MODEL || !in_array($modelid, $MODEL)) {
			$is_company_model = true;
		}
		
		//$this->get_categorys($categorys);
		if(is_array($this->categorys)) {
			foreach($this->categorys as $catid => $cat) {
				$arrparentid = $this->get_arrparentid($catid);
				$setting = string2array($cat['setting']);
				$arrchildid = $this->get_arrchildid($catid);
				$child = is_numeric($arrchildid) ? 0 : 1;
				if($categorys[$catid]['arrparentid']!=$arrparentid || $categorys[$catid]['arrchildid']!=$arrchildid || $categorys[$catid]['child']!=$child) $this->db->update(array('arrparentid'=>$arrparentid,'arrchildid'=>$arrchildid,'child'=>$child),array('catid'=>$catid));

				$catname = $cat['catname'];
				$letters = gbk_to_pinyin($catname);
				$letter = new_addslashes(strtolower(implode('', $letters)));
				$listorder = $cat['listorder'] ? $cat['listorder'] : $catid;

				//不生成静态时
				$url = $this->update_url($catid,$is_company_model);
				if($cat['url']!=$url) $this->db->update(array('url'=>$url), array('catid'=>$catid));

				$this->db->update(array('letter'=>$letter,'listorder'=>$listorder), array('catid'=>$catid));
			}
		}

		//删除在非正常显示的栏目
		foreach($this->categorys as $catid => $cat) {
			if($cat['parentid'] != 0 && !isset($this->categorys[$cat['parentid']])) {
				$this->db->delete(array('catid'=>$catid));
			}
		}
		return true;
	}

	/**
	 * 找出子目录列表
	 * @param array $categorys
	 */
	private function get_categorys($categorys = array()) {
		if (is_array($categorys) && !empty($categorys)) {
			foreach ($categorys as $catid => $c) {
				$this->categorys[$catid] = $c;
				$result = array();
				foreach ($this->categorys as $_k=>$_v) {
					if($_v['parentid']) $result[] = $_v;
				}
				$this->get_categorys($r);
			}
		}
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
        $setting = getcache('yp_setting', 'yp');
        if ($is_company_model) {
        	if ($setting['enable_rewrite']) {
        		$url = 'yp-list-company-'.$catid;
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

	/**
	 *
	 * 获取父栏目ID列表
	 * @param integer $catid              栏目ID
	 * @param array $arrparentid          父目录ID
	 * @param integer $n                  查找的层次
	 */
	private function get_arrparentid($catid, $arrparentid = '', $n = 1) {
		if($n > 5 || !is_array($this->categorys) || !isset($this->categorys[$catid])) return false;
		$parentid = $this->categorys[$catid]['parentid'];
		$arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
		if($parentid) {
			$arrparentid = $this->get_arrparentid($parentid, $arrparentid, ++$n);
		} else {
			$this->categorys[$catid]['arrparentid'] = $arrparentid;
		}
		$parentid = $this->categorys[$catid]['parentid'];
		return $arrparentid;
	}

	/**
	 *
	 * 获取子栏目ID列表
	 * @param $catid 栏目ID
	 */
	private function get_arrchildid($catid) {
		$arrchildid = $catid;
		if(is_array($this->categorys)) {
			foreach($this->categorys as $id => $cat) {
				if($cat['parentid'] && $id != $catid && $cat['parentid']==$catid) {
					$arrchildid .= ','.$this->get_arrchildid($id);
				}
			}
		}
		return $arrchildid;
	}

	/**
	 * 更新权限
	 * @param  $catid
	 * @param  $priv_datas
	 * @param  $is_admin
	 */
	private function update_priv($catid,$priv_datas,$is_admin = 1) {
		$this->priv_db = pc_base::load_model('category_priv_model');
		$this->priv_db->delete(array('catid'=>$catid,'is_admin'=>$is_admin));
		if(is_array($priv_datas) && !empty($priv_datas)) {
			foreach ($priv_datas as $r) {
				$r = explode(',', $r);
				$action = $r[0];
				$roleid = $r[1];
				$this->priv_db->insert(array('catid'=>$catid,'roleid'=>$roleid,'is_admin'=>$is_admin,'action'=>$action,'siteid'=>$this->siteid));
			}
		}
	}

	/**
	 * 检查栏目权限
	 * @param $action 动作
	 * @param $roleid 角色
	 * @param $is_admin 是否为管理组
	 */
	private function check_category_priv($action,$roleid,$is_admin = 1) {
		$checked = '';
		foreach ($this->privs as $priv) {
			if($priv['is_admin']==$is_admin && $priv['roleid']==$roleid && $priv['action']==$action) $checked = 'checked';
		}
		return $checked;
	}
	/**
	 * 重新统计栏目信息数量
	 */
	public function count_items() {
		$this->content_db = pc_base::load_model('content_model');
		$result = getcache('category_content_'.$this->siteid,'yp');
		foreach($result as $r) {
			if($r['type'] == 0) {
				$modelid = $r['modelid'];
				$this->content_db->set_model($modelid);
				$number = $this->content_db->count(array('catid'=>$r['catid']));
				$this->db->update(array('items'=>$number),array('catid'=>$r['catid']));
			}
		}
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	/**
	 * json方式加载模板
	 */
	public function public_tpl_file_list() {
		$style = isset($_GET['style']) && trim($_GET['style']) ? trim($_GET['style']) : exit(0);
		$catid = isset($_GET['catid']) && intval($_GET['catid']) ? intval($_GET['catid']) : 0;
		$batch_str = isset($_GET['batch_str']) ? '['.$catid.']' : '';
		if ($catid) {
			$cat = getcache('category_content_'.$this->siteid,'yp');
			$cat = $cat[$catid];
			$cat['setting'] = string2array($cat['setting']);
		}
		pc_base::load_sys_class('form','',0);
		if($_GET['type']==1) {
			$html = array('page_template'=>form::select_template($style, 'content',(isset($cat['setting']['page_template']) && !empty($cat['setting']['page_template']) ? $cat['setting']['page_template'] : 'category'),'name="setting'.$batch_str.'[page_template]"','page'));
		} else {
			$html = array('category_template'=> form::select_template($style, 'content',(isset($cat['setting']['category_template']) && !empty($cat['setting']['category_template']) ? $cat['setting']['category_template'] : 'category'),'name="setting'.$batch_str.'[category_template]"','category'),
				'list_template'=>form::select_template($style, 'content',(isset($cat['setting']['list_template']) && !empty($cat['setting']['list_template']) ? $cat['setting']['list_template'] : 'list'),'name="setting'.$batch_str.'[list_template]"','list'),
				'show_template'=>form::select_template($style, 'content',(isset($cat['setting']['show_template']) && !empty($cat['setting']['show_template']) ? $cat['setting']['show_template'] : 'show'),'name="setting'.$batch_str.'[show_template]"','show')
			);
		}
		if ($_GET['module']) {
			unset($html);
			if ($_GET['templates']) {
				$templates = explode('|', $_GET['templates']);
				if ($_GET['id']) $id = explode('|', $_GET['id']);
				if (is_array($templates)) {
					foreach ($templates as $k => $tem) {
						$t = $tem.'_template';
						if ($id[$k]=='') $id[$k] = $tem;
						$html[$t] = form::select_template($style, $_GET['module'], $id[$k], 'name="'.$_GET['name'].'['.$t.']" id="'.$t.'"', $tem);
					}
				}
			}

		}
		if (CHARSET == 'gbk') {
			$html = array_iconv($html, 'gbk', 'utf-8');
		}
		echo json_encode($html);
	}

	/**
	 * 快速进入搜索
	 */
	public function public_ajax_search() {
		if($_GET['catname']) {
			if(preg_match('/([a-z]+)/i',$_GET['catname'])) {
				$field = 'letter';
				$catname = strtolower(trim($_GET['catname']));
			} else {
				$field = 'catname';
				$catname = trim($_GET['catname']);
				if (CHARSET == 'gbk') $catname = iconv('utf-8','gbk',$catname);
			}
			$result = $this->db->select("$field LIKE('$catname%') AND siteid='$this->siteid' AND child=0",'catid,type,catname,letter',10);
			if (CHARSET == 'gbk') {
				$result = array_iconv($result, 'gbk', 'utf-8');
			}
			echo json_encode($result);
		}
	}

	public function export() {
		$modelid = intval($_GET['modelid']);
		if (!$modelid) showmessage(L('illegal_parameters'));
		$tree = pc_base::load_sys_class('tree');
		$category_items = array();
		$tree->icon = array('','','');
		$tree->nbsp = '-';
		$categorys = array();
		$result = getcache('category_yp_'.$modelid,'yp');
		if(!empty($result)) {
			foreach($result as $r) {
				$categorys[$r['catid']]['catname'] = $r['catname'];
				$categorys[$r['catid']]['parentid'] = $r['parentid'];
				$categorys[$r['catid']]['catid'] = $r['catid'];
				$categorys[$r['catid']]['child'] = $r['child'];
			}
		}
		$str  = "\$spacer\$catname\n";
		$tree->init($categorys);
		$categorys = $tree->get_tree(0, $str);
		$show_header = $show_validator = $show_scroll = 1;
		include $this->admin_tpl('catgory_export');
	}
}
?>