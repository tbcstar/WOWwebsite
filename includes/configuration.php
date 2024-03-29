<?php
	if(!defined('INIT_SITE'))
		exit();
		
	#############################
	## CRAFTEDWEB配置文件      ##
	## 第一代                  ##
	## 作者：				   ##
	## Anthony @ CraftedDev    ##
	## ------------------------##
	## 请注意：                ##
	## true = Enabled          ##
	## false = Disabled        ##
	#############################
	
	/*************************/
	/* 常规设置      
	/*************************/
	 $useDebug = true; //如果你的网站有问题，设置为“true”，如果没有，设置为“false”。
	 //所有错误都将被记录并在"include /error-log.php"中可见。如果设置为false，错误日志将为空。
	 //This will also enable/disable errors on the Admin- & Staff panel.
	 
	 $maintainance = false; //维护模式下,将对所有人关闭网站。 True = enable, false = disable
	 $maintainance_allowIPs = array('herp.derp.13.37'); //允许特定的IP地址查看网站，即使您启用维护模式。
	 //Example: '123.456.678', '987.654.321'
	 
	 $website_title = 'TBCstar | 时光回溯 | 亚洲最佳无类型服务器'; //网站的标题，显示在用户的浏览器中。
	 $shutDownOnMismatch = false; //这决定了当数据库和网站核心之间存在不匹配时，网站是否会关闭。
	 
	 $default_email = 'admin@tbcstar.com'; //将发送wich电子邮件的默认电子邮件地址。

	 $website_domain = 'http://www.tbcstar.com/'; //提供域名和路径到你的网站。
	 //Example: http://yourserver.com/
	 //如果你的网站在子目录下， include that aswell. Ex: http://yourserver.com/cataclysm/
	 
	 $showLoadTime = false; 
	 //在页脚显示页面加载时间。
	 
	 $footer_text = 'Copyright &copy; TBCstar | 时光回溯 2021<br/>
	 版权所有'; //设置页脚文本，显示在底部。
	 //Tips: &copy; = Copyright symbol. <br/> = line break.
	 
	 $timezone = 'Asia/Shanghai'; //为你的网站设置时区。默认:欧洲/贝尔格莱德(GMT +1)
	 //支持时区的完整列表可以在这里找到: http://php.net/manual/en/timezones.php
	 
	 $core_expansion = 1; //服务器的版本。
	 // 0 = Vanilla
	 // 1 = The Burning Crusade
	 // 2 = Wrath of The Lich King
	 // 3 = Cataclysm
	 
	 $adminPanel_enable = true; //启用或禁用管理员面板。
	 $staffPanel_enable = false; //启用或禁用Staff面板。
	 
	 $adminPanel_minlvl = 3; //最低的GM级别，其中帐户能够登录到管理面板。默认值:5
	 $staffPanel_minlvl = 2; //能够登录到员工面板的最低GM级别。默认值:3
	 
	 $staffPanel_permissions['Pages'] = true;
	 $staffPanel_permissions['News'] = true;
	 $staffPanel_permissions['Shop'] = true;
	 $staffPanel_permissions['Donations'] = true;
	 $staffPanel_permissions['Logs'] = true;
	 $staffPanel_permissions['Interface'] = true;
	 $staffPanel_permissions['Users'] = true;
	 $staffPanel_permissions['Realms'] = true;
	 $staffPanel_permissions['Services'] = true;
	 $staffPanel_permissions['Tools->Tickets'] = true;
	 $staffPanel_permissions['Tools->Account Access'] = true;
	 $staffPanel_permissions['editNewsComments'] = true;
	 $staffPanel_permissions['editShopItems'] = true;
	 
	 //Pages = 禁用/启用页面&创建自定义页面。
	 //News = 编辑/删除/发布新闻。
	 //Shop = 添加/编辑/删除商店物品。
	 //Donations = 查看捐赠的概述和日志。
	 //Logs = 查看投票和捐赠商店日志。
	 //Interface = 编辑菜单，模板和幻灯片。
	 //Users = 查看和编辑用户数据。
	 //Realms = 编辑/删除/添加伺服器。
	 //Services = 编辑投票链接和字符服务。
	 //Tools->Tickets = 查看/锁定/删除门票。
	 //Tools->Account Access = 编辑/删除/添加帐户访问。
	 //editNewsComments = 编辑/删除新闻评论。
	 //editShopItems = 编辑/删除商店物品。
	 
	$enablePlugins = true; //启用或禁用插件的使用。插件可能会让你的站点慢一点。
	 
	/*************************/
	/* 幻灯片设置 
	/*************************/
	$enableSlideShow = true; //启用或禁用幻灯片。这将只在主页上显示。
	
	/*************************/
	/* 网站压缩设置   
	/*************************/
	
	$compression['gzip'] = true; //这很难解释，但它可能大大提高你的网站速度。
	$compression['sanitize_output'] = true; //将删除所编写的HTML代码中的所有空白。这应该会稍微提高网站速度。
	//“模仿者”将很难窃取你的HTML代码:>
	
	$useCache = true; //启用/禁用缓存的使用。它还处于早期开发阶段，目前只应用于核心中的少数内容。
	//在启用此功能时，您可能不会注意到任何差异，除非您有很多访问者。谁知道呢，我还没试过呢。
	
	
	/*************************/
	/* 新闻设置   
	/*************************/
	$news['enable'] = true; // 启用/禁用网页上的新闻系统。
	$news['maxShown'] = 2; //将在主页上显示的新闻帖子的最大数量。
							 //人们仍然可以通过点击“所有新闻”按钮查看所有的文章。
	$news['enableComments'] = true; //让人们能够评论你的新闻帖子。
	$news['limitHomeCharacters'] = false; //这将限制在新闻帖子中显示的字符。人们将不得不点击“阅读更多…”按钮
	//阅读整篇新闻。
	
	
	/***** 服务器状态 ******/
	$serverStatus['enable'] = true; //这将启用/禁用伺服器状态框。
	$serverStatus['nextArenaFlush'] = true; //将显示为您的领域刷新的下一个竞技场。
	$serverStatus['uptime'] = true; //这将显示您的伺服器的正常运行时间。
	$serverStatus['playersOnline'] = true; //这将显示当前的在线玩家
	$serverStatus['factionBar'] = true; //这将显示在线玩家阵营。
	
	
	/*************************/
	/* mySQL连接设置
	/*************************/
	
	$connection['host'] = 'game.tbcstar.com';
	$connection['user'] = 'root';
	$connection['password'] = 'A112233a!';
	$connection['logondb'] = 'auth';
	$connection['webdb'] = 'tbcstar';
	$connection['worlddb'] = 'world';
	$connection['realmlist'] = 'game.tbcstar.com';
	
	// host = IP地址或域名地址（可以修改端口）
	// user = 一个可以查看/写入整个数据库的mySQL用户
	// password = The password for the user you specified
	// logondb = The name of your "auth" or "realmdb" database name. Default: auth
	// webdb = The name of the database with CraftedWeb data. Default: craftedweb
	// worlddb = The name of your world database. Default: world
	// realmlist = This could be your server IP or DNS. Ex: logon.yourserver.com
	
	/*************************/
	/* 注册设置
	/*************************/
	$registration['userMaxLength'] = 16;
	$registration['userMinLength'] = 3;
	$registration['passMaxLength'] = 22;
	$registration['passMinLength'] = 5;
	$registration['validateEmail'] = false;
	$registration['captcha'] = true;
	
	//userMaxLength = 用户名的最大长度
	//userMinLength = 用户名的最小长度
	//passMaxLength = 密码的最大长度
	//passMinLength = 密码的最小长度
	//validateEmail = 验证电子邮件地址是否是正确的电子邮件地址。可能无法在某些PHP版本上工作。
	//captcha = 启用/禁用验证码(反机器人)
	
	/*************************/
	/* 投票设置
	/*************************/
	$vote['timer'] = 43200;
	$vote['type'] = 'confirm';
	$vote['multiplier'] = 1;
	
	// timer = ：每个链接上的每个投票之间的计时器，以秒为单位。默认值:43200(12小时)
	// type = 投票系统类型。 
	//         'instant' = 当用户点击投票按钮时立即给出投票点数。
	//         'confirm' = 当用户返回到你的网站时给予投票点数。(希望通过点击你的标题网站)
	// multiplier = 乘以每次投票给出的投票点数。适用于特殊节日等。
	
	/*************************/
	/* 捐赠设置
	/*************************/
	$donation['paypal_email'] = 'admin@tbcstar.com';
	$donation['coins_name'] = '捐赠积分';
	$donation['currency'] = 'USD';
	$donation['emailResponse'] = true;
	$donation['sendResponseCopy'] = false;
	$donation['copyTo'] = 'admin@tbcstar.com';
	$donation['responseSubject'] = '感谢您对我们的支持！';
	$donation['donationType'] = 2;
	
	// paypal_email = 将发送到wich支付的PayPal邮箱地址。
	// coins_name = 用户将购买的捐款积分的名称。
	// currency = 您希望用户使用的货币名称。默认值:
	// emailResponse = 启用此功能将使捐赠者在捐赠后收到一封验证电子邮件，其中包含捐赠信息。
	// sendResponseCopy = 如果您希望收到上述电子邮件回复的副本，请将此设置为“true”。
	// copyTo = 启用sendResponseCopy来激活此函数。请输入付款副本的电子邮件地址。
	// responseSubject =  启用sendResponseCopy来激活此函数。发送给捐赠者的回复邮件的主题。
	// donationType = 用户将如何捐赠。1 =他们可以输入他们希望购买多少枚硬币，其价值可以随着乘数而增加。
	// 2 = 将显示选项列表，您可以设置下面的列表。
	
	/*  只有当您将“donationType”设置为2时，才有必要编辑此选项 */
	/* 只需按照模板输入您的自定义值 */
	/* array('NAME/TITLE', COINS TO ADD, PRICE) */
	$donationList = array(
			array('10点积分 - 5$', 10, 5),
			array('20点积分 - 8$', 20, 8),
			array('50点积分 - 20$', 50, 20),
			array('100点积分 - 35$', 100, 35 ),
			array('200点积分 - 70$', 200, 70 )
	);
	
	/*************************/
	/* 投票和捐赠商店设置
	/*************************/
	$voteShop['enableShop'] = true;
	$voteShop['enableAdvancedSearch'] = true;
	$voteShop['shopType'] = 2;
	
	// enableShop = 启用/禁用投票商店的使用。"true" =启用，"false" =禁用。
	// enableAdvancedSearch = 启用/禁用高级搜索功能。"true" =启用，"false" =禁用。
	// shopType = 您希望使用的商店类型。 1 = "搜索". 2 = 列出所有可用的物品。
	
	
	/*************************/
	$donateShop['enableShop'] = true;
	$donateShop['enableAdvancedSearch'] = true;
	$donateShop['shopType'] = 2;
	
	// enableShop = 启用/禁用投票商店的使用。"true" =启用，"false" =禁用。
	// enableAdvancedSearch = 启用/禁用高级搜索功能。"true" =启用，"false" =禁用。
	// shopType = 您希望使用的商店类型。 1 = "搜索". 2 = 列出所有可用的物品。
	
	/*************************/
	/* 社交插件设置
	/*************************/
	$social['enableFacebookModule'] = false;
	$social['facebookGroupURL'] = 'http://www.facebook.com/YourServer';
	
	// enableFacebookModule = 这将在服务器状态的左边创建一个Facebook框。"true" =启用，"false" =禁用。
	// facebookGroupURL = 你的facebook群的完整URL。
	// 注意!由于某些主题的宽度，这个特性可能会有一些bug。不过我还是祝你好运。
	
	/*************************/
	/* 论坛设置
	/*************************/
	$forum['type'] = 'phpbb';
	$forum['autoAccountCreate'] = true;
	$forum['forum_path'] = "forum.tbcstar.com";
	$forum['forum_db'] = 'tbcstarBBS';
	
	// type = 你使用的论坛类型。(phpbb链入页面)
	// autoAccountCreate = 当用户在网站注册时，此功能创建一个论坛帐户。
	// forum_path = 到论坛的路径。例子:如果你的网站上有。然后输入"/forum/"。(外部 "")
	// forum_db = 论坛的数据库名称。如果您的论坛数据库与您的登录数据库在同一位置，
	// 			  这将在您的管理面板上启用“最新的论坛活动”。
	######NOTE#######
	// autoAccountCreate只支持phpBB, vBulletin将在不久的将来支持。
	
	/************************/
	/* 高级设置，主要用于进一步开发。
	/* 不要碰这些配置，除非你知道你在做什么! */
	/************************/
	$current_revision = '1.0'; //当前的数据库和已安装的CraftedWeb的核心修订。
	
	$core_pages = array('账户面板' => 'account.php','购物车' => 'cart.php',
	'修改密码' => 'changepass.php','捐赠' => 'donate.php','捐赠商城' => 'donateshop.php',
	'忘记密码' => 'forgotpw.php','首页' => 'home.php','注销' => 'logout.php',
	'新闻' => 'news.php','战友招募' => 'raf.php','注册' => 'register.php',
	'角色复活' => 'revive.php','修改Email' => 'settings.php','支持' => 'support.php',
	'角色传送' => 'teleport.php','角色解卡' => 'unstuck.php','投票' => 'vote.php',
	'投票商店' => 'voteshop.php','确认服务' => 'confirmservice.php');
	
	###根据资料片加载最大物品等级###
	switch($GLOBALS['core_expansion']) 
	{
		case(0):
		$maxItemLevel = 100;
		break;
		case(1):
		$maxItemLevel = 175;
		break;
		default:
		case(2):
		$maxItemLevel = 284;
		break;
		case(3):
		$maxItemLevel = 416;
		break;
	}
	
	if($GLOBALS['core_expansion']>1) 
		$tooltip_href = 'wotlk.cavernoftime.com/';
	else
		$tooltip_href = 'wotlk.cavernoftime.com/';
	
	//设置时区。
	date_default_timezone_set($GLOBALS['timezone']);
	
	//设置错误处理。
	if(file_exists('includes/classes/error.php'))
		require('includes/classes/error.php');
		
	elseif(file_exists('../classes/error.php'))
		require('../classes/error.php');
		
	elseif(file_exists('../includes/classes/error.php'))
		require('../includes/classes/error.php');
	
	elseif(file_exists('../../includes/classes/error.php'))
		require('../../includes/classes/error.php');
	
	elseif(file_exists('../../../includes/classes/error.php'))
		require('../../../includes/classes/error.php');
	
	loadCustomErrors(); //加载自定义错误
?>