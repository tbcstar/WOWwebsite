<?php

include('documents/alert.php');

if($alert_enabled == TRUE)
{
	echo '<div id="alert"><b>公告：</b> ';
	echo $alert_message; 
	echo '</div>';
}

?>