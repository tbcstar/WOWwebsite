<?php
define('INIT_SITE', TRUE);
include('../../includes/misc/headers.php');
include('../../includes/configuration.php');
include('../functions.php');
$server = new server;
$account = new account;

$server->selectDB('webdb');

###############################
if($_POST['function']=='post') 
{
	if(empty($_POST['title']) || empty($_POST['author']) || empty($_POST['content']))
		die('<span class="red_text">请输入所有字段。</span>');

	mysql_query("INSERT INTO news VALUES ('','".mysql_real_escape_string($_POST['title'])."','".mysql_real_escape_string($_POST['content'])."',
	'".mysql_real_escape_string($_POST['author'])."','".mysql_real_escape_string($_POST['image'])."',
	'".date("Y-m-d H:i:s")."')");
	$server->logThis("发布新闻");
	echo "发布新闻成功。";
}
################################
elseif($_POST['function']=='delete') 
{
	if(empty($_POST['id']))
		die('未指定ID。正在中止...');

	mysql_query("DELETE FROM news WHERE id='".mysql_real_escape_string($_POST['id'])."'");
	mysql_query("DELETE FROM news_comments WHERE id='".mysql_real_escape_string($_POST['id'])."'");
	$server->logThis("删除了一条新闻");
}
##############################
elseif($_POST['function']=='edit') 
{
	$id = (int)$_POST['id'];
	$title = ucfirst(mysql_real_escape_string($_POST['title']));
	$author = ucfirst(mysql_real_escape_string($_POST['author']));
	$content = mysql_real_escape_string($_POST['content']);
	
	if(empty($id) || empty($title) || empty($content))
	 	die("请输入两个字段。");
    else 
	{
		mysql_query("UPDATE news SET title='".$title."', author='".$author."', body='".$content."' WHERE id='".$id."'");
		$server->logThis("更新新闻内容，ID： <b>".$id."</b>");
		return;
	}
}
#############################
elseif($_POST['function']=='getNewsContent') 
{
	$result = mysql_query("SELECT * FROM news WHERE id='".(int)$_POST['id']."'");
	$row = mysql_fetch_assoc($result);
	$content = str_replace('<br />', "\n", $row['body']);
	
	echo "标题： <br/><input type='text' id='editnews_title' value='".$row['title']."'><br/>内容：<br/><textarea cols='55' rows='8' id='editnews_content'>"
	.$content."</textarea><br/>作者：<br/><input type='text' id='editnews_author'><br/><input type='submit' value='Save' onclick='editNewsNow(".$row['id'].")'>";
}

?>