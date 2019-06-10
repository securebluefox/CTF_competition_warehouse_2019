<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<textarea id="catids" style="width:388px;height:245px;margin-bottom:10px; line-height:20px; color:#333; font-family:'宋体'"><?php echo $categorys?></textarea>
<input type="submit" name="submit" class="button" value=" <?php echo L('copy')?> " onclick="$('#catids').select();document.execCommand('Copy');alert('<?php echo L('content_copy_to_ban')?>');">