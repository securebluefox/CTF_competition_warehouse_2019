<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = $show_validator = $show_scroll = 1; 
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<h2 class="title-1 f14 lh28">(<?php echo $r['name'];?>) <?php echo L('dianping_call_type')?></h2>
<div class="bk10"></div>
<div class="explain-col">
<strong><?php echo L('call_info')?></strong><br />
<?php echo L('call_infos')?></div>
<div class="bk10"></div>
 
<fieldset>
	<legend><?php echo L('iframe_call')?></legend>
    <?php echo L('vote_phpcall')?><br />
<input name="jscode1" id="jscode1" value='<iframe  onload="Javascript:SetCwinHeight()" src="{APP_PATH}index.php?m=dianping&c=index&a=init&dianpingid={id_encode(ROUTE_M."_$catid",$id,$siteid)}&iframe=1&dianping_type=<?php echo $r['id']?>&module={ROUTE_M}&modelid={$modelid}&is_checkuserid=1&contentid={$id}" width="100%" height="1" id="dianping_iframeid" frameborder="0" scrolling="no"></iframe>' style="width:410px"> <input type="button" onclick="$('#jscode1').select();document.execCommand('Copy');" value="<?php echo L('copy_code_use')?>" class="button" style="width:114px">
</fieldset>
<div class="bk10"></div> 

</div>
</body>
</html>