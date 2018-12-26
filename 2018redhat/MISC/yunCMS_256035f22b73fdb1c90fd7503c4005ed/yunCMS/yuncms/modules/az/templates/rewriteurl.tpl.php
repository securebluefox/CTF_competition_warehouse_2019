<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
if(ROUTE_A=='public_rewriteurl') {
?>
<div class="pad-10">
<div class="explain-col">
<strong><?php echo L('description')?></strong><br />
<?php echo L('note')?>
</div>
<div class="bk10"></div>
<fieldset>
<legend>Apache Web Server</legend>
<pre>
RewriteEngine on
RewriteRule ^yp.html index.php?m=yp&c=index&a=init
RewriteRule ^yp-([a-z]+)-([0-9]+).html index.php?m=yp&c=index&a=model&modelid=$2
<?php echo $apache_rewriteurl?>
RewriteRule ^yp-show-([0-9]+)-([0-9]+).html index.php?m=yp&c=index&a=show&catid=$1&id=$2
RewriteRule ^web-([0-9]+).html index.php?m=yp&c=com_index&a=init&userid=$1
RewriteRule ^web-(.*)-([0-9]*)-([0-9]*)-([0-9]*)-([0-9]*)-([0-9]*).html index.php?m=yp&c=com_index&a=$1&modelid=$2&catid=$3&id=$4&userid=$5&page=$6
RewriteRule ^web-(.*)-([0-9]*)-([0-9]*)-([0-9]*)-([0-9]*)-([0-9]*)-([0-9]*).html index.php?m=yp&c=com_index&a=$1&modelid=$2&catid=$3&id=$4&tid=$5&userid=$6&page=$7
</pre>
</fieldset>
<div class="bk10"></div>
<fieldset>
<legend>IIS Web Server</legend>
<pre>
RewriteEngine on
RewriteRule ^(.*)/yp\.html\?*(.*)$ $1/index\.php\?m=yp&c=index&a=init
RewriteRule ^(.*)/yp\-([a-z]+)\-([0-9]+)\.html\?*(.*)$ $1/index\.php\?m=yp&c=index&a=model&modelid=$3
<?php echo $iis_rewriteurl?>
RewriteRule ^(.*)/yp\-show\-([0-9]+)\-([0-9]+)\.html\?*(.*)$ $1/index\.php\?m=yp&c=index&a=show&catid=$2&id=$3
RewriteRule ^(.*)/web\-([0-9]+)\.html\?*(.*)$ $1/index\.php\?m=yp&c=com_index&a=init&userid=$2
RewriteRule ^(.*)/web\-(.*)\-([0-9]*)\-([0-9]*)\-([0-9]*)\-([0-9]*)\-([0-9]*)\.html\?*(.*)$ $1/index\.php\?m=yp&c=com_index&a=$2&modelid=$3&catid=$4&id=$5&userid=$6&page=$7
RewriteRule ^(.*)/web\-(.*)\-([0-9]*)\-([0-9]*)\-([0-9]*)\-([0-9]*)\-([0-9]*)\-([0-9]*)\.html\?*(.*)$ $1/index\.php\?m=yp&c=com_index&a=$2&modelid=$3&catid=$4&id=$5&tid=$6&userid=$7&page=$8
</pre>
</fieldset>
</div>
<?php }?>
</html>