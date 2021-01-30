<?php
/**********************
CraftedWeb第一代
主要加载程序文件
*********************/

require('includes/misc/headers.php'); //Load sessions, erorr reporting & ob.

if(file_exists('install/index.php'))
	header("Location: install/index.php");

define('INIT_SITE', TRUE);

require('includes/configuration.php'); //加载配置文件

if(isset($GLOBALS['not_installed']) && $GLOBALS['not_installed']==true)
{
	if(file_exists('install/index.php'))
		header("Location: install/index.php");
	else
		exit('<b>错误</b>。看起来你的网站还没有安装，但是找不到安装程序!');	
}

if($GLOBALS['maintainance']==TRUE && !in_array($_SERVER['REMOTE_ADDR'],$GLOBALS['maintainance_allowIPs']))
{ 
  die("<center><h3>网站维护</h3>
      ".$GLOBALS['website_title']." 目前正在进行一些主要的维护，并将尽快提供。
      <br/><br/>Sincerely
  </center>");
}


require('includes/misc/connect.php'); //Load connection class
$connect = new connect;

$connect->connectToDB();
$connect->checkRevision();

require('includes/misc/func_lib.php'); 
require('includes/misc/compress.php'); 

require('includes/classes/account.php'); 
require('includes/classes/server.php'); 
require('includes/classes/website.php'); 
require('includes/classes/shop.php'); 
require('includes/classes/character.php'); 
require('includes/classes/cache.php'); 
require('includes/classes/plugins.php'); 

/******* 加载插件 ***********/
plugins::globalInit();

plugins::init('classes');
plugins::init('javascript');
plugins::init('modules');
plugins::init('styles');
plugins::init('pages');

//加载配置。
if($GLOBALS['enablePlugins']==true)
{
	if($_SESSION['loaded_plugins']!=NULL)
	{
		foreach($_SESSION['loaded_plugins'] as $folderName)
		{
			if(file_exists('plugins/'.$folderName.'/config.php'))
				include_once('plugins/'.$folderName.'/config.php');
		}
	}
}

$account = new account;
$account->getRemember(); //Remember thingy.

//这是为了防止错误 "Undefined index: p"
if (!isset($_GET['p'])) 
	$_GET['p'] = 'login';
	
###投票系统####
if(isset($_SESSION['votingUrlID']) && $_SESSION['votingUrlID']!=0 && $GLOBALS['vote']['type']=='confirm')
{
    if(website::checkIfVoted((int)$_SESSION['votingUrlID'],$GLOBALS['connection']['webdb'])==TRUE) 
		die("?p=vote");
	
	$acct_id = account::getAccountID($_SESSION['cw_user']);
	
	$next_vote = time() + $GLOBALS['vote']['timer'];
	
	connect::selectDB('webdb');
	
	mysql_query("INSERT INTO votelog VALUES('','".(int)$_SESSION['votingUrlID']."',
	'".$acct_id."','".time()."','".$next_vote."','".$_SERVER['REMOTE_ADDR']."')");
     
	$getSiteData = mysql_query("SELECT points,url FROM votingsites WHERE id='".(int)$_SESSION['votingUrlID']."'");
	$row = mysql_fetch_assoc($getSiteData);
	
	if(mysql_num_rows($getSiteData)==0)
	{
		header('Location: index.php');
		unset($_SESSION['votingUrlID']);
	}
	
	//Update the points table.
	$add = $row['points'] * $GLOBALS['vote']['multiplier'];
	mysql_query("UPDATE account_data SET vp=vp + ".$add." WHERE id=".$acct_id);
	
	unset($_SESSION['votingUrlID']);
	
	header("Location: ?p=vote");
}

###会话安全###
if(!isset($_SESSION['last_ip']) && isset($_SESSION['cw_user'])) 
	$_SESSION['last_ip'] = $_SERVER['REMOTE_ADDR'];
	
elseif(isset($_SESSION['last_ip']) && isset($_SESSION['cw_user'])) 
{
	if($_SESSION['last_ip']!=$_SERVER['REMOTE_ADDR'])
		header("Location: ?p=logout");
	else
		$_SESSION['last_ip']=$_SERVER['REMOTE_ADDR'];
}
?>

