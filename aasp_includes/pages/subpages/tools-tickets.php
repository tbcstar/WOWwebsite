<?php 
    global $GameServer, $GameAccount, $GamePage;
    $conn = $GameServer->connect();

    $GamePage->validatePageAccess("Tools->Tickets");

?>
<div class="box_right_title">Tickets</div>
<?php if(!isset($_GET['guid'])) { ?>
<table class="center">
        <tr>
            <td><input type="checkbox" id="tickets_offline">查看离线tickets</td>
            <td>
            <select id="tickets_realm">
           		 <?php
				 $GameServer->selectDB("webdb", $conn);

				$result = $conn->query("SELECT char_db, name, description FROM realms;");
                if ($result->num_rows == 0)
				{
					echo "<option value='NULL'>找不到服务器。</option>";
				}
				else 
				{
					echo "<option value='NULL'>--选择一个服务器--</option>";
					while ($row = $result->fetch_assoc())
					{
						echo "<option value='". $row['char_db'] ."'>". $row['name'] ." - <i>". $row['description'] ."</i></option>";
					}
				}
				 ?>
            </select>
            </td>
            <td>
            <input type="submit" value="Load" onclick="loadTickets()">
            </td>
        </tr>
</table>
<hr/>
<span id="tickets">
	   <?php 
	    if(isset($_SESSION['lastTicketRealm']))
		   {
			   ##############################
				if($GLOBALS['core_expansion'] == 3)
					$guidString = "playerGuid";
				else
					$guidString = "guid";
				
				if($GLOBALS['core_expansion'] == 3)
					$closedString = "closed";
				else
					$closedString = "closedBy";
					
				if($GLOBALS['core_expansion'] == 3)
				
					$ticketString = "guid";
				else
					$ticketString = "ticketId";
				############################

			    $offline = $_SESSION['lastTicketRealmOffline'];
			    $realm   = $conn->escape_string($_SESSION['lastTicketRealm']);


				if($realm == "NULL")
				   die("<pre>请选择一个服务器。</pre>");

				$conn->select_db($realm);

                $result = $conn->query("SELECT ". $ticketString .", name, message, createtime, ". $guidString .", ". $closedString ." 
                    FROM gm_tickets ORDER BY ticketId DESC;");
				if ($result->num_rows == 0)
				{
				    die("<pre>没有发现tickets！</pre>");
				}

				echo "
			        <table class='center'>
				    <tr>
					   <th>ID</th>
					   <th>名字</th>
					   <th>信息</th>
					   <th>创建日期</th>
					   <th>Ticket状态</th>
					   <th>玩家状态</th>
					   <th>敏捷工具</th>
				    </tr>
				";

				while ($row = $result->fetch_assoc())
				{
					$get = $conn->query("SELECT COUNT(online) FROM characters WHERE guid='" . $row[$guidString] . "' AND online='1';");
                    if ($get->data_seek(0) == 0 && $offline == "on")
					{
    					echo "<tr>";
                        echo "<td><a href='?p=tools&s=tickets&guid=". $row[$ticketString] ."&db=". $realm ."'>". $row[$ticketString] ."</td>";
                        echo "<td><a href='?p=tools&s=tickets&guid=". $row[$ticketString] ."&db=". $realm ."'>". $row['name'] ."</td>";
                        echo "<td><a href='?p=tools&s=tickets&guid=". $row[$ticketString] ."&db=". $realm ."'>". substr($row['message'], 0, 15) ."...</td>";
                        echo "<td><a href='?p=tools&s=tickets&guid=". $row[$ticketString] ."&db=". $realm ."'>". date('Y-m-d H:i:s', $row['createtime']) ."</a></td>";

                        if ($row[$closedString] == 1)
                        {
                            echo "<td><font color='red'>关闭</font></td>";
                        }
                        else
                        {
                            echo "<td><font color='green'>打开</font></td>";
                        }		

						$get = $conn->query("SELECT COUNT(online) FROM characters WHERE guid=". $row[$guidString] ." AND online=1;");
                        if ($get->data_seek(0) > 0)
                        {
						   echo "<td><font color='green'>在线</font></td>";
                        }
						else
						{
						   echo "<td><font color='red'>离线</font></td>";
						}
						?> <td><a href="#" onclick="deleteTicket('<?php echo $row[$ticketString]; ?>','<?php echo $realm; ?>')">删除</a>
								&nbsp;
								<?php if($row[$closedString]==1) 
								{ ?>
									<a href="#" onclick="openTicket('<?php echo $row[$ticketString]; ?>','<?php echo $realm; ?>')">打开</a>
								<?php }
								else 
								{
								?>
							   <a href="#" onclick="closeTicket('<?php echo $row[$ticketString]; ?>','<?php echo $realm; ?>')">关闭</a>
							   <?php
								}
								?>
								</td><?php
							echo "<tr>";
							}
            }
            echo "</table>";
		}
		else
		    echo "<pre>请选择一个服务器。</pre>";
	   ?>
</span>
<?php } 
elseif(isset($_GET['guid'])) 
{
	if($GLOBALS['core_expansion'] == 3)
		$guidString = "playerGuid";
	else
		$guidString = "guid";	
	
	if($GLOBALS['core_expansion'] == 3)
		$closedString = "closed";
	else
		$closedString = "closedBy";	
		
	if($GLOBALS['core_expansion'] == 3)
		$ticketString = "guid";
	else
		$ticketString = "ticketId";
	
	$conn->select_db($_GET['db']);
    $result = $conn->query("SELECT name, message, createtime, ". $guidString .", ". $closedString ." 
        FROM gm_tickets WHERE ". $ticketString ."='" . $conn->escape_string($_GET['guid']) ."';");
    $row = $result->fetch_assoc();
	?>
    <table style="width: 100%;" class="center">
        <tr>
            <td>
            	<span class='blue_text'>提交者：</span>
            </td>	
            <td>
				<?php echo $row['name']; ?>
            </td>
                
            <td>
            	<span class='blue_text'>创建日期：</span>
            </td>
            <td>
				<?php echo date("Y-m-d H:i:s",$row['createtime']); ?>
            </td>
               
            <td>
            	<span class='blue_text'>Ticket状态：</span>
            </td>
            <td>
				<?php
                if($row[$closedString]==1) 
                    echo "<font color='red'>关闭</font>";
                else
                    echo "<font color='green'>打开</font>";
                ?>
            </td>
            
            <td>
            	<span class='blue_text'>玩家状态：</span>
            </td>
            <td>
            	<?php
				$get = $conn->query("SELECT COUNT(online) FROM characters WHERE guid=". $row[$guidString] ." AND online=1;");
                if ($get->data_seek(0) > 0)
                {
				   	echo "<font color='green'>在线</font>";
                }
				else
				{
				   echo "<font color='red'>离线</font>";
				}
			    ?>
            </td>
                
        </tr>
    </table>
    <hr/>
    <?php
	echo nl2br($row['message']);
	?>
    <hr/>
    <pre>
        <a href="?p=tools&s=tickets">&laquo; 回到tickets</a>
        &nbsp; &nbsp; &nbsp;
        <a href="#" onclick="deleteTicket('<?php echo $_GET['guid']; ?>','<?php echo $_GET['db']; ?>')">移除tickets</a>
        &nbsp; &nbsp; &nbsp;
        <?php if($row[$closedString]==1) 
			{ ?>
				<a href="#" onclick="openTicket('<?php echo $_GET['guid']; ?>','<?php echo $_GET['db']; ?>')">打开ticket</a>
			<?php }
			else 
			{
			?>
		  		<a href="#" onclick="closeTicket('<?php echo $_GET['guid']; ?>','<?php echo $_GET['db']; ?>')">关闭ticket</a>
		   <?php
			}
		   ?>
    </pre>
    <?php
}

?>
