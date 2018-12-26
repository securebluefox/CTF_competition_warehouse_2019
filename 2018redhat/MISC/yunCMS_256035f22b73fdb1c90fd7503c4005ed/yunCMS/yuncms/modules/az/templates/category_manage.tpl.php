<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    <?php if(isset($big_menu)) { echo '<a class="add fb" href="'.$big_menu[0].'"><em>'.$big_menu[1].'</em></a>　';} else {$big_menu = '';} ?>
        <a href=<?php if (!$_GET['modelid'] || $modelid == $yp_company_modelid) {?>'javascript:;' class="on"<?php } else {?>'index.php?m=yp&c=category&a=init&modleid=<?php echo $yp_company_modelid?>&pc_hash=<?php echo $_SESSION['pc_hash']?><?php }?>'><em><?php echo L('manage_business_category')?></em></a><?php if ($yp_models){ foreach ($yp_models as $mid => $m) {?><span>|</span><a href=<?php if ($modelid==$mid) {?>'javascript:;' class="on"<?php } else {?>"index.php?m=yp&c=category&a=init&modelid=<?php echo $mid?>&pc_hash=<?php echo $_SESSION['pc_hash']?>"<?php }?>><?php echo L('manage').$m['name'].L('category_yp')?></a><?php } }?><span>|</span></div>
</div>
<style type="text/css">
	html{_overflow-y:scroll}
</style>
<form name="myform" action="?m=yp&c=category&a=listorder&modelid=<?php echo $modelid?>" method="post">
<div class="pad_10">
<div class="explain-col">
<?php echo L('category_cache_tips');?>,<?php echo L('update_cache');?>。<input type="button" class="button" name="updatecache" value="<?php echo L('update_cache')?>" onclick="location.href='?m=yp&c=category&a=public_cache&module=yp&modelid=<?php echo $modelid?>';"/>
<input type="button" class="button" name="exporttypes" value="<?php echo L('import_category')?>"  onclick="cexport('<?php echo $modelid?>', '<?php echo L('import').$m['name'].L('yp_category')?>')"/>
</div>
<div class="bk10"></div>
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
            <tr>
            <th width="80"><?php echo L('listorder');?></th>
            <th width="80">catid</th>
            <th ><?php echo L('catname');?></th>
            <th align='center' width="40"><?php echo L('items');?></th>
            <th align='center' width="30"><?php echo L('vistor');?></th>
			<th width="240"><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody>
    <?php echo $categorys;?>
    </tbody>
    </table>

    <div class="btn">
	<input type="hidden" name="pc_hash" value="<?php echo $_SESSION['pc_hash'];?>" />
	<input type="submit" class="button" name="dosubmit" value="<?php echo L('listorder')?>" /></div>  </div>
</div>
</div>
</form>
<script language="JavaScript">
<!--
function add_sub(id, title) {
	window.top.art.dialog({id:'add'}).close();
	window.top.art.dialog({title:title, id:'add', iframe:'?m=yp&c=category&a=add&parentid='+id+'&menuid=<?php echo $_GET['menuid']?>&modelid=<?php echo $modelid?>&pc_hash=<?php echo $_SESSION['pc_hash']?>' ,width:'700px',height:'500px'}, function(){var d = window.top.art.dialog({id:'add'}).data.iframe;
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'add'}).close()});
}
function add(id, title) {
	window.top.art.dialog({id:'add'}).close();
	window.top.art.dialog({title:title, id:'add', iframe:'?m=yp&c=category&a=add&modelid='+id+'&menuid=<?php echo $_GET['menuid']?>&pc_hash=<?php echo $_SESSION['pc_hash']?>' ,width:'700px',height:'500px'}, function(){var d = window.top.art.dialog({id:'add'}).data.iframe;
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'add'}).close()});
}
function edit(id, title) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:title, id:'edit', iframe:'?m=yp&c=category&a=edit&catid='+id+'&menuid=<?php echo $_GET['menuid']?>&modelid=<?php echo $modelid?>&pc_hash=<?php echo $_SESSION['pc_hash']?>' ,width:'700px',height:'500px'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function cexport(id, title) {
	window.top.art.dialog({id:'export'}).close();
	window.top.art.dialog({title:title, id:'export', iframe:'?m=yp&c=category&a=export&modelid='+id+'&menuid=<?php echo $_GET['menuid']?>&pc_hash=<?php echo $_SESSION['pc_hash']?>' ,width:'400px',height:'300px'});
}
//-->
</script>
</body>
</html>
