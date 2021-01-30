<?php 
$server = new server;
$account = new account;
$page = new page;

$page->validatePageAccess('Tools->Account Access');

?>
<div class="box_right_title">账号访问</div>
所有的GM账户如下。
<br/>&nbsp;
<table>
	<tr>
    	<th>ID</th>
        <th>用户名</th>
        <th>级别</th>
        <th>服务器</th>
        <th>状态</th>
        <th>最后一次登录</th>
        <th>动作</th>
    </tr>
    <?php
	$server->selectDB('logondb');
	$result = mysql_query("SELECT * FROM account_access");
	if(mysql_num_rows($result)==0) 
	 	echo "<b>没有发现GM账户!</b>";	
	else
	{
		while($row = mysql_fetch_assoc($result)) 
		{
			?>
            <tr style="text-align:center;">
            	<td><?php echo $row['id']; ?></td>
                <td><?php echo $account->getAccName($row['id']); ?></td>
                <td><?php echo $row['gmlevel']; ?></td>
                <td>
                <?php 
					if($row['RealmID']=='-1')
						echo '所有';
					else
					{
						$getRealm = mysql_query("SELECT name FROM realmlist WHERE id='".$row['RealmID']."'");
						if(mysql_num_rows($getRealm)==0)
							echo 'Unknown';
						$rows = mysql_fetch_assoc($getRealm);
						echo $rows['name'];
					}
				?>
                </td>
                <td>
                <?php
					$getData = mysql_query("SELECT last_login,online FROM account WHERE id='".$row['id']."'");
					$rows = mysql_fetch_assoc($getData);
					if($rows['online']==0)
					 	echo '<font color="red">离线</font>';
					else
						echo '<font color="green">在线</font>';	
				?>
                </td>
                <td>
                <?php
				 	echo $rows['last_login'];
				?>
                </td>
                <td>
                	<a href="#" onclick="editAccA(<?php echo $row['id']; ?>,<?php echo $row['gmlevel']; ?>,<?php echo $row['RealmID']; ?>)">编辑</a>
              		&nbsp;
                    <a href="#" onclick="removeAccA(<?php echo $row['id']; ?>)">移除</a>
                </td>
            </tr>
            <?php
		}
		
	}
	?>
</table>
<hr/>
<a href="#" class="content_hider" onclick="addAccA()">新增账号</a>