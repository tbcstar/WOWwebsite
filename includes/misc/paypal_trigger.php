<?php  
 
define('INIT_SITE', TRUE);
require('../configuration.php');
require('connect.php');

global $Connect, $conn;

$send = 'cmd=_notify-validate';

foreach ($_POST as $key => $value)
{
	if(get_magic_quotes_gpc() == 1)
	{
	    $value = urlencode(stripslashes($value));
	}
	else
	{
    	$value = urlencode($value);
	}
	 
 	$send .= "&$key=$value";
}

$head .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$head .= "Content-Type: application/x-www-form-urlencoded\r\n";
$head .= 'Content-Length: '.strlen($send)."\r\n\r\n";
$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

$Connect->selectDB('webdb');

if ($fp !== false)
{
    fwrite($fp, $head.$send);
    $resp = stream_get_contents($fp);
	
    $resp = end(explode("\n", $resp));
	 
	$item_number 	= mysqli_real_escape_string($conn, $_POST['item_number']);
	$item_name 		= mysqli_real_escape_string($conn, $item_number['0']);
	$mc_gross 		= mysqli_real_escape_string($conn, $_POST['mc_gross']);
	$txn_id 		= mysqli_real_escape_string($conn, $_POST['txn_id']);
	$payment_date 	= mysqli_real_escape_string($conn, $_POST['payment_date']);
	$first_name 	= mysqli_real_escape_string($conn, $_POST['first_name']);
	$last_name 		= mysqli_real_escape_string($conn, $_POST['last_name']);
	$payment_type 	= mysqli_real_escape_string($conn, $_POST['payment_type']);
	$payer_email 	= mysqli_real_escape_string($conn, $_POST['payer_email']);
	$address_city 	= mysqli_real_escape_string($conn, $_POST['address_city']);
	$address_country = mysqli_real_escape_string($conn, $_POST['address_country']);
	$custom 		= mysqli_real_escape_string($conn, $_POST['custom']);
	$mc_fee 		= mysqli_real_escape_string($conn, $_POST['mc_fee']);
	$fecha 			= date("Y-m-d");
	$payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
	$reciever 		= mysqli_real_escape_string($conn, $_POST['receiver_email']);		 
	
	if ($resp == 'VERIFIED')
	{
		if ($reciever!=$GLOBALS['donation']['paypal_email'])
		{
			exit();
		}
				
		mysqli_query($conn, "INSERT INTO payments_log(userid,paymentstatus,buyer_email,firstname,lastname,city,country,mc_gross,mc_fee,itemname,paymenttype,
		paymentdate,txnid,pendingreason,reasoncode,datecreation) values ('".$custom."','".$payment_status."','".$payer_email."',
		'".$first_name."','".$last_name."','".$address_city."','".$address_country."','".$mc_gross."',
		'".$mc_fee."','".$item_name."','".$payment_type."','".$payment_date."','".$txn_id."','".$pending_reason."',
		'".$reason_code."','".$fecha."')");
					
		$to = $payer_email;
		$subject = $GLOBALS['donation']['emailResponse'];
		$message = 'Hello '.$first_name.'
		We would like to inform you that the recent payment you did was successfull.
		
		If you require further assistance, please contact us via the forums.
		------------------------------------------
		Payment email: '.$payer_email.'
		Payment amount: '.$mc_gross.'
		Buyer name: '.$first_name.' '.$last_name.'
		Payment date: '.$payment_date.'
		Account ID: '.$custom.'
		------------------------------------------
		This payment is saved in our logs.
		
		Thank you, the Management.
		';
		$headers = 'From: '.$GLOBALS['default_email'].'' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		
		if ($GLOBALS['donation']['emailResponse'] == true) 
		{
			mail($to, $subject, $message, $headers); 
			if ($GLOBALS['donation']['sendResponseCopy'] == true)
			{
				mail($GLOBALS['donation']['copyTo'], $subject, $message, $headers); 
			}
		}

		$res = fgets ($fp, 1024);
		if($payment_status=="Completed")
		{
			if($GLOBALS['donation']['donationType'] == 2)
			{
				mysqli_query($conn, "INSERT INTO payments_log(userid,paymentstatus,buyer_email,firstname,lastname,mc_gross,paymentdate,datecreation) values ('".$custom."',
				'".$mc_gross."','".$payer_email."','".$first_name."','".$last_name."','".$mc_gross."','".$payment_date."','".$fecha."')");
				
				for ($row = 0; $row < count($GLOBALS['donationList']); $row++)
				{
					$coins = $mc_gross;
					if($coins == $GLOBALS['donationList'][$row][2])
					{
						mysqli_query($conn, "UPDATE account_data SET dp=dp + ".$GLOBALS['donationList'][$row][1]." WHERE id='".$custom."'");
					}
				}
			}
			elseif($GLOBALS['donation']['donationType'] == 2)
			{
				$coins = ceil($mc_gross);
				mysqli_query($conn, "UPDATE account_data SET dp = dp + ".$coins." WHERE id='".$custom."'");
			}
		}
	}
	else if ($resp == 'INVALID')
	{
		if($GLOBALS['donation']['donationType'] == 2)
		{
			 mysqli_query("INSERT INTO payments_log(userid,paymentstatus,buyer_email,firstname,
			 lastname,mc_gross,paymentdate,datecreation) values ('".$custom."','".$payment_status." - INVALID FUUUU ".$_POST['mc_gross']."','".$payer_email."',
			 '".$first_name."','".$last_name."','".$mc_gross."','".$payment_date."','".$fecha."')");
		}
	
 		mail($GLOBALS['donation']['copyTo'],"INVALID Donation","A payment was invalid. Information is shown below: <br/>
			User ID : ".$custom."
			Buyer Email: ".$payer_email."
			Amount: ".$mc_gross." USD
			Date: ".$payment_date."
			First name: ".$first_name."
			Last name: ".$last_name."
			","From: ".$GLOBALS['donation']['responseFrom']."");  
		  
	  	mail($payer_email,"Hello there. Unfortunately, the latest payment you did was invalid. Please contact us for more information. 
		  
			Best regards.
			The Management");
	
	 	mysqli_query($conn, "INSERT INTO payments_log(userid,paymentstatus,buyer_email,firstname,lastname,mc_gross,paymentdate,datecreation) values ('".$custom."','".$payment_status." - INVALID','".$payer_email."','".$first_name."','".$last_name."','".$mc_gross."','".$payment_date."','".$fecha."')");
    }
}

fclose ($fp); 