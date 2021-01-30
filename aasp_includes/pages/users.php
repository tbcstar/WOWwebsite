<?php
 	 $server->selectDB('webdb'); 
 	 $page = new page;
	 
	 $page->validatePageAccess('Users');
	 
     if($page->validateSubPage() == TRUE) {
		 $page->outputSubPage();
	 } else {
		 $server->selectDB('logondb');
		 $usersTotal = mysql_query("SELECT COUNT(*) FROM account");
		 $usersToday = mysql_query("SELECT COUNT(*) FROM account WHERE joindate LIKE '%".date("Y-m-d")."%'");
		 $usersMonth = mysql_query("SELECT COUNT(*) FROM account WHERE joindate LIKE '%".date("Y-m")."%'");
		 $usersOnline = mysql_query("SELECT COUNT(*) FROM account WHERE online=1");
		 $usersActive = mysql_query("SELECT COUNT(*) FROM account WHERE last_login LIKE '%".date("Y-m")."%'");
		 $usersActiveToday = mysql_query("SELECT COUNT(*) FROM account WHERE last_login LIKE '%".date("Y-m-d")."%'");	 
?>
<div class="box_right_title">用户概述</div>
<table style="width: 100%;">
<tr>
<td><span class='blue_text'>总用户</span></td><td><?php echo round(mysql_result($usersTotal,0)); ?></td>
<td><span class='blue_text'>今天新增用户</span></td><td><?php echo round(mysql_result($usersToday,0)); ?></td>
</tr>
<tr>
    <td><span class='blue_text'>本月新增用户</span></td><td><?php echo round(mysql_result($usersMonth,0)); ?></td>
    <td><span class='blue_text'>在线用户</span></td><td><?php echo round(mysql_result($usersOnline,0)); ?></td>
</tr>
<tr>
    <td><span class='blue_text'>活跃用户(本月)</span></td><td><?php echo round(mysql_result($usersActive,0)); ?></td>
    <td><span class='blue_text'>今天登录的用户</span></td><td><?php echo round(mysql_result($usersActiveToday,0)); ?></td>
</tr>
</table>
<hr/>
<a href="?p=users&s=manage" class="content_hider">用户管理</a>
<?php } ?>