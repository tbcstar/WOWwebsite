<?php 
  global $GamePage, $GameServer;
  $conn = $GameServer->connect();
  $GameServer->selectDB('webdb', $conn);
?>
<div class="box_right_title">服务器管理</div>
<table class="center">
<tr>
    <th>ID</th>
    <th>名字</th>
    <th>主机</th>
    <th>端口</th>
    <th>角色数据库</th>
    <th>动作</th>
</tr>
<?php
	$result = mysqli_query($conn, "SELECT * FROM realms ORDER BY id DESC;");
	while($row = mysqli_fetch_assoc($result)) { ?>
		<tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['host']; ?></td>
            <td><?php echo $row['port']; ?></td>
            <td><?php echo $row['char_db']; ?></td>
            <td>
                <a href="#" onclick="edit_realm(
                    <?php echo $row['id']; ?>, 
                    '<?php echo $row['name']; ?>', 
                    '<?php echo $row['host']; ?>',
                    '<?php echo $row['port']; ?>',
                    '<?php echo $row['char_db']; ?>')">
                    编辑
                </a> &nbsp; 
                <a href="#" onclick="delete_realm(
                    <?php echo $row['id']; ?>, 
                    '<?php echo $row['name']; ?>')">
                    删除
                </a>
                <br/>
                <a href="#" onclick="edit_console(
                    <?php echo $row['id']; ?>, 
                    '<?php echo $row['sendType']; ?>', 
                    '<?php echo $row['rank_user']; ?>',
                    '<?php echo $row['rank_pass']; ?>')">
                    编辑控制台设置</a>
              </td>
          </tr>
	<?php }
?>
</table>