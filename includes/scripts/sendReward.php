<?php

require "../ext_scripts_class_loader.php";

global $Connect, $Account, $Shop, $Character;
$conn = $Connect->connectToDB();

if (isset($_POST['item_entry'])) 
{
	$entry 				= $conn->escape_string($_POST['item_entry']);
	$character_realm 	= $conn->escape_string($_POST['character_realm']);
	$type 				= $conn->escape_string($_POST['send_mode']);
	
	if (empty($entry) || empty($character_realm) || empty($type))
	{
		echo '<b class="red_text">请指定一个角色。</b>';
	}
	else 
	{
		$Connect->selectDB("webdb", $conn);
		
		$realm = explode("*", $character_realm);

		$result         = $conn->query("SELECT price FROM shopitems WHERE entry=". $entry .";");
		$row 			= $result->fetch_assoc();
		$account_id 	= $Account->getAccountIDFromCharId($realm[0], $realm[1]);
		$account_name 	= $Account->getAccountName($account_id);
		
		if ($type=='vote') 
		{
        	if ($Account->hasVP($account_name, $row['price']) == FALSE)
        	{
				die('<b class="red_text">你没有足够的投票积分</b>');
        	}

	    	$Account->deductVP($account_id, $row['price']);
		
		} 
		elseif ($type == 'donate') 
		{
			if ($Account->hasDP($account_name,$row['price']) == FALSE)
			{
				die('<b class="red_text">你的钱不足 '.$GLOBALS['donation']['coins_name'].'</b>');
			}

	        $Account->deductDP($account_id,$row['price']);
		}
		
	   $Shop->logItem($type,$entry, $realm[0], $account_id, $realm[1],1);
       $result  = $conn->query("SELECT * FROM realms WHERE id=". $realm[1] .";");
	   $row 	= $result->fetch_assoc();
	   
	  	if($row['sendType'] == 'ra') 
	  	{
			require "../misc/ra.php";
            require "../classes/character.php";
		  
			sendRa("send items ".$Character->getCharname($realm[0])." \"您购买的物品\" \"感谢您对我们的支持！\" ".$entry." ",
			$row['rank_user'],$row['rank_pass'],$row['host'],$row['ra_port']); 
	  	} 
	  	elseif($row['sendType'] == 'soap') 
	  	{
			require "../misc/soap.php";
            require "../classes/character.php"; 
			 
			sendSoap("send items ".$Character->getCharname($realm[0])." \"您购买的物品\" \"感谢您对我们的支持！\" ".$entry." ",
			$row['rank_user'],$row['rank_pass'],$row['host'],$row['soap_port']);
	  	}
	}
}