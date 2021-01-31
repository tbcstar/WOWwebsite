<?php

function qpc_post($varname)
{
	return trim( stripslashes( (get_magic_quotes_gpc() ) ? $_POST[$varname] : addslashes( $_POST[$varname]) ) );
}

define('THIS_SCRIPT', 'vb_register.php');
$root_path = '../..'.$GLOBALS['forum']['forum_path'];


require_once($root_path . 'global.php');
require_once($root_path . '/includes/class_dm.php');
require_once($root_path  .'/includes/class_dm_user.php');

$userdm = new vB_DataManager_User($vbulletin, ERRTYPE_ARRAY);

$userdm->set('username', qpc_post('username'));
$userdm->set('email', qpc_post('email'));
$userdm->set('password', qpc_post('password'));
$userdm->set('usergroupid',qpc_post('usergroupid'));
$userdm->set('ipaddress', qpc_post('ipaddress'));
$userdm->set('referrerid', qpc_post('referrername'));
$userdm->set('timezoneoffset', qpc_post('timezoneoffset'));
$userdm->set_bitfield('options', 'adminemail', intval(qpc_post('adminemail')));
$userdm->set_bitfield('options', 'showemail', intval(qpc_post('showemail')));
$firstname		= qpc_post('firstname');
$lastname		= qpc_post('lastname');
$dst_setting 	= intval(qpc_post('dst'));

switch ($dst_setting)
{
	case 0:
	case 1:
		$userdm->set_bitfield('options', 'dstonoff', $dst_setting);
		break;

	case 2:
		$userdm->set_bitfield('options', 'dstauto', 1);
		break;
}

#如果有错误(电子邮件没有设置，电子邮件禁止，用户名，等等)，你可以检查错误使用
if (count($userdm->errors)) 
{
	for($i=0; $ierrors; $i++) 
	{
		print "ERROR{$i}:{$userdm->errors[$i]}n";
	}
} 
else 
{
	# 如果一切都好
	$newuserid = $userdm->save();
	echo "1";
} 