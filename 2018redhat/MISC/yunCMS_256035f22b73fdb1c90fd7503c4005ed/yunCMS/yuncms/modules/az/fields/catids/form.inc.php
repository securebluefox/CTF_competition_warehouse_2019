	function catids($field, $value, $fieldinfo) {
		if ($this->categroys) $CATEGORY = $this->categorys;
		else $CATEGORY = getcache('category_yp_'.$this->modelid, 'yp');
		extract($fieldinfo);
		
		$tree = pc_base::load_sys_class('tree');
		$setting = string2array($setting);
		$tree->icon = array('&nbsp;│','&nbsp;├','&nbsp;└');
		$tree->nbsp = '&nbsp;';
		$categorys = array();
		if ($setting['boxtype'] == 'multiple') {
			$selected_values = '';
			if($value) {
				$_array_selected = explode(',',$value);
				foreach($_array_selected AS $_array) {
					if($_array) $selected_values .= "<option value='$_array'>{$CATEGORY[$_array][catname]}</option>";
				}
			}
			if(!empty($CATEGORY)) {
				foreach($CATEGORY as $catid=>$r) {
					$r['disabled'] = $r['child'] ? 'disabled' : '';
					$r['cid'] = 'id="catid_'.$r['catid'].'"';
					$categorys[$catid] = $r;
				}
			}
			$str  = "<option value='\$catid' \$disabled \$cid>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
			$data = "<table><tr><td><select name='f_filed_1' id='f_$field' $css $formattribute>";
			$data .= $string;
			$data .= "</select></td><td><input id='addbutton' type='button' value='".L('add_to_list')."' disabled style='width:100px;color:#ff0000' onclick=\"transact('update','f_$field','$field', '5');\"><BR><BR>
			<input id='deletebutton' type='button' value='".L('del_form_list')."' style='width:100px;color:#ff0000' onclick=\"transact('delete','','$field');\"> </td><td><select name=\"info[$field][]\" multiple id='$field' size='8' style='width:195px;'>$selected_values</select></td></tr></table>";
		} else if ($setting['boxtype'] == 'down') {
			if(!empty($CATEGORY)) {
				foreach($CATEGORY as $catid=>$r) {
					$r['disabled'] = $r['child'] ? 'disabled' : '';
					$r['selected'] = $r['catid']==$value ? 'selected' : '';
					$categorys[$catid] = $r;
				}
			}
			$str  = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
			$data = "<select name='info[$field]' id='$field' $css onchange=\"get_additional(this);\">";
			$data .= $string;
			if ($value) {
				$data .= '<script type="text/javascript"> var obj = new Object(); obj.value=$(\'#'.$field.'\').val();</script>';
			}
		} else if ($setting['boxtype'] == 'pop') {
			$data = '';
			$container = 'yp'.random(3).date('is');
			if(!defined('DIALOG_INIT_1')) {
				define('DIALOG_INIT_1', 1);
				$data .= '<script type="text/javascript" src="'.JS_PATH.'dialog.js"></script>';
			}
			if(!defined('CATEGORY_MENU_1')) {
				define('CATEGORY_MENU_1', 1);
				$data .= '<script type="text/javascript" src="'.JS_PATH.'linkage/js/menu.js"></script>';
			}
			$var_catname = menu_level($value, 'category_yp_'.$this->modelid, 'yp');
			$var_input = '<input type="hidden" name="info['.$field.']" value="'.$value.'" id="'.$field.'_val">';
			$data .= '<div name="'.$field.'" value="" id="'.$field.'" class="ib">'.$var_catname.'</div>'.$var_input.' <input type="button" name="btn_'.$field.'" class="button" value="'.L('category_yp').L('linkage_select').'" onclick="open_menu(\''.$field.'\',\''.L('category_yp').'\','.$container.',\'category_yp_'.$this->modelid.'\', \'yp\', \''.L('category_yp').'\', \'catname\', \'get_additional\')">';

			$data .= '<script type="text/javascript">';
			$data .= 'var returnid_'.$field.'= \''.$field.'\';';
			//$string .= 'var returnkeyid_'.$field.' = \''.$linkageid.'\';';
			$data .=  'var '.$container.' = new Array(';
			foreach($CATEGORY AS $k=>$v) {
				if($v['parentid'] == 0) {
					$s[]='new Array(\''.$v['catid'].'\',\''.trim($v['catname']).'\',\''.$v['parentid'].'\')';
				} else {
					continue;
				}
			}
			if(is_array($s)) $s = implode(',',$s);
			$data .=$s;
			$data .= ')';
			$data .= '</script>';
			if ($value) {
				$data .= '<script type="text/javascript"> var obj = new Object(); obj.value=$(\'#'.$field.'_val\').val();</script>';
			}
			$this->formValidator .= '$("#'.$field.'").formValidator({onshow:"'.$errortips.'",onfocus:"'.$errortips.'"}).inputValidator({min:1,onerror:"'.$errortips.'"});';
		}

		return $data;
	}
