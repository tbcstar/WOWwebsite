<?php

require "../ext_scripts_class_loader.php";

global $Database, $Account;

$Database->selectDB("logondb");

if ( isset($_POST['register']) )
{
	$username        = $Database->conn->escape_string( trim($_POST['username']) );
    $email           = $Database->conn->escape_string( trim($_POST['email']) );
    $password        = $Database->conn->escape_string( trim($_POST['password']) );
    $repeat_password = $Database->conn->escape_string( trim($_POST['password_repeat']) );
    $captcha         = $Database->conn->escape_string( $_POST['captcha'] );
    $raf             = @$Database->conn->escape_string( $_POST['raf'] );

	$Account->register($username, $email, $password, $repeat_password, $captcha, $raf);
	echo TRUE;
}

if ( isset($_POST['check']) && $_POST['check'] == "username" )
{
	$username = $Database->conn->escape_string($_POST['value']);

    $statement = $Database->select("account", null, null, "username='$username'");
    $result = $statement->get_result();
    if ( $result->num_rows > 0 )
    {
        echo "<i class=\"red_text\">此用户名不可用</i>";
    }
    else
	{
        echo "<i class=\"green_text\">此用户名可用</i>";
	}
    $statement->close();
} 