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
        $inShop     = $Database->select("shopitems", "COUNT(id) AS items");
        $purchToday = $Database->select("shoplog", "COUNT(id) AS purchases", null, "date LIKE '%". date('Y-m-d') ."%'");
        $getAvg     = $Database->select("shopitems", "AVG(price) AS priceAvg");
        $totalPurch = $Database->select("shoplog", "COUNT(id) AS purchasesTotal");

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
<a href="?page=shop&selected=add" class="content_hider">添加物品</a>
<a href="?page=shop&selected=manage" class="content_hider">管理物品</a>
<a href="?page=shop&selected=tools" class="content_hider">工具</a>
<?php } ?>