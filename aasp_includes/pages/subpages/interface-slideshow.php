<?php 
  global $GamePage, $GameServer;
  $conn = $GameServer->connect();
  $GameServer->selectDB('webdb', $conn);
?>
<div class="box_right_title"><?php echo $GamePage->titleLink(); ?> &raquo; 幻灯片</div>
<?php 
if($GLOBALS['enableSlideShow'] == true)
{
	$status = '启用';
}
else
{
	$status = '禁用';
}

$GameServer->selectDB('webdb', $conn);
$count = mysqli_query($conn, "SELECT COUNT(*) AS sliderImages FROM slider_images;");
?>
幻灯片是 <b><?php echo $status; ?></b>。 你有<b><?php echo round(mysqli_fetch_assoc($count)['sliderImages']); ?></b>幻灯片中的图像。
<hr/>
<?php 
if(isset($_POST['addSlideImage']))
{
	$GamePage->addSlideImage($_FILES['slideImage_upload'], $_POST['slideImage_path'], $_POST['slideImage_url']);
}
?>
<a href="#addimage" onclick="addSlideImage()" class="content_hider">添加图片</a>
<div class="hidden_content" id="addSlideImage">
<form action="" method="post" enctype="multipart/form-data">
上传图片：<br/>
<input type="file" name="slideImage_upload"><br/>
或输入图片网址：（这将替换您上传的图片）<br/>
<input type="text" name="slideImage_path"><br/>
图片会重定向到哪里？（留空则不重定向）<br/>
<input type="text" name="slideImage_url"><br/>
<input type="submit" value="Add" name="addSlideImage">
</form>
</div>
<br/>&nbsp;<br/>
<?php 
$GameServer->selectDB('webdb', $conn);
$result = mysqli_query($conn, "SELECT * FROM slider_images ORDER BY position ASC");
if(mysqli_num_rows($result) == 0) 
{
	echo "幻灯片中没有图片！";
}
else 
{
	echo "<table>";
	$c = 1;
	while($row = mysqli_fetch_assoc($result))
	{
		echo "<tr class='center'>";
        echo "<td><h2>&nbsp; ". $c ." &nbsp;</h2><br/><a href='#remove' onclick='removeSlideImage(". $row['position'] .")'>移除</a></td>";
        echo "<td><img src='../". $row['path'] ."' alt='". $c ."' class='slide_image' maxheight='200'/></td>";
        echo "</tr>";
		$c++;
	}
	  echo "</table>";
}
?>

