<?php 
    $server->selectDB('webdb'); 
 	$page = new page;
	
	$page->validatePageAccess('Logs');
	
    if($page->validateSubPage() == TRUE) {
		$page->outputSubPage();
	} else {
		?>
        <div class='box_right_title'>嘿!你不应该在这里!</div>
        
		<pre>脚本可能对您进行了错误的重定向。还是……你是个黑客?无论如何,祝你好运。</pre>
        
        <a href="?p=logs&s=voteshop" class="content_hider">投票商店日志</a>
		<a href="?p=logs&s=donateshop" class="content_hider">公益商城日志</a>
		<?php
	 }
?>