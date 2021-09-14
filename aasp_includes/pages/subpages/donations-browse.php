<?php
	global $GamePage, $GameServer, $GameAccount; 
	$conn = $GameServer->connect();
	$GameServer->selectDB("webdb", $conn);
?>
    <div class="box_right_title"><?php echo $GamePage->titleLink(); ?> &raquo; 浏览</div>
<?php 
    $per_page = 20;

    $pages_query = $conn->query("SELECT COUNT(*) AS payments FROM payments_log;");
    $pages       = ceil($pages_query->fetch_assoc()['payments'] / $per_page);

	if ( $pages_query->fetch_assoc()['payments'] == 0 )
	{
		echo "似乎捐款日志是空的!";
	}
	else
	{
		$page   = (isset($_GET['page'])) ? $conn->escape_string($_GET['page']) : 1;
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
		$GameServer->selectDB("webdb", $conn);
		$countDonators = 0;
		$result = $conn->query("SELECT * FROM payments_log ORDER BY id DESC LIMIT ". $start .", ". $per_page .";");
		while ( $row = $result->fetch_assoc() )
		{?>
			<tr>
			<td><?php echo $row['datecreation']; ?></td>
			<td><?php echo $GameAccount->getAccName($row['userid']); ?></td>
			<td><?php echo $row['buyer_email']; ?></td>
			<td><?php echo $row['mc_gross']; ?>$</td>
			<td><?php echo $row['paymentstatus']; ?></td>
			</tr>
		<?php 
			$countDonators++; 
		} ?>
		</table>
		<hr/>

		<?php
		if ( $pages >= 1 && $page <= $pages )
		{
			if ( $page > 1 )
			{
				$prev = $page - 1;
				echo "<a href='?page=donations&selected=browse&log_page=". $prev ."' title='Previous'>Previous</a> &nbsp;";
			}
			for ( $x = 1; $x <= $pages; $x++ )
			{
				if ( $page == $x && $countDonators > 19 )
				{
					echo "<a href='?page=donations&selected=browse&log_page=". $x ."' title='Page ". $x ."'><b>". $x ."</b></a> ";
				}
				elseif ( $countDonators > 19 )
				{
					echo "<a href='?page=donations&selected=browse&log_page=". $x ."' title='Page ". $x ."'>". $x ."</a> ";
				}
			}
			if ( $page < $x - 1 )
			{
				$next = $page + 1;
				echo "&nbsp; <a href='?page=donations&selected=browse&log_page=". $next ."' title='Next'>下一页</a> &nbsp; &nbsp;";
			}
		}
	}
