<?php

    global $GameServer, $GamePage, $GameAccount;
    $conn = $GameServer->connect();
    $GameServer->selectDB("webdb", $conn);

    $GamePage->validatePageAccess("Donations");

	if($GamePage->validateSubPage() == TRUE) 
	{
		$GamePage->outputSubPage();
	} 
	else 
	{
		$donationsTotal       = $Database->select("payments_log", "mc_gross");
		$donationsTotalAmount = 0;
		while ($row           = $donationsTotal->fetch_assoc())
		{
			$donationsTotalAmount = $donationsTotalAmount + $row['mc_gross'];
		}

		$donationsThisMonth       = $Database->select("payments_log", "mc_gross", "paymentdate LIKE '%". date('Y-md') ."%'");
		$donationsThisMonthAmount = 0;
		while ($row = $donationsThisMonth->fetch_assoc())
		{
			$donationsThisMonthAmount = $donationsThisMonthAmount + $row['mc_gross'];
		}

		$q                    = $Database->select("payments_log", "mc_gross, userid", null, null, "ORDER BY paymentdate DESC LIMIT 1");
        $row                  = $q->fetch_assoc();
		$donationLatestAmount = $row['mc_gross'];

		$donationLatest = $GameAccount->getAccName($row['userid']);
?>
<div class="box_right_title">捐赠概览</div>
<table style="width: 100%;">
	<tr>
		<td><span class='blue_text'>总捐款</span></td>
		<td><?php echo $donationsTotal->num_rows; ?></td>

		<td><span class='blue_text'>捐款总额</span></td>
		<td><?php echo round($donationsTotalAmount,0); ?>元</td>
	</tr>
	<tr>
	    <td><span class='blue_text'>本月捐款</span></td>
	    <td><?php echo $donationsThisMonth->num_rows; ?></td>

	    <td><span class='blue_text'>本月捐款金额</span></td>
	    <td><?php echo round($donationsThisMonthAmount,0); ?>元</td>
	</tr>
	<tr>
	    <td><span class='blue_text'>最新的捐赠金额</span></td>
	    <td><?php echo round($donationLatestAmount); ?>元</td>

	    <td><span class='blue_text'>最新的捐赠者</span></td>
	    <td><?php echo $donationLatest; ?></td>
	</tr>
</table>
<hr/>
<a href="?page=donations&selected=browse" class="content_hider">浏览捐款</a>
<?php } ?>