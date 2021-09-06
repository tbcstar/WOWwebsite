<?php

class Character 
{
	
	public function unstuck($guid, $char_db) 
	{
        global $Connect, $Account, $Server;
        $conn = $Connect->connectToDB();

        $guId   = $conn->escape_string($guid);
        $charDb = $conn->escape_string($char_db);

        $rid  = $Server->getRealmId($charDb);
		
		$Connect->connectToRealmDB($rid);
		
        if ($this->isOnline($guId) == TRUE)
    	{
			echo '<b class="red_text">在继续之前，请先登出你的角色。';
    	}
		else 
		{
			if($GLOBALS['service']['unstuck']['currency']=='vp')
			{
				if($Account->hasVP($_SESSION['cw_user'],$GLOBALS['service']['unstuck']['price']) == FALSE)
				{
					die('<b class="red_text">没有足够的投票积分!</b>' );
				}
				else
				{
					$Account->deductVP($Account->getAccountID($_SESSION['cw_user']),$GLOBALS['service']['unstuck']['price']);	
				}
			}
		
			if($GLOBALS['service']['unstuck']['currency']=='dp')
			{
				if($Account->hasDP($_SESSION['cw_user'],$GLOBALS['service']['unstuck']['price']) == FALSE)
				{
					die( '<b class="red_text">积分不足'.$GLOBALS['donation']['coins_name'].'</b>' );
				}
				else
				{
					$Account->deductDP($Account->getAccountID($_SESSION['cw_user']),$GLOBALS['service']['unstuck']['price']);
				}
			}

		    $Account->connectToRealmDB($rid);
		    $getXYZ = $conn->query("SELECT * FROM character_homebind WHERE guid=". $guId .";");
            $row    = $getXYZ->fetch_assoc();
			
			$new_x = $row['posX']; 
			$new_y = $row['posY']; 
			$new_z = $row['posZ']; 
			$new_zone = $row['zoneId']; 
			$new_map = $row['mapId'];

            $conn->query("UPDATE characters 
                SET position_x='". $new_x ."', 
                position_y='". $new_y ."', 
                position_z='". $new_z ."', 
                zone='". $new_zone ."',
                map='". $new_map ."' 
                WHERE guid=". $guId .";");

			$Account->logThis("Performed unstuck on " . $this->getCharName($guId, $rid), 'Unstuck', $rid);

			return TRUE;
	  	}
	}
	
	public function revive($guid,$char_db) 
	{
        global $Connect, $Server, $Account;
        $conn = $Connect->connectToDB();

        $guId   = $conn->escape_string($guid);
        $charDb = $conn->escape_string($char_db);

        $rid  = $Server->getRealmId($charDb);
		
		$Connect->connectToRealmDB($rid);
		
		if ($this->isOnline($guId) == TRUE)
		{
			echo '<b class="red_text">请在继续之前退出游戏。';
		}
	    else 
		{
			if($GLOBALS['service']['revive']['currency'] == 'vp')
			{
				if($Account->hasVP($_SESSION['cw_user'],$GLOBALS['service']['unstuck']['price']) == FALSE)
				{
					die( '<b class="red_text">没有足够的投票积分！</b>' );
				}
				else
				{
					$Account->deductVP($Account->getAccountID($_SESSION['cw_user']),$GLOBALS['service']['revive']['price']);	
				}
			}
		
			if($GLOBALS['service']['revive']['currency']=='dp')
			{
				if($Account->hasDP($_SESSION['cw_user'],$GLOBALS['service']['unstuck']['price']) == FALSE)
				{
					die( '<b class="red_text">钱不够 '.$GLOBALS['donation']['coins_name'].'</b>' );
				}
				else
				{
					$Account->deductDP($Account->getAccountID($_SESSION['cw_user']),$GLOBALS['service']['revive']['price']);	
				}
			}

			$Account->connectToRealmDB($rid);
			$conn->query("DELETE FROM character_aura WHERE guid=". $guId ." AND spell=20584 OR guid=". $guId ." AND spell=8326;");
			
			$Account->logThis("进行了复活 " . $this->getCharName($guId, $rid), 'Revive', $rid);
			
			return TRUE;
	  	}
	}
	
	public function instant80($values) 
	{
        global $Connect, $Account;
        $conn = $Connect->connectToDB();

		die("此功能被禁用。 <br/><i>还有，你不应该在这里…</i>");

        $values = $conn->escape_string($values);
		$values = explode("*", $values);
		
		$Connect->connectToRealmDB($values[1]);
		
		if($this->isOnline($values[0]) == TRUE)
		{
			echo '<b class="red_text">请在继续之前退出游戏。';
		}
		else 
		{
			$service_values = explode("*",$GLOBALS['service']['instant58']);
			if ($service_values[1] == "dp") 
			{
				if($Account->hasDP($_SESSION['cw_user'],$GLOBALS['service']['instant58']['price']) == FALSE) 
				{
					echo '<b class="red_text">钱不够 '.$GLOBALS['donation']['coins_name'].'</b>';
					$error = true;
				}
			} 
			elseif($service_values[1] == "vp") 
			{
				if($Account->hasVP($_SESSION['cw_user'],$GLOBALS['service']['instant58']['price']) == FALSE) 
				{
					echo '<b class="red_text">没有足够的投票积分。</b>';
					$error = true;
				}
			} 

			if ($error != true) 
			{
				//User got coins. Boost them up to 58 :D
				$Connect->connectToRealmDB($values[1]);
				
				$conn->query("UPDATE characters SET level=58 WHERE guid=". $values[0] .";");

				$Account->logThis("立即达到58级 ".$this->getCharName($values[0], NULL), 'Instant', NULL);

				echo '<h3 class="green_text">角色级别被设置为58!</h3>';
			}
		}
 	}

	public function isOnline($char_guid) 
	{
        global $Connect;
        $conn = $Connect->connectToDB();

        $charGuid = $conn->escape_string($char_guid);
        $result    = $conn->query("SELECT COUNT('guid') FROM characters WHERE guid=". $charGuid ." AND online=1;");
        if ($result->data_seek( 0) == 0)
		{
			return FALSE;
		}
		else
		{
 			return TRUE;
		}
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
		if($value == 1)
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

		if(in_array($value,$a))
		{
			return '<img src="styles/global/images/icons/faction/0.gif" />';
		}
		elseif(in_array($value,$h))
		{
			return '<img src="styles/global/images/icons/faction/1.gif" />';
		}
	}
  
  
   public function getCharName($id,$realm_id) 
   	{
        global $Connect;
        $conn = $Connect->connectToDB();

        $ID      = $conn->escape_string($id);
        $realmID = $conn->escape_string($realm_id);
		
        $Connect->connectToRealmDB($realmID);

        $result = $conn->query("SELECT name FROM characters WHERE guid=". $ID .";");
        $row    = $result->fetch_assoc();
		return $row['name'];	
	}
}

$Character = new Character(); 