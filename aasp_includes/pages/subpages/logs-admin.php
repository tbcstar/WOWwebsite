<?php 
    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();
    $GameServer->selectDB('webdb', $conn);

$per_page = 20;

$pages_query = $conn->query("SELECT COUNT(*) AS logs FROM admin_log;");
$pages       = ceil($pages_query->fetch_assoc()['logs'] / $per_page);

$page  = ( isset($_GET['page']) ) ? $conn->escape_string($_GET['page']) : 1;
$start = ($page - 1) * $per_page;

if(isset($_SESSION['cw_staff']) && !isset($_SESSION['cw_admin']))
{
	if($_SESSION['cw_staff_level'] < $GLOBALS['adminPanel_minlvl'])
	{
		exit("嘿!你不应该在这里!");
	}
}
?>
<div class="box_right_title">管理日志</div>
<table class="center">
   <tr>
   	<th>日期</th>
       <th>用户</th>
       <th>动作</th>
       <th>IP</th>
   </tr>
   <?php
    $GameServer->selectDB('webdb', $conn);
    $result = $conn->query("SELECT * FROM admin_log ORDER BY id DESC LIMIT ". $start .", ". $per_page .";");
    while ($row    = $result->fetch_assoc())
    { ?>
		<tr>
            <td><?php echo date("Y-m-d H:i:s",$row['timestamp']); ?></td>
            <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
            <td><?php echo $row['action']; ?></td>
            <td><?php echo $row['ip']; ?></td>
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
		   echo "<a href='?p=logs&s=admin&page=". $prev ."' title='Previous'>上一页</a> &nbsp;";
		}
		for($x = 1; $x <= $pages; $x++)
		{
			if($page == $x)
			{
			   echo "<a href='?p=logs&s=admin&page=". $x ."' title='Page ". $x ."'><b>". $x ."</b></a> ";
			}
			else
			{
			   echo '<a href="?p=logs&s=admin&page='.$x.'" title="Page '.$x.'">'.$x.'</a> ';
			}
		}

		if($page < $x - 1)
		{
		   $next = $page+1;
		   echo "&nbsp; <a href='?p=logs&s=admin&page=". $next ."' title='Next'>下一页</a> &nbsp; &nbsp;";
		}
	} 
