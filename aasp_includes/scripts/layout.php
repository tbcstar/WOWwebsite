<?php
define('INIT_SITE', TRUE);
include('../../includes/misc/headers.php');
include('../../includes/configuration.php');
include('../functions.php');
$server = new server;
$account = new account;

$server->selectDB('webdb');

###############################
if($_POST['action']=="setTemplate") 
{
	mysql_query("UPDATE template SET applied='0' WHERE applied='1'");
	mysql_query("UPDATE template SET applied='1' WHERE id='".(int)$_POST['id']."'");
}
###############################
if($_POST['action']=="installTemplate") 
{
	mysql_query("INSERT INTO template VALUES('','".mysql_real_escape_string(trim($_POST['name']))."','".mysql_real_escape_string(trim($_POST['path']))."','0')");
	$server->logThis("安装模板".$_POST['name']);
}
###############################
if($_POST['action']=="uninstallTemplate") 
{
	mysql_query("DELETE FROM template WHERE id='".(int)$_POST['id']."'");
	mysql_query("UPDATE template SET applied='1' ORDER BY id ASC LIMIT 1");
	
	$server->logThis("卸载模板");
}
###############################
if($_POST['action']=="getMenuEditForm") 
{
	$result = mysql_query("SELECT * FROM site_links WHERE position='".(int)$_POST['id']."'");
	$rows = mysql_fetch_assoc($result);
	 ?>
    标题<br/>
    <input type="text" id="editlink_title" value="<?php echo $rows['title']; ?>"><br/>
    URL<br/>
    <input type="text" id="editlink_url" value="<?php echo $rows['url']; ?>"><br/>
    显示时间<br/>
    <select id="editlink_shownWhen">
             <option value="always" <?php if($rows['shownWhen']=="always") { echo "selected='selected'"; } ?>>总是</option>
             <option value="logged" <?php if($rows['shownWhen']=="logged") { echo "selected='selected'"; } ?>>用户已登录</option>
             <option value="notlogged" <?php if($rows['shownWhen']=="notlogged") { echo "selected='selected'"; } ?>>用户未登录</option>
    </select><br/>
    <input type="submit" value="Save" onclick="saveMenuLink('<?php echo $rows['position']; ?>')">
	
<?php }
###############################
if($_POST['action']=="saveMenu") 
{
	$title = mysql_real_escape_string($_POST['title']);
	$url = mysql_real_escape_string($_POST['url']);
	$shownWhen = mysql_real_escape_string($_POST['shownWhen']);
	$id = (int)$_POST['id'];
	
	if(empty($title) || empty($url) || empty($shownWhen)) {
		die("请输入所有字段。");
	}
	
	mysql_query("UPDATE site_links SET title='".$title."',url='".$url."',shownWhen='".$shownWhen."' WHERE position='".$id."'");
	
	$server->logThis("修改菜单");
	
	echo TRUE;
}
###############################
if($_POST['action']=="deleteLink") 
{
	mysql_query("DELETE FROM site_links WHERE position='".(int)$_POST['id']."'");
	
	$server->logThis("删除一个菜单链接");
	
	echo TRUE;
}
###############################
if($_POST['action']=="addLink") 
{
	$title = mysql_real_escape_string($_POST['title']);
	$url = mysql_real_escape_string($_POST['url']);
	$shownWhen = mysql_real_escape_string($_POST['shownWhen']);
	
	if(empty($title) || empty($url) || empty($shownWhen)) {
		die("请输入所有字段。");
	}
	
	mysql_query("INSERT INTO site_links VALUES('','".$title."','".$url."','".$shownWhen."')");
	
	$server->logThis("添加".$title."到菜单上");
	
	echo TRUE;
}
###############################
if($_POST['action']=="deleteImage") 
{
	$id = (int)$_POST['id'];
	mysql_query("DELETE FROM slider_images WHERE position='".$id."'");
	
	$server->logThis("图像已从幻灯片中删除");
	
	return;
}
###############################
if($_POST['action']=="disablePlugin") 
{
	$foldername = mysql_real_escape_string($_POST['foldername']);
	
	mysql_query("INSERT INTO disabled_plugins VALUES('".$foldername."')");
	
	include('../../plugins/'.$foldername.'/info.php');
	$server->logThis("禁用插件".$title);
}
###############################
if($_POST['action']=="enablePlugin") 
{
	$foldername = mysql_real_escape_string($_POST['foldername']);
	
	mysql_query("DELETE FROM disabled_plugins WHERE foldername='".$foldername."'");
	
	include('../../plugins/'.$foldername.'/info.php');
	$server->logThis("启用插件".$title);
}
###############################
?>