<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    <?php if(isset($big_menu)) { echo '<a class="add fb" href="'.$big_menu[0].'"><em>'.$big_menu[1].'</em></a>　';} else {$big_menu = '';} ?>
    <a href='javascript:;' class="on"><em><?php echo L('model_manage')?></em></a><span>|</span><a href='?m=yp&c=ypmodel&a=import&menuid=<?php echo $_GET['menuid']?>&pc_hash=<?php echo $_SESSION['pc_hash']?>' ><em><?php echo L('import_model')?></em></a><span>|</span><a href='?m=yp&c=ypmodel&a=public_rewriteurl&menuid=<?php echo $_GET['menuid']?>&pc_hash=<?php echo $_SESSION['pc_hash']?>' target="_blank" >[<?php echo L('apache_rewrite')?>]</a>
    </div>
</div>
<div class="pad-lr-10">
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
            <tr>
			 <th>modelid</th>
            <th width="100"><?php echo L('model_name');?></th>
			<th width="100"><?php echo L('tablename');?></th>
            <th ><?php echo L('description');?></th>
            <th width="100"><?php echo L('status');?></th>
            <th width="100"><?php echo L('items');?></th>
			<th width="230"><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody>
	<?php
	foreach($datas as $r) {
		$tablename = $r['name'];
	?>
    <tr>
		<td align='center'><?php echo $r['modelid']?></td>
		<td align='center'><?php echo $tablename?></td>
		<td align='center'><?php echo $r['tablename']?></td>
		<td align='center'>&nbsp;<?php echo $r['description']?></td>
		<td align='center'><?php echo $r['disabled'] ? L('icon_locked') : L('icon_unlock')?></td>
		<td align='center'><?php echo $r['items']?></td>
		<td align='center'><a href="?m=yp&c=ypmodel_field&a=init&modelid=<?php echo $r['modelid']?>&menuid=<?php echo $_GET['menuid']?>"><?php echo L('field_manage');?></a> | <a href="javascript:edit('<?php echo $r['modelid']?>','<?php echo addslashes($tablename);?>')"><?php echo L('edit');?></a> | <a href="?m=yp&c=ypmodel&a=disabled&modelid=<?php echo $r['modelid']?>&menuid=<?php echo $_GET['menuid']?>"><?php echo $r['disabled'] ? L('field_enabled') : L('field_disabled');?></a> | <a href="javascript:;" onclick="model_delete(this,'<?php echo $r['modelid']?>','<?php echo L('confirm_delete_model',array('message'=>addslashes($tablename)));?>',<?php echo $r['items']?>)"><?php echo L('delete')?></a> | <a href="?m=yp&c=ypmodel&a=export&modelid=<?php echo $r['modelid']?>&menuid=<?php echo $_GET['menuid']?>""><?php echo L('export');?></a></td>
	</tr>
	<?php } ?>
    </tbody>
    </table>
   <div id="pages"><?php echo $pages;?>
  </div>
</div>
<script type="text/javascript">
<!--
window.top.$('#display_center_id').css('display','none');
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit_model');?>《'+name+'》',id:'edit',iframe:'?m=yp&c=ypmodel&a=edit&modelid='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function model_delete(obj,id,name,items){
	if(items) {
		alert('<?php echo L('model_does_not_allow_delete');?>');
		return false;
	}
	window.top.art.dialog({content:name, fixed:true, style:'confirm', id:'model_delete'},
	function(){
	$.get('?m=yp&c=ypmodel&a=delete&modelid='+id+'&pc_hash='+pc_hash,function(data){
				if(data) {
					$(obj).parent().parent().fadeOut("slow");
				}
			})
		 },
	function(){});
};

//-->
</script>
</body>
</html>
