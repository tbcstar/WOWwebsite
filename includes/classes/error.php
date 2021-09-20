<?php
function buildError($error,$num) 
{
	if ($GLOBALS['useDebug']==false) 
		log_error($error,$num);
	else 
		errors($error,$num);
}

function errors($error,$num) 
{
	log_error(strip_tags($error),$num);
	die("<center><b>网页错误</b>  <br/>
		网站脚本遇到错误并关闭。<br/><br/>
		<b>错误信息：</b>".$error."  <br/>
		<b>错误代码：</b>".$num."
		<br/><br/><br/><i>由CraftedWeb
		<br/><font size='-2'>Professionally developed with love.</font></i></center>
		");
}

function log_error($error,$num) 
{
 error_log("*[".date("d M Y H:i")."] ".$error."\r\n", 3, "error.log");
}

function loadCustomErrors() 
{
  set_error_handler("customError");   
}

function customError($errno, $errstr)
{
    if ($errno!=8 && $errno!=2048 && $GLOBALS['useDebug']==TRUE) 
          error_log("*[".date("d M Y H:i")."] ".$errstr."\r\n", 3, "error.log");
}
?>