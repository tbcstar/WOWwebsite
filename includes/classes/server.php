<?php

class Server 
{
	public function getRealmId($char_db)
	{
        global $Connect;
        $conn = $Connect->connectToDB();
		$Connect->selectDB("webdb", $conn);

        $get = $conn->query("SELECT id FROM realms WHERE char_db='". $conn->escape_string($char_db) ."';");
        $row = $get->fetch_assoc();

		return $row['id'];
	}
	
	public function getRealmName($char_db)
	{
        global $Connect;
        $conn = $Connect->connectToDB();
        $Connect->selectDB("webdb", $conn);

        $get = $conn->query("SELECT name FROM realms WHERE char_db='". $conn->escape_string($char_db) ."';");
        $row = $get->fetch_assoc();

		return $row['name'];
	}
	
	public function serverStatus($realmId) 
	{

        global $Connect;
        $conn = $Connect->connectToDB();

        $realmId = $conn->escape_string($realm_id);
		//获取状态
	    $fp = fsockopen($GLOBALS['realms'][$realmId]['host'], $GLOBALS['realms'][$realmId]['port'], $errno, $errstr, 1);
		if (!$fp)
		{
	   		echo $status = "<h4 class='realm_status_title_offline'>" . $GLOBALS['realms'][$realmId]['name'] . " -  离线</h4>";
		}
		else 
		{
			echo $status = "<h4 class='realm_status_title_online'>" . $GLOBALS['realms'][$realmId]['name'] . " - 在线</h4>";

       	    echo "<span class='realm_status_text'>";

		   	/* Players online bar */
		   	if($GLOBALS['serverStatus']['factionBar'] == TRUE) 
		   	{   
                $Connect->selectDB('chardb', $conn, $realmId);
			   
                $getChars     = $conn->query("SELECT COUNT(online) AS online FROM characters WHERE online=1;");
                $total_online = $getChars->fetch_assoc();

                if ($total_online['online'] == 0)
			   	{
				  	$per_alliance = 50; 
				  	$per_horde = 50;

                    $alliance['online'] = 0;
                    $horde['online']    = 0;
			   	}
			   	else
			   	{
                    $getAlliance = $conn->query("SELECT COUNT(online) AS online FROM characters WHERE online=1 AND race IN(3, 4, 7, 11, 1, 22);");
                    $alliance = $getAlliance->fetch_assoc();

                    if ($alliance['online'] == 0 || empty($alliance['online']))
				   	{
					   	$per_alliance = 0;
				   	}
				   	else
				   	{
                        $per_alliance = ($alliance['online'] / $total_online['online']) * 100;
				   	}


                    $getHorde = $conn->query("SELECT COUNT(online) AS online FROM characters WHERE online=1 AND race IN(2, 5, 6, 8, 10, 9);");
                    $horde    = $getHorde->fetch_assoc();
                    if ($horde['online'] == 0 || empty($horde['online']))
				   	{
					   	$per_horde = 0;  
				   	}
				   	else
				   	{
                        $per_horde = (($horde['online'] / $total_online['online']) * 100);
				   	}
			   	}
                /*if ($per_alliance + $per_horde > 100)
			   {
				   	$per_horde = $per_horde - 1 ;
                }*/

		   		?>
		           <div class='srv_status_po'>
		                <div class='srv_status_po_alliance' style="width: <?php echo $per_alliance; ?>%;"></div>
		                <div class='srv_status_po_horde' style="width: <?php echo $per_horde; ?>%;"></div>
                        <div class='srv_status_text'>
                            <b style="color:blue;">联盟: <?php echo $alliance['online']; ?></b>
                            &nbsp;
                            <b style="color:red;">部落: <?php echo $horde['online']; ?></b>
                        </div>
		           </div>
		   		<?php
	    	}

		    echo "<table width='100%'><tr>";
			//Get players online
			if ($GLOBALS['serverStatus']['playersOnline'] == TRUE) 
			{
                $Connect->selectDB('chardb', $conn, $realmId);

                $getChars = $conn->query("SELECT COUNT(online) AS online FROM characters WHERE online=1;");

                $pOnline  = $getChars->fetch_assoc();
                if ($pOnline['online'] > 1 || $pOnline['online'] == 0) 
                {
                    echo "<td><b>". $pOnline['online'] ."</b> 在线玩家</td>";
                }
                elseif ($pOnline['online'] == 1)
                {
                    echo "<td><b>". $pOnline['online'] ."</b> 在线玩家</td>";
                }

			}

			//Get uptime
			if ($GLOBALS['serverStatus']['uptime']==TRUE) 
			{	
				$Connect->selectDB("logondb", $conn);
				$getUp = $conn->query("SELECT starttime FROM uptime WHERE realmid=". $realmId ." ORDER BY starttime DESC LIMIT 1;");
                $row   = $getUp->fetch_assoc();

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
            $Connect->selectDB('chardb', $conn, $realmId);
            $getFlush = $conn->query("SELECT value FROM worldstates WHERE comment='NextArenaPointDistributionTime';");
            $row      = $getFlush->fetch_assoc();
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