<?php         $html = <<<crifan
<form enctype="multipart/form-data" method="post" action="">
<input type="hidden" name="{$token_name}" value="{$token_hash}">
</form>
crifan;
echo $html;
#highlight_file("../controllers/Api.php");
echo $file;
?>