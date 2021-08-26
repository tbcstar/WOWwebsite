<?php 
    global $GamePage, $GameServer;

	$GamePage->validatePageAccess('Shop');

    if($GamePage->validateSubPage() == TRUE) 
    {
		$GamePage->outputSubPage();
	} 
	else 
    {   
        $conn = $GameServer->connect();
        $GameServer->selectDB('webdb', $conn);
        $inShop     = mysqli_query($conn, "SELECT COUNT(*) AS items FROM shopitems;");
        $purchToday = mysqli_query($conn, "SELECT COUNT(*) AS purchases FROM shoplog WHERE date LIKE '%" . date('Y-m-d') . "%';");
		$getAvg 	= mysqli_query($conn, "SELECT AVG(*) AS priceAvg FROM shopitems;");
        $totalPurch = mysqli_query($conn, "SELECT COUNT(*) AS purchasesTotal FROM shoplog;");

		//注意:如果没有设置值，round()函数将返回0:)
?>
<div class="box_right_title">商城概述</div>
<table style="width: 100%;">
	<tr>
        <td><span class='blue_text'>商城物品</span></td><td><?php echo round(mysqli_fetch_assoc($inShop)['items']); ?></td>
	</tr>
	<tr>
	    <td><span class='blue_text'>今日购买</span></td>
        <td><?php echo round(mysqli_fetch_assoc($purchToday)['purchases']); ?></td>
	    <td><span class='blue_text'>购买总额</span></td>
        <td><?php echo round(mysqli_fetch_assoc($totalPurch)['purchasesTotal']); ?></td>
	</tr>
	<tr>
	    <td><span class='blue_text'>平均物品价格</span></td>
        <td><?php echo round(mysqli_fetch_assoc($getAvg)['priceAvg']); ?></td>
	</tr>
</table>
<hr/>
<a href="?p=shop&s=add" class="content_hider">添加物品</a>
<a href="?p=shop&s=manage" class="content_hider">管理物品</a>
<a href="?p=shop&s=tools" class="content_hider">工具</a>
<?php } ?>