<?php 
  global $GamePage, $GameServer;
  $conn = $GameServer->connect();
  $GameServer->selectDB('webdb', $conn);
?>
<div class="box_right_title"><?php echo $GamePage->titleLink(); ?> &raquo; 菜单</div>
  <table class="center">
    <tr>
      <th>位置</th>
      <th>标题</th>
      <th>Url</th>
      <th>显示时间</th>
      <th>动作</th>
    </tr>
    <?php 
    $x = 1;
    $result = mysqli_query($conn, "SELECT * FROM site_links ORDER BY position ASC;");
    while ($row = mysqli_fetch_assoc($result))
    {
      ?>
      <tr>
        <td><?php echo $x; ?></td>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['url']; ?></td>
        <td><?php 
    		if($row['shownWhen'] == 'logged') 
        {
    			echo "已登录";
    		} 
        elseif($row['shownWhen'] == 'notlogged') 
        {
    			echo "未登录";
    		}
        else 
        {
    			echo ucfirst($row['shownWhen']);
    		}?>
        </td>
        <td>
            <a href="#" onclick="editMenu(<?php echo $row['position']; ?>)"
            >编辑</a> &nbsp; <a href="#" onclick="deleteLink(<?php echo $row['position']; ?>)">删除</a>
        </td>
      </tr>
      <?php $x++; 
    }?>
  </table>
<br/>
<a href="#" onclick="addLink()" class="content_hider">添加一个新链接</a> 