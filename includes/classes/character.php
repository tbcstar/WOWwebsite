<?php

class Character 
{
	
	public function unstuck ($guid, $char_db) 
	{
        global $Database, $Account, $Server;

        $guId   = $Database->conn->escape_string($guid);
        $charDb = $Database->conn->escape_string($char_db);

        $rid  = $Server->getRealmId($charDb);
		
		$Database->realm($rid);
		
        if ( $this->isOnline($guId) == TRUE )
    	{
			echo '<b class="red_text">在继续之前，请先登出你的角色。';
    	}
		else 
		{
			if ( DATA['service']['unstuck']['currency'] == 'vp' )
			{
				if ( $Account->hasVP($_SESSION['cw_user'], DATA['service']['unstuck']['price']) == FALSE )
				{
					die('<b class="red_text">没有足够的投票积分!</b>' );
				}
				else
				{
					$Account->deductVP($Account->getAccountID($_SESSION['cw_user']),DATA['service']['unstuck']['price']);	
				}
			}
		
			if ( DATA['service']['unstuck']['currency'] == 'dp' )
			{
				if ( $Account->hasDP($_SESSION['cw_user'], DATA['service']['unstuck']['price']) == FALSE )
				{
					die( '<b class="red_text">积分不足'.DATA['donation']['coins_name'].'</b>' );
				}
				else
				{
					$Account->deductDP($Account->getAccountID($_SESSION['cw_user']),DATA['service']['unstuck']['price']);
				}
			}

		    $Account->connectToRealmDB($rid);
		    $statement = $Database->select("character_homebind", null, null, "guild=$guId");
            $getXYZ = $statement->get_result();
            $row    = $getXYZ->fetch_assoc();
			
			$new_x = $row['posX']; 
			$new_y = $row['posY']; 
			$new_z = $row['posZ']; 
			$new_zone = $row['zoneId']; 
			$new_map = $row['mapId'];

            $Database->update("characters", array("position_x","position_y","position_z","zone","map"), array($new_x, $new_y, $new_z, $new_zone, $new_map, $guId), "guid", $guId);

			$Account->logThis("Performed unstuck on " . $this->getCharName($guId, $rid), 'Unstuck', $rid);

			return TRUE;
			$statement->close();
	  	}
	}
	
	public function revive($guid,$char_db) 
	{
        global $Database, $Server, $Account;

        $guId   = $Database->conn->escape_string($guid);
        $charDb = $Database->conn->escape_string($char_db);

        $rid  = $Server->getRealmId($charDb);
		
		$Database->realm($rid);
		
		if ( $this->isOnline($guId) == TRUE )
		{
			echo '<b class="red_text">请在继续之前退出游戏。';
		}
	    else 
		{
			if ( DATA['service']['revive']['currency'] == 'vp' )
			{
				if ( $Account->hasVP($_SESSION['cw_user'], DATA['service']['unstuck']['price']) == FALSE )
				{
					die( '<b class="red_text">没有足够的投票积分！</b>' );
				}
				else
				{
					$Account->deductVP($Account->getAccountID($_SESSION['cw_user']),DATA['service']['revive']['price']);	
				}
			}
		
			if ( DATA['service']['revive']['currency'] == 'dp' )
			{
				if ( $Account->hasDP($_SESSION['cw_user'], DATA['service']['unstuck']['price']) == FALSE )
				{
					die( '<b class="red_text">钱不够 '.DATA['donation']['coins_name'].'</b>' );
				}
				else
				{
					$Account->deductDP($Account->getAccountID($_SESSION['cw_user']),DATA['service']['revive']['price']);	
				}
			}

			$Account->connectToRealmDB($rid);
			$Database->conn->query("DELETE FROM character_aura WHERE guid=". $guId ." AND spell=20584 OR guid=". $guId ." AND spell=8326;");
			
			$Account->logThis("进行了复活 " . $this->getCharName($guId, $rid), 'Revive', $rid);
			
			return TRUE;
	  	}
	}
	
	public function instant80($values) 
	{
        global $Database, $Account;

		die("此功能被禁用。 <br/><i>还有，你不应该在这里…</i>");

        $values = $Database->conn->escape_string($values);
		$values = explode("*", $values);
		
		$Database->realm($values[1]);
		
		if ( $this->isOnline($values[0]) == TRUE )
		{
			echo '<b class="red_text">请在继续之前退出游戏。';
		}
		else 
		{
			$service_values = explode("*",DATA['service']['instant58']);
			if ( $service_values[1] == "dp" )
			{
				if ( $Account->hasDP($_SESSION['cw_user'], DATA['service']['instant80']['price']) == FALSE )
				{
					echo '<b class="red_text">钱不够 '.DATA['donation']['coins_name'].'</b>';
					$error = TRUE;
				}
			} 
			elseif ( $service_values[1] == "vp" )
			{
				if ( $Account->hasVP($_SESSION['cw_user'], DATA['service']['instant80']['price']) == FALSE )
				{
					echo '<b class="red_text">没有足够的投票积分。</b>';
					$error = TRUE;
				}
			} 

			if ( $error != true )
			{
				//User got coins. Boost them up to 58 :D
				$Database->realm($values[1]);
				
				$Database->update("characters", "level", 58, "guid", $values[0]);

				$Account->logThis("立即达到58级 ".$this->getCharName($values[0], NULL), 'Instant', NULL);

				echo '<h3 class="green_text">角色级别被设置为58!</h3>';
			}
		}
 	}

	public function isOnline($char_guid) 
	{
        global $Database;

        $charGuid   = $Database->conn->escape_string($char_guid);
            $statement = $Database->select("characters", "COUNT('guid')", null, "guid=". $charGuid ." AND online=1"); #("SELECT COUNT('guid') FROM characters WHERE guid=". $charGuid ." AND online=1;");
            $result = $statement->get_result();
            if ( $result->data_seek( 0) == 0 )
		{
			return false;
		}
		else
		{
 			return true;
		}
		$statement->close();
	}
  
	public function getRace($value) 
	{
		switch($value) 
		{
			default:
			return "未知";
			break;
			#######
			case(1):
			return "人类";
			break;
			#######		 
			case(2):
			return "兽人";
			break;
			#######
			case(3):
			return "矮人";
			break;
			#######
			case(4):
			return "暗夜精灵";
			break;
			#######
			case(5):
			return "不死族";
			break; 
			#######
			case(6):
			return "牛头人";
			break;
			#######
			case(7):
			return "侏儒";
			break;
			#######
			case(8):
			return "巨魔";
			break;
			#######
			case(9):
			return "哥布林";
			break;
			#######
			case(10):
			return "血精灵";
			break;
			#######
			case(11):
			return "德莱尼";
			break;
			#######
			case(22):
			return "狼人";
			break;
			#######
		}
	}
  
	public function getGender($value) 
	{
		if ( $value == 1 )
		{
			return '女性';
		}
		elseif($value == 0)
		{
			return '男性';
		}
		else
		{
			return '未知';
		}
	}
  
	public function getClass($value) 
	{
		switch($value) 
		{
			default:
				return "未知";
				break;
			#######
			case(1):
				return "战士";
				break;
			#######
			case(2):
				return "圣骑士";
				break;
			#######
			case(3):
				return "猎人";
				break;
			#######
			case(4):
				return "盗贼";
				break;
			#######
			case(5):
				return "牧师";
				break;
			#######
			case(6):
				return "死亡骑士";
				break;
			#######
			case(7):
				return "萨满";
				break;
			#######
			case(8):
				return "法师";
				break;
			#######
			case(9):
				return "术士";
				break;
			#######
			case(11):
				return "德鲁伊";
				break;
			#######
			case(12):
				return "武僧";
				break;
			####### 
		}
  	}
  
	public function getClassIcon($value) 
	{   
		return '<img src="styles/global/images/icons/class/'.$value.'.gif" />';
	}
  
	public function getFactionIcon($value) 
	{
		$a = array(1,3,4,7,11,22);
		$h = array(2,5,6,8,9,10);

		if ( in_array($value, $a) )
		{
			return '<img src="styles/global/images/icons/faction/0.gif" />';
		}
		elseif ( in_array($value, $h) )
		{
			return '<img src="styles/global/images/icons/faction/1.gif" />';
		}
	}
  
  
   public function getCharName($id,$realm_id) 
   	{
        global $Database;

        $ID      = $Database->conn->escape_string($id);
        $realmID = $Database->conn->escape_string($realm_id);
		
        $Database->realm($realmID);

        $statement = $Database->select("characters", "name", null, "guid=". $ID);
        $row    = $statement->get_result()->fetch_assoc();
		return $row['name'];
		$statement->close();
	}
}

$Character = new Character(); 