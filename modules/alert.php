<?php

include "documents/alert.php";

if($alert_enabled == true)
{
	echo '<div id="alert"><b>公告：</b> ';
	echo $alert_message; 
	echo '</div>';
}

?>