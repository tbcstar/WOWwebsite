<?php
/**
 * 
 * 项目付款的示例IPN处理程序
 * 
 * 本代码的目的是帮助您理解如何处理即时付款通知
 * 通过AlertPay按钮接收的付款的变量，并将其集成到您的PHP站点中。以下
 * 代码将只处理项目付款。处理订阅的IPNs，请参考适当的
 * 示例代码文件。
 *	
 * 将此代码放入您指定为Alert URL的页面中。
 * 从下面代码中的$_POST对象中读取的变量是预定义的IPN变量和
 * 条件块为您提供了处理IPN变量的逻辑占位符。这是你的责任
 * 根据您的需求编写适当的代码。
 *	
 * 如果您对这个脚本有任何问题或建议，请访问我们的网站:dev.alertpay.com
 * 
 *
 * 此代码和信息是“按原样”提供的，没有担保
 * 包括但不包括在内的任何种类的，无论是明示的还是暗示的
 * 限于对某一特定目的的适用性的默示保证。
 * 
 * @author AlertPay
 * @copyright 2010
 */
 
	//该值是由AlertPay帐户的IPN部分生成的安全代码。请换成你的。
	define("IPN_SECURITY_CODE", "NY86MQyPXGcUXJ2v");
	define("MY_MERCHANT_EMAIL", "youremail@gmail.com");

	//Setting information about the transaction
	$receivedSecurityCode 			= $_POST['ap_securitycode'];
	$receivedMerchantEmailAddress 	= $_POST['ap_merchant'];	
	$transactionStatus 				= $_POST['ap_status'];
	$testModeStatus 				= $_POST['ap_test'];	 
	$purchaseType 					= $_POST['ap_purchasetype'];
	$totalAmountReceived 			= $_POST['ap_totalamount'];
	$feeAmount 						= $_POST['ap_feeamount'];
    $netAmount 						= $_POST['ap_netamount'];
	$transactionReferenceNumber 	= $_POST['ap_referencenumber'];
	$currency 						= $_POST['ap_currency']; 	
	$transactionDate 				= $_POST['ap_transactiondate'];
	$transactionType 				= $_POST['ap_transactiontype'];
	
	//Setting the customer's information from the IPN post variables
	$customerFirstName 		= $_POST['ap_custfirstname'];
	$customerLastName 		= $_POST['ap_custlastname'];
	$customerAddress 		= $_POST['ap_custaddress'];
	$customerCity 			= $_POST['ap_custcity'];
	$customerState 			= $_POST['ap_custstate'];
	$customerCountry 		= $_POST['ap_custcountry'];
	$customerZipCode 		= $_POST['ap_custzip'];
	$customerEmailAddress 	= $_POST['ap_custemailaddress'];
	
	//Setting information about the purchased item from the IPN post variables
	$myItemName 			= $_POST['ap_itemname'];
	$myItemCode 			= $_POST['ap_itemcode'];
	$myItemDescription 		= $_POST['ap_description'];
	$myItemQuantity 		= $_POST['ap_quantity'];
	$myItemAmount 			= $_POST['ap_amount'];
	
	//Setting extra information about the purchased item from the IPN post variables
	$additionalCharges 	= $_POST['ap_additionalcharges'];
	$shippingCharges 	= $_POST['ap_shippingcharges'];
	$taxAmount 			= $_POST['ap_taxamount'];
	$discountAmount 	= $_POST['ap_discountamount'];
	 
	//Setting your customs fields received from the IPN post variables
	$myCustomField_1 = $_POST['apc_1'];
	$myCustomField_2 = $_POST['apc_2'];
	$myCustomField_3 = $_POST['apc_3'];
	$myCustomField_4 = $_POST['apc_4'];
	$myCustomField_5 = $_POST['apc_5'];
	$myCustomField_6 = $_POST['apc_6'];

	if ($receivedMerchantEmailAddress != MY_MERCHANT_EMAIL) {
		// The data was not meant for the business profile under this email address.
		// Take appropriate action 
	}
	else {	
		//Check if the security code matches
		if ($receivedSecurityCode != IPN_SECURITY_CODE) {
			// The data is NOT sent by AlertPay.
			// Take appropriate action.
		}
		else 
		{
			if ($transactionStatus == "Success") 
			{
				if ($testModeStatus == "1") 
				{
					// Since Test Mode is ON, no transaction reference number will be returned.
					// Your site is currently being integrated with AlertPay IPN for TESTING PURPOSES
					// ONLY. Don't store any information in your production database and 
					// DO NOT process this transaction as a real order.
					global $Connect;
                    $conn = $Connect->connectToDB();

					$conn->select_db("craftedcms");

					$conn->query("INSERT INTO paypal_payment_info (userid, paymentstatus, buyer_email, firstname, lastname) 
                        VALUES (". $myCustomField_1 .", '". $transactionStatus ."', '". $customerEmailAddress ."', '". $customerFirstName ."', '". $customerLastName ."');");
                }
				else 
				{
					// This REAL transaction is complete and the amount was paid successfully.
					// Process the order here by cross referencing the received data with your database. 														
					// Check that the total amount paid was the expected amount.
					// Check that the amount paid was for the correct service.
					// Check that the currency is correct.
					// ie: if ($totalAmountReceived == 50) ... etc ...
					// After verification, update your database accordingly.
				}			
			}
			else 
			{
					// Transaction was cancelled or an incorrect status was returned.
					// Take appropriate action.
			}
		}
	} 