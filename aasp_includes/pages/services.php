<?php 
	$server->selectDB('webdb'); 
 	$page = new page;
	
	$page->validatePageAccess('Services');
	
    if($page->validateSubPage() == TRUE) {
		$page->outputSubPage();
	} 
	else 
	{
		echo '<h2>禁止访问！</h2>或者... 这里什么也没有！'; 
	}
?>