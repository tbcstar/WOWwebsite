<?php
class character {
	
	public static function unstuck($guid,$char_db) 
	{
		$guid = (int)$guid;
		$rid = server::getRealmId($char_db);
		connect::connectToRealmDB($rid);
		
        if(character::isOnline($guid)==TRUE) 
			echo '<b class="red_text">请在继续之前退出游戏。';
		else 
		{
			if($GLOBALS['service']['unstuck']['currency']=='vp')
			{
				if(account::hasVP($_SESSION['cw_user'],$GLOBALS['service']['unstuck']['price'])==FALSE) 
					die('<b class="red_text">没有足够的投票积分!</b>' );
				else
					account::deductVP(account::getAccountID($_SESSION['cw_user']),$GLOBALS['service']['unstuck']['price']);	
		}
		
			if($GLOBALS['service']['unstuck']['currency']=='dp')
			{
				if(account::hasDP($_SESSION['cw_user'],$GLOBALS['service']['unstuck']['price'])==FALSE) 
					die( '<b class="red_text">积分不足'.$GLOBALS['donation']['coins_name'].'</b>' );
				else
					account::deductDP(account::getAccountID($_SESSION['cw_user']),$GLOBALS['service']['unstuck']['price']);
		}
			
		$getXYZ = mysql_query("SELECT * FROM `character_homebind` WHERE `guid`='".$guid."'");
       // $getXYZ = mysql_query("SELECT COUNT('guid') FROM character_homebind WHERE guid='".$guid."' AND online=1");		
		$row = mysql_fetch_assoc($getXYZ);
		
		$new_x = $row['posX']; 
		$new_y = $row['posY']; 
		$new_z = $row['posZ']; 
		$new_zone = $row['zoneId']; 
		$new_map = $row['mapId'];
		
		mysql_query("UPDATE characters SET position_x='".$new_x."', position_y='".$new_y."', position_z='".$new_z."', zone='".$new_zone."',map='".$new_map."' WHERE guid='".$guid."'");
		
		account::logThis("Performed unstuck on ".character::getCharName($guid,$rid),'Unstuck',$rid);
		
		return TRUE;
	  }
	}
	
	public static function revive($guid,$char_db) 
	{
		$guid = (int)$guid;
		$rid = server::getRealmId($char_db);
		connect::connectToRealmDB($rid);
		
		if(character::isOnline($guid)==TRUE) 
			echo '<b class="red_text">请在继续之前退出游戏。';
	    else 
		{
			if($GLOBALS['service']['revive']['currency']=='vp')
			{
				if(account::hasVP($_SESSION['cw_user'],$GLOBALS['service']['unstuck']['price'])==FALSE) 
					die( '<b class="red_text">没有足够的投票积分！</b>' );
				else
					account::deductVP(account::getAccountID($_SESSION['cw_user']),$GLOBALS['service']['revive']['price']);	
			}
		
		if($GLOBALS['service']['revive']['currency']=='dp')
		{
			if(account::hasDP($_SESSION['cw_user'],$GLOBALS['service']['unstuck']['price'])==FALSE) 
				die( '<b class="red_text">积分不足'.$GLOBALS['donation']['coins_name'].'</b>' );
			else
				account::deductDP(account::getAccountID($_SESSION['cw_user']),$GLOBALS['service']['revive']['price']);	
		}
			
		    mysql_query("DELETE FROM character_aura WHERE guid = '".$guid."' AND spell = '20584' OR guid = '".$guid."' AND spell = '8326'");
			
			account::logThis("Performed a revive on ".character::getCharName($guid,$rid),'Revive',$rid);
			
			return TRUE;
	  }
	}
	
	public static function instant80($values) 
	{
		die("此功能被禁用。 <br/>
		<i>还有，你不应该在这里…</i>
		");
		$values = mysql_real_escape_string($values);
		$values = explode("*",$values);
		
		connect::connectToRealmDB($values[1]);
		
		if(character::isOnline($values[0])==TRUE) 
			echo '<b class="red_text">请在继续之前退出游戏。';
		else 
		{
		$service_values = explode("*",$GLOBALS['service']['instant80']);
		if ($service_values[1]=="dp") 
		{
			if(account::hasDP($_SESSION['cw_user'],$GLOBALS['service']['instant80']['price'])==FALSE) 
			{
				echo '<b class="red_text">积分不足'.$GLOBALS['donation']['coins_name'].'</b>';
				$error = true;
			}
		} 
		elseif($service_values[1]=="vp") 
		{
			if(account::hasVP($_SESSION['cw_user'],$GLOBALS['service']['instant80']['price'])==FALSE) 
			{
				echo '<b class="red_text">没有足够的投票积分！</b>';
				$error = true;
			}
		} 
		
		if ($error!=true) 
		{
			//用户有积分，可以提高等级到58级。:D
			connect::connectToRealmDB($values[1]);
			mysql_query("UPDATE characters SET level='58' WHERE guid = '".$values[0]."'");
			
			account::logThis("Performed an instant max level on ".character::getCharName($values[0],NULL),'Instant',NULL);
			
			echo '<h3 class="green_text">等级已提升到58级！</h3>';
		}
	}
 }
 
 public static function isOnline($char_guid) 
 {
	 $char_guid = (int)$char_guid;
	 $result = mysql_query("SELECT COUNT('guid') FROM characters WHERE guid='".$char_guid."' AND online=1");
	 if (mysql_result($result,0)==0) 
		 return FALSE;
	 else 
		 return TRUE;
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
	 if($value==1) 
		 return "女性";
	 elseif($value=9)
		 return "男性";
	 else 
		 return "未知";
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
		   return '<img src="styles/global/images/icons/faction/0.gif" />';
	   elseif(in_array($value,$h)) 
		   return '<img src="styles/global/images/icons/faction/1.gif" />';
  }
  
  
   public static function getCharName($id,$realm_id) 
   {
		$id = (int)$id;
		connect::connectToRealmDB($realm_id);
		
		$result = mysql_query("SELECT name FROM characters WHERE guid='".$id."'");
		$row = mysql_fetch_assoc($result);
		return $row['name'];	
	}
}	
?>