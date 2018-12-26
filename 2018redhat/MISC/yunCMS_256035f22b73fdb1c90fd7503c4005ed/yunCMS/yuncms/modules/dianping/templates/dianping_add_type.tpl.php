<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	$("#name").formValidator({onshow:"<?php echo L("input").L('dianping_type_name')?>",onfocus:"<?php echo L("input").L('dianping_type_name')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('dianping_type_name')?>"}).defaultPassed(); 
 	})
//-->
</script>
<div class="pad-lr-10">
<form action="?m=dianping&c=dianping&a=add_type" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="60"><?php echo L('dianping_type_name')?>：</th>
		<td><input type="text" name="type[name]" id="name"
			size="30" class="input-text" value="<?php echo $name;?>"></td>
	</tr> 
	
	<tr>
		<th><?php echo L('dianping_type_data')?>：</th>
		<td><textarea name="type[data]" id="data" cols="29"
			rows="6"><?php echo $data;?></textarea> <br><?php echo L('dianping_datainfo')?></td>
	</tr> 
	
	<tr>
    <th ><?php echo L('is_guest')?></th>
    <td ><input type="checkbox" name="setting[guest]" value="1"></td>
    </tr>
 	<tr>
	<th width="120"><?php echo L('is_check')?></th>
	<td class="y-bg"><input type="checkbox" name="setting[check]" value="1" ></td>
	</tr>
	<tr>
	<th width="120"><?php echo L('is_code')?></th>
	<td class="y-bg"><input type="checkbox" name="setting[code]" value="1" ></td>
	</tr>
	<tr>
	<th width="120"><?php echo L('is_checkuserid')?></th>
	<td class="y-bg"><input type="checkbox" name="setting[is_checkuserid]" value="1"> (<font color=red> is_checkuserid=1 </font>)</td>
	</tr>
	<tr>
	<th width="120"><?php echo L('add_points')?></th>
	<td class="y-bg"><input type="input" name="setting[add_point]" value="0" class="input-text"> <?php echo L('points_info')?></td>
	</tr>
	<tr>
	<th width="120"><?php echo L('del_points')?></th>
	<td class="y-bg"><input type="input" name="setting[del_point]" value="0" class="input-text"> <?php echo L('points_info')?></td>
	</tr>
	
	
	<input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> ">
 
</table>
</form>
</div>
</body>
</html>
