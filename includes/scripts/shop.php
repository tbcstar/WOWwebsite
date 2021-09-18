<?php
session_start();
define('INIT_SITE', TRUE);
require "../configuration.php";
require "../misc/connect.php";
require "../classes/account.php";
require "../classes/character.php";
require "../classes/shop.php";

global $Database, $Account, $Shop, $Character, $Server;


if ( $_POST['action'] == "removeFromCart" )
{
	unset($_SESSION[$_POST['cart']][$_POST['entry']]);
	return;
}

if ( $_POST['action'] == 'addShopitem' )
{
   	$entry  = $Database->conn->escape_string($_POST['entry']);
   	$shop 	= $Database->conn->escape_string($_POST['shop']);
	
   	if ( isset($_SESSION[$_POST['cart']][$entry]) )
   	{
		$_SESSION[$_POST['cart']][$entry]['quantity']++;
   	}
   	else
   	{
		$Database->selectDB("webdb");

		$statement = $Database->select("shopitems", "entry, price", null, "entry='$entry' AND in_shop='$shop'");
        $result = $statement->get_result();
		if ( $result->num_rows != 0 )
		{
			$row = $result->fetch_assoc();
			$_SESSION[$_POST['cart']][$row['entry']] = array("quantity" => 1, "price" => $row['price']);
		}
		$statement->close();
  	}
}

if ( $_POST['action'] == "clear" )
{
	unset($_SESSION['donateCart']);
	unset($_SESSION['voteCart']);
}

if ( $_POST['action'] == "getMinicart" )
{
	$num = 0;
	$totalPrice = 0;
	
	if ( $_POST['cart'] == "donateCart" )
	{
	   $curr = DATA['website']['donation']['coins_name'];
	}
	else
	{
	   $curr = "投票点数"; 
	}

	if ( !isset($_SESSION[$_POST['cart']]) )
	{
		echo "<b>显示购物车</b> 0 物品 (0 $curr)";
		exit();
	}

	$Database->selectDB("webdb");
    if ( is_array($_SESSION[$_POST['cart']]) || is_object($_SESSION[$_POST['cart']]) )
	{
		foreach($_SESSION[$_POST['cart']] as $entry => $value) 
		{
		    $num += $_SESSION[$_POST['cart']][$entry]['quantity'];

			$shop_filt = substr($_POST['cart'],0,-4);

			$shop_filt = $Database->conn->escape_string($shop_filt);

            $statement = $Database->select("shopitems", "price", null, "entry='$entry' AND in_shop='$shop_filt'");
            $result = $statement->get_result();
			$row 	= $result->fetch_assoc();


			$totalPrice += ( $_SESSION[$_POST['cart']][$entry]['quantity'] * $row['price'] );
		}
	}

	echo "<b>显示购物车:</b> $num 物品 ($totalPrice $curr)";
}

if ( $_POST['action'] == "saveQuantity" )
{
	if ( $_POST['quantity'] == 0 )
	{
		unset($_SESSION[$_POST['cart']][$_POST['entry']]);
	}
	else
	{	
	    $_SESSION[$_POST['cart']][$_POST['entry']]['quantity'] = $_POST['quantity'];
	}
}

if ( $_POST['action'] == "checkout" )
{
	$totalPrice = 0;
	
	$values = explode('*',$_POST['values']);
	
	$Database->selectDB("webdb");
	
	if ( isset($_SESSION['donateCart']) )
	{
		#####捐赠购物车
	 	if ( is_array($_SESSION['donateCart']) || is_object($_SESSION['donateCart']) )
	 	{
			foreach($_SESSION['donateCart'] as $entry => $value) 
			{
				$result = $Database->select("shopitems", "price", null, "entry='$entry' AND in_shop='donate'");
				$row = $result->fetch_assoc();

				$add = $row['price'] * $_SESSION['donateCart'][$entry]['quantity'];

				$totalPrice += + $add;
			}
		}


	  	if ( $Account->hasDP($_SESSION['cw_user'], $totalPrice) == FALSE )
        {
			die("你的钱不够 ". DATA['website']['donation']['coins_name'] ."!");
        }

		$host      = DATA['realms'][$values[1]]['host'];
        $rank_user = DATA['realms'][$values[1]]['rank_user'];
        $rank_pass = DATA['realms'][$values[1]]['rank_pass'];
        $ra_port   = DATA['realms'][$values[1]]['ra_port'];
		$soap_port = DATA['realms'][$values[1]]['soap_port'];
	  	
	  	if ( is_array($_SESSION['donateCart']) || is_object($_SESSION['donateCart']) )
	  	{
			foreach($_SESSION['donateCart'] as $entry => $value) 
			{
				if ( $_SESSION['donateCart'][$entry]['quantity'] > 12 )
				{
					$num = $_SESSION['donateCart'][$entry]['quantity'];

					while($num>0) 
					{
						if ( $num > 12 )
						{
						    $command = "send items ". $Character->getCharname($values[0], $values[1]) ." \"Your requested item\" \"Thanks for supporting us!\" $entry:12 ";
                        }
						else
						{
						    $command = "send items ". $Character->getCharname($values[0], $values[1]) ." \"Your requested item\" \"Thanks for supporting us!\" $entry:$num ";
                        }
						$Shop->logItem("donate",$entry,$values[0],$Account->getAccountID($_SESSION['cw_user']),$values[1],$num);
						$Server->sendRA($command, $rank_user, $rank_pass, $host, $ra_port);

						$num = $num - 12;
					} 

				} 
				else 
				{
				    $command = "send items ".$Character->getCharname($values[0],$values[1])." \"Your requested item\" \"Thanks for supporting us!\" ".$entry.":".$_SESSION['donateCart'][$entry]['quantity']." ";
					$Shop->logItem("donate",$entry,$values[0],$Account->getAccountID($_SESSION['cw_user']),$values[1],$_SESSION['donateCart'][$entry]['quantity']);
				    $Server->sendRA($command, $rank_user, $rank_pass, $host, $soap_port);
				}
			}
		}

	   $Account->deductDP($Account->getAccountID($_SESSION['cw_user']),$totalPrice);
	   unset($_SESSION['donateCart']);
	}
   ######
   
   	if ( isset($_SESSION['voteCart']) )
   	{
	 	#####捐赠购物车
	 	if ( is_array($_SESSION['voteCart']) || is_object($_SESSION['voteCart']) )
	 	{
		 	foreach($_SESSION['voteCart'] as $entry => $value) 
		 	{
				$statement = $Database->select("shopitems", "price", null, "entry='$entry' AND in_shop='vote'");
                $result = $statement->get_result();
				$row = $result->fetch_assoc();

				$add = $row['price'] * $_SESSION['voteCart'][$entry]['quantity'];

				$totalPrice = $totalPrice + $add;
				$statement->close();
		  	}
	  	}

	  	if ( $Account->hasVP($_SESSION['cw_user'], $totalPrice) == FALSE )
	  	{
			die("你没有足够的投票积分!");
	  	}

		$host		= DATA['realms'][$values[1]]['host'];
		$rank_user	= DATA['realms'][$values[1]]['rank_user'];
		$rank_pass	= DATA['realms'][$values[1]]['rank_pass'];
		$ra_port	= DATA['realms'][$values[1]]['ra_port'];
		$soap_port  = DATA['realms'][$values[1]]['soap_port'];

		if ( is_array($_SESSION['voteCart']) || is_object($_SESSION['voteCart']) )
		{
			foreach($_SESSION['voteCart'] as $entry => $value) 
			{
				if ( $_SESSION['voteCart'][$entry]['quantity'] > 12 )
				{
					$num = $_SESSION['voteCart'][$entry]['quantity'];

					while($num > 0) 
					{
						if ( $num > 12 )
						{
							$command = "send items ".$Character->getCharname($values[0],$values[1])." \"Your requested item\" \"Thanks for supporting us!\" ".$entry.":12 ";
						}
						else
						{
							$command = "send items ".$Character->getCharname($values[0],$values[1])." \"Your requested item\" \"Thanks for supporting us!\" ".$entry.":".$num." ";
						}
						$Shop->logItem("vote",$entry,$values[0],$Account->getAccountID($_SESSION['cw_user']),$values[1],$num);	
				        $Server->sendRA($command, $rank_user, $rank_pass, $host, $soap_port);
						$num = $num - 12;
					} 

			  	} 
				else 
				{
				    $command = "send items ".$Character->getCharname($values[0],$values[1])." \"Your requested item\" \"Thanks for supporting us!\" ".$entry.":".$_SESSION['voteCart'][$entry]['quantity']." ";
					$Shop->logItem("vote",$entry,$values[0],$Account->getAccountID($_SESSION['cw_user']),$values[1],$_SESSION['voteCart'][$entry]['quantity']); 
				    $Server->sendRA($command, $rank_user, $rank_pass, $host, $soap_port);
				}
	  		}
	  	}
	  	$Account->deductVP($Account->getAccountID($_SESSION['cw_user']),$totalPrice);
	  	unset($_SESSION['voteCart']);
   	}
   	######
   	echo TRUE;
}

if ( $_POST['action'] == "removeItem" )
{
	if ( $Account->isGM($_SESSION['cw_user']) == FALSE )
	{
		exit();
	}

	$entry = $Database->conn->escape_string($_POST['entry']);
	$shop  = $Database->conn->escape_string($_POST['shop']);

	$Database->selectDB("webdb", $Database->conn);
	$Database->conn->query("DELETE FROM shopitems WHERE entry='$entry' AND in_shop='$shop';");
}

if ( $_POST['action'] == "editItem" )
{
	if ( $Account->isGM($_SESSION['cw_user']) == FALSE )
	{
    	exit();
	}

	$entry = $Database->conn->escape_string($_POST['entry']);
	$shop  = $Database->conn->escape_string($_POST['shop']);
	$price = $Database->conn->escape_string($_POST['price']);
	
	$Database->selectDB("webdb");
	
	if($price > 0)
	{
		$Database->update("shopitems", "price", $price, array("entry", "in_shop"), array($entry, $shop));
	}
} 