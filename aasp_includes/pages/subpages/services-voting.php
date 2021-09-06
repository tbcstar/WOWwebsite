<?php 
    global $GamePage, $GameServer, $GameAccount;
    $conn = $GameServer->connect();
    $GameServer->selectDB('webdb', $conn);
?> 
<div class="box_right_title">投票链接</div>
<table class="center">
<tr>
     <th>标题</th>
    <th>积分</th>
    <th>图片</th>
    <th>链接</th>
    <th>动作</th>
</tr>
<?php
$result = $conn->query("SELECT * FROM votingsites ORDER BY id ASC;");
while ($row = $result->fetch_assoc())
{ ?>
	     <tr>
              <td><?php echo $row['title']; ?></td>
              <td><?php echo $row['points']; ?></td>
              <td><img src="<?php echo $row['image']; ?>"></td>
              <td><?php echo $row['url']; ?></td>
              <td><a href="#" onclick="editVoteLink('<?php echo $row['id']; ?>','<?php echo $row['title']; ?>','<?php echo $row['points']; ?>',
              '<?php echo $row['image']; ?>','<?php echo $row['url']; ?>')">编辑</a> 
              <br/> <a href="#" onclick="removeVoteLink('<?php echo $row['id']; ?>')">删除</a><br />
              </td>   
          </tr>
  <?php 
  }
?>
</table>
<br/>
<a href="#" class="content_hider" onclick="addVoteLink()">添加一个新的投票网站</a>