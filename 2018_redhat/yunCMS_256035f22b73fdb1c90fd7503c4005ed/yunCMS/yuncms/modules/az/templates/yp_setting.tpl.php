<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	})
//-->
</script>

<form name="myform" id="myform" action="?m=yp&c=yp&a=setting" method="post">
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
<li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',3,1);"><?php echo L('base_setting')?></li>
<li id="tab_setting_2" onclick="SwapTab('setting','on','',3,2);"><?php echo L('seo_setting')?></li>
<li id="tab_setting_3" onclick="SwapTab('setting','on','',3,3);"><?php echo L('role_setting')?></li>
</ul>
<div id="div_setting_1" class="contentList pad-10">

<table width="100%" class="table_form ">
		<tr>
		<th width="200"><strong><?php echo L('position_setting')?></strong>：</th>
		<td class="y-bg" id="position_list">
		<?php echo L('position_id')?><input type="text" class="input-text" name="setting[position][1][posid]" size='5' value="<?php echo $setting['position'][1]['posid']?>"> <?php echo L('position_name')?><input type="text" size="13" class="input-text" name="setting[position][1][name]" value="<?php echo $setting['position'][1]['name']?>"> <?php echo L('consume')?><input type="text" class="input-text" name="setting[position][1][point]" size="5" value="<?php echo $setting['position'][1]['point']?>"/><?php echo L('points_house')?> <?php echo L('big_nums')?><input type="text" class="input-text" name="setting[position][1][num]" size="5" value="<?php echo $setting['position'][1]['num']?>"/><div class="bk10"></div>
		<?php echo L('position_id')?><input type="text" class="input-text" name="setting[position][2][posid]" size='5' value="<?php echo $setting['position'][2]['posid']?>"> <?php echo L('position_name')?><input type="text" size="13" class="input-text" name="setting[position][2][name]" value="<?php echo $setting['position'][2]['name']?>"> <?php echo L('consume')?><input type="text" class="input-text" name="setting[position][2][point]" size="5" value="<?php echo $setting['position'][2]['point']?>"/><?php echo L('points_house')?> <?php echo L('big_nums')?><input type="text" class="input-text" name="setting[position][2][num]" size="5" value="<?php echo $setting['position'][2]['num']?>"/>
		</td>
	  </tr>
	  <tr>
        <th width="200"><strong><?php echo L('company_is_check')?></strong>：</th>
        <td>
			<input type='radio' name='setting[ischeck]' value='1' <?php if ($setting['ischeck']) {?>checked<?php }?>> <?php echo L('yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
		  	<input type='radio' name='setting[ischeck]' value='0' <?php if (!$setting['ischeck']) {?>checked<?php }?>>  <?php echo L('no')?>
		</td>
      </tr>
      <tr>
        <th width="200"><strong><?php echo L('is_pay')?></strong>：</th>
        <td>
			<input type='radio' name='setting[isbusiness]' value='1' <?php if ($setting['isbusiness']) {?>checked<?php }?>> <?php echo L('yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
		  	<input type='radio' name='setting[isbusiness]' value='0' <?php if (!$setting['isbusiness']) {?>checked<?php }?>>  <?php echo L('no')?>
		</td>
      </tr>
      <tr>
        <th width="200"><strong><?php echo L('enable_rewrite')?></strong>：</th>
        <td>
			<input type='radio' name='setting[enable_rewrite]' value='1' <?php if ($setting['enable_rewrite']) {?>checked<?php }?>> <?php echo L('yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
		  	<input type='radio' name='setting[enable_rewrite]' value='0' <?php if (!$setting['enable_rewrite']) {?>checked<?php }?>>  <?php echo L('no')?>
		</td>
      </tr>
	  <tr>
        <th width="200"><strong><?php echo L('encode_page_cache')?></strong>：</th>
        <td>
			<input type='radio' name='setting[encode_page_cache]' value='1' <?php if ($setting['encode_page_cache']) {?>checked<?php }?>> <?php echo L('yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
		  	<input type='radio' name='setting[encode_page_cache]' value='0' <?php if (!$setting['encode_page_cache']) {?>checked<?php }?>>  <?php echo L('no')?>
		</td>
      </tr>
</table>

</div>
<div id="div_setting_2" class="contentList pad-10 hidden">
<table width="100%" class="table_form ">
		<tr>
      <th width="200"><strong><?php echo L('yp_title')?></strong>：<br><?php echo L('title_for_search_engines')?></th>
      <td><input type="text" size="30" name="setting[seo_title]" value="<?php echo $setting['seo_title']?>" id="seo_title" class="input-text"></td>
    </tr>
	<tr>
      <th><strong><?php echo L('yp_keywords')?></strong>：<br><?php echo L('keyword_for_search_engines')?></th>
      <td><textarea name='setting[seo_keywords]' cols='60' rows='2' id='seo_keywords'><?php echo $setting['seo_keywords']?></textarea></td>
    </tr>
	<tr>
      <th><strong><?php echo L('yp_description')?></strong>：<br><?php echo L('description_for_search_engines')?></th>
      <td><textarea name='setting[seo_description]' cols='60' rows='2' id='seo_description'><?php echo $setting['seo_description']?></textarea></td>
    </tr>
</table>
</div>


<div id="div_setting_3" class="contentList pad-10 hidden">
 <div class="table-list" id="load_priv">
<table width="100%" class="table-list">
			  <thead>
				<tr>
				  <th align="left"><?php echo L('group_name')?></th>
                  <th width="100"><?php echo L('no_audit_information')?> <img src="<?php echo IMG_PATH?>yp/i.png" title="<?php echo L('check_group_no_audit_information')?>"></th>
				  <?php if (is_array($yp_models)) { foreach ($yp_models as $gid => $g) {?>
				   <th width="120"><?php echo $g['name'];?><?php echo L('add_and_view')?> <img src="<?php echo IMG_PATH?>yp/i.png" title="<?php echo L('set_membership').$g['name'].L('maximumb_information')?>"></th>
				  <?php }}?>
			  </tr>
			    </thead>
				 <tbody>

				  <?php foreach ($member_models as $k => $value){?>
		 		  <tr>
				  <td><?php echo $value['name'];?></td>
                 <td align="center">
				 <input type="checkbox" name="priv[<?php echo $k;?>][allowpostverify]" <?php if($yp_setting[$k]['allowpostverify']){echo 'checked';}?> value="1">
				 </td>
				 <?php if (is_array($yp_models)) { foreach ($yp_models as $s => $v){?>
				 <td align="center"><input type="checkbox" name="priv[<?php echo $k;?>][<?php echo $v['modelid']?>]" value="1" <?php if($yp_setting[$k][$v['modelid']]){echo 'checked';}?> >/<input type="checkbox" name="priv[<?php echo $k;?>][view][<?php echo $v['modelid']?>]" value="1" <?php if($yp_setting[$k]['view'][$v['modelid']]){echo 'checked';}?>></td>
				 <?php } }?>
  				 </tr>
 				 <?php }?>

			  </tbody>
			</table>
</div>
 </div>




 <div class="bk15"></div>
    <input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">

</form>
</div>

</div>
<!--table_form_off-->
</div>

<script language="JavaScript">
<!--
	window.top.$('#display_center_id').css('display','none');
	function SwapTab(name,cls_show,cls_hide,cnt,cur){
		for(i=1;i<=cnt;i++){
			if(i==cur){
				 $('#div_'+name+'_'+i).show();
				 $('#tab_'+name+'_'+i).attr('class',cls_show);
			}else{
				 $('#div_'+name+'_'+i).hide();
				 $('#tab_'+name+'_'+i).attr('class',cls_hide);
			}
		}
	}
	function change_tpl(modelid) {
		if(modelid) {
			$.getJSON('?m=admin&c=category&a=public_change_tpl&modelid='+modelid, function(data){$('#template_list').val(data.template_list);$('#category_template').html(data.category_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
		}
	}
	function load_file_list(id) {
		if(id=='') return false;
		$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&catid=<?php echo $parentid?>', function(data){$('#category_template').html(data.category_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
	}
	<?php if($modelid) echo "change_tpl($modelid)";?>

//-->
</script>