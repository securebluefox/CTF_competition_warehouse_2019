<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
		$("#name").formValidator({onshow:"<?php echo L("input").L('model_name')?>",onfocus:"<?php echo L("input").L('model_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('model_name')?>"});
		$("#tablename").formValidator({onshow:"<?php echo L("input").L('model_tablename')?>",onfocus:"<?php echo L("input").L('model_tablename')?>"}).regexValidator({regexp:"^([a-zA-Z0-9]|[_]){0,20}$",onerror:"<?php echo L("model_tablename_wrong");?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('model_tablename')?>"}).ajaxValidator({type : "get",url : "",data :"m=yp&c=ypmodel&a=public_check_tablename",datatype : "html",async:'false',success : function(data){	if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('model_tablename').L('exists')?>",onwait : "<?php echo L('connecting')?>"});
	})
//-->
</script>
<div class="pad-lr-10">
<form action="?m=yp&c=ypmodel&a=add" method="post" id="myform">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('model_name')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[name]" id="name" size="30" /></td>
  </tr>
  <tr>
    <th><?php echo L('model_tablename')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[tablename]" id="tablename" size="30" /></td>
  </tr>
    <tr>
    <th><?php echo L('description')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[description]" id="description"  size="30"/></td>
  </tr>
  <tr>
    <th><?php echo L('display_in_navigation')?></th>
    <td class="y-bg"><input type='radio' name='setting[ismenu]' id="show_menu" value='1' checked> <?php echo L('yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
	  <input type='radio' name='setting[ismenu]' id="hide_menu" value='0'  > <?php echo L('no')?></td>
  </tr>
</table>
</fieldset>
<fieldset id="template_show">
	<legend><?php echo L('template_setting')?></legend>
	<table width="100%"  class="table_form">
  <tr>
  <th width="120"><?php echo L('available_styles');?></th>
        <td>
		<?php echo form::select($style_list, '', 'name="info[default_style]" id="default_style" onchange="load_file_list(this.value)"', L('please_select'))?>
		</td>
</tr>
		<tr>
        <th width="120"><?php echo L('category_index_tpl')?>：</th>
        <td  id="model_template">
		</td>
      </tr>
	  <tr>
        <th width="120"><?php echo L('category_list_tpl')?>：</th>
        <td  id="list_template">
		</td>
      </tr>
	  <tr>
        <th width="120"><?php echo L('content_tpl')?>：</th>
        <td  id="show_template">
		</td>
      </tr>
</table>
</fieldset>
<fieldset>
<legend><?php echo L('seo_setting')?></legend>
	<table width="100%"  class="table_form">
  	<tr>
  		<th width="200"><?php echo L('meta_title')?></th>
    	<td><input name='setting[meta_title]' type='text' id='meta_title' value='' size='60' maxlength='60'></td>
	</tr>
	<tr>
        <th width="200"><?php echo L('meta_keywords')?></th>
        <td><textarea name='setting[meta_keywords]' id='meta_keywords' style="width:90%;height:40px"></textarea></td>
    </tr>
	<tr>
        <th width="200"><?php echo L('meta_description')?></th>
        <td><textarea name='setting[meta_description]' id='meta_description' style="width:90%;height:50px"></textarea></td>
	</tr>
</table>
</fieldset>
<div class="bk15"></div>
    <input type="submit" class="dialog" id="dosubmit" name="dosubmit" value="<?php echo L('submit');?>" />
</form>
</div>
<script language="JavaScript">
<!--
	function load_file_list(id) {
		if (id=='') return false;
		$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&module=yp&templates=model|list|show&name=info&pc_hash='+pc_hash, function(data){$('#model_template').html(data.model_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
	}

$('#show_menu').click(function (){
	$('#template_show').show();
	$('#setting_show').show();
})
$('#hide_menu').click(function (){
	$('#template_show').hide();
	$('#setting_show').hide();
})
	//-->
</script>
</body>
</html>