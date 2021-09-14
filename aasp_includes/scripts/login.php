<?php

    define('INIT_SITE', TRUE);
    include "../../includes/misc/headers.php";
    include "../../includes/configuration.php";
    include "../functions.php";

    global $GameServer;
    $conn = $GameServer->connect();

###############################
    if (isset($_POST['login']))
    {
        if (empty($_POST['username']) || empty($_POST['password']) &&
            !isset($_POST['username']) || !isset($_POST['password']))
        {
            die("请输入账号和密码。");
        }

        $username     = $conn->escape_string(strtoupper(trim($_POST['username'])));
        $password     = $conn->escape_string(strtoupper(trim($_POST['password'])));
        $passwordHash = sha1("". $username .":". $password ."");

        if($conn->select_db($GLOBALS['connection']['logon']['database']) == false)
        {
            die("数据库错误。 (". $conn->error.")");
        }
        $result = $conn->query("SELECT COUNT(id) FROM account WHERE username='". $username ."' AND sha_pass_hash = '". $passwordHash ."';");

        $getId    = $conn->query("SELECT id FROM account WHERE username='". $username ."';");
        $row      = $getId->fetch_assoc();
        $uid      = $row['id'];
        $result   = $conn->query("SELECT gmlevel FROM account_access WHERE id=$uid AND gmlevel>=". $GLOBALS[$_POST['panel'] . 'Panel_minlvl'] .";");

        if ($result->num_rows == 0)
        {
            die("指定的帐号无法登录!" );
        }

        $rank = $result->fetch_assoc();

        $_SESSION['cw_' . $_POST['panel']]            = ucfirst(strtolower($username));
        $_SESSION['cw_' . $_POST['panel'] . '_id']    = $uid;
        $_SESSION['cw_' . $_POST['panel'] . '_level'] = $rank['gmlevel'];

        if (empty($_SESSION['cw_' . $_POST['panel']]) || empty($_SESSION['cw_' . $_POST['panel'] . '_id']) || empty($_SESSION['cw_' . $_POST['panel'] . '_level']))
        {
            die('脚本遇到了一个错误。(1个或多个会话被设置为空)');
        }

        sleep(1);
        die(TRUE);
    }
###############################  
?>