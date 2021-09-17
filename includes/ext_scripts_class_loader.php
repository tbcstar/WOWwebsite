<?php
	//这个文件的存在只是为了从所有的/脚本文件中清除一些空间。
	session_start();
	
	define('INIT_SITE', TRUE); //初始化配置
	
    require "../configuration.php";
    require "../misc/connect.php";
    require "../misc/func_lib.php";
    require "../classes/account.php";
    require "../classes/website.php";
    require "../classes/character.php";
    require "../classes/server.php";
	
	global $Database;

	$Database->connect();