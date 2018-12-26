<?php
defined('IN_PHPCMS') or exit('No permission resources.');

pc_base::load_app_class('admin','admin',0);
class template extends admin {
	private $tpl;

	function __construct() {
		parent::__construct();
		$this->tpl = PC_PATH.'templates'.DIRECTORY_SEPARATOR.pc_base::load_config('system', 'tpl_name').DIRECTORY_SEPARATOR.'yp'.DIRECTORY_SEPARATOR.'companytplnames.php';
	}

	function init () {
		if (file_exists($this->tpl)) {
			$companytplnames = include $this->tpl;
		}
		$group_cache = getcache('grouplist','member');
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=yp&c=template&a=add\', title:\''.L('add_new_template').'\', width:\'600\', height:\'230\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);',L('add_new_template'));
		$show_header = true;
		include $this->admin_tpl('template_list');
	}

	//添加新模板
	public function add() {
		if (isset($_POST['dosubmit'])) {
			if (empty($_POST['info']['title'])) showmessage(L('fill_template_name'));
			if (empty($_POST['info']['dir'])) showmessage(L('fill_template_path'));
			if (empty($_POST['info']['thumb'])) showmessage(L('upload_thumbnail'));
			if ($_POST['filezip']) {
				pc_base::load_app_class('pclzip', '', 0);

				$filezip = $_POST['filezip'];
				$filezip = str_replace(array(APP_PATH,'/'), array(PHPCMS_PATH,DIRECTORY_SEPARATOR), $filezip);
				$pclzip_source_path = CACHE_PATH.'caches_yp'.DIRECTORY_SEPARATOR.'uptemplate';

				//解压缩
				$archive = new PclZip($filezip);

				if($archive->extract(PCLZIP_OPT_PATH, $pclzip_source_path, PCLZIP_OPT_REPLACE_NEWER) == 0) {
					die("Error : ".$archive->errorInfo(true));
				}

				//拷贝upload文件夹到企业模板目录
				$copy_from = $pclzip_source_path.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR;
				$copy_to = PC_PATH.'templates'.DIRECTORY_SEPARATOR.pc_base::load_config('system','tpl_name').DIRECTORY_SEPARATOR.'yp'.DIRECTORY_SEPARATOR.'com_'.$_POST['info']['dir'].DIRECTORY_SEPARATOR;

				$this->copyfailnum = 0;
				$this->copyDir($copy_from, $copy_to);
				$this->copyDir($copy_to.'style'.DIRECTORY_SEPARATOR, PHPCMS_PATH.DIRECTORY_SEPARATOR.'statics'.DIRECTORY_SEPARATOR.'yp'.DIRECTORY_SEPARATOR.'com_'.$_POST['info']['dir'].DIRECTORY_SEPARATOR);

				$attachment_db = pc_base::load_model('attachment_model');
				//删除上传的模板压缩包
				unlink($filezip);
				//删除附件表中的记录
				$uploadfile_name = str_replace(pc_base::load_config('system','upload_url'), '', $_POST['filezip']);
				$attachment_db->delete(array('filepath'=>$uploadfile_name));
				//删除解压的缓存文件
		 		$this->deleteDir($pclzip_source_path);
				$this->deleteDir($copy_to.'style'.DIRECTORY_SEPARATOR);
				//检查文件操作权限，是否复制成功
				$uploadthumb_name = str_replace(pc_base::load_config('system','upload_url'), '', $_POST['info']['thumb']);
				if($this->copyfailnum > 0) {
					$attachment_db->delete(array('filepath'=>$uploadthumb_name));
					$uploadthumb_dir = str_replace(array(APP_PATH,'/'), array(PHPCMS_PATH,DIRECTORY_SEPARATOR), $_POST['info']['thumb']);
					unlink($uploadthumb_dir);
					showmessage(L('template_upload_not_successful'), '?m=yp&c=template&a=init');
				}
			}
			if (file_exists($this->tpl)) {
				$companytplnames = include $this->tpl;
				$ids = array_keys($companytplnames);
				$id = max($ids)+1;
			} else {
				$id = 1;
			}
			$r = $attachment_db->get_one(array('filepath'=>$uploadthumb_name), 'aid');
			$aid = $r['aid'];
			$companytplnames[$id] = array(
										'id' => $id,
										'title' => $_POST['info']['title'],
										'dir' => 'com_'.$_POST['info']['dir'],
										'thumb' => $_POST['info']['thumb'],
										'aid' => $aid,
									);
			$data = "<?php\nreturn ".var_export($companytplnames, true).";\n?>";
			//是否开启互斥锁
			if(pc_base::load_config('system', 'lock_ex')) {
				$file_size = file_put_contents($this->tpl, $data, LOCK_EX);
			} else {
				$file_size = file_put_contents($this->tpl, $data);
			}
			$attachment_db->update(array('status'=>1), array('aid'=>$aid));
			showmessage(L('add_success'), '', '', 'add');
		} else {
			$show_header = $show_validator = $show_scroll = 1;
			pc_base::load_sys_class('form', 0, 0);
			include $this->admin_tpl('template_add');
		}
	}

	//修改模板名称
	public function public_edit() {
		$id =intval($_GET['id']);
		$title = $_GET['title'];
		if (!$id || !$title) exit('0');
		if (CHARSET != 'utf-8') {
			$title = iconv('UTF-8', CHARSET, $title);
		}
		if (file_exists($this->tpl)) {
			$companytplnames = include $this->tpl;
			if (is_array($companytplnames)) {
				$companytplnames[$id]['title'] = $title;
				$data = "<?php\nreturn ".var_export($companytplnames, true).";\n?>";
				//是否开启互斥锁
				if(pc_base::load_config('system', 'lock_ex')) {
					$file_size = file_put_contents($this->tpl, $data, LOCK_EX);
				} else {
					$file_size = file_put_contents($this->tpl, $data);
				}
				exit('1');
			} else {
				exit('0');
			}
		} else {
			exit('0');
		}
	}

	//设置默认模板
	public function tpl_default () {
		$id = intval($_GET['id']);
		if (!$id) showmessage(L('illegal_operation'));
		if (file_exists($this->tpl)) {
			$companytplnames = include $this->tpl;
			if (is_array($companytplnames)) {
				foreach ($companytplnames as $tplid => $tpl) {
					if ($tplid == $id) $companytplnames[$tplid]['defaulttpl'] = 1;
					else $companytplnames[$tplid]['defaulttpl'] = 0;
				}
				$data = "<?php\nreturn ".var_export($companytplnames, true).";\n?>";
				//是否开启互斥锁
				if(pc_base::load_config('system', 'lock_ex')) {
					$file_size = file_put_contents($this->tpl, $data, LOCK_EX);
				} else {
					$file_size = file_put_contents($this->tpl, $data);
				}
				showmessage(L('operation_success'), '?m=yp&c=template&a=init');
			} else {
				showmessage(L('upload_business_templates'), '?m=yp&c=template&a=add');
			}
		} else {
			showmessage(L('upload_business_templates'), '?m=yp&c=template&a=add');
		}
	}

	//设置模板权限
	public function edit_priv () {
		$id = intval($_GET['id']);
		if (file_exists($this->tpl)) {
			$companytplnames = include $this->tpl;
		}
		if (isset($_POST['dosubmit'])) {
			$groups = implode(',', $_POST['priv_groupid']);
			$companytplnames[$id]['groups'] = $groups;
			$data = "<?php\nreturn ".var_export($companytplnames, true).";\n?>";
			//是否开启互斥锁
			if(pc_base::load_config('system', 'lock_ex')) {
				$file_size = file_put_contents($this->tpl, $data, LOCK_EX);
			} else {
				$file_size = file_put_contents($this->tpl, $data);
			}
			showmessage(L('operation_success'), '', '', 'edit_priv');
		} else {
			$group_cache = getcache('grouplist','member');
			$current_priv = $companytplnames[$id]['groups'];
			$current_priv = explode(',', $current_priv);
			$show_header = true;
			include $this->admin_tpl('template_priv');
		}
	}

	//模板禁用、启用
	public function disabled () {
		$id = intval($_GET['id']);
		$t = intval($_GET['t']);
		$companytplnames = include $this->tpl;
		$companytplnames[$id]['disabled'] = $t;
		$data = "<?php\nreturn ".var_export($companytplnames, true).";\n?>";
		//是否开启互斥锁
		if(pc_base::load_config('system', 'lock_ex')) {
			$file_size = file_put_contents($this->tpl, $data, LOCK_EX);
		} else {
			$file_size = file_put_contents($this->tpl, $data);
		}
		showmessage(L('operation_success'), '?m=yp&c=template&a=init');
	}

	//删除模板
	public function delete () {
		$id = intval($_GET['id']);
		$companytplnames = include $this->tpl;
		$aid = $companytplnames[$id]['aid'];
		$attachment_db = pc_base::load_model('attachment_model');
		$r = $attachment_db->get_one(array('aid'=>$aid), 'filepath');
		$file_dir = pc_base::load_config('system','upload_path').$r['filepath'];
		@unlink($file_dir);
		$attachment_db->delete(array('aid'=>$aid));
		unset($companytplnames[$id]);
		$data = "<?php\nreturn ".var_export($companytplnames, true).";\n?>";
		//是否开启互斥锁
		if(pc_base::load_config('system', 'lock_ex')) {
			$file_size = file_put_contents($this->tpl, $data, LOCK_EX);
		} else {
			$file_size = file_put_contents($this->tpl, $data);
		}
		showmessage(L('operation_success'), '?m=yp&c=template&a=init');
	}

	/**
	 * 拷贝模板文件至企业模板存放目录
	 * @param $dirFrom 文件源地址
	 * @param $dirTo 企业模板地址
	 */
	private function copyDir($dirFrom,$dirTo) {
	    //如果遇到同名文件无法复制，则直接退出
	    if(is_file($dirTo)){
	        die("have on pri $dirTo");
	    }
	    //如果目录不存在，则建立之
	    if(!file_exists($dirTo)){
	        mkdir($dirTo);
	    }

	    $handle = opendir($dirFrom); //打开当前目录

	    //循环读取文件
	    while(false !== ($file = readdir($handle))) {
	    	if($file != '.' && $file != '..'){ //排除"."和"."
		        //生成源文件名
			    $fileFrom = $dirFrom.DIRECTORY_SEPARATOR.$file;
		     	//生成目标文件名
		        $fileTo = $dirTo.DIRECTORY_SEPARATOR.$file;
		        if(is_dir($fileFrom)){ //如果是子目录，则进行递归操作
		            $this->copyDir($fileFrom, $fileTo);
		        } else { //如果是文件，则直接用copy函数复制
					if (!copy($fileFrom, $fileTo)) {
						$this->copyfailnum++;
					    echo "copy $fileFrom to $fileTo failed \n";
					}
		        }
	    	}
	    }
	}

	/**
	 * 删除解压的缓存文件
	 * @param string $dirName 删除文件的目录
	 * @return boolen true or false
	 */
	private function deleteDir($dirName){
	    $result = false;
	    if(! is_dir($dirName)){
	        echo " $dirName is not a dir!";
	        exit(0);
	    }
	    $handle = opendir($dirName); //打开目录
	    while(($file = readdir($handle)) !== false) {
	        if($file != '.' && $file != '..'){ //排除"."和"."
	            $dir = $dirName.DIRECTORY_SEPARATOR.$file;
	            //$dir是目录时递归调用deleteDir,是文件则直接删除
	            is_dir($dir) ? $this->deleteDir($dir) : unlink($dir);
	        }
	    }
	    closedir($handle);
	    $result = rmdir($dirName) ? true : false;
	    return $result;
	}

	//检查模板名称是否存在
	public function public_check_title() {
		$title = $_GET['title'];
		if (CHARSET != 'utf-8') {
			$title = iconv('utf-8', CHARSET, $title);
		}
		if (file_exists($this->tpl)) {
			$companytplname = include $this->tpl;
			if (is_array($companytplname)) {
				foreach ($companytplname as $tpl) {
					if ($tpl['title']==$title) exit('0');
				}
			}
			exit('1');
		} else {
			exit('1');
		}
	}

	//检查模板路径是否存在
	public function public_check_dir() {
		$dir = 'com_'.$_GET['dir'];
		if (file_exists($this->tpl)) {
			$companytplname = include $this->tpl;
			if (is_array($companytplname)) {
				foreach ($companytplname as $tpl) {
					if ($tpl['dir']==$dir) exit('0');
				}
			}
			exit('1');
		} else {
			exit('1');
		}
	}
}
?>