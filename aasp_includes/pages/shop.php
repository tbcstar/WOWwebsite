<?php 
     $page = new page;
	 
	 $page->validatePageAccess('Shop');
	 
     if($page->validateSubPage() == TRUE) {
		 $page->outputSubPage();
	 } else {
		 $server->selectDB('webdb');
		 $inShop = mysql_query("SELECT COUNT(*) FROM shopitems");
		 $purchToday = mysql_query("SELECT COUNT(*) FROM shoplog WHERE date LIKE '%".date('Y-m-d')."%'");
		 $getAvg = mysql_query("SELECT AVG(*) AS priceAvg FROM shopitems");
		 $totalPurch = mysql_query("SELECT COUNT(*) FROM shoplog");
		 
		 //注意:如果没有设置值，round()函数将返回0:)
?>
<div class="box_right_title">商城概述</div>
<table style="width: 100%;">
<tr>
<td><span class='blue_text'>商城物品</span></td><td><?php echo round(mysql_result($inShop,0));?></td>
</tr>
<tr>
    <td><span class='blue_text'>今日购买</span></td><td><?php echo round(mysql_result($purchToday,0)); ?></td>
    <td><span class='blue_text'>购买总额</span></td><td><?php echo round(mysql_result($totalPurch,0)); ?></td>
</tr>
<tr>
    <td><span class='blue_text'>平均物品价格</span></td><td><?php echo round(mysql_result($getAvg,0)); ?></td>
</tr>
</table>
<hr/>
<a href="?p=shop&s=add" class="content_hider">添加物品</a>
<a href="?p=shop&s=manage" class="content_hider">管理物品</a>
<a href="?p=shop&s=tools" class="content_hider">工具</a>
<?php } ?>