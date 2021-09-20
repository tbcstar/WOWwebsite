<?php
session_start();
define('INIT_SITE', TRUE);
require('../configuration.php');
require('../misc/connect.php');
require('../classes/account.php');
require('../classes/character.php');
require('../classes/shop.php');

connect::connectToDB();


if($_POST['action']=='removeFromCart') 
{
	unset($_SESSION[$_POST['cart']][$_POST['entry']]);
	return;
}

if($_POST['action']=='addShopitem') 
{
   $entry = (int)$_POST['entry'];
   $shop = mysql_real_escape_string($_POST['shop']);
	
   if(isset($_SESSION[$_POST['cart']][$entry]))
		$_SESSION[$_POST['cart']][$entry]['quantity']++;
   else
   {
	connect::selectDB('webdb');

	$result = mysql_query('SELECT entry,price FROM shopitems WHERE entry="'.$entry.'" AND in_shop="'.$shop.'"');
	if(mysql_num_rows($result)!=0) 
	{
		$row = mysql_fetch_array($result);
		$_SESSION[$_POST['cart']][$row['entry']] = array("quantity" => 1, "price" => $row['price']);
	} 
  }
}

if($_POST['action']=='clear') 
{
	unset($_SESSION['donateCart']);
	unset($_SESSION['voteCart']);
}

if($_POST['action']=='getMinicart') 
{
	$num = 0;
	$totalPrice = 0;
	
	if($_POST['cart']=="donateCart")
	   $curr = $GLOBALS['donation']['coins_name'];
	else 
	   $curr = "Vote Points"; 
	
	if(!isset($_SESSION[$_POST['cart']]))
	{
		echo "<b>显示购物车:</b> 0 物品 (0 ".$curr.")";
		exit();
	}
	
	connect::selectDB('webdb');
	foreach($_SESSION[$_POST['cart']] as $entry => $value) 
	{
		    $num = $num + $_SESSION[$_POST['cart']][$entry]['quantity'];
			
			$shop_filt = substr($_POST['cart'],0,-4);

			$result = mysql_query("SELECT price FROM shopitems WHERE entry='".$entry."' AND in_shop='".mysql_real_escape_string($shop_filt)."'");
			$row = mysql_fetch_assoc($result);

			
			$totalPrice = $totalPrice + ( $_SESSION[$_POST['cart']][$entry]['quantity'] * $row['price'] );
	  }

	echo "<b>显示购物车:</b> ".$num." 物品 (".$totalPrice." ".$curr.")";
}

if($_POST['action']=='saveQuantity') 
{
	if($_POST['quantity']==0)
		unset($_SESSION[$_POST['cart']][$_POST['entry']]);
	else	
	    $_SESSION[$_POST['cart']][$_POST['entry']]['quantity'] = $_POST['quantity'];
}

if($_POST['action']=='checkout') 
{
	$totalPrice = 0;
	
	$values = explode('*',$_POST['values']);
	
	connect::selectDB('webdb');
	require("../misc/ra.php");
	require("../misc/soap.php");
	
	if(isset($_SESSION['donateCart'])) 
	{
	 #####捐赠购物车
	 foreach($_SESSION['donateCart'] as $entry => $value) 
	 {
		$result = mysql_query("SELECT price FROM shopitems WHERE entry='".$entry."' AND in_shop='donate'");
		$row = mysql_fetch_assoc($result);
		
		$add = $row['price'] * $_SESSION['donateCart'][$entry]['quantity'];
		
		$totalPrice = $totalPrice + $add;
	  }
	
			
	  if(account::hasDP($_SESSION['cw_user'],$totalPrice)==FALSE)
		  die("你的积分不足".$GLOBALS['donation']['coins_name']."!");

	  $host      = $GLOBALS['realms'][$values[1]]['host'];
	  $rank_user = $GLOBALS['realms'][$values[1]]['rank_user'];
	  $rank_pass = $GLOBALS['realms'][$values[1]]['rank_pass'];
	  $ra_port   = $GLOBALS['realms'][$values[1]]['ra_port'];
	  $soap_port = $GLOBALS['realms'][$values[1]]['soap_port'];
	  
	  foreach($_SESSION['donateCart'] as $entry => $value) 
	  {
		  if($_SESSION['donateCart'][$entry]['quantity']>12) 
		  {
			$num = $_SESSION['donateCart'][$entry]['quantity'];
			
			while($num>0) 
			{
				if($num>12) 
				$command = "send items ".character::getCharname($values[0],$values[1])." \"您购买的物品\" \"感谢您对我们的支持！\" ".$entry.":12 ";
				else
				$command = "send items ".character::getCharname($values[0],$values[1])." \"您购买的物品\" \"感谢您对我们的支持！\" ".$entry.":".$num." ";
			 shop::logItem("donate",$entry,$values[0],account::getAccountID($_SESSION['cw_user']),$values[1],$num);
			 sendSoap($command,$rank_user,$rank_pass,$host,$soap_port);	
			 
				$num = $num - 12;
				} 
			 
		  } 
		  else 
		  {
		    $command = "send items ".character::getCharname($values[0],$values[1])." \"您购买的物品\" \"感谢您对我们的支持！\" ".$entry.":".$_SESSION['donateCart'][$entry]['quantity']." ";
			shop::logItem("donate",$entry,$values[0],account::getAccountID($_SESSION['cw_user']),$values[1],$_SESSION['donateCart'][$entry]['quantity']);
		    sendSoap($command,$rank_user,$rank_pass,$host,$soap_port);	
		  }
	  }
	  
	   account::deductDP(account::getAccountID($_SESSION['cw_user']),$totalPrice);
	   unset($_SESSION['donateCart']);
	}
   ######
   
   if(isset($_SESSION['voteCart'])) 
   {
	 #####捐赠购物车
	 foreach($_SESSION['voteCart'] as $entry => $value) 
	 {
		$result = mysql_query("SELECT price FROM shopitems WHERE entry='".$entry."' AND in_shop='vote'");
		$row = mysql_fetch_assoc($result);

		$add = $row['price'] * $_SESSION['voteCart'][$entry]['quantity'];
		
		$totalPrice = $totalPrice + $add;
	  }
	  
	  if(account::hasVP($_SESSION['cw_user'],$totalPrice)==FALSE)
		  die("你没有足够的投票积分！");

	  $host = $GLOBALS['realms'][$values[1]]['host'];
	  $rank_user = $GLOBALS['realms'][$values[1]]['rank_user'];
	  $rank_pass = $GLOBALS['realms'][$values[1]]['rank_pass'];
	  $ra_port = $GLOBALS['realms'][$values[1]]['ra_port'];
	  
	  foreach($_SESSION['voteCart'] as $entry => $value) 
	  {
		  if($_SESSION['voteCart'][$entry]['quantity']>12) 
		  {
			$num = $_SESSION['voteCart'][$entry]['quantity'];
			
			while($num>0) 
			{
				if($num>12) 
				$command = "send items ".character::getCharname($values[0],$values[1])." \"您购买的物品\" \"感谢您对我们的支持！\" ".$entry.":12 ";
				else
					$command = "send items ".character::getCharname($values[0],$values[1])." \"您购买的物品\" \"感谢您对我们的支持！\" ".$entry.":".$num." ";
				 shop::logItem("vote",$entry,$values[0],account::getAccountID($_SESSION['cw_user']),$values[1],$num);	
		         sendSoap($command,$rank_user,$rank_pass,$host,$soap_port);	
					$num = $num - 12;
				} 
			 
		  } 
		  else 
		  {
		    $command = "send items ".character::getCharname($values[0],$values[1])." \"您购买的物品\" \"感谢您对我们的支持！\" ".$entry.":".$_SESSION['voteCart'][$entry]['quantity']." ";
			shop::logItem("vote",$entry,$values[0],account::getAccountID($_SESSION['cw_user']),$values[1],$_SESSION['voteCart'][$entry]['quantity']); 
		    sendSoap($command,$rank_user,$rank_pass,$host,$soap_port);	
		  }
	  }
	  account::deductVP(account::getAccountID($_SESSION['cw_user']),$totalPrice);
	  unset($_SESSION['voteCart']);
   }
   ######
   echo TRUE;
}

if($_POST['action']=='removeItem')
{
	if(account::isGM($_SESSION['cw_user'])==FALSE) 
    	exit();
	
	$entry = (int)$_POST['entry'];
	$shop = mysql_real_escape_string($_POST['shop']);
	
	connect::selectDB('webdb');
	mysql_query("DELETE FROM shopitems WHERE entry='".$entry."' AND in_shop='".$shop."'");
}

if($_POST['action']=='editItem')
{
	if(account::isGM($_SESSION['cw_user'])==FALSE) 
    	exit();
	
	$entry = (int)$_POST['entry'];
	$shop = mysql_real_escape_string($_POST['shop']);
	$price = (int)$_POST['price'];
	
	connect::selectDB('webdb');
	
	if($price > 0)
		mysql_query("UPDATE shopitems SET price='".$price."' WHERE entry='".$entry."' AND in_shop='".$shop."'");
}
?>