<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	$("#catname").formValidator({onshow:"<?php echo L('input_catname');?>",onfocus:"<?php echo L('input_catname');?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L('input_catname');?>"});
	<?php if ($parent_select) {?>$("input:checkbox[name='info[modelid][]']").formValidator({tipid:"modelTip",onshow:"<?php echo L('please_select_models')?>",onfocus:"<?php echo L('please_select_models')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L('please_select_models')?>"}).defaultPassed();<?php }?>
})
function SwapTab(name,cls_show,cls_hide,cnt,cur){
	for (i=1;i<=cnt;i++){
		if (i==cur){
			$('#div_'+name+'_'+i).show();
			$('#tab_'+name+'_'+i).attr('class',cls_show);
		} else {
			$('#div_'+name+'_'+i).hide();
			$('#tab_'+name+'_'+i).attr('class',cls_hide);
		}
	}
}
//-->
</script>

<form name="myform" id="myform" action="?m=yp&c=category&a=edit" method="post">
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
<li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',2,1);"><?php echo L('catgory_basic');?></li>
<li id="tab_setting_2" onclick="SwapTab('setting','on','',2,2);"><?php echo L('additional_field_select')?></li>
</ul>
<div id="div_setting_1" class="contentList pad-10">

<table width="100%" class="table_form ">
      <tr>
        <th width="200"><strong><?php echo L('parent_category')?>：</strong></th>
        <td>
		<?php echo select_category('category_yp_'.$modelid,$parentid,'name="info[parentid]" id="parentid"',L('please_select_parent_category'),0,-1);?>
		</td>
      </tr>
      <tr>
        <th><strong><?php echo L('catname')?>：</strong></th>
        <td><input type="text" name="info[catname]" id="catname" class="input-text" value="<?php echo $catname;?>"></td>
      </tr>
	<tr>
        <th><strong><?php echo L('catgory_img')?>：</strong></th>
        <td><?php echo form::images('info[image]', 'image', $image, 'content');?></td>
      </tr>
	<tr>
        <th><strong><?php echo L('description')?>：</strong></th>
        <td>
		<textarea name="info[description]" maxlength="255" style="width:300px;height:60px;"><?php echo $description;?></textarea>
		</td>
      </tr>
	<tr>
      <th width="200"><?php echo L('meta_title');?></th>
      <td><input name='setting[meta_title]' type='text' id='meta_title' value='<?php echo $setting['meta_title'];?>' size='60' maxlength='60'></td>
    </tr>
    <tr>
      <th ><?php echo L('meta_keywords');?></th>
      <td><textarea name='setting[meta_keywords]' id='meta_keywords' style="width:90%;height:40px"><?php echo $setting['meta_keywords'];?></textarea></td>
    </tr>
    <input type="hidden" name="info[modelid]" value="<?php echo $modelid?>">
    <tr>
      <th ><?php echo L('meta_description');?></th>
      <td><textarea name='setting[meta_description]' id='meta_description' style="width:90%;height:50px"><?php echo $setting['meta_description'];?></textarea></td>
    </tr>
</table>
</div>

<div id="div_setting_2" class="contentList pad-10" style="display:none;">
<table width="100%" class="table_form">
<tbody>
 	<tr id="additional_tip">
     <th width="100"><strong><?php echo L('additional_field')?></strong></th>
      <td id="additional_html">
		  <?php if (is_array($additional_field)) { foreach ($additional_field as $a) {?><label class="ib" style="width:125px;"><?php if (in_array($a['fieldid'], $parent_addition)) {?><input type="checkbox" name='additional[]' disabled checked="checked" value="<?php echo $a['fieldid']?>"><?php } else {?><input type="checkbox" name='additional[]' value="<?php echo $a['fieldid']?>"<?php if (in_array($a['fieldid'], $additional)) {?> checked <?php }?>><?php }?> <?php echo $a['name']?></label><?php } }?>
	  </td>
    </tr>
    <tr>
     <th width="100"><strong><?php echo L('dianping_type')?></strong></th>
      <td id="additional_html">
		  <?php echo form::select($dianping_arr,$commenttypeid,'name="info[commenttypeid]" id="commenttypeid"',L('please_select_dianping_type'));?> <input type="checkbox" name="usechild" value="1" checked> <?php echo L('used_to_child')?>
	  </td>
    </tr>
</tbody>
</table>
</div>

 <div class="bk15"></div>
	<input name="catid" type="hidden" value="<?php echo $catid;?>">
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">

</form>
</div>

</div>
<!--table_form_off-->
</div>
<script type="text/javascript">
$('#parentid').change(function (){
    var parentid = $('#parentid').val();
    var catid = <?php echo $catid?>;
	$.get('<?php echo APP_PATH?>index.php', {m:'yp', c:'category', a:'public_get_additional', catid:catid, parentid:parentid, modelid:'<?php echo $modelid?>'}, function(data){
        $('#additional_html').html(data);
    })
})
</script>
</body>
</html>