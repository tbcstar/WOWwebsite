<?php 
    global $GameServer, $GamePage;
    $conn = $GameServer->connect();
    $GameServer->selectDB("webdb", $conn);
	
    if($GamePage->validateSubPage() == TRUE) 
    {
		$GamePage->outputSubPage();
	} 
	else 
	{
		?>
        <div class='box_right_title'>嘿!你不应该在这里!</div>
        
		<pre>脚本可能对您进行了错误的重定向。还是……你是黑客!?无论如何,祝你好运。</pre>
        
        <a href="?page=tools&selected=tickets" class="content_hider">工单</a>
		<a href="?page=tools&selected=accountaccess" class="content_hider">账户访问</a>
		<?php
	 }
?>