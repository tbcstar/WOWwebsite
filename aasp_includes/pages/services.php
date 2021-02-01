<?php 
	global $Server, $Page;
	$Server->selectDB('webdb');
	
	$Page->validatePageAccess('Services');
	
    if($Page->validateSubPage() == TRUE) 
    {
		$Page->outputSubPage();
	} 
	else 
	{
		echo '<h2>禁止访问！</h2>或者... 这里什么也没有！'; 
	}
?>