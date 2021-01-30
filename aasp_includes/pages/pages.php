<?php
	$server->selectDB('webdb');
	$page = new page;
	
	$page->validatePageAccess('Pages');
	
    if($page->validateSubPage() == TRUE) {
		$page->outputSubPage();
	} else {
 ?>

<div class="box_right_title">页面</div>

<?php if(!isset($_GET['action'])) { ?>

<table class="center">
<tr>
		<th>名称</th><th>文件名</th><th>动作</th>
</tr>
<?php
	$result = mysql_query("SELECT * FROM custom_pages ORDER BY id ASC");
	while($row = mysql_fetch_assoc($result)) { 
     $check = mysql_query("SELECT COUNT(filename) FROM disabled_pages WHERE filename='".$row['filename']."'");
	 if(mysql_result($check,0)==0) {
		 $disabled = false;
	 } else {
		 $disabled = true;
	 }
    ?>
	<tr <?php if($disabled==true) { echo "style='color: #999;'"; }?>>
         <td width="50"><?php echo $row['name']; ?></td>
         <td width="100"><?php echo $row['filename']; ?>(Database)</td>
         <td><select id="action-<?php echo $row['filename']; ?>"><?php if($disabled==true) {  ?>
             <option value="1">启用</option>
		 <?php } else { ?>
			 <option value="2">禁用</option>
		 <?php } ?>
         <option value="3">编辑</option>
         <option value="4">移除</option>
         </select> &nbsp;<input type="submit" value="保存" onclick="savePage('<?php echo $row['filename']; ?>')"></td>
    </tr>
<?php }

foreach ($GLOBALS['core_pages'] as $k => $v) { 
$filename = substr($v, 0, -4);
unset ($check);
$check = mysql_query("SELECT COUNT(filename) FROM disabled_pages WHERE filename='".$filename."'");
	 if(mysql_result($check,0)==0) {
		 $disabled = false;
	 } else {
		 $disabled = true;
	 }
?>

    <tr <?php if($disabled==true) { echo "style='color: #999;'"; }?>>
        <td><?php echo $k; ?></td>
        <td><?php echo $v; ?></td>
        <td><select id="action-<?php echo $filename; ?>">
             <?php if($disabled==true) { ?>
             <option value="1">启用</option>
		 <?php } else { ?>
			 <option value="2">禁用</option>
		 <?php } ?>
        </select> &nbsp;<input type="submit" value="保存" onclick="savePage('<?php echo $filename; ?>')"></td>
    </tr>
<?php } ?>

</table>

<?php } elseif($_GET['action']=='new') {
	 
 ?>


<?php } elseif($_GET['action']=='edit') {
	
	if(isset($_POST['editpage'])) {
		
		$name = mysql_real_escape_string($_POST['editpage_name']);
		$filename = trim(strtolower(mysql_real_escape_string($_POST['editpage_filename'])));
		$content = mysql_real_escape_string(htmlentities($_POST['editpage_content']));
		
	if(empty($name) || empty($filename) || empty($content)) {
		echo "<h3>请输入 <u>所有</u> 字段。</h3>";
	} else {
		mysql_query("UPDATE custom_pages SET name='".$name."',filename='".$filename."',
		content='".$content."' WHERE filename='".mysql_real_escape_string($_GET['filename'])."'");

		echo "<h3>页面更新成功。</h3> 
		<a href='".$GLOBALS['website_domain']."?p=".$filename."' target='_blank'>查看页面</a>";
	}
	}
	
$result = mysql_query("SELECT * FROM custom_pages WHERE filename='".mysql_real_escape_string($_GET['filename'])."'"); 
$row = mysql_fetch_assoc($result);
?>
	   
     <h4>Editing <?php echo $_GET['filename']; ?>.php</h4>
    <form action="?p=pages&action=edit&filename=<?php echo $_GET['filename']; ?>" method="post">
	Name<br/>
    <input type="text" name="editpage_name" value="<?php echo $row['name']; ?>"><br/>
    Filename<br/>
    <input type="text" name="editpage_filename" value="<?php echo $row['filename']; ?>"><br/>
    Content<br/>
    <textarea cols="77" rows="14" id="wysiwyg" name="editpage_content"><?php echo $row['content']; ?></textarea>    
    <br/>
    <input type="submit" value="保存" name="editpage">
    
<?php } } ?>