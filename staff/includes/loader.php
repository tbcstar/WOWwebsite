<?php

#############################
## 员工面板加载头文件 ##
## ------------------------##
#############################

	require "../includes/misc/headers.php"; //Load headers

	define('INIT_SITE', true);
	include "../includes/configuration.php";

    if ( DATA['admin']['enabled'] == false ) exit();

	require "../includes/misc/compress.php"; //Load compression file
    include "../aasp_includes/functions.php";

    global $GameServer, $GameAccount, $GamePage;

    $conn = $GameServer->connect();

    if (isset($_SESSION['cw_staff']) && !isset($_SESSION['cw_staff_id']) && $_GET['page'] != 'notice')
    {
        header("Location: ?page=notice&error=看起来好像没有创建会话!您已注销，以防止对该网站的任何威胁。");
    }