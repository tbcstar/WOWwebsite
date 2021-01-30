<?php
define('INIT_SITE', TRUE);
include('../../includes/misc/headers.php');
include('../../includes/configuration.php');

###############################
if(isset($_POST['test'])) 
{
	$errors = array();
	
	/* 测试连接 */
	if(!mysql_connect($GLOBALS['connection']['host'],$GLOBALS['connection']['user'],
	$GLOBALS['connection']['password'])) 
		$errors[] = "mySQL连接错误。请检查您的设置。";
	else 
	{
		if(!mysql_select_db($GLOBALS['connection']['webdb']))
			$errors[] = "数据库错误。无法连接到网站数据库。";
		
		if(!mysql_select_db($GLOBALS['connection']['logondb']))
			$errors[] = "数据库错误。无法连接到登录数据库。";
		
		if(!mysql_select_db($GLOBALS['connection']['worlddb']))
			$errors[] = "数据库错误。无法连接到世界数据库。";
	}
	
	if (!empty($errors)) 
	{
			foreach($errors as $error) 
			{
				echo  "<strong>*", $error ,"</strong><br/>";
			}
			
		} 
		else
			echo "没有错误发生。设置是正确的。";
}
###############################
?>