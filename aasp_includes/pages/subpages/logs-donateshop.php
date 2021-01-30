<?php $page = new page; 
$server = new server;
$account = new account;
?> 
<div class="box_right_title">公益商店日志</div>
<?php $result = mysql_query("SELECT * FROM shoplog WHERE shop='donate' ORDER BY id DESC LIMIT 10 "); 
if(mysql_num_rows($result)==0) {
	echo "Seems like the donation shop log was empty!";
} else {
?>
 <input type='text' value='Search...' id="logs_search" onkeyup="searchLog('donate')"><hr/>
<div id="logs_content">
<table width="100%">
        <tr><th>用户</th><th>角色</th><th>服务器</th><th>物品</th><th>日期</th></tr>
        <?php while($row = mysql_fetch_assoc($result)) { ?>
		<tr class="center">
            <td><?php echo $account->getAccName($row['account']); ?></td>
            <td><?php echo $account->getCharName($row['char_id'],$row['realm_id']); ?></td>
            <td><?php echo $server->getRealmName($row['realm_id']); ?></td>
            <td><a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
			<?php echo $server->getItemName($row['entry']); ?></a></td>
            <td><?php echo $row['date']; ?></td>
        </tr>	
		<?php } ?>
</table>
</div>
<?php } ?>