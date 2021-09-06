<?php

require('../ext_scripts_class_loader.php');

global $Connect, $Account;
$conn = $Connect->connectToDB();

$Connect->selectDB('logondb', $conn);

if (isset($_POST['register'])) 
{
	$username        = $conn->escape_string(trim($_POST['username']));
    $email           = $conn->escape_string(trim($_POST['email']));
    $password        = $conn->escape_string(trim($_POST['password']));
    $repeat_password = $conn->escape_string(trim($_POST['password_repeat']));
    $captcha         = $conn->escape_string($_POST['captcha']);
    $raf             = $conn->escape_string($_POST['raf']);

	$Account->register($username, $email, $password, $repeat_password, $captcha, $raf);
	echo TRUE;
}

if(isset($_POST['check'])) 
{
	if($_POST['check'] == "username") 
	{
		$username = $conn->escape_string($_POST['value']);

		$result = $conn->query("SELECT COUNT(id) FROM account WHERE username='". $username ."';");
		if(mysqli_data_seek($result, 0) == 1)
		{
			echo "<i class='green_text'>此用户名可用</i>";
		}
		else
		{
			echo "<i class='red_text'>此用户名不可用</i>";
		}
	}
} 