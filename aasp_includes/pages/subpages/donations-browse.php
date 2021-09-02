<?php 
  global $GamePage, $GameServer, $GameAccount; 
  $conn = $GameServer->connect();
  $GameServer->selectDB('webdb', $conn);
?>
<div class="box_right_title"><?php echo $GamePage->titleLink(); ?> &raquo; 浏览</div>
<?php 

$per_page = 20;

$pages_query = mysqli_query($conn, "SELECT COUNT(*) AS payments FROM payments_log;");
$pages       = ceil(mysqli_fetch_assoc($pages_query)['payments'] / $per_page);

if(mysqli_data_seek($pages_query,0) == 0) 
{
   echo "捐款日志好像是空的!";
} 
else 
{

$page   = (isset($_GET['page'])) ? mysqli_real_escape_string($conn, $_GET['page']) : 1;
$start  = ($page - 1) * $per_page;
?>
<table class="center">
   <tr>
   		<th>日期</th>
   		<th>用户</th>
   		<th>Email</th>
   		<th>金额</th>
   		<th>状态</th>
   	</tr>
   <?php
    $GameServer->selectDB('webdb', $conn);
	$result = mysqli_query($conn, "SELECT * FROM payments_log ORDER BY id DESC LIMIT ". $start .", ". $per_page .";");
	while($row = mysqli_fetch_assoc($result)) { ?>
		<tr>
	        <td><?php echo $row['datecreation']; ?></td>
	        <td><?php echo $GameAccount->getAccName($row['userid']); ?></td>
	        <td><?php echo $row['buyer_email']; ?></td>
	        <td><?php echo $row['mc_gross']; ?></td>
	        <td><?php echo $row['paymentstatus']; ?></td>
	    </tr>
	<?php } ?>
</table>
<hr/>
<?php
if($pages>=1 && $page <= $pages) 
{
	if($page>1)
	{
	   $prev = $page-1;
	   echo '<a href="?p=donations&s=browse&page='.$prev.'" title="Previous">上一页</a> &nbsp;';
	}
	for($x=1; $x<=$pages; $x++)
	{
		if($page == $x) 
		   echo '<a href="?p=donations&s=browse&page='.$x.'" title="Page '.$x.'"><b>'.$x.'</b></a> ';
		else   
		   echo '<a href="?p=donations&s=browse&page='.$x.'" title="Page '.$x.'">'.$x.'</a> ';
	}
	
	if($page<$x - 1)
	{
	   $next = $page+1;
	   echo '&nbsp; <a href="?p=donations&s=browse&page='.$next.'" title="Next">下一页</a> &nbsp; &nbsp;';
	}
}
}
?>
