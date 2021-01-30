<?php
define('INIT_SITE', TRUE);
require('../configuration.php');
require('connect.php');

$send = 'cmd=_notify-validate';

 foreach ($_POST as $key => $value)
 {
     if(get_magic_quotes_gpc() == 1)
         $value = urlencode(stripslashes($value));
     else
         $value = urlencode($value);
		 
     $send .= "&$key=$value";
 }
 
 $head .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
 $head .= "Content-Type: application/x-www-form-urlencoded\r\n";
 $head .= 'Content-Length: '.strlen($send)."\r\n\r\n";
 $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
 
 connect::selectDB('webdb');

 if ($fp !== false)
 {
    fwrite($fp, $head.$send);
    $resp = stream_get_contents($fp);
	
    $resp = end(explode("\n", $resp));
	 
	$item_number = mysql_real_escape_string($_POST['item_number']);
	$item_name = mysql_real_escape_string($item_number['0']);
	$mc_gross = mysql_real_escape_string($_POST['mc_gross']);
	$txn_id = mysql_real_escape_string($_POST['txn_id']);
	$payment_date = mysql_real_escape_string($_POST['payment_date']);
	$first_name = mysql_real_escape_string($_POST['first_name']);
	$last_name = mysql_real_escape_string($_POST['last_name']);
	$payment_type = mysql_real_escape_string($_POST['payment_type']);
	$payer_email = mysql_real_escape_string($_POST['payer_email']);
	$address_city = mysql_real_escape_string($_POST['address_city']);
	$address_country = mysql_real_escape_string($_POST['address_country']);
	$custom = mysql_real_escape_string($_POST['custom']);
	$mc_fee = mysql_real_escape_string($_POST['mc_fee']);
	$fecha = date("Y-m-d");
	$payment_status = mysql_real_escape_string($_POST['payment_status']);
	$reciever = mysql_real_escape_string($_POST['receiver_email']);		 
	
if ($resp == 'VERIFIED')
{
	if ($reciever!=$GLOBALS['donation']['paypal_email'])
		exit();
			
	mysql_query("INSERT INTO payments_log(userid,paymentstatus,buyer_email,firstname,lastname,city,country,mc_gross,mc_fee,itemname,paymenttype,
	paymentdate,txnid,pendingreason,reasoncode,datecreation) values ('".$custom."','".$payment_status."','".$payer_email."',
	'".$first_name."','".$last_name."','".$address_city."','".$address_country."','".$mc_gross."',
	'".$mc_fee."','".$item_name."','".$payment_type."','".$payment_date."','".$txn_id."','".$pending_reason."',
	'".$reason_code."','".$fecha."')");
				
	$to = $payer_email;
	$subject = $GLOBALS['donation']['emailResponse'];
	$message = '你好 '.$first_name.'
	谨在此通知您，您最近的付款已成功。
	
	如果您需要更多帮助，请通过论坛与我们联系。
	------------------------------------------
	支付账号：'.$payer_email.'
	支付金额：'.$mc_gross.'
	商家名称：'.$first_name.' '.$last_name.'
	付款日期：'.$payment_date.'
	账户ID：'.$custom.'
	------------------------------------------
	这笔付款已保存在我们的日志中。
	
	感谢您，TBCstar项目组。
	';
	$headers = '来自： '.$GLOBALS['default_email'].'' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	
	if ($GLOBALS['donation']['emailResponse']==true) 
	{
		mail($to, $subject, $message, $headers); 
		if ($GLOBALS['donation']['sendResponseCopy']==true)
			mail($GLOBALS['donation']['copyTo'], $subject, $message, $headers); 
	}

	$res = fgets ($fp, 1024);
	if($payment_status=="Completed")
	{
		if($GLOBALS['donation']['donationType']==2)
		{
			mysql_query("INSERT INTO payments_log(userid,paymentstatus,buyer_email,firstname,lastname,mc_gross,paymentdate,datecreation) values ('".$custom."',
			'".$mc_gross."','".$payer_email."','".$first_name."','".$last_name."','".$mc_gross."','".$payment_date."','".$fecha."')");
			
			for ($row = 0; $row < count($GLOBALS['donationList']); $row++)
			{
				$coins = $mc_gross;
				if($coins == $GLOBALS['donationList'][$row][2])
					mysql_query("UPDATE account_data SET dp=dp + ".$GLOBALS['donationList'][$row][1]." WHERE id='".$custom."'");
			}
		}
		elseif($GLOBALS['donation']['donationType']==2)
		{
			$coins = ceil($mc_gross);
			mysql_query("UPDATE account_data SET dp=dp + ".$coins." WHERE id='".$custom."'");
		}
				
 }
}
else if ($resp == 'INVALID')
{
	if($GLOBALS['donation']['donationType']==2)
	{
		 mysql_query("INSERT INTO payments_log(userid,paymentstatus,buyer_email,firstname,
		 lastname,mc_gross,paymentdate,datecreation) values ('".$custom."','".$payment_status." - INVALID FUUUU ".$_POST['mc_gross']."','".$payer_email."',
		 '".$first_name."','".$last_name."','".$mc_gross."','".$payment_date."','".$fecha."')");
	}
	
	
 mail($GLOBALS['donation']['copyTo'],"无效的捐赠","付款无效。信息如下:<br/>
		  用户ID：".$custom."
		  账号：".$payer_email."
		  金额：".$mc_gross." USD
		  日期：".$payment_date."
		  名：".$first_name."
		  姓：".$last_name."
		  ","来自：".$GLOBALS['donation']['responseFrom']."");  
		  
		  mail($payer_email,"你好。不幸的是，你最近的付款无效。请与我们联系以获取更多信息。 
		  
		  致以最亲切的问候。
		  TBCstar项目组");
	
		 mysql_query("INSERT INTO payments_log(userid,paymentstatus,buyer_email,firstname,lastname,mc_gross,paymentdate,datecreation) values ('".$custom."','".$payment_status." - INVALID','".$payer_email."','".$first_name."','".$last_name."','".$mc_gross."','".$payment_date."','".$fecha."')");
    }
 }

 
 fclose ($fp); 
 
?> 