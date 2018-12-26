<?php
include $this->admin_tpl('header','admin');
?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>formvalidatorregex.js" charset="UTF-8"></script>

<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	$("#name").formValidator({onshow:"<?php echo L('input_certificate_title')?>",onfocus:"<?php echo L('input_certificate_title')?>"}).inputValidator({min:1,onerror:"<?php echo L('input_certificate_title')?>"});
	$('#organization').formValidator({onshow:"<?php echo L('input_organization_title')?>",onfocus:"<?php echo L('input_organization_title')?>",oncorrect:"<?php echo L('input_right')?>"}).inputValidator();
	$('#addtime').formValidator({onshow:"<?php echo L('input_addtime')?>",onfocus:"<?php echo L('input_addtime')?>",oncorrect:"<?php echo L('input_right')?>"}).inputValidator();
	$("#endtime").formValidator({onshow:"<?php echo L('input_endtime')?>",onfocus:"<?php echo L('input_endtime')?>",oncorrect:"<?php echo L('input_right')?>"}).inputValidator();

	});
//-->
</script>
<div class="pad_10">
<form action="?m=yp&c=certificate_ht&a=edit&id=<?php echo $id; ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="100"><?php echo L('certificate_name')?></th>
		<td><input type="text" name="info[name]" id="name"
			size="30" class="input-text" value="<?php echo $name;?>"></td>
	</tr>

	<tr>
		<th width="100"><?php echo L('organization_name')?></th>
		<td><input type="text" name="info[organization]" id="organization"
			size="30" class="input-text" value="<?php echo $organization;?>"></td>
	</tr>
	<tr id="logoinfo">
		<th width="100"><?php echo L('certificate_thumb')?></th>
		<td>
		<Img src="<?php echo $thumb?>" width="200" height="200"><br><br>
		<?php echo form::images('info[thumb]', 'thumb', $thumb, 'info')?></td>
	</tr>

	<tr>
		<th width="100"><?php echo L('effective_start_time')?></th>
		<td><?php echo form::date('info[addtime]',date('Y-m-d',$addtime)); ?></td>
	</tr>

	<tr>
		<th width="100"><?php echo L('effective_end_time')?></th>
		<td><?php echo form::date('info[endtime]',date('Y-m-d',$endtime)); ?></td>
	</tr>

	<tr>
		<th><?php echo L('certificate_status')?></th>
		<td><input name="info[status]" type="radio" value="1" <?php if($status==1){echo "checked";}?>>&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;<input
			name="info[status]" type="radio" value="0" <?php if($status==0){echo "checked";}?>>&nbsp;<?php echo L('no')?></td>
	</tr>

	<tr>
		<th></th>
		<td><input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
</html>

