<?php

require "includes/classes/template_parse.php";
 
global $Database, $Plugins;
$Database->selectDB("webdb");

if ( $getTemplate = $Database->select("template", "path", null, "applied='1'")->get_result() )
{
	$template = $getTemplate->fetch_assoc();

	if ( !file_exists("styles/". $template['path'] ."/style.css") || 
		!file_exists("styles/" . $template['path'] . "/template.html") )
	{
		buildError("<b>模板错误：</b>活动模板不存在或缺少文件。 (". $template['path'].")", NULL);
		exit_page();
	}?>
	<!-- Boostrap Styling -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- CraftedCMS Styling -->
	<link rel="stylesheet" href="styles/<?php echo $template['path']; ?>/style.css" />
	<link rel="stylesheet" href="styles/global/style.css" /><?php
	$Plugins->load('styles');
}
else
{
	buildError("<b>获取模板路径时出错，有关详细信息，请参阅日志。</b>", NULL, $Database->conn->error);
} 