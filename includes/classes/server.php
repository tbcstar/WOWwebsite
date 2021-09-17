<?php

class Server 
{
	public function getRealmId($char_db)
	{
        global $Database;
        $Database->selectDB("webdb");

        $char_db = $Database->conn->escape_string($char_db);

        $statement = $Database->select("realms", "id", null, "char_db=$char_db");
        $result = $statement->get_result();
        $row = $result->fetch_assoc();

		return $row['id'];
		$statement->close();
	}
	
	public function getRealmName($char_db)
	{
        global $Database;
        $Database->selectDB("webdb");

        $char_db = $Database->conn->escape_string($char_db);

        $statement = $Database->select("realms", "name", null, "char_db=$char_db");
        $result = $statement->get_result();
        $row = $result->fetch_assoc();

		return $row['name'];
		$statement->close();
	}
	
	public function serverStatus($realmId) 
	{

        global $Database;

        $realmId = $Database->conn->escape_string($realm_id);
		//获取状态
	    $server_response = fsockopen(DATA['characters']['host'], DATA['characters']['port'], $errno, $errstr, 1);
        if ( $server_response === false )
		{
	   		echo $status = "<h4 class='realm_status_title_offline'>离线</h4>";
		}
		else 
		{
			echo $status = "<h4 class='realm_status_title_online'>在线</h4>";

       	    echo "<span class='realm_status_text'>";

		   	/* Players online bar */
		   	if ( DATA['website']['server_status']['faction_bar'] == true )
		   	{   
                $Database->selectDB('chardb', $realmId);
			   
                $statement = $Database->select("characters", "COUNT(online) AS online", null, "online=1");
                $result = $statement->get_result();
                $total_online = $result->fetch_assoc();
                $statement->close();

                if ( $total_online['online'] == 0 )
			   	{
				  	$per_alliance = 50; 
				  	$per_horde = 50;

                    $alliance['online'] = 0;
                    $horde['online']    = 0;
			   	}
			   	else
			   	{
                    $statement = $Database->select("characters", "COUNT(online) AS online", null, "online=1 AND race IN(3, 4, 7, 11, 1, 22)");
                    $getAlliance = $statement->get_result();
                    $alliance = $getAlliance->fetch_assoc();
                    $statement->close();

                    if ( $alliance['online'] == 0 || empty($alliance['online']) )
				   	{
					   	$per_alliance = 0;
				   	}
				   	else
				   	{
                        $per_alliance = ($alliance['online'] / $total_online['online']) * 100;
				   	}


                    $statement = $Database->select("characters", "COUNT(online) AS online", null, "online=1 AND race IN(2, 5, 6, 8, 10, 9)");
                    $getHorde = $statement->get_result();
                    $horde    = $getHorde->fetch_assoc();
                    $statement->close();
                    if ( $horde['online'] == 0 || empty($horde['online']) )
				   	{
					   	$per_horde = 0;  
				   	}
				   	else
				   	{
                        $per_horde = (($horde['online'] / $total_online['online']) * 100);
				   	}
			   	}
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

            /** Get players online
            */
            if ( DATA['website']['server_status']['players_online'] == true )
            {
                $Database->selectDB('chardb', $conn, $realmId);

                $statement = $Database->select("characters", "COUNT(online) AS online", null, "online=1");
                $getChars = $statement->get_result();
                $pOnline  = $getChars->fetch_assoc();
                $statement->close();
                if ( $pOnline['online'] > 1 || $pOnline['online'] == 0 ) 
                {
                    echo "<td><b>". $pOnline['online'] ."</b> 在线玩家</td>";
                }
                elseif ( $pOnline['online'] == 1 )
                {
                    echo "<td><b>". $pOnline['online'] ."</b> 在线玩家</td>";
                }

			}

            /** Get uptime
            */
            if ( DATA['website']['server_status']['uptime'] == true )
			{	
				$Database->selectDB("logondb");
                $statement = $Database->select("uptime", "starttime", "realmid=$realmId ORDER BY starttime DESC LIMIT 1");
                $getUp = $statement->get_result();
                $row   = $getUp->fetch_assoc();
                $statement->close();

				$time 	= time();
				$uptime = $time - $row['starttime'];

			 	echo '
					<td>
						   <b>'.convTime($uptime).'</b> 运行时间
					</td>
					</tr>';
			}
		}
		if ( DATA['website']['server_status']['next_arena_flush'] == true )
		{
			//Arena flush
            $Database->selectDB('chardb', $realmId);

            $statement = $Database->select("worldstates", "value", null, "comment='NextArenaPointDistributionTime'");
            $row      = $getFlush->fetch_assoc();
		 	$flush 	= date('d M H:i', $row['value']);
		 	$statement->close();
				 
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