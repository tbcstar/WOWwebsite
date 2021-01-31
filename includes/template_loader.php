<?php 
 require("includes/classes/template_parse.php"); 

 global $Connect, $Plugins, $conn;
 $Connect->selectDB('webdb');
 
 $getTemplate = mysqli_query($conn, "SELECT path FROM template WHERE applied='1' ORDER BY id ASC LIMIT 1");
 $row = mysqli_fetch_assoc($getTemplate);
 
 $template['path']=$row['path'];
 
 
 if(!file_exists("styles/".$template['path']."/style.css") || !file_exists("styles/".$template['path']."/template.html")) 
 {
	buildError("<b>模板错误:</b>活动模板不存在或缺少文件。",NULL);
	exit_page();
 }
 
 ?>
<link rel="stylesheet" href="styles/<?php echo $template['path']; ?>/style.css" />

<?php
	$Plugins->load('styles');
?>