<?php 
    global $GamePage, $GameServer, $GameAccount; 
    $conn = $GameServer->connect();
?>
<div class="box_right_title"><?php echo $GamePage->titleLink(); ?> &raquo; 管理账号</div>

<?php
if(isset($_GET['char']))
{
	echo "搜寻结果 <b>" . $_GET['char'] . "</b><pre>";
        $GameServer->selectDB("webdb", $conn);

        $character = $conn->escape_string($_GET['char']);

        $result = $conn->query("SELECT name, id FROM realms;");

        while ($row = $result->fetch_assoc())
	{
        #$GameServer->connectToRealmDB($row['id']);
        $conn->select_db("characters");
        $get = $conn->query("SELECT account, name FROM characters WHERE name='". ucfirst($character) ."' OR guid='$character';");

        if ($get->num_rows > 0)
        {
            $rows = $get->fetch_assoc();
            echo "<a href='?page=users&selected=manage&user=". $rows['account'] ."'>". $rows['name'] ." - ". $row['name'] ."</a><br/>";
        }
        else
        {
            echo "无记录 \"$character\" 在哪里找到的。";
        }
	}
	echo "</pre><hr/>";
}

if(isset($_GET['user']))  {
	
	$GameServer->selectDB("logondb", $conn);
    $value  = $conn->escape_string(strtoupper($_GET['user']));
    $result = $conn->query("SELECT * FROM account WHERE username='$value' OR id='$value';");
    if ($result->num_rows == 0)
	{
		echo "<span class='red_text'>没有找到结果！</span>";
	} 
	else 
	{
		$row = $result->fetch_assoc();
		<table width="100%">
			<tr>
            <td><span class='blue_text'>账号名称</span></td>
            <td><?php echo ucfirst(strtolower($row['username'])); ?> (<?php echo $row['last_ip']; ?>)</td>

            <td><span class='blue_text'>加入</span></td>
            <td><?php echo $row['joindate']; ?></td>
			</tr>
			<tr>
                <td><span class='blue_text'>Email地址</span></td>
                <td><?php echo $row['email']; ?></td>

                <td><span class='blue_text'>投票积分</span></td>
                <td><?php echo $GameAccount->getVP($row['id']); ?></td>
			</tr>
			<tr>
                <td><span class='blue_text'>账号状态</span></td>
                <td><?php echo $GameAccount->getBan($row['id']); ?></td>

                <td><span class='blue_text'><?php echo $GLOBALS['donation']['coins_name']; ?></span></td>
                <td><?php echo $GameAccount->getDP($row['id']); ?></td>
			</tr>
			<tr><td><a href='?page=users&selected=manage&getlogs=<?php echo $row['id']; ?>'>帐户付款&购买记录</a><br />
            <a href='?page=users&selected=manage&getslogs=<?php echo $row['id']; ?>'>服务记录</a></td>
			<td></td>
			<td><a href='?page=users&selected=manage&editaccount=<?php echo $row['id']; ?>'>编辑帐户信息</a></tr>
			</table>
            <hr/>
            <b>角色</b><br/>
            <table>
            <tr>
            	<th>Guid</th>
                <th>名字</th>
                <th>等级</th>
                <th>职业</th>
                <th>种族</th>
                <th>服务器</th>
                <th>状态</th>
                <th>动作</th>
            </tr>
            <?php
            $GameServer->selectDB("webdb", $conn);
            $result = $conn->query("SELECT name, id FROM realms;");

            if (is_numeric($_GET['user']))
			{
                $account_id = $conn->escape_string($_GET['user']);
            }
            if (!is_numeric($_GET['user']))
            {
                $user = $conn->escape_string($_GET['user']);
                $account_id = $GameAccount->getAccID($user);

            }
            while ($row = $result->fetch_assoc())
            {


                #$conn = $GameServer->connectToRealmDB($row['id']);
                $conn->select_db("characters");

                $result  = $conn->query("SELECT name, guid, level, class, race, gender, online FROM characters 
                    WHERE name='$user' OR account='$account_id';");

                if (!$result) die($conn->error);

                while ($rows = $result->fetch_assoc())
				{ ?>
                    <tr class="center">
                    	<td><?php echo $rows['guid']; ?></td>
                        <td><?php echo $rows['name']; ?></td>
                        <td><?php echo $rows['level']; ?></td>
                        <td><img src="/styles/global/images/icons/class/<?php echo $rows['class']; ?>.gif" /></td>
                        <td><img src="/styles/global/images/icons/race/<?php echo $rows['race'].'-'.$rows['gender']; ?>.gif" /></td>
                        <td><?php echo $row['name']; ?></td>
                        <td>
                        <?php
						if($rows['online']==1)
							echo '<font color="#009900">在线</font>';
						else
							echo '<font color="#990000">离线</font>';	
						?>
                        </td>
                        <td><a href="#" onclick="characterListActions('<?php echo $rows['guid']; ?>','<?php echo $row['id']; ?>')">操作列表</a></td>
                    </tr>
                    <?php 
				}
			 }
			 ?>
             </table>
            <hr/>
		<?php
	}
 }
elseif (isset($_GET['getlogs'])) {
	?>
	选择账号： <a href='?page=users&selected=manage&user=<?php echo $_GET['getlogs']; ?>'><?php echo $GameAccount->getAccName($_GET['getlogs']); ?></a><p />

	<h4 class='payments' onclick='loadPaymentsLog(<?php echo $conn->escape_string($_GET['getlogs']); ?>)'>付款记录</h4>
	<div class='hidden_content' id='payments'></div>
	<hr/>
	<h4 class='payments' onclick='loadDshopLog(<?php echo $conn->escape_string($_GET['getlogs']); ?>)'>捐赠商城记录</h4>
	<div class='hidden_content' id='dshop'></div>
	<hr/>
	<h4 class='payments' onclick='loadVshopLog(<?php echo $conn->escape_string($_GET['getlogs']); ?>)'>投票商店记录</h4>
	<div class='hidden_content' id='vshop'></div>
	<?php
}
elseif (isset($_GET['editaccount'])) 
{
   ?>账户选择: <a href='?page=users&selected=manage&user=<?php echo $_GET['editaccount']; ?>'><?php echo $GameAccount->getAccName($_GET['editaccount']); ?></a><p />
	<table width="100%">
		<input type="hidden" id="account_id" value="<?php echo $_GET['editaccount']; ?>" />
	   	<tr>
			<td>E-mail</td>
			<td><input type="text" id="edit_email" class='noremove' value="<?php echo $GameAccount->getEmail($_GET['editaccount']); ?>"/>
		</tr> 
	   	<tr>
	   		<td>设置密码</td>
	   		<td><input type="text" id="edit_password" class='noremove'/></td>
	   	</tr>
	   	<tr>
	   		<td>投票积分</td>
	   		<td><input type="text" id="edit_vp" value="<?php echo $GameAccount->getVP($_GET['editaccount']); ?>" class='noremove'/> 
	   	</tr>
	   	<tr>
	   		<td><?php echo $GLOBALS['donation']['coins_name']; ?></td> 
			<td><input type="text" id="edit_dp" value="<?php echo $GameAccount->getDP($_GET['editaccount']); ?>" class='noremove'/></td>
		</tr>
	   	<tr>
	   		<td></td>
	   		<td><input type="submit" value="Save" onclick="save_account_data()"/></td>
	   	</tr>
   </table>
   <hr/>
<?php } 
elseif (isset($_GET['getslogs'])) 
{
    $getLogs = $conn->escape_string($_GET['getslogs']);
    ?>
	所选账号: <a href='?page=users&selected=manage&user=<?php echo $getLogs; ?>'><?php echo $GameAccount->getAccName($getLogs); ?></a><p />
	<table>
    	<tr>
        	<th>服务</th>
            <th>描述</th>
            <th>服务器</th>
            <th>日期</th>
        </tr>
        <?php
		$GameServer->selectDB("webdb", $conn);
		$result = $conn->query("SELECT * FROM user_log WHERE account=". $getLogs .";");
        if ($result->num_rows == 0)
		{
			echo "没有找到该帐户的记录！";
		}
		else
		{
			while ($row = $result->fetch_assoc())
			{
				echo "<tr class='center'>";
                echo "<td>". $row['service'] ."</td>";
                echo "<td>". $row['desc'] ."</td>";
                echo "<td>". $GameServer->getRealmName($row['realmid']) ."</td>";
                echo "<td>". date('Y-m-d H:i', $row['timestamp']) ."</td>";
                echo "</tr>";
			}
		} ?>
    </table>
    <hr/>
<?php
} ?>
<table width="100%">
	<tr>
    	<td>用户名或ID: </td>	
        <form action="" method="get">
        	<input type="hidden" name="p" value="users">
        	<input type="hidden" name="s" value="manage">
        <td><input type="text" name="user"></td>
        <td><input type="submit" value="Go"></td>
    </tr></form>

    <tr>
        <td>角色名或GUID: </td>	
        <form action="" method="get">
        	<input type="hidden" name="p" value="users">
        	<input type="hidden" name="s" value="manage">
        <td><input type="text" name="char"></td>
        <td><input type="submit" value="Go"></td>
   	</tr></form>
</table>