<?php
#############################
##管理面板加载程序文件 ##
## ------------------------##
#############################

    require('../includes/misc/headers.php'); //Load headers

	define('INIT_SITE', TRUE);
	include('../includes/configuration.php');

    if ($GLOBALS['adminPanel_enable'] == FALSE)
      exit();

	require('../includes/misc/compress.php'); //加载压缩文件
	include('../aasp_includes/functions.php');

    global $GameServer, $GameAccount, $GamePage;

    $conn = $GameServer->connect();

    if (isset($_SESSION['cw_admin']) && !isset($_SESSION['cw_admin_id']) && $_GET['page'] != 'notice')
        header("位置： ?page=notice&error= 看起来好像没有创建会话!以避免对该站点造成任何威胁，将断开您的连接。");
?>