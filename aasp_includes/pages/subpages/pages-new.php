<?php $page = new page;
if(isset($_POST['newpage'])) {
	
	$name = mysql_real_escape_string($_POST['newpage_name']);
	$filename = trim(strtolower(mysql_real_escape_string($_POST['newpage_filename'])));
	$content = mysql_real_escape_string(htmlentities($_POST['newpage_content']));
	
	if(empty($name) || empty($filename) || empty($content)) {
		echo "<h3>Please enter <u>all</u> fields.</h3>";
	} else {
		mysql_query("INSERT INTO custom_pages VALUES ('','".$name."','".$filename."','".$content."',
		'".date("Y-m-d H:i:s")."')");

		echo "<h3>页面已成功创建。</h3> 
		<a href='".$GLOBALS['website_domain']."?p=".$filename."' target='_blank'>View Page</a><br/><br/>";
	}
} ?>
<div class="box_right_title"><?php echo $page->titleLink(); ?> &raquo; 新建页面</div>
<form action="?p=pages&s=new" method="post">
名称 <br/>
<input type="text" name="newpage_name"><br/>
文件名 <i>(This is what the ?p=FILENAME will refer to. Eg. ?p=connect where Filename is 'connect')<br/>
<input type="text" name="newpage_filename"><br/>
内容<br/>
<textarea cols="77" rows="14" id="wysiwyg" name="newpage_content">
<?php if(isset($_POST['newpage_content'])) { echo $_POST['newpage_content']; } ?></textarea>    <br/>
<input type="submit" value="创建" name="newpage">