<?php
if($GLOBALS['showLoadTime']==TRUE) 
{
	$end = number_format((microtime(true) - $GLOBALS['start']),2);
	echo "页面加载中 ", $end, " 秒钟 <br/>";
}
echo $GLOBALS['footer_text'];
?>