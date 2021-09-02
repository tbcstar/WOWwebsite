<?php

require('../ext_scripts_class_loader.php');

global $Connect, $Account;
$conn = $Connect->connectToDB();

$Connect->selectDB('logondb');

if (isset($_POST['register'])) 
{
	$username        = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email           = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password        = mysqli_real_escape_string($conn, trim($_POST['password']));
    $repeat_password = mysqli_real_escape_string($conn, trim($_POST['password_repeat']));
    $captcha         = mysqli_real_escape_string($conn, $_POST['captcha']);
    $raf             = mysqli_real_escape_string($conn, $_POST['raf']);

	$Account->register($username, $email, $password, $repeat_password, $captcha, $raf);
	echo TRUE;
}

if(isset($_POST['check'])) 
{
	if($_POST['check'] == "username") 
	{
		$username = mysqli_real_escape_string($conn, $_POST['value']);

		$result = mysqli_query($conn, "SELECT COUNT(id) FROM account WHERE username='". $username ."';");
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