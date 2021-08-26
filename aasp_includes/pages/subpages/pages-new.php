<?php
    global $GamePage, $GameServer;
    $conn = $GameServer->connect();
    if(isset($_POST['newpage'])) {
	
	$name 		= mysqli_real_escape_string($conn, $_POST['newpage_name']);
	$filename 	= mysqli_real_escape_string($conn, trim(strtolower($_POST['newpage_filename'])));
	$content 	= mysqli_real_escape_string($conn, htmlentities($_POST['newpage_content']));
	
	if(empty($name) || empty($filename) || empty($content)) {
		echo "<h3>请输入 <u>所有</u> 字段。</h3>";
	}
	else
	{
        mysqli_query($conn, "INSERT INTO custom_pages VALUES ('','" . $name . "','" . $filename . "','" . $content . "', '" . date("Y-m-d H:i:s") . "')");

        echo "<h3>页面创建成功。</h3><a href='" . $GLOBALS['website_domain'] . "?p=" . $filename . "' target='_blank'>查看页面</a><br/><br/>";
    }
} ?>
<div class="box_right_title"><?php echo $GamePage->titleLink(); ?> &raquo; 新建页面</div>
<form action="?p=pages&s=new" method="post">
    名称 <br/>
    <input type="text" name="newpage_name"><br/>
    文件名 <i>(这就是 ?p=FILENAME 所指的内容。 例如。?p=connect 其中文件名是 'connect')<br/>
    <input type="text" name="newpage_filename"><br/>
    内容<br/>
    <textarea cols="77" rows="14" id="wysiwyg" name="newpage_content">
<?php 
if (isset($_POST['newpage_content']))
    {
        echo $_POST['newpage_content'];
    } ?></textarea>    <br/>
        <input type="submit" value="Create" name="newpage">