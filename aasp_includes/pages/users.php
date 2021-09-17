<?php
    global $GameServer, $GamePage;
    $conn = $GameServer->connect();

    $GameServer->selectDB("webdb");

    $GamePage->validatePageAccess("Users");

    if ($GamePage->validateSubPage() == TRUE)
    {
		$Page->outputSubPage();
	} 
	else 
	{
        $GameServer->selectDB("logondb");
        $usersTotal       = $Database->select("account", "COUNT(*) AS totalUsers");
        $usersToday       = $Database->select("account", "COUNT(*) AS dailyUsers", null, "joindate LIKE '%". date("Y-m-d") ."%'");
        $usersMonth       = $Database->select("account", "COUNT(*) AS monthlyUsers", null, "joindate LIKE '%". date("Y-m") ."%'");
        $usersOnline      = $Database->select("account", "COUNT(*) AS onlineUsers", null, "online=1");
        $usersActive      = $Database->select("account", "COUNT(*) AS activeUsers", null, "last_login LIKE '%". date("Y-m") ."%'");
        $usersActiveToday = $Database->select("account", "COUNT(*) AS activeUsersToday", null, "last_login LIKE '%". date("Y-m-d") ."%'");
?>
<div class="box_right_title">用户概述</div>
<table style="width: 100%;">
<tr>
    <td><span class='blue_text'>总用户数</span></td>
    <td><?php echo round($usersTotal->fetch_assoc()['totalUsers']); ?></td>
    
    <td><span class='blue_text'>今日新增用户</span></td>
    <td><?php echo round($usersToday->fetch_assoc()['dailyUsers']); ?></td>
</tr>
<tr>
    <td><span class='blue_text'>本月新用户</span></td>
    <td><?php echo round($usersMonth->fetch_assoc()['monthlyUsers']); ?></td>

    <td><span class='blue_text'>在线用户</span></td>
    <td><?php echo round($usersOnline->fetch_assoc()['onlineUsers']); ?></td>
</tr>
<tr>
    <td><span class='blue_text'>活跃用户(本月)</span></td>
    <td><?php echo round($usersActive->fetch_assoc()['activeUsers']); ?></td>

    <td><span class='blue_text'>今日登陆用户</span></td>
    <td><?php echo round($usersActiveToday->fetch_assoc()['activeUsersToday']); ?></td>
</tr>
</table>
<hr/>
<a href="?page=users&selected=manage" class="content_hider">用户管理</a>
<?php } ?>