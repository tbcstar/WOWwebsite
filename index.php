<?php
require('includes/loader.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta name="description" content="TBCstar，时光回溯，亚洲最佳无类型服务器，魔兽世界TBC，战场和竞技场pvp，副本，wow服务器，最好的wow私人服务器，没有延迟或崩溃!2.4.3" />
<meta name="keywords" content="WOW wotlk, tbc 243, wotlk 3.3.5, Classless WOW, Classless,WoW, 无类别, 无类别WOW, 魔兽幻化, 幻化, 243, 3.3.5, tbc, wlk, Wintergrasp, Retail, The Wrath of the Lich King, Pathfinding, LoS, Best Scripts, WOTLK, Quality Private Server, Professional, Naxxramas, Naxx, Nax, Obsidian Sanctum, OS, EOT, Malygos, Eye of Eternity, Ulduar, Icecrown Citadel, ICC, ToC, Trial of the Crusade, full, full scripted, best scripted, Wintergrasp, blizzlike, blizlike" />
<meta name="Author" content="tbcstar.com" />
<meta name="Robots" content="all " />
<?php require('includes/template_loader.php'); ?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
<title>
<?php 
echo $website_title .' - '; 

while ($page_title = current($GLOBALS['core_pages'])) 
{
    if ($page_title == $_GET['p'].'.php') 
	{
        echo key($GLOBALS['core_pages']);
		$foundPT = true;
    }
    next($GLOBALS['core_pages']);
}
if(!isset($foundPT))
	echo ucfirst($_GET['p']);
?>
</title>

<?php
	$content = new Page('styles/'.$template['path'].'/template.html');
	$content->loadCustoms(); //Load custom modules
	
	$content->replace_tags(array('content' => 'modules/content.php')); //Main content 
	$content->replace_tags(array('menu' => 'modules/menu.php'));
	$content->replace_tags(array('login' => 'modules/login.php'));
	$content->replace_tags(array('account' => 'modules/account.php'));
	$content->replace_tags(array('serverstatus' => 'modules/server_status.php'));
	$content->replace_tags(array('slideshow' => 'modules/slideshow.php'));
	$content->replace_tags(array('footer' => 'modules/footer.php'));
	$content->replace_tags(array('loadjava' => 'includes/javascript_loader.php'));
	$content->replace_tags(array('social' => 'modules/social.php'));
	$content->replace_tags(array('alert' => 'modules/alert.php'));
?>
</head>

<body>
<?php
	$content->output();
?>
</body>
</html>