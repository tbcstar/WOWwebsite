<?php
/**********************
CraftedWeb第一代
主要加载程序文件
*********************/

require('includes/misc/headers.php'); //Load sessions, error reporting & ob.

if (file_exists("install/index.php"))
{
	header("Location: install/index.php");
}

define('INIT_SITE', TRUE);

require('includes/configuration.php'); //加载配置文件

if(isset($GLOBALS['not_installed']) && $GLOBALS['not_installed'] == TRUE)
{
	if (file_exists("install/index.php"))
	{
		header("Location: install/index.php");
        exit;
	}
	else
	{
		die("<b>错误</b>。似乎你的网站还没有安装，但没有安装程序可以找到!");
	}
}

if($GLOBALS['maintainance']==TRUE && !in_array($_SERVER['REMOTE_ADDR'],$GLOBALS['maintainance_allowIPs']))
{ 
    die(
        htmlentities("<center><h3>网站维护</h3>". $GLOBALS['website_title'] ." 目前正在进行一些重大维护，将尽快恢复。<br/><br/>TBCstar 项目组</center>"));
}

require('includes/misc/connect.php'); //Load connection class
global $Connect;

$conn = $Connect->connectToDB();

require('includes/misc/func_lib.php'); 
require('includes/misc/compress.php'); 

require('includes/classes/account.php'); 
require('includes/classes/server.php'); 
require('includes/classes/website.php'); 
require('includes/classes/shop.php'); 
require('includes/classes/character.php'); 
require('includes/classes/cache.php'); 
require('includes/classes/plugins.php'); 

global $Plugins, $Account, $Website;

/******* 加载插件 ***********/
$Plugins->globalInit();

$Plugins->init("classes");
$Plugins->init("javascript");
$Plugins->init("modules");
$Plugins->init("styles");
$Plugins->init("pages");

//加载配置。
if($GLOBALS['enablePlugins'] == TRUE)
{
	if($_SESSION['loaded_plugins'] != NULL)
	{
		if (is_array($_SESSION['loaded_plugins']) || is_object($_SESSION['loaded_plugins']))
		{
			foreach($_SESSION['loaded_plugins'] as $folderName)
			{
				if (file_exists("plugins/". $folderName ."/config.php"))
				{
					include_once("plugins/". $folderName ."/config.php");
				}
			}
		}
	}
}

$Account->getRemember(); //Remember thingy.

//这是为了防止错误 "Undefined index: p"
if (!isset($_GET['page']))
{
	$_GET['page'] = 'login';
}

###投票系统####
if(isset($_SESSION['votingUrlID']) && $_SESSION['votingUrlID']!=0 && $GLOBALS['vote']['type'] == 'confirm')
{
    if ($Website->checkIfVoted($conn->escape_string($_SESSION['votingUrlID']), $GLOBALS['connection']['webdb']) == TRUE)
        {
            die(htmlentities("?page=vote"));
        }
	
	$acct_id = $Account->getAccountID($_SESSION['cw_user']);
	
	$next_vote = time() + $GLOBALS['vote']['timer'];
	
	$Connect->selectDB("webdb", $conn);

	$conn->query("INSERT INTO votelog (`siteid`, `userid`, `timestamp`, `next_vote`, `ip`) VALUES 
        (". $conn->escape_string($_SESSION['votingUrlID']) .", ". $acct_id .", '" . time() . "', ". $next_vote .", '" . $_SERVER['REMOTE_ADDR'] . "');");

	$getSiteData = $conn->query("SELECT points,url FROM votingsites WHERE id=". $conn->escape_string($_SESSION['votingUrlID']) .";");
    $row         = $getSiteData->fetch_assoc();
	
	if ($getSiteData->num_rows == 0)
	{
		header("Location: index.php");
		unset($_SESSION['votingUrlID']);
	}
	
	//Update the points table.
	$add = $row['points'] * $GLOBALS['vote']['multiplier'];
	$conn->query("UPDATE account_data SET vp=vp + ". $add ." WHERE id=". $acct_id .";");
	
	unset($_SESSION['votingUrlID']);
	
	header("Location: ?page=vote");
}

###会话安全###
if(!isset($_SESSION['last_ip']) && isset($_SESSION['cw_user']))
{
	$_SESSION['last_ip'] = $_SERVER['REMOTE_ADDR'];
}
elseif(isset($_SESSION['last_ip']) && isset($_SESSION['cw_user'])) 
{
	if($_SESSION['last_ip']!=$_SERVER['REMOTE_ADDR'])
	{
		header("Location: ?page=logout");
	}
	else
	{
		$_SESSION['last_ip']=$_SERVER['REMOTE_ADDR'];
	}
}