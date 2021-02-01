<?php

define('INIT_SITE', TRUE);
include('../../includes/misc/headers.php');
include('../../includes/configuration.php');

###############################
if(isset($_POST['test'])) 
{
	$errors = array();
	
	/* 测试连接 */
	if(!mysqli_connect($GLOBALS['connection']['host'],$GLOBALS['connection']['user'],
	$GLOBALS['connection']['password'])) 
		$errors[] = "mySQL连接错误。请检查您的设置。";
	else 
	{
		if(!mysqli_select_db($GLOBALS['connection']['webdb']))
			$errors[] = "数据库错误。无法连接到Web数据库。";
		
		if(!mysqli_select_db($GLOBALS['connection']['logondb']))
			$errors[] = "数据库错误。无法连接到Auth数据库。";
		
		if(!mysqli_select_db($GLOBALS['connection']['worlddb']))
			$errors[] = "数据库错误。无法连接到World数据库。";
	}
	
	if (!empty($errors)) 
	{
		if (is_array($errors) || is_object($errors))
		{
			foreach($errors as $error) 
			{
				echo  "<strong>*", $error ,"</strong><br/>";
			}
		}
	} 
	else
		echo "没有错误发生。设置是正确的。";
}
###############################
?>