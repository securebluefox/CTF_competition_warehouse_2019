<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="subnav">
  <h2 class="title-1 line-x f14 fb blue lh28"><?php echo L('business_template_manage')?></h2>
<div class="content-menu ib-a blue line-x"><?php if(isset($big_menu)) { echo '<a class="add fb" href="'.$big_menu[0].'"><em>'.$big_menu[1].'</em></a>　';} else {$big_menu = '';} ?>
　<a class="on" href="javascript:void(0);"><em><?php echo L('template_manage')?></em></a><span>
</div></div>
<div class="pad-lr-10">
<form name="myform" action="?m=yp&c=template&a=listorder" method="post">
    <table width="100%" cellspacing="0" class="table-list nHover">
        <thead>
            <tr>
            <th width="40"> </th>
			<th width="40" align="center">ID</th>
			<th ><?php echo L('template_info')?></th>
			<th width="160"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
        <tbody>
 <?php
if(is_array($companytplnames)){
	foreach($companytplnames as $kid => $info){
		$current_priv = explode(',', $info['groups']);
?>
	<tr>
	<td align="center" width="40"> </td>
	<td width="40" align="center"><?php echo $info['id']?></td>
	<td>
    <div class="col-left mr10" style="width:146px; height:112px">
<a href="javascript:void(0);" onclick="preview('<?php echo $info['thumb']?>');"><img src="<?php echo $info['thumb']?>" width="146" height="112" style="border:1px solid #eee" align="left"></a>
</div>
<div class="col-auto">
    <h2 class="title-1 f14 lh28 mb6 blue"><span id="title_<?php echo $info['id']?>" title="<?php echo L('edit_template_click')?>" onclick="show_edit('<?php echo $info['id']?>');"><?php echo $info['title']?></span><span id="title_edit_<?php echo $info['id']?>" style="display:none;"><input type="text" id="title_value_<?php echo $info['id']?>" name="title" value="<?php echo $info['title']?>" size="20" onblur="edit_title('<?php echo $info['id']?>');"></span></h2>
    <div class="lh22"><?php echo L('permissions')?></div>
<p class="gray4"><?php if ($info['defaulttpl']){ ?><?php echo L('unlimited')?><?php } elseif (is_array($current_priv) && !empty($current_priv)) { foreach ($current_priv as $cp){ ?><?php echo $group_cache[$cp]['name']?>、<?php } }?></p>
</div>
	</td>
	<td align="center"><span style="height:22"><?php if ($info['defaulttpl']) {?><em style="color:red;"><?php echo L('default_template')?></em><?php } else {?><a href='index.php?m=yp&c=template&a=tpl_default&id=<?php echo $info['id']?>' ><?php echo L('default_setting')?></a><?php }?></span><?php if ($info['defaulttpl']==0){ ?> | <span style="height:22"><a href='javascript:void(0);' onclick="edit_priv('<?php echo $info['id']?>')"><?php echo L('permissions')?></a></span> | <span style="height:22"><a href='?m=yp&c=template&a=disabled&id=<?php echo $info['id']?>&t=<?php echo $info['disabled'] ? 0 : 1 ;?>'><?php if ($info['disabled']){ ?><font color="red"><?php echo L('enabled')?></font><?php } else {?><?php echo L('field_disabled')?><?php }?></a></span> |
<span style="height:22"><a href="javascript:confirmurl('?m=yp&c=template&a=delete&id=<?php echo $info['id']?>','<?php echo L('confirm',array('message'=>$info['title']))?>')"><?php echo L('delete')?></a></span> <?php }?></td>
	</tr>
<?php
	}
}
?>
</tbody>
    </table>

    <div class="btn"></div>
 <div id="pages">
</form>
</div>
<script type="text/javascript">
function preview(filepath) {
		window.top.art.dialog({title:'<?php echo L('preview')?>',fixed:true, content:'<img src="'+filepath+'" onload="$(this).LoadImage(true, 600, 500,\'http://test.phpcms.cn/statics/images/s_nopic.gif\');"/>'});
}

function show_edit(id) {
	if ($('#title_'+id).css('display')!='none') {
		$('#title_'+id).hide();
		$('#title_edit_'+id).show();
	}
}

function edit_title(id) {
	var title = $('#title_value_'+id).val();
	$.get("index.php", {m:'yp', c:'template', a:'public_edit', id:id, title:title, time:Math.random()}, function (data){
		if (data=='1') {
			$('#title_'+id).html(title);
			$('#title_'+id).show();
			$('#title_edit_'+id).hide();
		}
	});
}

function edit_priv(id) {
	window.top.art.dialog({id:'edit_priv',iframe:'?m=yp&c=template&a=edit_priv&id='+id+'&pc_hash=<?php echo $_SESSION['pc_hash']?>', title:'<?php echo L('used_permissions')?>', width:'540', height:'460', lock:true}, function(){var d = window.top.art.dialog({id:'edit_priv'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit_priv'}).close()});
}
</script>
</body>
</html>