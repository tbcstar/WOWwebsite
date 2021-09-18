<?php

require "../ext_scripts_class_loader.php";

if ( isset($_POST['siteid']) )
{

	global $Database, $Account, $Website;
	
	$siteid = $Database->conn->escape_string($_POST['siteid']);
	
    $Database->selectDB("webdb");
	
    if ( $Website->checkIfVoted($siteid))
    {
        die("?page=vote");
    }

	$Database->selectDB("webdb");
	$statement = $Database->select("votingsites", null, null, "id='$siteid'");
	$check = $statement->get_result();
	if ( $check->num_rows == 0 )
	{
		die("?page=vote");
	}
	$statement->close();

    if ( DATA['website']['vote']['type'] == "instant" )
    {
        $account_id = $Account->getAccountID($_SESSION['cw_user']);

		if ( empty($account_id) )
		{
			exit();
		}

		$next_vote = time() + DATA['website']['vote']['timer'];

		$Database->selectDB("webdb");

		$Database->insert("votelog", ["siteid", "userid", "timestamp", "next_vote", "ip"], [$siteid, $account_id, time(), $next_vote, $_SERVER['REMOTE_ADDR']]);

		$statement = $Database->select("votingsites", "points, url", null, "id='$siteid'");
		$getSiteData = $statement->get_result();
		$row         = $getSiteData->fetch_assoc($getSiteData);
		
		//Update the points table.
		$add = $row['points'] * DATA['website']['vote']['multiplier'];
		$Database->update("account_data", array("vp" => "vp + $add"), array("id" => $account_id));

		echo $row['url'];
		$statement->close();
	}
	elseif ( DATA['website']['vote']['type'] == "confirm" )
	{
		$Database->selectDB("webdb");
		$statement = $Database->select("votingsites", "points, url", null, "id='$siteid'");
		$getSiteData = $statement->get_result();
		$row = $getSiteData->fetch_assoc();

		$_SESSION['votingUrlID'] = $Database->conn->escape_string($_POST['siteid']);

		echo $row['url'];
		$statement->close();
	}
	else
	{
		die("错误!");
	}
} 