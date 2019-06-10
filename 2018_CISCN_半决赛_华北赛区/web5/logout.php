<?php
session_start();
unset($_SESSION['uid']);
unset($_SESSION['username']);
setcookie('_xsrf','guoqi',time()-3600);
setcookie("iv", 'guoqi',time()-3600);
setcookie("cipher", 'guoqi',time()-3600);
echo '<meta http-equiv="Refresh" content="0;url=/login"/>';