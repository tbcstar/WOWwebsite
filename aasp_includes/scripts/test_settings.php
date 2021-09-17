<?php

    define('INIT_SITE', TRUE);
    include "../../includes/misc/headers.php";
    include "../../includes/configuration.php";

###############################
if(isset($_POST['test'])) 
{
	$errors = array();
	
	/* 测试连接 */
	if (!$conn = new mysqli(DATA['website']['connection']['host'], DATA['website']['connection']['username'], DATA['website']['connection']['password']))
		$errors[] = "mySQL连接错误。请检查您的设置。";
	else 
	{
	    if (!$Database->conn->select_db(DATA['website']['connection']['name']))
			$errors[] = "数据库错误。无法连接到Web数据库。";
		
		if (!$Database->conn->select_db(DATA['logon']['database']))
			$errors[] = "数据库错误。无法连接到Auth数据库。";
		
		if (!$Database->conn->select_db(DATA['world']['database']))
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