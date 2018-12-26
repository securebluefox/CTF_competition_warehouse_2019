<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10"> 

<form name="form" action="?m=dianping&c=dianping&a=dianping_data&menuid=<?php echo $_GET['menuid'];?>" method="get" >
<input type="hidden" value="dianping" name="m">
<input type="hidden" value="dianping" name="c">
<input type="hidden" value="dianping_data" name="a">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col"><?php echo L('module')?>: <?php echo form::select($module_arr,'','name="search[module]"',$default)?> <?php echo L('username')?>  <input type="text" value="" class="input-text" name="search[username]" size='10'>  <?php echo L('times')?>  <?php echo form::date('search[start_time]','','1')?> <?php echo L('to')?>   <?php echo form::date('search[end_time]','','1')?>    <input type="submit" value="<?php echo L('determine_search')?>" class="button" name="dosubmit"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<form name="myform" id="myform" action="?m=dianping&c=dianping&a=delete_dianping_type" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('dianpingid[]');"></th>
			<th width="8%" align="center"><?php echo L('dianping_type_id')?></th>
 			<th width="30%"><?php echo L('dianping_type_name')?></th>
			<th width="50%" align="center"><?php echo L('dianping_type_data')?></th>
 			<th width="12%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center" width="35"><input type="checkbox" name="dianpingid[]" value="<?php echo $info['id']?>"></td>
		<td width="8%" align="center"><?php echo $info['id'];?></td>
 		<td width="30%"><?php echo $info['name']?></td>
		<td align="center" width="50%"><?php echo $info['data'];?> </td>
	  	<td align="center" width="12%">
		<a href="###" onclick="edit_type(<?php echo $info['id'];?>, '<?php echo $info['name'];?>')"
 title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a href="javascript:call(<?php echo $info['id']?>);void(0);"><?php echo L('call_code')?></a>
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="btn"><a href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('selected_all')?></a>/<a
	href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a>
<input name="submit" type="submit" class="button" value="<?php echo L('delete_select');?>" onClick="return confirm(<?php echo L('delete_confirm');?>)">&nbsp;&nbsp;
<input type="button" class="button" value="<?php echo L('update_dianpingtype')?>" onClick="javascript:location.href='?m=dianping&c=dianping&a=do_js&pc_hash=<?php echo $_SESSION['pc_hash']?>'">
</div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function call(id) {
	window.top.art.dialog({id:'call'}).close();
	window.top.art.dialog({title:'<?php echo L('call_code')?>', id:'call', iframe:'?m=dianping&c=dianping&a=public_call&typeid='+id, width:'600px', height:'300px'}, function(){window.top.art.dialog({id:'call'}).close();}, function(){window.top.art.dialog({id:'call'}).close();})
}

function edit_type(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'修改 '+name+' ',id:'edit',iframe:'?m=dianping&c=dianping&a=edit_type&dianpingid='+id,width:'550',height:'350'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
} 
</script>

