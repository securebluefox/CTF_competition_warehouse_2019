<?php defined('IN_PHPCMS') or exit('No permission resources.');?>
<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td>选项类型</td>
      <td>
	  <input type="radio" name="setting[boxtype]" value="pop" /> 弹出风格
	  <input type="radio" name="setting[boxtype]" value="down" /> 下拉风格 
	  <input type="radio" name="setting[boxtype]" value="multiple" /> 多选列表框
	  </td>
    </tr>
	<tr> 
      <td>是否作为筛选字段</td>
      <td>
	  <input type="radio" name="setting[filtertype]" value="1"/> 是 
	  <input type="radio" name="setting[filtertype]" value="0"/> 否
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