<?php global $Page, $Server, $Account, $conn;
?>
	 
<div class="box_right_title">投票商店日志</div>
<?php 

$per_page = 40;

$pages_query 	= mysqli_query($conn, "SELECT COUNT(*) FROM shoplog WHERE shop='vote'");
$pages 			= ceil(mysqli_data_seek($pages_query,0) / $per_page );

$page 	= ( isset($_GET['page']) ) ? (int)$_GET['page'] : 1;
$start 	= ($page - 1) * $per_page;

$result = mysqli_query($conn, "SELECT * FROM shoplog WHERE shop='vote' ORDER BY id DESC LIMIT ".$start.",".$per_page.";"); 
if(mysqli_num_rows($result) == 0) 
{
	echo "看来投票商店的日志是空的!";
} 
else 
{
?>
 <input type='text' value='Search...' id="logs_search" onkeyup="searchLog('vote')">
 <?php echo "<br/><b>Showing ". $start ."-". ($start + $per_page) ."</b>"; ?>
 <hr/>
<div id="logs_content">
<table width="100%">
        <tr>
        	<th>用户</th>
        	<th>角色</th>
        	<th>服务器</th>
        	<th>物品</th>
        	<th>日期</th>
        </tr>
        <?php 
        while($row = mysqli_fetch_assoc($result)) 
    	{ ?>
			<tr class="center">
	            <td><?php echo $Account->getAccName($row['account']); ?></td>
	            <td><?php echo $Account->getCharName($row['char_id'], $row['realm_id']); ?></td>
	            <td><?php echo $Server->getRealmName($row['realm_id']); ?></td>
	            <td><a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
				<?php echo $Server->getItemName($row['entry']); ?></a></td>
	            <td><?php echo $row['date']; ?></td>
	        </tr>
		<?php } ?>
</table>
<hr/>

<?php
if($pages >= 1 && $page <= $pages) 
{
	if($page > 1)
	{
	   $prev = $page - 1;
	   echo '<a href="?p=logs&s=voteshop&page='.$prev.'" title="Previous">上一页</a> &nbsp;';
	}
	for($x = 1; $x <= $pages; $x++)
	{
		if($page == $x)
		{
		   echo '<a href="?p=logs&s=voteshop&page='.$x.'" title="Page '.$x.'"><b>'.$x.'</b></a> ';
		}
		else
		{
		   echo '<a href="?p=logs&s=voteshop&page='.$x.'" title="Page '.$x.'">'.$x.'</a> ';
		}
	}
	
	if($page < $x - 1)
	{
	   $next = $page+1;
	   echo '&nbsp; <a href="?p=logs&s=voteshop&page='.$next.'" title="Next">下一页</a> &nbsp; &nbsp;';
	}
}
?>
</div>
<?php } ?>