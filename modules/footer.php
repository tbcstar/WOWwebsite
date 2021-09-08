<?php
if($GLOBALS['showLoadTime'] == TRUE) 
{
	$end = number_format( ( microtime(TRUE) - $GLOBALS['start'] ), 2);
	echo "页面加载中 ". $end ." 秒。 <br/>";
}
echo $GLOBALS['footer_text'];
?>