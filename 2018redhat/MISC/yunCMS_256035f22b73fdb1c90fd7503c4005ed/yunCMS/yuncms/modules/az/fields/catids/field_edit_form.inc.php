<?php defined('IN_PHPCMS') or exit('No permission resources.');?>
<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td>选项类型</td>
      <td>
	  <input type="radio" name="setting[boxtype]" value="pop" <?php if($setting['boxtype']=='pop') echo 'checked';?>/> 弹出风格
	  <input type="radio" name="setting[boxtype]" value="down" <?php if($setting['boxtype']=='down') echo 'checked';?> /> 下拉风格
	  <input type="radio" name="setting[boxtype]" value="multiple" <?php if($setting['boxtype']=='multiple') echo 'checked';?> /> 双多选框
	  </td>
    </tr>
	<tr> 
      <td>是否作为筛选字段</td>
      <td>
	  <input type="radio" name="setting[filtertype]" value="1" <?php if($setting['filtertype']) echo 'checked';?> /> 是 
	  <input type="radio" name="setting[filtertype]" value="0" <?php if(!$setting['filtertype']) echo 'checked';?>/> 否
	  </td>
    </tr>
</table>
<SCRIPT LANGUAGE="JavaScript">
<!--
	function fieldtype_setting(obj) {
	if(obj!='varchar') {
		$('#minnumber').css('display','');
	} else {
		$('#minnumber').css('display','none');
	}
}
//-->
</SCRIPT>