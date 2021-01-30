<?php
define('INIT_SITE', TRUE);
include('../../includes/misc/headers.php');
include('../../includes/configuration.php');
include('../functions.php');
$server = new server;
$account = new account;

$server->selectDB('logondb');

###############################
if($_POST['action']=='edit') 
{
	$email = mysql_real_escape_string(trim($_POST['email']));
	$password = mysql_real_escape_string(trim(strtoupper($_POST['password'])));
	$vp = (int)$_POST['vp'];
	$dp = (int)$_POST['dp'];
	$id = (int)$_POST['id'];
	$extended = NULL;
	
	$chk1 = mysql_query("SELECT COUNT FROM account WHERE email='".$email."' AND id='".$od."'");
	if(mysql_query($chk1,0)>0)
		$extended .= "电子邮件已更改为".$email."<br/>"; 
	
	mysql_query("UPDATE account SET email='".$email."' WHERE id='".$id."'");
	$server->selectDB('webdb');
	
	mysql_query("INSERT IGNORE INTO account_data VALUES('".$id."','','','')");
	
		$chk2 = mysql_query("SELECT COUNT FROM account_data WHERE vp='".$vp."' AND id='".$od."'");
		if(mysql_query($chk2,0)>0)
			$extended .= "投票积分已更新为".$vp."<br/>"; 
			
		$chk3 = mysql_query("SELECT COUNT FROM account_data WHERE dp='".$dp."' AND id='".$od."'");
		if(mysql_query($chk3,0)>0)
			$extended .= "捐赠积分已更新为".$dp."<br/>"; 	
	
	
	mysql_query("UPDATE account_data SET vp='".$vp."', dp ='".$dp."' WHERE id='".$id."'");
	
	if(!empty($password)) 
	{
		$username = strtoupper(trim($account->getAccName($id)));
		
		$password = sha1("".$username.":".$password."");
		$server->selectDB('logondb');
		mysql_query("UPDATE account SET sha_pass_hash='".$password."' WHERE id='".$id."'");
		mysql_query("UPDATE account SET v='0',s='0' WHERE id='".$id."'");
		$extended .= "密码已更改<br/>";
	}
	
	
	$server->logThis("帐户信息已更改为".ucfirst(strtolower($account->getAccName($id))),$extended);
	echo "设置已保存。";
}
###############################
if($_POST['action']=='saveAccA')
{
	$id = (int)$_POST['id'];
	$rank = (int)$_POST['rank'];
	$realm = mysql_real_escape_string($_POST['realm']);
	
	mysql_query("UPDATE account_access SET gmlevel='".$rank."',RealmID='".$realm."' WHERE id='".$id."'");
	$server->logThis("帐户访问权限已更改为".ucfirst(strtolower($account->getAccName($id))));
}
###############################
if($_POST['action']=='removeAccA')
{
	$id = (int)$_POST['id'];
	
	mysql_query("DELETE FROM account_access WHERE id='".$id."'");
	$server->logThis("修改GM帐户访问权限".ucfirst(strtolower($account->getAccName($id))));
}
###############################
if($_POST['action']=='addAccA')
{
	$user = mysql_real_escape_string($_POST['user']);
	$realm = mysql_real_escape_string($_POST['realm']);
	$rank = (int)$_POST['rank'];
	
	$guid = $account->getAccID($user);
	
	mysql_query("INSERT INTO account_access VALUES('".$guid."','".$rank."','".$realm."')");
	$server->logThis("添加了GM帐户访问权限".ucfirst(strtolower($account->getAccName($guid))));
}
###############################
if($_POST['action']=='editChar') 
{
	$guid = (int)$_POST['guid'];
	$rid = (int)$_POST['rid'];
	$name = mysql_real_escape_string(trim(ucfirst(strtolower($_POST['name']))));
	$class = (int)$_POST['class'];
	$race = (int)$_POST['race'];
	$gender = (int)$_POST['gender'];
	$money = (int)$_POST['money'];
	$accountname = mysql_real_escape_string($_POST['account']);
	$accountid = $account->getAccID($accountname);	
		
	if(empty($guid) || empty($rid) || empty($name) || empty($class) || empty($race))
		exit('Error');
	
	$server->connectToRealmDB($rid);	
	
	$onl = mysql_query("SELECT COUNT(*) FROM characters WHERE guid='".$guid."' AND online=1");
	if(mysql_result($onl,0)>0)
		exit('角色必须在线才能使任何更改生效！');
	
	mysql_query("UPDATE characters SET name='".$name."',class='".$class."',race='".$race."',gender='".$gender."', money='".$money."', account='".$accountid."'
	WHERE guid='".$guid."'");
	
	echo '角色已保存！';
	
	$chk = mysql_query("SELECT COUNT(*) FROM characters WHERE name='".$name."'");
	if(mysql_result($chk,0)>1)
		echo '<br/><b>注意：</b似乎有不止一个角色有这个名字，这可能会迫使他们在进入时改变名字。';
	
	$server->logThis("角色数据已更改为 ".$name);
}
###############################
?>