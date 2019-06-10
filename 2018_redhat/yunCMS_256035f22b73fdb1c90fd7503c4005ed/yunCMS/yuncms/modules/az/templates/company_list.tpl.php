<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    <a href=<?php if (!$_GET['modelid']) {?>'javascript:;' class="on"<?php } else {?>'index.php?m=yp&c=company&a=init&pc_hash=<?php echo $_SESSION['pc_hash']?><?php }?>'><em><?php echo L('manage_business')?></em></a> <span>|</span><a href='?m=yp&c=certificate_ht&a=init&pc_hash=<?php echo $_SESSION['pc_hash']?>' ><em><?php echo L('certificate_manage')?></em></a><span>|</span><a href="?m=yp&c=certificate_ht&a=init&pc_hash=<?php echo $_SESSION['pc_hash']?>"><?php echo L('other')?></a></div>
</div>

<div class="pad-lr-10">
<form name="myform2" id="myform2" action="?m=yp&c=company&a=seache_company&pc_hash=<?php echo $_SESSION['pc_hash']?>" method="post" >
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
			<td><div class="explain-col"> <?php echo L('username_t')?>  <input type="text" value="" class="input-text" name="search[username]" size="10">  <?php echo L('times_t')?>  <?php echo form::date('search[start_time]', '', 1)?> <?php echo L('to')?> <?php echo form::date('search[end_time]', '', 1)?>  <input type="submit" value=" <?php echo L('search')?> " class="button" name="dosubmit"></div></td>
		</tr>
    </tbody>
</table>
</form>


<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col">
		<?php echo L('all').L('yp_category')?>: &nbsp;&nbsp; <a href="?m=yp&c=company&a=init&menuid=<?php echo $_GET[menuid];?>"><?php echo L('all')?></a> &nbsp;&nbsp;
		<a href="?m=yp&c=company&a=init&elite=1&menuid=<?php echo $_GET[menuid];?>"><?php echo L('elite_company')?></a>&nbsp;&nbsp;
		<a href="?m=yp&c=company&a=init&status=1&menuid=<?php echo $_GET[menuid];?>"><?php echo L('passed_checked')?></a>&nbsp;&nbsp;
		<a href="?m=yp&c=company&a=init&status=0&menuid=<?php echo $_GET[menuid];?>"><font color=red><?php echo L('pending')?></font></a>&nbsp;
				</div>
		</td>
		</tr>
    </tbody>
</table>

<form name="myform" id="myform" action="?m=yp&c=company&a=delete" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('userid[]');"></th>
			<th width="35" align="center"><?php echo L('company_userid')?></th>
			<th width="100"><?php echo L('username')?></th>
			<th><?php echo L('company_name')?></th>
			<th width="8%" align="center"><?php echo L('groupname')?></th>
			<th width="6%" align="center"><?php echo L('elite')?></th>
			<th width='10%' align="center"><?php echo L('server_end_time')?></th>
			<th width="8%" align="center"><?php echo L('status')?></th>
			<th width="12%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	$groups = getcache('grouplist', 'member');
	foreach($infos as $info){
		$memberinfo = $this->db->get_one(array('userid'=>$info['userid']), 'username, groupid');
		?>
	<tr>
		<td align="center" width="35"><input type="checkbox" name="userid[]" value="<?php echo $info['userid']?>"></td>
		<td align="center" width="35"><?php echo $info['userid']?></td>
		<td align="center" width="100"><a href="javascript:void(0);" title="<?php echo L('edit_user_info')?>" onclick="EditUser('<?php echo $info['userid']?>', '<?php echo $memberinfo['username']?>')"><?php echo $memberinfo['username']?></a></td>
		<td><a href="<?php echo $info['url'];?>" title="<?php echo L('goto_web')?>" target="_blank"><?php echo $info['companyname']?></a> </td>
		<td align="center" width="8%"> <?php echo $groups[$memberinfo['groupid']]['name']?></td>
		<td align="center" width="6%"><?php if($info['elite']=='1'){echo '<font color=red>'.L('elite').'</font>';}else{ echo L('normal');}?></td>
		<td align="center" width="10%"><?php echo L('permanent')?></td>
		<td width="8%" align="center"><?php if ($info['status']==0) {?>
 			<a
			href='?m=yp&c=company&a=passed_check&userid=<?php echo $info['userid']?>&status=1'
			onClick="return confirm('<?php echo L('are_you_true').$info['companyname'].L('pass_w')?>')" title="<?php echo L('pass_click')?>"><font color=red><?php echo L('pending')?></font></a>
 			<?php }elseif($info['status']=='1'){echo '<font color="green">'.L('normal').'</font>';}?> </td>
		<td align="center" width="12%"><a href="###"
			onclick="edit(<?php echo $info['userid']?>, '<?php echo new_addslashes($info['companyname'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href='?m=yp&c=company&a=delete&userid=<?php echo $info['userid']?>'
			onClick="return confirm('<?php echo L('confirm', array('message' => new_addslashes($info['companyname'])))?>')"><?php echo L('delete')?></a>
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
<a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('select_all')?></a>/<a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a> &nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onclick="return confirm('<?php echo L('confirm', array('message' => L('selected')))?>')" value="<?php echo L('delete')?>"/>
&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=yp&c=company&a=passed_check&status=1'" value="<?php echo L('passed_checked')?>"/>
&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=yp&c=company&a=passed_check&status=0'" value="<?php echo L('unpass_checked')?>"/>
&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=yp&c=company&a=elite&status=1'" value="<?php echo L('elite_company')?>"/>
&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=yp&c=company&a=elite&status=0'" value="<?php echo L('unelite_company')?>"/>

</div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
<script type="text/javascript">

function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=yp&c=company&a=edit&userid='+id,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}

function EditUser(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit_ajax_user')?>'+name+'<?php echo L('right_symbol')?>',id:'edit',iframe:'?m=member&c=member&a=edit&userid='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
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
</script>
</body>
</html>
