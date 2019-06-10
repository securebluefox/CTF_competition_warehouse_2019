<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<form name="myform" id="myform" action="?m=yp&c=template&a=edit_priv&id=<?php echo $id?>" method="post">
<div class="pad-10">
<div class="col-tab">
<div id="div_setting_5" class="contentList pad-10">
<table width="100%" >

	  <tr>
        <th width="200"><?php echo L('group_private')?>：</th>
        <td>
			<table width="100%" class="table-list">
			  <thead>
				<tr>
				  <th align="left"><?php echo L('group_name');?></th><th><?php echo L('allow_user');?></th>
			  </tr>
			    </thead>
				 <tbody>
			<?php
			foreach($group_cache as $_key=>$_value) {
			if($_value['groupid']==1) continue;
			?>
		  		<tr>
				  <td><?php echo $_value['name'];?></td>
				  <td align="center"><input type="checkbox" name="priv_groupid[]" value="<?php echo $_value['groupid'];?>"<?php if(in_array($_value['groupid'], $current_priv)){ ?> checked<?php }?>> </td>
			  </tr>
			<?php }?>
			 </tbody>
			</table>
		</td>
      </tr>
</table>
</div>
</div>
</div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog">
</form>
