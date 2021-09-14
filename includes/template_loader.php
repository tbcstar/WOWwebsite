<?php 
require "includes/classes/template_parse.php";
 
global $Connect, $Plugins;
$conn = $Connect->connectToDB();
$Connect->selectDB("webdb", $conn);

if ($getTemplate = $conn->query("SELECT `path` FROM template WHERE applied='1';"))
{

$template = $getTemplate->fetch_assoc();

if (!file_exists("styles/". $template['path'] ."/style.css") || !file_exists("styles/" . $template['path'] . "/template.html"))
{
	buildError("<b>模板错误: </b>活动模板不存在或文件缺失. (". $template['path'].")", NULL);
	exit_page();
}
 
?>
<link rel="stylesheet" href="styles/<?php echo $template['path']; ?>/style.css" />
<link rel="stylesheet" href="styles/global/style.css" />
<?php
	$Plugins->load('styles');
}
else
{
    buildError("<b>获取模板路径时出错，有关详细信息，请参阅日志。</b>", NULL, $conn->error);
}
?>