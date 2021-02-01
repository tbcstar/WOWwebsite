<?php

class Connect 
{
	
	public static $connectedTo = NULL;

    public static function connectToDB() 
	{
		if(self::$connectedTo != 'global')
		{
			if ($conn = mysqli_connect($GLOBALS['connection']['host'], $GLOBALS['connection']['user'], $GLOBALS['connection']['password']))
			{
				return $conn; 
			}
			else
			{
				buildError("<b>数据库连接错误:</b> 无法建立连接。错误: ". mysqli_error($conn), NULL);
			}
			self::$connectedTo = 'global';
		}
	}
	 
	public static function connectToRealmDB($realmid) 
	{ 
		self::selectDB('webdb');

		if($GLOBALS['realms'][$realmid]['mysqli_host'] != $GLOBALS['connection']['host'] 
		|| $GLOBALS['realms'][$realmid]['mysqli_user'] != $GLOBALS['connection']['user'] 
		|| $GLOBALS['realms'][$realmid]['mysqli_pass'] != $GLOBALS['connection']['password'])
		{
			$conn = mysqli_connect($GLOBALS['realms'][$realmid]['mysqli_host'],
						$GLOBALS['realms'][$realmid]['mysqli_user'],
						$GLOBALS['realms'][$realmid]['mysqli_pass'])
						or 
						buildError("<b>数据库连接错误:</b> 无法建立到Realm的连接。错误: ". mysqli_error($conn),NULL);
		}
		else
		{
			self::connectToDB();
		}
		mysqli_select_db($conn, $GLOBALS['realms'][$realmid]['chardb']) or 
		buildError("<b>数据库选择错误:</b> 无法选择realm数据库。错误: ". mysqli_error($conn),NULL);
		self::$connectedTo = 'chardb';
	}
	 
	 
	public static function selectDB($db) 
	{
		global $conn;
		 
		switch($db) 
		{
			default: 
				mysqli_select_db($conn, $db);
				break;

			case('logondb'):
				mysqli_select_db($conn, $GLOBALS['connection']['logondb']);
				break;

			case('webdb'):
				mysqli_select_db($conn, $GLOBALS['connection']['webdb']);
				break;

			case('worlddb'):
				mysqli_select_db($conn, $GLOBALS['connection']['worlddb']);
				break;
		}
			return TRUE;
	}
}

	$Connect 	= new Connect();
	$conn 		= $Connect->connectToDB();

	/*************************/
	/* 服务器和服务价格自动设置
	/*************************/
	$realms		= array();
	$service 	= array();

	mysqli_select_db($conn, $connection['webdb']);

	//Realms
	$getRealms = mysqli_query($conn, "SELECT * FROM realms ORDER BY id ASC");
	while($row = mysqli_fetch_assoc($getRealms)) 
	{
		$realms[$row['id']]['id']			= $row['id'];
		$realms[$row['id']]['name']			= $row['name'];
		$realms[$row['id']]['chardb']		= $row['char_db'];
		$realms[$row['id']]['description']	= $row['description'];
		$realms[$row['id']]['port']			= $row['port'];

		$realms[$row['id']]['rank_user']	= $row['rank_user'];
		$realms[$row['id']]['rank_pass']	= $row['rank_pass'];
		$realms[$row['id']]['ra_port']		= $row['ra_port'];
		$realms[$row['id']]['soap_host']	= $row['soap_port'];

		$realms[$row['id']]['host']			= $row['host'];

		$realms[$row['id']]['sendType']		= $row['sendType'];

		$realms[$row['id']]['mysqli_host']	= $row['mysqli_host'];
		$realms[$row['id']]['mysqli_user']	= $row['mysqli_user'];
		$realms[$row['id']]['mysqli_pass']	= $row['mysqli_pass'];
	}

		 //Service prices
	$getServices = mysqli_query($conn, "SELECT enabled,price,currency,service FROM service_prices");
	while($row = mysqli_fetch_assoc($getServices))
	{
		$service[$row['service']]['status']=$row['enabled'];
		$service[$row['service']]['price']=$row['price'];
		$service[$row['service']]['currency']=$row['currency'];
	}


	## Unset Magic Quotes
	if (get_magic_quotes_gpc()) 
	{
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		while (list($key, $val) = each($process)) 
		{
			if (is_array($val) || is_object($val))
			{
				foreach ($val as $k => $v) 
				{
					unset($process[$key][$k]);
					if (is_array($v)) 
					{
						$process[$key][stripslashes($k)] = $v;
						$process[] = &$process[$key][stripslashes($k)];
					} 
					else 
					{
						$process[$key][stripslashes($k)] = stripslashes($v);
					}
				}
			}
		}
		unset($process);
	}