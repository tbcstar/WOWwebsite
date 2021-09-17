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

        $username     = $Database->conn->escape_string(strtoupper(trim($_POST['username'])));
        $password     = $Database->conn->escape_string(strtoupper(trim($_POST['password'])));
        $passwordHash = sha1("". $username .":". $password ."");

        if($Database->conn->select_db(DATA['logon']['database']) == false)
        {
            die("数据库错误。 (". $Database->conn->error.")");
        }

        $result = $Database->select("account", "COUNT(id)", null, "username='$username' AND sha_pass_hash = '$passwordHash'")->get_result();

        $getId    = $Database->select("account", "id", null, "username='$username'")->get_result();
        $row      = $getId->fetch_assoc();
        $uid      = $row['id'];
        $result   = $Database->select("account_access", "gmlevel", null, "id=$uid AND gmlevel>=". DATA[$_POST['panel']]['minlvl'] )->get_result();

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

        die(TRUE);
    }
###############################  
?>