<?php global $Page, $Server, $Account, $conn; ?> 
<div class="box_right_title">公益商城日志</div>
<?php $result = mysqli_query($conn, "SELECT * FROM shoplog WHERE shop='donate' ORDER BY id DESC LIMIT 10;"); 
if(mysqli_num_rows($result) == 0) 
{
	echo "看来公益商城的日志是空的!";
} 
else 
{?>
  <input type='text' value='Search...' id="logs_search" onkeyup="searchLog('donate')"><hr/>
  <div id="logs_content">
    <table width="100%">
      <tr>
        <th>用户</th>
        <th>角色</th>
        <th>服务器</th>
        <th>物品</th>
        <th>日期</th>
      </tr>
      <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr class="center">
            <td><?php echo $Account->getAccName($row['account']); ?></td>
            <td><?php echo $Account->getCharName($row['char_id'],$row['realm_id']); ?></td>
            <td><?php echo $Server->getRealmName($row['realm_id']); ?></td>
            <td><a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
    	       <?php echo $Server->getItemName($row['entry']); ?></a></td>
            <td><?php echo $row['date']; ?></td>
        </tr>	
  		<?php } ?>
    </table>
  </div>
<?php } ?>