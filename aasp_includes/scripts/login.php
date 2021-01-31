<?php
define('INIT_SITE', TRUE);
include('../../includes/misc/headers.php');
include('../../includes/configuration.php');;

global $conn;

###############################
if(isset($_POST['login'])) 
{
	 $username = mysqli_real_escape_string($conn, strtoupper(trim($_POST['username']))); 
	 $password = mysqli_real_escape_string($conn, strtoupper(trim($_POST['password'])));
	 if(empty($username) || empty($password))
		 die("请输入账号和密码。");
	 
		 $password = sha1("".$username.":".$password."");
		 mysqli_select_db($conn, $GLOBALS['connection']['logondb']);
		 
		 $result = mysqli_query($conn, "SELECT COUNT(id) FROM account WHERE username='".$username."' AND 
		 sha_pass_hash = '".$password."'");
		 if(mysqli_result($result,0)==0)
			 die("无效的用户名和密码。");
		 
		 $getId = mysqli_query($conn, "SELECT id FROM account WHERE username='".$username."'");
		 $row = mysqli_fetch_assoc($getId);
		 $uid = $row['id'];
		 $result = mysqli_query($conn, "SELECT gmlevel FROM account_access WHERE id='".$uid."' 
		 AND gmlevel >= '".$GLOBALS[$_POST['panel'].'Panel_minlvl']."'");
		
		 if(mysqli_num_rows($result)==0)
			 die("使用的帐户无法登录!");
			 
		 $rank = mysqli_fetch_assoc($result);	 
		 
		 $_SESSION['cw_'.$_POST['panel']]=ucfirst(strtolower($username));
		 $_SESSION['cw_'.$_POST['panel'].'_id']=$uid;
		 $_SESSION['cw_'.$_POST['panel'].'_level']=$rank['gmlevel'];
		 
		 if(empty($_SESSION['cw_'.$_POST['panel']]) || empty($_SESSION['cw_'.$_POST['panel'].'_id'])
		 || empty($_SESSION['cw_'.$_POST['panel'].'_level']))
		 	die('脚本遇到错误。(将1个或多个会话定义为null)');
		 
		 sleep(1);
		 die(TRUE);
  }
###############################  
?>