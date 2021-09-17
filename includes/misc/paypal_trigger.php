<?php  
 
define('INIT_SITE', TRUE);
require "../configuration.php";
require "connect.php";

global $Database;
$conn = $Database->connect();

$send = 'cmd=_notify-validate';

if (is_array($_POST) || is_object($_POST))
{
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
}

$head .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$head .= "Content-Type: application/x-www-form-urlencoded\r\n";
$head .= 'Content-Length: '.strlen($send)."\r\n\r\n";

$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

$Database->selectDB("webdb");

if ( $fp !== false )
{
    fwrite($fp, $head.$send);
    $resp = stream_get_contents($fp);
	
    $resp = end(explode("\n", $resp));
	 
    $item_number = $Database->conn->escape_string($_POST['item_number']);
    $reciever = $Database->conn->escape_string($_POST['receiver_email']);

    $values = array
    (
        "item_name"       => $Database->conn->escape_string($item_number['0']),
        "mc_gross"        => $Database->conn->escape_string($_POST['mc_gross']),
        "txn_id"          => $Database->conn->escape_string($_POST['txn_id']),
        "payment_date"    => $Database->conn->escape_string($_POST['payment_date']),
        "first_name"      => $Database->conn->escape_string($_POST['first_name']),
        "last_name"       => $Database->conn->escape_string($_POST['last_name']),
        "payment_type"    => $Database->conn->escape_string($_POST['payment_type']),
        "payer_email"     => $Database->conn->escape_string($_POST['payer_email']),
        "address_city"    => $Database->conn->escape_string($_POST['address_city']),
        "address_country" => $Database->conn->escape_string($_POST['address_country']),
        "custom"          => $Database->conn->escape_string($_POST['custom']),
        "mc_fee"          => $Database->conn->escape_string($_POST['mc_fee']),
        "fecha"           => date("Y-m-d"),
        "payment_status"  => $Database->conn->escape_string($_POST['payment_status'])
    );
	
	if ($resp == 'VERIFIED')
	{
		if ( $reciever != DATA['website']['donation']['paypal_email'] )
		{
			exit();
		}

        $Database->insert("payments_log", $values, array_keys($values));

        $to      = $values['payer_email'];
		$subject = DATA['website']['donation']['email_response'];
		$message = 
		    '您好 '. $values['first_name'] .'
		我们想通知您，您的付款已成功。
		
		如果您需要进一步的帮助，请通过论坛与我们联系。
		------------------------------------------
		Payment email: '. $values['payer_email'] .'
        Payment amount: '. $values['mc_gross'] .'
        Buyer name: '. $values['first_name'] .' '. $values['last_name'] .'
        Payment date: '. $values['payment_date'] .'
		Account ID: '. $values['custom'] .'
		------------------------------------------
		这笔付款保存在我们的日志中。

		感谢您对TBCstar 时光回溯项目的支持。';

		$headers = 'From: ' . DATA['website']['email'] . '' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		
		if ( DATA['website']['donation']['email_response'] == true )
		{
			mail($to, $subject, $message, $headers); 
			if (DATA['website']['donation']['send_response_copy'] == true)
			{
				mail(DATA['website']['donation']['copy_to'], $subject, $message, $headers);
			}
		}

		$res = fgets ($fp, 1024);
		if ( $value['payment_status'] == "Completed" )
		{
			if ( DATA['website']['donation']['type'] == 2 )
			{
				$variables = array($values['custom'], $values['mc_gross'], $values['payer_email'], $values['first_name'], $values['last_name'], $values['mc_gross'], $values['payment_date'], $values['fecha']);
                $columns = array("userid", "paymentstatus", "buyer_email", "firstname", "lastname", "mc_gross", "paymentdate", "datecreation");
                $Database->insert("payments_log", $variables, $columns);
				
				for ($row = 0; $row < count(DATA['website']['donation_list']); $row++)
				{
					$coins = $values['mc_gross'];
					if ($coins == DATA['website']['donation_list'][$row][2])
					{
                        $Database->update("account_data", array("dp" =>"dp +".DATA['website']['donation_list'][$row][1]), array("id" => $values['custom']));
                    }
				}
			}
			elseif ( DATA['website']['donation']['type'] == 2 )
			{
				$coins = ceil($mc_gross);
				$Database->update("account_data", array("dp"=>"dp +".$values['coins']), array("id"=> $values['custom']));
			}
		}
	}
	else if ($resp == 'INVALID')
	{
		if ( DATA['website']['donation']['type'] == 2 )
		{
			$variables = array($values['custom'], $values['payment_status'], $values['payment_status'] . " - INVALID FUUUU " . $values['mc_gross'], $values['payer_email'], $values['first_name'], $values['last_name'], $values['mc_gross'], $values['payment_date'], $values['fecha']);
            $columns = array("userid", "paymentstatus", "buyer_email", "firstname", "lastname", "mc_gross", "paymentdate", "datecreation");
            $Database->insert("payments_log", $variables, $columns);
		}
	
 		mail(DATA['website']['donation']['copy_to'], "INVALID Donation", "付款无效。 信息如下所示：<br/>
			User ID : " . $values['custom'] . "
			Buyer Email: " . $values['payer_email'] . "
			Amount: " . $values['mc_gross'] . " USD
			Date: " . $values['payment_date'] . "
			First name: " . $values['first_name'] . "
			Last name: " . $values['last_name'] . "
			", "From: " . DATA['website']['email'] . "");
		  
	  	mail($values['payer_email'], "你好呀。 很遗憾，您最近一次付款无效。 请与我们联系以获取更多信息。
		  
			Best regards.
			TBCstar 项目组");
	

            $variables = array($values['custom'],$values['payment_status'] . " - INVALID",$values['payer_email'],$values['first_name'],$values['last_name'],$values['mc_gross'],$values['payment_date'],$values['fecha']);
            $columns = array("userid", "paymentstatus", "buyer_email", "firstname", "lastname", "mc_gross", "paymentdate", "datecreation");
            $Database->insert("payments_log", $variables, $columns);
    }
}

fclose ($fp); 