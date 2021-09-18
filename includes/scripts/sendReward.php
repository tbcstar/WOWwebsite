<?php

require "../ext_scripts_class_loader.php";

global $Database, $Account, $Shop, $Character, $Server;

if ( isset($_POST['item_entry']) )
{
	$entry 				= $Database->conn->escape_string($_POST['item_entry']);
	$character_realm 	= $Database->conn->escape_string($_POST['character_realm']);
	$type 				= $Database->conn->escape_string($_POST['send_mode']);
	
	if ( empty($entry) || empty($character_realm) || empty($type) )
	{
		echo "<b class=\"red_text\">请指定一个角色。</b>";
	}
	else 
	{
		$Database->selectDB("webdb");
		
		$realm = explode("*", $character_realm);

		$statement      = $Database->select("shopitems", "price", null, "entry='$entry'");
        $result         = $statement->get_result(); 
		$row 			= $result->fetch_assoc();
		$account_id 	= $Account->getAccountIDFromCharId($realm[0], $realm[1]);
		$account_name 	= $Account->getAccountName($account_id);
		
		if ( $type == "vote" )
		{
        	if ( $Account->hasVP($account_name, $row['price']) == FALSE )
        	{
				die("<b class=\"red_text\">你没有足够的投票积分</b>");
        	}

	    	$Account->deductVP($account_id, $row['price']);
		
		} 
		elseif ( $type == "donate" )
		{
			if ( $Account->hasDP($account_name, $row['price']) == FALSE )
			{
				die("<b class=\"red_text\">你的钱不足 ". DATA['website']['donation']['coins_name'] ."</b>");
			}

	        $Account->deductDP($account_id,$row['price']);
		}
		$statement->close();

	    $Shop->logItem($type,$entry, $realm[0], $account_id, $realm[1],1);
        $statement = $Database->select("realms", null, null, "id='". $realm[1]."'");
        $result = $statement->get_result();
	    $row 	= $result->fetch_assoc();
	   
	  	if ( $row['sendType'] == "ra" )
	  	{
            require "../classes/character.php";

			$Server->sendRA("send items ". $Character->getCharname($realm[0]) ." \"您购买的物品\" \"感谢您对我们的支持！\" ". $entry ." ", $row['rank_user'], $row['rank_pass'], $row['host'], $row['ra_port']);
            $statement->close();
	  	} 
        elseif ( $row['sendType'] == "soap" )
	  	{
            require "../classes/character.php"; 

			$Server->sendSoap("send items ". $Character->getCharname($realm[0]) ." \"您购买的物品\" \"感谢您对我们的支持！\" ". $entry ." ", $row['rank_user'], $row['rank_pass'], $row['host'], $row['soap_port']);
            $statement->close();
	  	}
	}
}