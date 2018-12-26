<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="pad-10">
<div class="explain-col">
<?php echo L('updateurl_tips');?>
</div>
<div class="bk10"></div>

<div class="table-list">
<table width="100%" cellspacing="0">

<form action="?m=yp&c=update_urls&a=content" method="post" id="myform" name="myform">
  <input type="hidden" name="dosubmit" value="1">
  <input type="hidden" name="type" value="lastinput">
<thead>
<tr>
<th align="center" width="150"><?php echo L('according_model');?></th>
<th align="center" width="386"><?php echo L('select_category_area');?></th>
<th align="center"><?php echo L('select_operate_content');?></th>
</tr>
</thead>
<tbody  height="200" class="nHover td-line">
	<tr>
      <td align="center" rowspan="6">
	<?php
			$models = getcache('yp_model','model');
			$model_datas = array($company_modelid=>L('business_model'));
			if(is_array($models)) {foreach($models as $_k=>$_v) {
				$model_datas[$_v['modelid']] = $_v['name'];
			} }
			echo form::select($model_datas,$modelid,'name="modelid" id="modelid" size="2" style="height:200px;width:130px;" onclick="change_model(this.value)"');
		?>
	</td>
    </tr>
	<tr>
      <td align="center" rowspan="6">
<select name='catids[]' id='catids'  multiple="multiple"  style="height:200px;" title="<?php echo L('push_ctrl_to_select');?>">
<option value='0' selected><?php echo L('no_limit_category');?></option>
<?php echo $string;?>
</select></td>
      <td><font color="red"><?php echo L('every_time');?> <input type="text" name="pagesize" value="100" size="4"> <?php echo L('information_items');?></font></td>
    </tr>
    <tr>
      <td> <input type="button" name="dosubmit3" value=" <?php echo L('update_navigation_menu')?> " class="button" onclick="myform.action='?m=yp&c=update_urls&a=menu';myform.submit();">   <input type="button" name="dosubmit3" value=" <?php echo L('update_navigation_model')?> " class="button" onclick="myform.action='?m=yp&c=update_urls&a=model';myform.submit();"></td>
    </tr>
    <tr>
      <td> <input type="button" name="dosubmit3" value=" <?php echo L('update_category_url')?> " class="button" onclick="myform.action='?m=yp&c=update_urls&a=category';myform.submit();"></td>
    </tr>
	<tr>
      <td><?php echo L('update_all');?> <input type="submit" name="dosubmit1" value=" <?php echo L('submit_start_update');?> " class="button" onclick="myform.type.value='all';"></td>
    </tr>
	<tr>
      <td><?php echo L('last_information');?> <input type="text" name="number" value="100" size="5"> <?php echo L('information_items');?><input type="submit" class="button" name="dosubmit2" value=" <?php echo L('submit_start_update');?> " onclick="myform.type.value='lastinput';"></td>
    </tr>
	<tr>
      <td></td>
    </tr>
	</tbody>
	</form>
</table>

</div>
</div>
<script language="JavaScript">
<!--
	window.top.$('#display_center_id').css('display','none');
	function change_model(modelid) {

		window.location.href='?m=yp&c=update_urls&a=init&modelid='+modelid+'&pc_hash='+pc_hash;
	}

    $('#myform').submit(function() {
        if ($('#modelid').val()==null) {
            alert('<?php echo L('please_select_models')?>');
            return false;
        }
        return true;
    })
//-->
</script>