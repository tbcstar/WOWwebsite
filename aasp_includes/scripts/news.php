<?php
define('INIT_SITE', TRUE);
include('../../includes/misc/headers.php');
include('../../includes/configuration.php');
include('../functions.php');
global $Server, $Account, $conn;

$Server->selectDB('webdb');

###############################
if($_POST['function'] == 'post') 
{
	if(empty($_POST['title']) || empty($_POST['author']) || empty($_POST['content']))
	{
		die('<span class="red_text">请输入所有字段。</span>');
	}

	mysqli_query($conn, "INSERT INTO news (title,body,author,image,date) VALUES
	('".mysqli_real_escape_string($conn, $_POST['title'])."','".mysqli_real_escape_string($conn, $_POST['content'])."',
	'".mysqli_real_escape_string($conn, $_POST['author'])."','".mysqli_real_escape_string($conn, $_POST['image'])."',
	'".date("Y-m-d H:i:s")."');");

	$Server->logThis("发布新闻");
	echo "发布新闻成功。";
}
################################
elseif($_POST['function'] == 'delete') 
{
	if(empty($_POST['id']))
		die('未指定ID。正在中止...');

	mysqli_query($conn, "DELETE FROM news WHERE id='".mysqli_real_escape_string($conn, $_POST['id'])."';");
	mysqli_query($conn, "DELETE FROM news_comments WHERE id='".mysqli_real_escape_string($conn, $_POST['id'])."';");
	$Server->logThis("删除了一条新闻");
}
##############################
elseif($_POST['function'] == 'edit') 
{
	$id = (int)$_POST['id'];
	$title 		= mysqli_real_escape_string($conn, ucfirst($_POST['title']));
	$author 	= mysqli_real_escape_string($conn, ucfirst($_POST['author']));
	$content 	= mysqli_real_escape_string($conn, $_POST['content']);
	
	if(empty($id) || empty($title) || empty($content))
	{
	 	die("请输入两个字段。");
	}
    else 
	{
		mysqli_query($conn, "UPDATE news SET title='".$title."', author='".$author."', body='".$content."' WHERE id='".$id."';");
		$Server->logThis("更新新闻内容，ID： <b>".$id."</b>");
		return;
	}
}
#############################
elseif($_POST['function'] == 'getNewsContent') 
{
	$result = mysqli_query($conn, "SELECT * FROM news WHERE id='".(int)$_POST['id']."'");
	$row 	= mysqli_fetch_assoc($result);
	$content = str_replace('<br />', "\n", $row['body']);
	
	echo "标题： <br/><input type='text' id='editnews_title' value='".$row['title']."'><br/>内容：<br/><textarea cols='55' rows='8' id='editnews_content'>"
	.$content."</textarea><br/>作者：<br/><input type='text' id='editnews_author'><br/><input type='submit' value='Save' onclick='editNewsNow(".$row['id'].")'>";
}

?>