<?php
    define('INIT_SITE', TRUE);
    include "../../includes/misc/headers.php";
    include "../../includes/configuration.php";
    include "../functions.php";

    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();

    $GameServer->selectDB("logondb", $conn);

    # Organized Alphabeticaly

    switch ($_POST['action'])
    {
        case "addAccA":
        {
            $user  = $conn->escape_string($_POST['user']);
            $realm = $conn->escape_string($_POST['realm']);
            $rank  = $conn->escape_string($_POST['rank']);

            $guid = $GameAccount->getAccID($user);

            $conn->query("INSERT INTO account_access VALUES(". $guid .", ". $rank .", ". $realm .");");
            $GameServer->logThis("添加了 GM 帐户访问权限 " . ucfirst(strtolower($GameAccount->getAccName($guid))));
            break;
        }

        case "edit":
        {
            $email    = $conn->escape_string(trim($_POST['email']));
            $password = $conn->escape_string(trim(strtoupper($_POST['password'])));
            $vp       = $conn->escape_string($_POST['vp']);
            $dp       = $conn->escape_string($_POST['dp']);
            $id       = $conn->escape_string($_POST['id']);
            $extended = NULL;

            $chk1 = $conn->query("SELECT COUNT(*) FROM account WHERE email='". $email ."' AND id=". $id .";");
            if ($chk1->data_seek(0) > 1)
            {
                $extended .= "将电子邮件更改为". $email ."<br/>";
            }
            $conn->query("UPDATE account SET email='". $email ."' WHERE id=". $id .";");

            $GameServer->selectDB("webdb", $conn);

            $conn->query("INSERT INTO account_data (id) VALUES(". $id .");");

            $chk2 = $conn->query("SELECT COUNT(*) FROM account_data WHERE vp=". $vp ." AND id=". $id .";");
            if ($chk2->data_seek(0) > 1)
            {
                $extended .= "将投票点更新为 ". $vp ."<br/>";
            }

            $chk3 = $conn->query("SELECT COUNT(*) FROM account_data WHERE dp=". $dp ." AND id=". $id .";");
            if ($chk3->data_seek(0) > 1)
            {
                $extended .= "将捐赠积分更新为 ". $dp ."<br/>";
            }

            $conn->query("UPDATE account_data SET vp=". $vp .", dp =". $dp ." WHERE id=". $id .";");

            if (!empty($password))
            {
                $username = strtoupper(trim($GameAccount->getAccName($id)));

                $password = sha1("". $username .":". $password ."");
                $GameServer->selectDB("logondb", $conn);
                $conn->query("UPDATE account SET sha_pass_hash='". $password ."' WHERE id=". $id .";");
                $conn->query("UPDATE account SET v='0', s='0' WHERE id=". $id .";");
                $extended .= "更改密码<br/>";
            }


            $GameServer->logThis("修改了账户信息 " . ucfirst(strtolower($GameAccount->getAccName($id))), $extended);
            echo "设置已保存。";
            break;
        }

        case "editChar":
        {
            $guid            = $conn->escape_string($_POST['guid']);
            $rid             = $conn->escape_string($_POST['rid']);
            $name            = $conn->escape_string(trim(ucfirst(strtolower($_POST['name']))));
            $class           = $conn->escape_string($_POST['class']);
            $race            = $conn->escape_string($_POST['race']);
            $gender          = $conn->escape_string($_POST['gender']);
            $money           = $conn->escape_string($_POST['money']);
            $GameAccountname = $conn->escape_string($_POST['account']);
            $GameAccountid   = $GameAccount->getAccID($GameAccountname);

            if (empty($guid) || empty($rid) || empty($name) || empty($class) || empty($race))
            {
                exit('错误');
            }

            $GameServer->connectToRealmDB($rid);

            $online = $conn->query("SELECT COUNT(*) FROM characters WHERE guid=". $guid ." AND online=1;");
            if ($online->data_seek(0) > 0)
            {
                exit('角色必须在线才能使任何更改生效！');
            }

            $conn->query("UPDATE characters SET name='". $name ."', class=". $class .", race=". $race .", gender=". $gender .", money=". $money .", account=". $GameAccountid ." WHERE guid=". $guid .";");

            echo '角色被救了！';
            $chk = $conn->query("SELECT COUNT(*) FROM characters WHERE name='". $name ."';");

            if ($chk->data_seek(0) > 1)
            {
                echo '<br/><b>注意:</b> 如果有超过 1 个角色使用此名称，这可能会迫使他们在登录时重命名。';
            }

            $GameServer->logThis("修改后的角色数据 " . $name);

            break;
        }

        case "removeAccA":
        {
            $id = $conn->escape_string($_POST['id']);

            $conn->query("DELETE FROM account_access WHERE id=". $id .";");
            $GameServer->logThis("修改了 GM 帐户访问权限 " . ucfirst(strtolower($GameAccount->getAccName($id))));

            break;
        }

        case "saveAccA":
        {
            $id    = $conn->escape_string($_POST['id']);
            $rank  = $conn->escape_string($_POST['rank']);
            $realm = $conn->escape_string($_POST['realm']);

            $conn->query("UPDATE account_access SET gmlevel=". $rank .", RealmID=". $realm ." WHERE id=". $id .";");
            $GameServer->logThis("修改了帐户访问权限 " . ucfirst(strtolower($GameAccount->getAccName($id))));

            break;
        }

        default:
            # code...
            break;
    }