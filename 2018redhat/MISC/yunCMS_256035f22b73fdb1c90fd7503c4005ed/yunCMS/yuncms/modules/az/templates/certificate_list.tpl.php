<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = $show_header = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    <a href=<?php if (ROUTE_C == 'company') {?>'javascript:;' class="on"<?php } else {?>'index.php?m=yp&c=company&a=init&pc_hash=<?php echo $_SESSION['pc_hash']?><?php }?>'><em><?php echo L('manage_business')?></em></a> <span>|</span><a href='?m=yp&c=certificate_ht&a=init&pc_hash=<?php echo $_SESSION['pc_hash']?>' <?php if(ROUTE_C == 'certificate_ht'){echo "class=\"on\"";}?> ><em><?php echo L('certificate_manage')?></em></a><span>|</span><a href="?m=yp&c=certificate_ht&a=init&pc_hash=<?php echo $_SESSION['pc_hash']?>"><?php echo L('other')?></a></div>
</div>

<div class="pad-lr-10">
<form name="myform2" id="myform2" action="?m=yp&c=certificate_ht&a=search&pc_hash=<?php echo $_SESSION['pc_hash']?>" method="post" >
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col">
		<?php echo L('all').L('yp_category')?>: &nbsp;&nbsp; <a href="?m=yp&c=certificate_ht&a=init&menuid=<?php echo $_GET[menuid];?>&pc_hash=<?php echo $_SESSION['pc_hash']?>"><?php echo L('all')?></a> &nbsp;&nbsp;
		<a href="?m=yp&c=certificate_ht&a=init&status=1&menuid=<?php echo $_GET[menuid];?>&pc_hash=<?php echo $_SESSION['pc_hash']?>"><?php echo L('pass_certificate')?></a>&nbsp;&nbsp;
		<a href="?m=yp&c=certificate_ht&a=init&status=0&menuid=<?php echo $_GET[menuid];?>&pc_hash=<?php echo $_SESSION['pc_hash']?>"><font color=red><?php echo L('unpass_certificate')?>(<?php echo $need_pass;?>)</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<?php echo L('username')?>：<input type="text" value="" class="input-text" name="search[username]" size="10"> <input type="submit" value=" <?php echo L('search')?> " class="button" name="search_submit">

				</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
<form name="myform" id="myform" action="?m=yp&c=certificate_ht&a=delete&pc_hash=<?php echo $_SESSION['pc_hash']?>" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
			<th width="35" align="center"><?php echo L('company_userid')?></th>
			<th><?php echo L('certificate_t_name')?></th>
			<th width="12%" align="center"><?php echo L('certificate_t_thumb')?></th>
			<th width="10%" align="center"><?php echo L('effective_start_t_time')?></th>
			<th width='10%' align="center"><?php echo L('effective_end_t_time')?></th>
			<th width="8%" align="center"><?php echo L('status')?></th>
			<th width="12%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center" width="35"><input type="checkbox" name="id[]" value="<?php echo $info['id']?>"></td>
		<td align="center" width="35"><?php echo $info['userid']?></td>
		<td><?php echo $info['name']?></td>
		<td align="center" width="12%"><img src="<?php echo $info['thumb'];?>" width='50' height='50'></td>
		<td align="center" width="10%"><?php echo date('Y-m-d',$info['addtime'])?></td>
		<td align="center" width="10%"><?php echo date('Y-m-d',$info['endtime'])?></td>
		<td width="8%" align="center"><?php if($info['status']=='1'){echo L('normal');}else {echo '<font color=red>'.L('pending').'</font>';}?></td>
		<td align="center" width="12%"><a href="###"
			onclick="edit(<?php echo $info['id']?>, '<?php echo new_addslashes($info['name'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a>

		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="btn">
<a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('select_all')?></a>/<a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a> &nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onclick="return confirm('<?php echo L('confirm', array('message' => L('selected')))?>')" value="<?php echo L('delete')?>"/> <input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=yp&c=certificate_ht&a=passed_check&pc_hash=<?php echo $_SESSION['pc_hash'];?>'" value="<?php echo L('passed_checked')?>"></div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
<script type="text/javascript">

function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=yp&c=certificate_ht&a=edit&id='+id,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='userid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:"<?php echo L('before_select_operations')?>",lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}
//向下移动
function listorder_up(id) {
	$.get('?m=link&c=link&a=listorder_up&linkid='+id,null,function (msg) {
	if (msg==1) {
	//$("div [id=\'option"+id+"\']").remove();
		alert('<?php echo L('move_success')?>');
	} else {
	alert(msg);
	}
	});
}
</script>
</body>
</html>
