<?php

global $GamePage, $GameServer;
$conn = $GameServer->connect();
$GameServer->selectDB("webdb", $conn);
if(isset($_POST['newpage']))
{
	$name     = $conn->escape_string($_POST['newpage_name']);
    $filename = $conn->escape_string(trim(strtolower($_POST['newpage_filename'])));
    $content  = $conn->escape_string(htmlentities($_POST['newpage_content']));
	
	if(empty($name) || empty($filename) || empty($content)) {
		echo "<h3>请输入 <u>所有</u> 字段。</h3>";
	}
	else
	{
        $conn->query("INSERT INTO custom_pages (name, filename, content, date) VALUES 
            ('". $name ."', '". $filename ."', '". $content ."', '". date("Y-m-d H:i:s") ."');");

        echo "<h3>页面创建成功。</h3><a href='../?page=". $filename ."' target='_blank'>查看页面</a><br/><br/>";
    }
} ?>
<div class="box_right_title"><?php echo $GamePage->titleLink(); ?> &raquo; 新建页面</div>
<form action="?page=pages&selected=new" method="post">
    名称 <br/>
    <input type="text" name="newpage_name"><br/>
    文件名 <i>(这就是 ?page=FILENAME 所指的内容。 例如。?page=connect 其中文件名是 'connect')<br/>
    <input type="text" name="newpage_filename"><br/>
    内容<br/>
    <textarea cols="77" rows="14" id="wysiwyg" name="newpage_content">
<?php 
if (isset($_POST['newpage_content']))
    {
        echo $_POST['newpage_content'];
    } ?></textarea>    <br/>
        <input type="submit" value="Create" name="newpage">