<?php
	global $Server, $Page, $conn;
 	$Server->selectDB('webdb');

	$Page->validatePageAccess('Users');

    if($Page->validateSubPage() == TRUE) 
    {
		$Page->outputSubPage();
	} 
	else 
	{
		$Server->selectDB('logondb');
		$usersTotal 	= mysqli_query($conn, "SELECT COUNT(*) FROM account;");
		$usersToday 	= mysqli_query($conn, "SELECT COUNT(*) FROM account WHERE joindate LIKE '%".date("Y-m-d")."%';");
		$usersMonth 	= mysqli_query($conn, "SELECT COUNT(*) FROM account WHERE joindate LIKE '%".date("Y-m")."%';");
		$usersOnline 	= mysqli_query($conn, "SELECT COUNT(*) FROM account WHERE online=1;");
		$usersActive 	= mysqli_query($conn, "SELECT COUNT(*) FROM account WHERE last_login LIKE '%".date("Y-m")."%';");
		$usersActiveToday = mysqli_query($conn, "SELECT COUNT(*) FROM account WHERE last_login LIKE '%".date("Y-m-d")."%';");	 
?>
<div class="box_right_title">用户概述</div>
<table style="width: 100%;">
<tr>
<td><span class='blue_text'>总用户数</span></td>
<td><?php echo round(mysqli_data_seek($usersTotal,0)); ?></td>
<td><span class='blue_text'>今日新增用户</span></td>
<td><?php echo round(mysqli_data_seek($usersToday,0)); ?></td>
</tr>
<tr>
    <td><span class='blue_text'>本月新用户</span></td>
    <td><?php echo round(mysqli_data_seek($usersMonth,0)); ?></td>
    <td><span class='blue_text'>在线用户</span></td>
    <td><?php echo round(mysqli_data_seek($usersOnline,0)); ?></td>
</tr>
<tr>
    <td><span class='blue_text'>活跃用户(本月)</span></td>
    <td><?php echo round(mysqli_data_seek($usersActive,0)); ?></td>
    <td><span class='blue_text'>今日登陆用户</span></td>
    <td><?php echo round(mysqli_data_seek($usersActiveToday,0)); ?></td>
</tr>
</table>
<hr/>
<a href="?p=users&s=manage" class="content_hider">用户管理</a>
<?php } ?>