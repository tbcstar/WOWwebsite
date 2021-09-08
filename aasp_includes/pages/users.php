<?php
    global $GameServer, $GamePage;
    $conn = $GameServer->connect();

    $GameServer->selectDB("webdb", $conn);

    $GamePage->validatePageAccess("Users");

    if ($GamePage->validateSubPage() == TRUE)
    {
		$Page->outputSubPage();
	} 
	else 
	{
        $GameServer->selectDB("logondb", $conn);
        $usersTotal       = $conn->query("SELECT COUNT(*) AS totalUsers FROM account;");
        $usersToday       = $conn->query("SELECT COUNT(*) AS dailyUsers FROM account WHERE joindate LIKE '%". date("Y-m-d") ."%';");
        $usersMonth       = $conn->query("SELECT COUNT(*) AS monthlyUsers FROM account WHERE joindate LIKE '%". date("Y-m") ."%';");
        $usersOnline      = $conn->query("SELECT COUNT(*) AS onlineUsers FROM account WHERE online=1;");
        $usersActive      = $conn->query("SELECT COUNT(*) AS activeUsers FROM account WHERE last_login LIKE '%". date("Y-m") ."%';");
        $usersActiveToday = $conn->query("SELECT COUNT(*) AS activeUsersToday FROM account WHERE last_login LIKE '%". date("Y-m-d") ."%';"); 
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