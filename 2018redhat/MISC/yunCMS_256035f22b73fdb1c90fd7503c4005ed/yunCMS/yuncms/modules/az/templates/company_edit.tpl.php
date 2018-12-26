<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_validator = 1;
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript">
	var catids_num = 5;
</script>
<div class="pad-10">
<form name="myform" id="myform" action="?m=yp&c=company&a=edit&userid=<?php echo $userid?>" method="post" enctype="multipart/form-data">
	<table width="100%" cellspacing="0" class="table_form">
		<?php if(is_array($forminfos)) {
				foreach($forminfos as $field=>$info) {
					if($info['isomnipotent']) continue;
					foreach($forminfos as $_fm=>$_fm_value) {
					if($_fm_value['isomnipotent']) {
						$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
						}
					}
		?>
		<tr>
			<th width="100"><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?>：</th>
			<td><?php echo $info['form']?><?php echo $info['tips']?></td>
		</tr>
		<?php
			}	}
		?>
		<tr>
			<th></th>
			<td>
				<input name="forward" type="hidden" value="<?php echo HTTP_REFERER?>">
				<input name="modelid" type="hidden" value="<?php echo $modelid?>">
				<input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog"></td>
		</tr>
	</table>
</form>
</div>
</body>
</html>
<script type="text/javascript" src="<?php echo JS_PATH?>member_common.js"></script>
<script type="text/javascript">

$('#myform').submit( function (){
	if ($("#catids option").size()<1){
		alert('<?php echo L('please_select_company_model')?>');
		return false;
	} else {
		$("#catids option").each(function() {
			$(this).attr('selected','selected');
		});
	}
	return true;
});
<!--
//只能放到最下面
$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, 	function(){$(obj).focus();
	boxid = $(obj).attr('id');
	if($('#'+boxid).attr('boxid')!=undefined) {
		check_content(boxid);
	}
	})}});
	<?php echo $formValidator;?>
})
//-->
</script>