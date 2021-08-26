<?php
    define('INIT_SITE', TRUE);
    include('../../includes/misc/headers.php');
    include('../../includes/configuration.php');
    include('../functions.php');
    global $GameServer, $GameAccount;

    $conn = $GameServer->connect();
    $GameServer->selectDB('webdb', $conn);

###############################
if($_POST['action'] == "setTemplate") 
{
	mysqli_query($conn, "UPDATE template SET applied='0' WHERE applied='1';");
	mysqli_query($conn, "UPDATE template SET applied='1' WHERE id='".(int)$_POST['id']."';");
}
###############################
if($_POST['action'] == "installTemplate") 
{
	mysqli_query($conn, "INSERT INTO template VALUES('','".mysqli_real_escape_string($conn, trim($_POST['name']))."','".mysqli_real_escape_string($conn, trim($_POST['path']))."','0')");
	$GameServer->logThis("安装模板 ".$_POST['name']);
}
###############################
if($_POST['action'] == "uninstallTemplate") 
{
	mysqli_query($conn, "DELETE FROM template WHERE id='".(int)$_POST['id']."';");
	mysqli_query($conn, "UPDATE template SET applied='1' ORDER BY id ASC LIMIT 1;");
	
	$GameServer->logThis("卸载模板");
}
###############################
if($_POST['action'] == "getMenuEditForm") 
{
	$result = mysqli_query($conn, "SELECT * FROM site_links WHERE position='".(int)$_POST['id']."';");
	$rows 	= mysqli_fetch_assoc($result);
	 ?>
    标题<br/>
    <input type="text" id="editlink_title" value="<?php echo $rows['title']; ?>"><br/>
    URL<br/>
    <input type="text" id="editlink_url" value="<?php echo $rows['url']; ?>"><br/>
    显示时间<br/>
    <select id="editlink_shownWhen">
             <option value="always" <?php if($rows['shownWhen'] == "always") { echo "selected='selected'"; } ?>>总是</option>
             <option value="logged" <?php if($rows['shownWhen'] == "logged") { echo "selected='selected'"; } ?>>用户已登录</option>
             <option value="notlogged" <?php if($rows['shownWhen'] == "notlogged") { echo "selected='selected'"; } ?>>用户未登录</option>
    </select><br/>
    <input type="submit" value="Save" onclick="saveMenuLink('<?php echo $rows['position']; ?>')">
	
<?php }
###############################
if($_POST['action'] == "saveMenu") 
{
	$title 		= mysqli_real_escape_string($conn, $_POST['title']);
	$url 		= mysqli_real_escape_string($conn, $_POST['url']);
	$shownWhen 	= mysqli_real_escape_string($conn, $_POST['shownWhen']);
	$id 		= (int)$_POST['id'];

	if(empty($title) || empty($url) || empty($shownWhen)) 
	{
		die("请输入所有字段。");
	}

	mysqli_query($conn, "UPDATE site_links SET title='".$title."',url='".$url."',shownWhen='".$shownWhen."' WHERE position='".$id."';");
	
	$GameServer->logThis("修改菜单");
	
	echo TRUE;
}
###############################
if($_POST['action'] == "deleteLink") 
{
	mysqli_query($conn, "DELETE FROM site_links WHERE position='".(int)$_POST['id']."';");
	
	$GameServer->logThis("删除一个菜单链接");
	
	echo TRUE;
}
###############################
if($_POST['action'] == "addLink") 
{
	$title 		= mysqli_real_escape_string($conn, $_POST['title']);
	$url 		= mysqli_real_escape_string($conn, $_POST['url']);
	$shownWhen 	= mysqli_real_escape_string($conn, $_POST['shownWhen']);
	
	if(empty($title) || empty($url) || empty($shownWhen)) 
	{
		die("请输入所有字段。");
	}

	mysqli_query($conn, "INSERT INTO site_links (title, url, shownWhen) VALUES('" . $title . "','" . $url . "','" . $shownWhen . "');");

	$GameServer->logThis("添加".$title."到菜单上");

	echo TRUE;
}
###############################
if($_POST['action'] == "deleteImage") 
{
	$id = (int)$_POST['id'];
	mysqli_query($conn, "DELETE FROM slider_images WHERE position='".$id."';");
	
	$GameServer->logThis("图像已从幻灯片中删除");
	
	return;
}
###############################
if($_POST['action'] == "disablePlugin") 
{
	$foldername = mysqli_real_escape_string($conn, $_POST['foldername']);
	
	mysqli_query($conn, "INSERT INTO disabled_plugins VALUES('".$foldername."');");
	
	include('../../plugins/'.$foldername.'/info.php');
	$GameServer->logThis("禁用插件".$title);
}
###############################
if($_POST['action'] == "enablePlugin") 
{
	$foldername = mysqli_real_escape_string($conn, $_POST['foldername']);

	mysqli_query($conn, "DELETE FROM disabled_plugins WHERE foldername='".$foldername."';");

	include('../../plugins/'.$foldername.'/info.php');
	$GameServer->logThis("启用插件".$title);
}
###############################
?>