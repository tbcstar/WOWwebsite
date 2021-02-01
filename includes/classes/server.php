<?php

class Server 
{
	
	public function getRealmId($char_db)
	{
		global $Connect, $conn;
		$Connect->selectDB('webdb');
		$get = mysqli_query($conn, "SELECT id FROM realms WHERE char_db='".mysqli_real_escape_string($conn, $char_db)."'");
		$row = mysqli_fetch_assoc($get);
		return $row['id'];
	}
	
	public function getRealmName($char_db)
	{
		$Connect->selectDB('webdb');
		$get = mysqli_query($conn, "SELECT name FROM realms WHERE char_db='".mysqli_real_escape_string($conn, $char_db)."'");
		$row = mysqli_fetch_assoc($get);
		return $row['name'];
	}
	
	public static function serverStatus($realm_id) 
	{

		global $Connect, $conn;
		//获取状态
	    $fp = fsockopen($GLOBALS['realms'][$realm_id]['host'], $GLOBALS['realms'][$realm_id]['port'], $errno, $errstr, 1);
		if (!$fp)
		{
	   		echo $status = '<h4 class="realm_status_title_offline">'.$GLOBALS['realms'][$realm_id]['name'].' -  离线</h4>';
		}
		else 
		{
			echo $status = '<h4 class="realm_status_title_online">'.$GLOBALS['realms'][$realm_id]['name'].' - 在线</h4>';

       	echo '<span class="realm_status_text">';

		   	/* Players online bar */
		   	if($GLOBALS['serverStatus']['factionBar'] == TRUE) 
		   	{   
			   	$Connect->connectToRealmDB($realm_id);
			   	$getChars = mysqli_query($conn, "SELECT COUNT(online) FROM characters WHERE online=1");
			   	$total_online = mysqli_data_seek($getChars,0);

			   	$getAlliance = mysqli_query($conn, "SELECT COUNT(online) FROM characters WHERE online=1 AND race IN('3','4','7','11','1','22')");
			   	$alliance = mysqli_data_seek($getAlliance,0);
			   
			   	$getHorde = mysqli_query($conn, "SELECT COUNT(online) FROM characters WHERE online=1 AND race IN('2','5','6','8','10','9')");
			   	$horde = mysqli_data_seek($getHorde,0);

			   	if($total_online == 0) 
			   	{
				  	$per_alliance = 50; 
				  	$per_horde = 50;
			   	}
			   	else
			   	{
				   	if($alliance == 0)
				   	{
					   	$per_alliance = 0;
				   	}
				   	else
				   	{
					   	$per_alliance = round(($alliance / $total_online) * 100);
				   	}

				   	if($horde == 0)
				   	{
					   	$per_horde = 0;  
				   	}
				   	else
				   	{
					   	$per_horde = round(($horde / $total_online) * 100);
				   	}
			   	}

			   if($per_alliance + $per_horde > 100)
			   {
				   	$per_horde = $per_horde - 1 ;
			   }

		   		?>
		           <div class='srv_status_po'>
		                  <div class='srv_status_po_alliance' style="width: <?php echo $per_alliance; ?>%;"></div>
		                  <div class='srv_status_po_horde' style="width: <?php echo $per_horde; ?>%;"></div>
		                  <div class='srv_status_text'>联盟: <?php echo $alliance;?> &nbsp;  部落: <?php echo $horde;?></div>
		           </div>
		   		<?php
	    	}

		    echo '<table width="100%"><tr>';
			//Get players online
			if ($GLOBALS['serverStatus']['playersOnline'] == TRUE) 
			{
				$Connect->connectToRealmDB($realm_id);
				$getChars = mysqli_query($conn, "SELECT COUNT(online) FROM characters WHERE online=1");
				$pOnline = mysqli_data_seek($getChars,0);
				echo '<td>
						<b>',$pOnline,'</b> 在线玩家
					  </td>';
			}

			//Get uptime
			if ($GLOBALS['serverStatus']['uptime']==TRUE) 
			{	
				$Connect->selectDB('logondb');
				$getUp 	= mysqli_query($conn, "SELECT starttime FROM uptime WHERE realmid='".$realm_id."' ORDER BY starttime DESC LIMIT 1"); 
				$row 	= mysqli_fetch_assoc($getUp); 

				$time 	= time();
				$uptime = $time - $row['starttime'];

			 	echo '
					<td>
						   <b>'.convTime($uptime).'</b> 运行时间
					</td>
					</tr>';
			}
		}
		if ($GLOBALS['serverStatus']['nextArenaFlush']==TRUE) 
		{
			//Arena flush
		 	$Connect->connectToRealmDB($realm_id);
		 	$getFlush 	= mysqli_query($conn, "SELECT value FROM worldstates WHERE comment='NextArenaPointDistributionTime'");
		 	$row 		= mysqli_fetch_assoc($getFlush);
		 	$flush 	= date('d M H:i', $row['value']);
				 
		 	echo '<tr>
			 	   <td>
				   	   竞技场新赛季: <b>'.$flush.'</b>
				   </td>';
		}
		echo '</tr>
		      </table>
			  </span>';
  	}
}

$Server = new Server(); 