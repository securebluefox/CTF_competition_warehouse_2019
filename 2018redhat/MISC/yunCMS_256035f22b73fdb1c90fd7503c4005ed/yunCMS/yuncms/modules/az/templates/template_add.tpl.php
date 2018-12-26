<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<form method="post" action="?m=yp&c=template&a=add" name="myform" id="myform">
<table class="table_form" width="100%" cellspacing="0">
<tbody>
	<tr>
		<th width="80"><strong><?php echo L('template_name')?></strong></th>
		<td><input name="info[title]" id="title" class="input-text" type="text" size="20" ></td>
	</tr>
	<tr>
		<th width="80"><strong><?php echo L('template_dir')?></strong></th>
		<td>com_<input name="info[dir]" id="dir" class="input-text" type="text" size="20" ></td>
	</tr>
	<tr>
		<th><strong><?php echo L('thumb_template')?></strong></th>
		<td><?php echo form::images('info[thumb]', 'thumb', '', 'yp', '', '30', '', 'readonly')?></td>
	</tr>
	<tr>
		<th><strong><?php echo L('zip_template')?></strong></th>
		<td><?php echo form::upfiles('filezip', 'filezip', '', 'yp', '', '30', '', 'readonly', 'zip')?> </td>
	</tr>
	<tr>
		<th><strong><?php echo L('description')?></strong></th>
		<td><?php echo L('zip_template_note')?></td>
	</tr>
	</tbody>
</table>
<input type="submit" name="dosubmit" id="dosubmit" value=" <?php echo L('ok')?> " class="dialog">&nbsp;<input type="reset" class="dialog" value=" <?php echo L('clear')?> ">
</form>
</div>
</body>
</html>
<script type="text/javascript">

$(document).ready(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'220',height:'70'}, function(){this.close();$(obj).focus();})}});
	$('#title').formValidator({onshow:"<?php echo L('please_input_template_dir')?>",onfocus:"<?php echo L('please_input_template_dir')?>",oncorrect:"<?php echo L('right')?>"}).inputValidator({min:1,onerror:<?php echo L('please_input_template_dir')?>"}).ajaxValidator({type:"get",url:"",data:"m=yp&c=template&a=public_check_title",datatype:"html",cached:false,async:'true',success : function(data) {
        if( data == "1" )
		{
            return true;
		}
        else
		{
            return false;
		}
	},
	error: function(){alert("<?php echo L('server_no_data')?>");},
	onerror : "<?php echo L('dir_exist')?>",
	onwait : "<?php echo L('checking')?>"
	});
	$('#dir').formValidator({onshow:"<?php echo L('please_input_template_name')?>",onfocus:"<?php echo L('please_input_template_name')?>",oncorrect:"<?php echo L('right')?>"}).inputValidator({min:1,onerror:"<?php echo L('please_input_template_name')?>"}).ajaxValidator({type:"get",url:"",data:"m=yp&c=template&a=public_check_dir",datatype:"html",cached:false,async:'true',success : function(data) {
        if( data == "1" )
		{
            return true;
		}
        else
		{
            return false;
		}
	},
	error: function(){alert("<?php echo L('server_no_data')?>");},
	onerror : "<?php echo L('title_exist')?>",
	onwait : "<?php echo L('checking')?>"
	});
	$('#thumb').formValidator({onshow:"<?php echo L('please_upload_thumb')?>",onfocus:"<?php echo L('please_upload_thumb')?>",oncorrect:"<?php echo L('right')?>"}).inputValidator({min:1,onerror:"<?php echo L('please_upload_thumb')?>"});
});
</script>