<?php

#############################
## 员工面板加载头文件 ##
## ------------------------##
#############################

	require('../includes/misc/headers.php'); //Load headers

	define('INIT_SITE', TRUE);
	include('../includes/configuration.php');

    if ($GLOBALS['adminPanel_enable'] == FALSE)
        exit();

	require('../includes/misc/compress.php'); //Load compression file
	include('../aasp_includes/functions.php');

    global $GameServer, $GameAccount, $GamePage;

    $conn = $GameServer->connect();

    if (isset($_SESSION['cw_staff']) && !isset($_SESSION['cw_staff_id']) && $_GET['p'] != 'notice')
        header("Location: ?p=notice&e=看起来好像没有创建会话!您已注销，以防止对该网站的任何威胁。");
?>