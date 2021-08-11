<?php

class Character 
{
	
	public static function unstuck($guid, $char_db) 
	{
		global $Connect, $conn, $Account;
		$guid 	= (int)$guid;
		$rid 	= $Server->getRealmId($char_db);
		$Connect->connectToRealmDB($rid);
		
        if($this->isOnline($guid) == TRUE)
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

		    connect::connectToRealmDB($rid);
		    $getXYZ = mysqli_query($conn, "SELECT * FROM character_homebind WHERE guid='".$guid."'");
			$row 	= mysqli_fetch_assoc($getXYZ);
			
			$new_x = $row['posX']; 
			$new_y = $row['posY']; 
			$new_z = $row['posZ']; 
			$new_zone = $row['zoneId']; 
			$new_map = $row['mapId'];

			mysqli_query($conn, "UPDATE characters SET position_x='".$new_x."', position_y='".$new_y."', 
			position_z='".$new_z."', zone='".$new_zone."',map='".$new_map."' WHERE guid='".$guid."'");

			$Account->logThis("Performed unstuck on ".$this->getCharName($guid,$rid),'Unstuck',$rid);

			return TRUE;
	  	}
	}
	
	public static function revive($guid,$char_db) 
	{
		global $Connect, $conn, $Server, $Account;
		$guid 	= (int)$guid;
		$rid 	= $Server->getRealmId($char_db);
		$Connect->connectToRealmDB($rid);
		
		if($this->isOnline($guid) == TRUE)
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

			connect::connectToRealmDB($rid);
		    mysqli_query($conn, "DELETE FROM character_aura WHERE (guid = '".$guid."' AND spell = '55164') OR (guid = '".$guid."' AND spell = '20584') OR (guid = '".$guid."' AND spell = '8326')");
			
			$Account->logThis("使复活 ".$this->getCharName($guid,$rid),'Revive',$rid);
			
			return TRUE;
	  	}
	}
	
	public static function instant80($values) 
	{
		global $Connect, $Account, $conn;
		die("此功能被禁用。 <br/><i>还有，你不应该在这里…</i>");
		$values = mysqli_real_escape_string($conn, $values);
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
				mysqli_query($conn, "UPDATE characters SET level='58' WHERE guid = '".$values[0]."'");

				$Account->logThis("立即达到58级 ".$this->getCharName($values[0], NULL), 'Instant', NULL);

				echo '<h3 class="green_text">角色级别被设置为58!</h3>';
			}
		}
 	}

	public static function isOnline($char_guid) 
	{
		global $conn;
		$char_guid 	= (int)$char_guid;
		$result 	= mysqli_query($conn, "SELECT COUNT('guid') FROM characters WHERE guid='".$char_guid."' AND online=1");
		if (mysqli_data_seek($result,0) == 0)
		{
			return FALSE;
		}
		else
		{
 			return TRUE;
		}
	}
  
	public static function getRace($value) 
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
  
	public static function getGender($value) 
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
  
	public static function getClass($value) 
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
  
	public static function getClassIcon($value) 
	{   
		return '<img src="styles/global/images/icons/class/'.$value.'.gif" />';
	}
  
	public static function getFactionIcon($value) 
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
  
  
   public static function getCharName($id,$realm_id) 
   	{
   		global $Connect, $conn;
		$id = (int)$id;
		$Connect->connectToRealmDB($realm_id);
		
		$result = mysqli_query($conn, "SELECT name FROM characters WHERE guid='".$id."'");
		$row = mysqli_fetch_assoc($result);
		return $row['name'];	
	}
}

$Character = new Character(); 