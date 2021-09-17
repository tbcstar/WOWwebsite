<?php

$_SESSION = array();
session_destroy(); 
echo $_GET['error'];
?>
<hr/>
<a href="index.php" title="Log in again">点击这里再次登录</a>
