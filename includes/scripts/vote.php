<?php

require('../ext_scripts_class_loader.php');

if (isset($_POST['siteid']))
{

  global $Connect, $Account, $Website;
  $conn = $Connect->connectToDB();
	
  $siteid = $conn->escape_string($_POST['siteid']);
	
  $Connect->selectDB("webdb", $conn);
	
  if ($Website->checkIfVoted($siteid, $GLOBALS['connection']['webdb']))
  {
    die("?p=vote");
  }

  $Connect->selectDB('webdb', $conn);
  $check = $conn->query("SELECT COUNT(*) FROM votingsites WHERE id=". $siteid .";");
  if ($check->data_seek(0) == 0)
  {
    die("?p=vote");
  }

  if ($GLOBALS['vote']['type'] == "instant")
  {
    $acct_id = $Account->getAccountID($_SESSION['cw_user']);

    if (empty($acct_id))
    {
      exit();
    }

    $next_vote = time() + $GLOBALS['vote']['timer'];

    $Connect->selectDB("webdb", $conn);
	
    $conn->query("INSERT INTO votelog (`siteid`, `userid`, `timestamp`, `next_vote`, `ip`) VALUES (". $siteid .", ". $acct_id .", '". time() ."', ". $next_vote .", '". $_SERVER['REMOTE_ADDR'] ."');");

    $getSiteData = $conn->query("SELECT points, url FROM votingsites WHERE id=". $siteid .";");
    $row         = $getSiteData->fetch_assoc($getSiteData);
		
    //Update the points table.
    $add = $row['points'] * $GLOBALS['vote']['multiplier'];
    $conn->query("UPDATE account_data SET vp=vp + ". $add ." WHERE id=". $acct_id .";");

    echo $row['url'];
  }
  elseif ($GLOBALS['vote']['type'] == 'confirm')
  {
    $Connect->selectDB('webdb', $conn);
    $getSiteData = $conn->query("SELECT points, url FROM votingsites WHERE id=". $siteid .";");
    $row = $getSiteData->fetch_assoc($getSiteData);


    $_SESSION['votingUrlID'] = $conn->escape_string($_POST['siteid']);

    echo $row['url'];
  }
  else
  {
    die("错误!");
  }
} 