<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<?php include PHPCMS_PATH.'star_config.php';?>
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
 
 
 <table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">按类型查看: 
		
		&nbsp;&nbsp;<a href="<?php echo APP_PATH;?>index.php?m=dianping&c=dianping&a=dianping_data&menuid=<?php echo $_GET['menuid']?>"><font color=red>全部</a></a>	
	<?php
	if(is_array($dianping_type_array)){
		foreach($dianping_type_array as $type=>$type_array){
	?>
	&nbsp;&nbsp;
<a href="<?php echo APP_PATH;?>index.php?m=dianping&c=dianping&a=dianping_data&menuid=<?php echo $_GET['menuid'];?>&pc_hash=G6Ihra&typeid=<?php echo $type;?>"><?php echo $type_array['type_name'];?></a>	
	<?php }}?>
		</div>
		</td>
		</tr>
    </tbody>
</table>


 <form action="?" method="get">
 <input type="hidden" name="m" value="reviews">
  <input type="hidden" name="c" value="check">
   <input type="hidden" name="a" value="ajax_checks">
    <input type="hidden" name="type" value="-1">
    <input type="hidden" name="reviewsid" value="">
<div class="comment">
<?php if(is_array($infos)) foreach($infos as $v) :
?>
<div  id="tbody_<?php echo $v['id']?>">
<h5 class="title fn" ><span class="rt"><?php if( $v['status'] == 0) {?><input type="button" value="<?php echo L('pass')?>" class="button" onclick="check(<?php echo $v['id']?>, 1, <?php echo $v['dianpingid'];?>)" /> <?php }?><input  class="button"  type="button" value="<?php echo L('delete')?>" onclick="check(<?php echo $v['id']?>, -1, '<?php echo $v['dianpingid']?>')" />
</span><?php echo $v['username']?> (127.0.0.1) 于 <?php echo format::date($v['addtime'], 1)?> </h5>
<?php if (!empty($v['content'])) {?>
    <div class="content">
    	<pre><?php echo $v['content']?></pre>
    </div>
<?php }?>
        <?php
		$star_type = $v['startype']; // 评分方案
		$star_li = explode('|', $star_config[$star_type]['star_name']);
		$star_img = explode('|', $star_config[$star_type]['star_images']);
		$star_n = 0;
    ?>
    <div id="star_show">
 		<?php 
		$data = string2array($v['data']);
		foreach ($data as $name=>$val){
		?>
	 		<b><?php echo $name;?>：</b>
	 		<?php for($n=1;$n<=$val;$n++){?>
	 		<img alt="分" src="<?php echo APP_PATH;?>/statics/images/star2.gif">
	 		<?php }?> 
	 		<?php for($k=1;$k<=(5-$val);$k++){?>
	 		<img alt="分" src="<?php echo APP_PATH;?>/statics/images/star1.gif">
	 		<?php }?>  
		<?php }?>
    </div>
    <div class="bk20 hr mb8"></div>
</div>
<?php endforeach;?>
</div>
 </form>
<div id="pages"><?php echo $pages;?></div>
</div>
<script type="text/javascript">
<?php if(!isset($_GET['show_center_id'])) {?> window.top.$('#display_center_id').css('display','none');<?php }?>
function check(id, type, dianpingid) {
	if(type == -1 && !confirm('确定要删除吗？')) {
		return false;
	}
	$.get('?m=dianping&c=dianping&a=ajax_checks&id='+id+'&type='+type+'&dianpingid='+dianpingid+'&'+Math.random()+'&pc_hash=<?php echo $_SESSION['pc_hash'];?>', function(data){if(data!=1){if(data==0){alert('<?php echo L('illegal_parameters')?>')}else{alert(data)}}else{$('#tbody_'+id).remove();}});
}
</script>
</body>
</html>