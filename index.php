<?php
	require "includes/loader.php"; //Load all php scripts
?>
<!DOCTYPE>
<html>
<head>
	<meta name="description" content="TBCstar，时光回溯，亚洲最佳无类别服务器，无职业魔兽，魔兽世界TBC，魔兽世界WIL，战场和竞技场pvp，副本，wow服务器，最好的wow私人服务器，没有延迟或崩溃!" />
	<meta name="keywords" content="WOW wotlk, tbc 243, wotlk 335, Classless WOW, Classless,WoW, 无类别, 无类别WOW, 魔兽幻化, 幻化, 243, 335, tbc, wlk, Wintergrasp, Retail, 无职业, Pathfinding, LoS, Best Scripts, WOTLK, Quality Private Server, Professional, Naxxramas, Naxx, Nax, Obsidian Sanctum, OS, EOT, Malygos, Eye of Eternity, Ulduar, Icecrown Citadel, ICC, ToC, Trial of the Crusade, full, full scripted, best scripted, Wintergrasp, blizzlike, blizlike" />
	<meta name="Author" content="tbcstar.com" />
	<meta name="Robots" content="all " />
	<?php require "includes/template_loader.php"; ?>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
	<title>
        <?php $Website->getTitle(); ?>
    </title>

    <?php
        $content = new Page("styles/". $template['path'] ."/template.html");

        $content->loadCustoms(); //Load custom modules

        $content->replace_tags( ['content'      => 'modules/content.php'] ); //Main content 
        $content->replace_tags( ['menu'         => 'modules/menu.php'] );
        $content->replace_tags( ['login'        => 'modules/login.php'] );
        $content->replace_tags( ['account'      => 'modules/account.php'] );
        $content->replace_tags( ['serverstatus' => 'modules/server_status.php'] );
        $content->replace_tags( ['slideshow'    => 'modules/slideshow.php'] );
        $content->replace_tags( ['footer'       => 'modules/footer.php'] );
        $content->replace_tags( ['loadjava'     => 'includes/javascript_loader.php'] );
        $content->replace_tags( ['social'       => 'modules/social.php'] );
        $content->replace_tags( ['alert'        => 'modules/alert.php'] );
    ?>
</head>

<body>
    <?php $content->output(); ?>
</body>
</html> 