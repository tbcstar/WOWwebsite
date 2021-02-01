<?php 
	global $Server, $Page;
    $Server->selectDB('webdb');
	
    if($Page->validateSubPage() == TRUE) 
    {
		$Page->outputSubPage();
	} 
	else 
	{
		?>
        <div class='box_right_title'>嘿!你不应该在这里!</div>
        
		<pre>脚本可能对您进行了错误的重定向。还是……你是黑客!?无论如何,祝你好运。</pre>
        
        <a href="?p=tools&s=tickets" class="content_hider">工单</a>
		<a href="?p=tools&s=accountaccess" class="content_hider">账户访问</a>
		<?php
	 }
?>