<?php 

require('../ext_scripts_class_loader.php');

if($_POST['action']=='unstuck') 
{
	$guid = (int)$_POST['guid'];
	$realm_id = server::getRealmId($_POST['char_db']);
	connect::connectToRealmDB($realm_id);
	
	character::unstuck($guid,$_POST['char_db']);
}	

if($_POST['action']=='revive') 
{
	$guid = (int)$_POST['guid'];
	$realm_id = server::getRealmId($_POST['char_db']);
	connect::connectToRealmDB($realm_id);
	
	character::revive($guid,$_POST['char_db']);
}	

if ($_POST['action']=='getLocations') 
{
	$values = explode('*',$_POST['values']);
	
	$char = mysql_real_escape_string($values[0]);
	$realm_id = server::getRealmId($values[1]);
	connect::connectToRealmDB($realm_id);
	
	$result = mysql_query("SELECT race FROM characters WHERE guid='".$char."'");
	$row = mysql_fetch_assoc($result);
	$alliance = array(1,3,4,7,11);
	if (in_array($row['race'],$alliance)) 
	{
		//联盟
		$locations_name = array( 1  => "Stormwind" , 2 => "Ironforge", 3 => "Darnassus", 4 => "The Exodar", 5 => "Dalaran", 6 => "Shattrath");
        $locations_image = array("Stormwind" => "spell_arcane_teleportstormwind", "Ironforge" => "spell_arcane_teleportironforge", "Darnassus"  => "spell_arcane_teleportdarnassus", 
		"The Exodar" => "spell_arcane_teleportexodar","Dalaran" => "spell_arcane_teleportdalaran", "Shattrath" => "spell_arcane_teleportshattrath");
	} else {
		//部落
		$locations_name = array( 1  => "Orgrimmar" , 2 => "Undercity", 3 => "Thunder Bluff", 4 => "Silvermoon", 5 => "Dalaran", 6 => "Shattrath");
        $locations_image = array("Orgrimmar" => "spell_arcane_teleportorgrimmar", "Undercity" => "spell_arcane_teleportundercity", 
		"Thunder Bluff"  => "spell_arcane_teleportthunderbluff", "Silvermoon" => "spell_arcane_teleportsilvermoon", "Dalaran" => "spell_arcane_teleportdalaran", 
		"Shattrath" => "spell_arcane_teleportshattrath");
	}
	echo '<h3>选择位置</h3>';
	foreach ($locations_name as $v) 
	{
	 ?>
        <div class="charBox" style="cursor:pointer;" onclick="portTo('<?php echo $v; ?>','<?php echo $values[1]; ?>','<?php echo $values[0]; ?>')">
        <table width="100%">
               <tr> <td width="15%"><img src="styles/global/images/icons/<?php echo $locations_image[$v]?>.jpg" /></td>
               <td align="left" width="90%"><b><?php echo $v; ?></b><br/>
                </td>
               </tr>
        </table>
        </div>
<?php }
}

if ($_POST['action']=='teleport') 
{
	$character = mysql_real_escape_string($_POST['character']);
	$char_db = mysql_real_escape_string($_POST['char_db']);
	$location = mysql_real_escape_string($_POST['location']);
	
    $realm_id = server::getRealmId($_POST['char_db']);
	connect::connectToRealmDB($realm_id);
	$result = mysql_query("SELECT race,account,level,online FROM characters WHERE guid='".$character."'");
    
	if (mysql_num_rows($result) == 0)
		die("<span class='alert'>该帐户中不存在该角色！</span>");
		
	else 
	{
	$row = mysql_fetch_assoc($result);
	
	if($row['online']==1)
		die("请先退出游戏，然后再继续。");
	
	$acct = $row['account'];
	$race = $row['race'];
	$level = $row['level'];
	
	 if($GLOBALS['service']['teleport']['currency']=="vp" && $GLOBALS['service']['teleport']['price']>0) 
	 {
		 if(account::hasVP($_SESSION['cw_user'],$GLOBALS['service']['teleport']['price'])==FALSE)
		     die("Insufficent Vote Points!");
	 } 
	 elseif($GLOBALS['service']['teleport']['currency']=="dp" && $GLOBALS['service']['teleport']['price']>0) 
	 {
		  if(account::hasDP($_SESSION['cw_user'],$GLOBALS['service']['teleport']['price'])==FALSE)
		     die("Insufficent ".$GLOBALS['donation']['coins_name']."!");
	 }
    	
	$map = $x = $y = $z = NULL;
	
	switch($location)
	{
		//暴风城
		case "Stormwind":
			$map = "0";
			$x = "-8913.23";
			$y = "554.633";
			$z = "93.7944";
			break;
		//铁炉堡
		case "Ironforge":
			$map = "0";
			$x = "-4981.25";
			$y = "-881.542";
			$z = "501.66";
			break;
		//达纳苏斯
		case "Darnassus":
			$map = "1";
			$x = "9951.52";
			$y = "2280.32";
			$z = "1341.39";
			break;
		//埃索达
		case "The Exodar":
			$map = "530";
			$x = "-3987.29";
			$y = "-11846.6";
			$z = "-2.01903";
			break;
		//奥格瑞玛
		case "Orgrimmar":
			$map = "1";
			$x = "1676.21";
			$y = "-4315.29";
			$z = "61.5293";
			break;
		//雷霆崖
		case "Thunder Bluff":
			$map = "1";
			$x = "-1196.22";
			$y = "29.0941";
			$z = "176.949";
			break;
		//幽暗城
		case "Undercity":
			$map = "0";
			$x = "1586.48";
			$y = "239.562";
			$z = "-52.149";
			break;
		//银月城
		case "Silvermoon":
			$map = "530";
			$x = "9473.03";
			$y = "-7279.67";
			$z = "14.2285";
			break;
		//沙塔斯
		case "Shattrath":
			$map = "530";
			$x = "-1863.03";
			$y = "4998.05";
			$z = "-21.1847";
			break;
		//达拉然
		case "Dalaran":
			$map = "571";
			$x = "5812.79";
			$y = "647.158";
			$z = "647.413";
			break;	
	} 

	//disallows factions to use enemy portals
	switch($race)
	{
		//联盟
		case 1:
		case 3:
		case 4:
		case 7:
		case 11:
			if((($location >=5) && ($location <=8)) && ($location != 9))
				die("<span class='alert'>联盟玩家<b>无法</b>传送到部落地区!</span>");	
			break;
		//部落
		case 2:
		case 5:
		case 6:
		case 8:
		case 10:
			if ((($location >=1) && ($location <=4)) && ($location != 9))
				die("<span class='alert'>部落玩家<b>无法</b>传送到联盟地区！</span>");
			break;
		default:
			die("<span class='alert'>这不是有效的种族！</span>");
			break;
	}
	
	if ($location == "Dalaran" && $level < 68)
		die("正在中止...<br/><span class='alert'>你的角色必须达到68级或更高才能传送到诺森德!</span>");

	if($GLOBALS['service']['teleport']['currency']=="vp")
		 account::deductVP(account::getAccountID($_SESSION['cw_user']),$GLOBALS['service']['teleport']['price']);
	elseif($GLOBALS['service']['teleport']['currency']=="dp")
		account::deductDP(account::getAccountID($_SESSION['cw_user']),$GLOBALS['service']['teleport']['price']);
	
	connect::connectToRealmDB($realm_id);
	
	//get pos x, y etc for the logs.
	$result = mysql_query("SELECT position_x, position_y, position_z, map FROM characters WHERE guid='".$character."'"); 
	$pos = mysql_fetch_assoc($result);
	
	$char_x = $pos['position_x'];
	$char_y = $pos['position_y'];
	$char_z = $pos['position_z'];
	$char_map = $pos['map'];
	$from = "X: ".$char_x." - Y: ".$char_y." - Z: ".$char_z." - MAP ID: ".$char_map;
	
    mysql_query("UPDATE characters SET position_x = ".$x.", position_y= ".$y.", position_z = ".$z.", map = ".$map." WHERE account = ".$acct." 
			     AND guid = '".$character."'");
     
	 if($GLOBALS['service']['teleport']['currency']=="vp")
		 echo $GLOBALS['service']['teleport']['price']."投票积分已从您的帐户中扣除。";
	 elseif($GLOBALS['service']['teleport']['currency']=="dp")
		echo $GLOBALS['service']['teleport']['price']." ".$GLOBALS['donation']['coins_name']."已从您的账户中扣除。";
		
		account::logThis("Teleported ".character::getCharName($character,$realm_id)." to ".$location,'Teleport',$realm_id);
	
		echo true;
	}
	
}

if($_POST['action']=='service') 
{
	$guid = (int)$_POST['guid'];
	$realm_id = (int)$_POST['realm_id'];
	$serviceX = mysql_real_escape_string($_POST['service']);
	
	
	if(character::isOnline($guid)==TRUE) 
			die('<b class="red_text">请先退回到登录界面，然后再继续。');
	
	if($GLOBALS['service'][$serviceX]['currency']=='vp')
	{
		if(account::hasVP($_SESSION['cw_user'],$GLOBALS['service'][$serviceX]['price'])==FALSE)
			die('<b class="red_text">投票积分不足！</b>');
	}
	
	if($GLOBALS['service'][$serviceX]['currency']=='dp')
	{
		if(account::hasDP($_SESSION['cw_user'],$GLOBALS['service'][$serviceX]['price'])==FALSE)
			die('<b class="red_text">捐赠积分不足！'.$GLOBALS['donation']['coins_name'].'</b>');
	}
	
	switch($serviceX)
	{
		default:
			die("未知错误");
		break;
		
		case('appearance'):
			$command = "customize";
			$info = "定制角色";
		break;
		
		case('name'):
			$command = "rename";
			$info = "角色重命名";
		break;
		
		case('faction'):
			$command = "changefaction";
			$info = "改变阵营";
		break;
		
		case('race'):
		 	$command = "changerace";
			$info = "改变种族";
		break;
		
	}
	
	connect::selectDB('webdb');
	$getRA = mysql_query("SELECT sendType,host,ra_port,soap_port,rank_user,rank_pass FROM realms WHERE id='".$realm_id."'");
	$row = mysql_fetch_assoc($getRA);
	
	if($row['sendType']=='ra') 
	{
		 require('../misc/ra.php');
		 
		 sendRa("character ".$command." ".character::getCharname($guid,$realm_id),
		 $row['rank_user'],$row['rank_pass'],$row['host'],$row['ra_port']);

    } 
	elseif($row['sendType']=="soap") 
	{
		 require('../misc/soap.php');
		 
		 sendSoap("character ".$command." ".character::getCharname($guid,$realm_id),
		 $row['rank_user'],$row['rank_pass'],$row['host'],$row['soap_port']);
    }
	
	if($GLOBALS['service'][$serviceX]['currency']=='vp')
		account::deductVP(account::getAccountID($_SESSION['cw_user']),$GLOBALS['service'][$serviceX]['price']);
	
	if($GLOBALS['service'][$serviceX]['currency']=='dp')
		account::deductDP(account::getAccountID($_SESSION['cw_user']),$GLOBALS['service'][$serviceX]['price']);
		
		account::logThis("Performed a ".$info." on ".character::getCharName($guid,$realm_id),$serviceX,$realm_id);
	
	echo true;
}

?>