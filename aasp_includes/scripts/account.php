<?php
    define('INIT_SITE', TRUE);
    include('../../includes/misc/headers.php');
    include('../../includes/configuration.php');
    include('../functions.php');

    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();

    $GameServer->selectDB("logondb", $conn);

    # Organized Alphabeticaly

    switch ($_POST['action'])
    {
        case "addAccA":
        {
            $user  = mysqli_real_escape_string($conn, $_POST['user']);
            $realm = mysqli_real_escape_string($conn, $_POST['realm']);
            $rank  = mysqli_real_escape_string($conn, $_POST['rank']);

            $guid = $GameAccount->getAccID($user);

            mysqli_query($conn, "INSERT INTO account_access VALUES(". $guid .", ". $rank .", ". $realm .");");
            $GameServer->logThis("添加了 GM 帐户访问权限 " . ucfirst(strtolower($GameAccount->getAccName($guid))));
            break;
        }

        case "edit":
        {
            $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
            $password = mysqli_real_escape_string($conn, trim(strtoupper($_POST['password'])));
            $vp       = mysqli_real_escape_string($conn, $_POST['vp']);
            $dp       = mysqli_real_escape_string($conn, $_POST['dp']);
            $id       = mysqli_real_escape_string($conn, $_POST['id']);
            $extended = NULL;

            $chk1 = mysqli_query($conn, "SELECT COUNT(*) FROM account WHERE email='". $email ."' AND id=". $id .";");
            if (mysqli_data_seek($chk1, 0) > 1)
            {
                $extended .= "将电子邮件更改为". $email ."<br/>";
            }
            mysqli_query($conn, "UPDATE account SET email='". $email ."' WHERE id=". $id .";");

            $GameServer->selectDB('webdb', $conn);

            mysqli_query($conn, "INSERT INTO account_data (id) VALUES(". $id .");");

            $chk2 = mysqli_query($conn, "SELECT COUNT(*) FROM account_data WHERE vp=". $vp ." AND id=". $id .";");
            if (mysqli_data_seek($conn, $chk2, 0) > 1)
            {
                $extended .= "将投票点更新为 ". $vp ."<br/>";
            }

            $chk3 = mysqli_query($conn, "SELECT COUNT(*) FROM account_data WHERE dp=". $dp ." AND id=". $id .";");
            if (mysqli_data_seek($conn, $chk3, 0) > 1)
            {
                $extended .= "将捐赠积分更新为 ". $dp ."<br/>";
            }

            mysqli_query($conn, "UPDATE account_data SET vp=". $vp .", dp =". $dp ." WHERE id=". $id .";");

            if (!empty($password))
            {
                $username = strtoupper(trim($GameAccount->getAccName($id)));

                $password = sha1("". $username .":". $password ."");
                $GameServer->selectDB('logondb', $conn);
                mysqli_query($conn, "UPDATE account SET sha_pass_hash='". $password ."' WHERE id=". $id .";");
                mysqli_query($conn, "UPDATE account SET v='0', s='0' WHERE id=". $id .";");
                $extended .= "更改密码<br/>";
            }


            $GameServer->logThis("修改了账户信息 " . ucfirst(strtolower($GameAccount->getAccName($id))), $extended);
            echo "设置已保存。";
            break;
        }

        case "editChar":
        {
            $guid            = mysqli_real_escape_string($conn, $_POST['guid']);
            $rid             = mysqli_real_escape_string($conn, $_POST['rid']);
            $name            = mysqli_real_escape_string($conn, trim(ucfirst(strtolower($_POST['name']))));
            $class           = mysqli_real_escape_string($conn, $_POST['class']);
            $race            = mysqli_real_escape_string($conn, $_POST['race']);
            $gender          = mysqli_real_escape_string($conn, $_POST['gender']);
            $money           = mysqli_real_escape_string($conn, $_POST['money']);
            $GameAccountname = mysqli_real_escape_string($conn, $_POST['account']);
            $GameAccountid   = $GameAccount->getAccID($GameAccountname);

            if (empty($guid) || empty($rid) || empty($name) || empty($class) || empty($race))
            {
                exit('错误');
            }

            $GameServer->connectToRealmDB($rid);

            $online = mysqli_query($conn, "SELECT COUNT(*) FROM characters WHERE guid=". $guid ." AND online=1;");
            if (mysqli_data_seek($online, 0) > 0)
            {
                exit('角色必须在线才能使任何更改生效！');
            }

            mysqli_query($conn, "UPDATE characters SET name='". $name ."', class=". $class .", race=". $race .", gender=". $gender .", money=". $money .", account=". $GameAccountid ." WHERE guid=". $guid .";");

            echo '角色被救了！';
            $chk = mysqli_query($conn, "SELECT COUNT(*) FROM characters WHERE name='". $name ."';");

            if (mysqli_data_seek($chk, 0) > 1)
            {
                echo '<br/><b>注意:</b> 如果有超过 1 个角色使用此名称，这可能会迫使他们在登录时重命名。';
            }

            $GameServer->logThis("修改后的角色数据 " . $name);

            break;
        }

        case "removeAccA":
        {
            $id = mysqli_real_escape_string($conn, $_POST['id']);

            mysqli_query($conn, "DELETE FROM account_access WHERE id=". $id .";");
            $GameServer->logThis("修改了 GM 帐户访问权限 " . ucfirst(strtolower($GameAccount->getAccName($id))));

            break;
        }

        case "saveAccA":
        {
            $id    = mysqli_real_escape_string($conn, $_POST['id']);
            $rank  = mysqli_real_escape_string($conn, $_POST['rank']);
            $realm = mysqli_real_escape_string($conn, $_POST['realm']);

            mysqli_query($conn, "UPDATE account_access SET gmlevel=". $rank .", RealmID=". $realm ." WHERE id=". $id .";");
            $GameServer->logThis("修改了帐户访问权限 " . ucfirst(strtolower($GameAccount->getAccName($id))));

            break;
        }

        default:
            # code...
            break;
    }