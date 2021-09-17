<?php 
    global $GameServer, $GamePage;
    $conn = $GameServer->connect();
    $GameServer->selectDB("webdb");
	
	$GamePage->validatePageAccess("Services");
	
    if($GamePage->validateSubPage() == TRUE) 
    {
		$GamePage->outputSubPage();
	} 
	else 
	{
		echo '<h2>禁止访问！</h2>或者... 这里什么也没有！'; 
	}
?>