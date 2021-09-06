<?php 
    global $GamePage, $GameServer;

	$GamePage->validatePageAccess("Shop");

    if($GamePage->validateSubPage() == TRUE) 
    {
		$GamePage->outputSubPage();
	} 
	else 
    {   
        $conn = $GameServer->connect();
        $GameServer->selectDB("webdb", $conn);
        $inShop     = $conn->query("SELECT COUNT(id) AS items FROM shopitems;");
        $purchToday = $conn->query("SELECT COUNT(id) AS purchases FROM shoplog WHERE date LIKE '%". date('Y-m-d') ."%';");
        $getAvg     = $conn->query("SELECT AVG(price) AS priceAvg FROM shopitems;");
        $totalPurch = $conn->query("SELECT COUNT(id) AS purchasesTotal FROM shoplog;");



        //Note: The round() function will return 0 if no value is set :)
?>
<div class="box_right_title">商城概述</div>
<table style="width: 100%;">
	<tr>
        <td><span class='blue_text'>商城中的商品</span></td>
        <td><?php echo round($inShop->fetch_assoc()['items']); ?></td>

        <td><span class='blue_text'>平均物品成本</span></td>
        <td><?php echo round($getAvg->fetch_assoc()['priceAvg']); ?> Vote Points</td>
	</tr>
	<tr>
	    <td><span class='blue_text'>今日购买</span></td>
        <td><?php echo round($purchToday->fetch_assoc()['purchases']); ?></td>
	    <td><span class='blue_text'>购买总额</span></td>
        <td><?php echo round($totalPurch->fetch_assoc()['purchasesTotal']); ?></td>
	</tr>
</table>
<hr/>
<a href="?p=shop&s=add" class="content_hider">添加物品</a>
<a href="?p=shop&s=manage" class="content_hider">管理物品</a>
<a href="?p=shop&s=tools" class="content_hider">工具</a>
<?php } ?>