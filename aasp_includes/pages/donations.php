<?php 
	 $server->selectDB('webdb'); 
 	 $page = new page;
	 
	 $page->validatePageAccess('Donations');
	 
     if($page->validateSubPage() == TRUE) {
		 $page->outputSubPage();
	 } else {
		$donationsTotal = mysql_query("SELECT mc_gross FROM payments_log");
		$donationsTotalAmount = 0;
		while($row = mysql_fetch_assoc($donationsTotal)) 
		{
			$donationsTotalAmount = $donationsTotalAmount + $row['mc_gross'];
		}
		
		$donationsThisMonth = mysql_query("SELECT mc_gross FROM payments_log WHERE paymentdate LIKE '%".date('Y-md')."%'");
		$donationsThisMonthAmount = 0;
		while($row = mysql_fetch_assoc($donationsThisMonth)) 
		{
			$donationsThisMonthAmount = $donationsThisMonthAmount + $row['mc_gross'];
		}
		
		$q = mysql_query("SELECT mc_gross,userid FROM payments_log ORDER BY paymentdate DESC LIMIT 1");
		$row = mysql_fetch_assoc($q);
		$donationLatestAmount = $row['mc_gross'];
		
		$donationLatest = $account->getAccName($row['userid']);
?>
<div class="box_right_title">捐赠概览</div>
<table style="width: 100%;">
<tr>
<td><span class='blue_text'>总捐款</span></td><td><?php echo mysql_num_rows($donationsTotal); ?></td>
<td><span class='blue_text'>捐款总额</span></td><td><?php echo round($donationsTotalAmount,0); ?>$</td>
</tr>
<tr>
    <td><span class='blue_text'>本月捐款</span></td><td><?php echo mysql_num_rows($donationsThisMonth); ?></td>
    <td><span class='blue_text'>本月捐款金额</span></td><td><?php echo round($donationsThisMonthAmount,0); ?>$</td>
</tr>
<tr>
    <td><span class='blue_text'>最新的捐赠金额</span></td><td><?php echo round($donationLatestAmount); ?>$</td>
    <td><span class='blue_text'>最新的捐赠者</span></td><td><?php echo $donationLatest; ?></td>
</tr>
</table>
<hr/>
<a href="?p=donations&s=browse" class="content_hider">浏览捐款</a>
<?php } ?>