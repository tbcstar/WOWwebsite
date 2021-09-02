<?php
function setError($haystack)
{
    return strpos($haystack, "Error") !== false;
}

    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();
?>
<div class="box_right_title">控制面板</div>
<table style="width: 605px;">
<tr>
    <td><span class='blue_text'>在线账号</span></td>
    <td><?php echo $GameServer->getActiveConnections(); ?></td>

    <td><span class='blue_text'>活跃账户(本月)</span></td>
    <td><?php echo $GameServer->getActiveAccounts(); ?></td>
</tr>
<tr>
    <td><span class='blue_text'>今天登录的账户</span></td>
    <td><?php echo $GameServer->getAccountsLoggedToday(); ?></td>

    <td><span class='blue_text'>今天创建的账户</span></td>
    <td><?php echo $GameServer->getAccountsCreatedToday(); ?></td>
</tr>
</table>
</div>

<?php
	$GameServer->checkForNotifications();
?>

<div class="box_right">
  <div class="box_right_title">管理面板日志</div>
  <?php
  $GameServer->selectDB('webdb', $conn);
  $result = mysqli_query($conn, "SELECT * FROM admin_log ORDER BY id DESC LIMIT 25;");
  if(mysqli_num_rows($result) == 0) 
  {
      echo "管理日志为空!";
  } 
  else 
  { ?>
  <table class="center">
    <tr>
      <th>日期</th>
      <th>用户</th>
      <th>动作</th>
    </tr>
    <?php
    while($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?php echo date("Y-m-d H:i:s",$row['timestamp']); ?></td>
        <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
        <td><?php echo $row['action']; ?></td>
      </tr>
    <?php } ?>
  </table><br/>
  <a href="?p=logs&s=admin" title="Get more logs">旧日志...</a>
  <?php } ?>
</div>