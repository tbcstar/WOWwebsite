<?php

global $Connect;
$conn = $Connect->connectToDB();
$Connect->selectDB('webdb', $conn);
if (!isset($_SESSION['cw_user']))
{
	$sql = "WHERE shownWhen = 'always' OR shownWhen = 'notlogged'"; 
}
else
{
	$sql = "WHERE shownWhen = 'always' OR shownWhen = 'logged'";
}
$getMenuLinks = mysqli_query($conn, "SELECT * FROM site_links ". $sql ." ORDER BY position ASC;");
if (mysqli_num_rows($getMenuLinks) == 0) 
{
	buildError("<b>模板错误:</b> 在Web数据库中没有发现菜单链接!", NULL);
	echo "<br/>没有找到菜单链接!";
}

while($row = mysqli_fetch_assoc($getMenuLinks)) 
{
	$curr = substr($row['url'],3);
	if ($_GET['p'] == $curr)
	{
		echo '<a href="'.$row['url'].'" class="current">'.$row['title'].'</a>';
	}
	else
	{
 		echo '<a href="'.$row['url'].'">'.$row['title'].'</a>';
	}
}
?>