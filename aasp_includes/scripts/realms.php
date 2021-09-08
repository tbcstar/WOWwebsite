<?php

    define('INIT_SITE', TRUE);
    include('../../includes/misc/headers.php');
    include('../../includes/configuration.php');
    include('../functions.php');

    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();

    $GameServer->selectDB("webdb", $conn);

    
    #                                                                   #
        ############################################################
    #                                                                   #
    if ($GLOBALS['core_expansion'] == 3)
    {
        $guidString = 'playerGuid';
    }
    else
    {
        $guidString = 'guid';
    }

    if ($GLOBALS['core_expansion'] == 3)
    {
        $closedString = 'closed';
    }
    else
    {
        $closedString = 'closedBy';
    }

    if ($GLOBALS['core_expansion'] == 3)
    {
        $ticketString = 'guid';
    }
    else
    {
        $ticketString = 'ticketId';
    }
    

    # Organized Alphabeticaly


    switch ($_POST['action'])
    {

        case "closeTicket":
        {
            $id = $conn->escape_string($_POST['id']);
            $db = $conn->escape_string($_POST['db']);
            $conn->select_db($db);

            $conn->query("UPDATE gm_tickets SET ". $closedString ."=1 WHERE ". $ticketString ."=". $id .";");


            break;
        }

        case "delete":
        {
            $id = $conn->escape_string($_POST['id']);

            $conn->query("DELETE FROM realms WHERE id=". $id .";");

            $GameServer->logThis("删除服务器");

            break;
        }

        case "deleteTicket":
        {
            $id = $conn->escape_string($_POST['id']);
            $db = $conn->escape_string($_POST['db']);
            $conn->select_db($db);

            $conn->query("DELETE FROM gm_tickets WHERE ". $ticketString ."=". $id .";");

            break;
        }
        
        case "edit":
        {
            $id     = $conn->escape_string($_POST['id']);
            $new_id = $conn->escape_string($_POST['new_id']);
            $name   = $conn->escape_string(trim($_POST['name']));
            $host   = $conn->escape_string(trim($_POST['host']));
            $port   = $conn->escape_string($_POST['port']);
            $chardb = $conn->escape_string(trim($_POST['chardb']));

            if (empty($name) || empty($host) || empty($port) || empty($chardb))
                die("<span class='red_text'>请输入所有字段。</span><br/>");

            $GameServer->logThis("更新了服务器信息 " . $name);

            $conn->query("UPDATE realms SET id=". $new_id .", name='". $name ."', host='". $host ."', port='". $port ."', char_database='". $chardb ."' 
                WHERE id=". $id .";");
            return TRUE;

            break;
        }
        
        case "edit_console":
        {
            $id   = $conn->escape_string($_POST['id']);
            $type = $conn->escape_string($_POST['type']);
            $user = $conn->escape_string(trim($_POST['user']));
            $pass = $conn->escape_string(trim($_POST['pass']));

            if (empty($id) || empty($type) || empty($user) || empty($pass))
            {
                die();
            }

            $GameServer->logThis("更新了服务器的控制台信息，服务器ID: " . $id);

            $conn->query("UPDATE realms SET sendType='". $type ."', rank_user='". $user ."', rank_pass='". $pass ."' WHERE id=". $id . ";");
            return TRUE;

            break;
        }
        
        case "getPresetRealms":
        {
            echo '<h3>选择一个服务器</h3><hr/>';
            $GameServer->selectDB('webdb', $conn);

            $result = $conn->query("SELECT id, name, description FROM realms ORDER BY id ASC;");
            while ($row = $result->fetch_assoc())
            {
                echo '<table width="100%">';
                echo '<tr>';
                echo '<td width="60%">';
                echo '<b>' . $row['name'] . '</b>';
                echo '<br/>' . $row['description'];
                echo '</td>';

                echo '<td>';
                echo '<input type="submit" value="选择" onclick="savePresetRealm(' . $row['id'] . ')">';
                echo '</td>';
                echo '</tr>';
                echo '</table>';
                echo '<hr/>';
        }
            break;
        }
        
        case "loadTickets":
        {
            $offline = $conn->escape_string($_POST['offline']);
            $realm   = $conn->escape_string($_POST['realm']);

            $_SESSION['lastTicketRealm']        = $realm;
            $_SESSION['lastTicketRealmOffline'] = $offline;

            if ($realm == "NULL")
                die("<pre>请选择一个服务器。</pre>");

            $GameServer->selectDB($realm, $conn);

            $result = $conn->query("SELECT ". $ticketString .", name, message, createtime, ". $guidString .", ". $closedString ." FROM gm_tickets ORDER BY ticketId DESC;");
            if ($result->num_rows == 0)
                die("<pre>没有找到tickets！</pre>");

            echo "<table class='center'>
                   <tr>
                       <th>ID</th>
                       <th>名字</th>
                       <th>消息</th>
                       <th>创建</th>
                       <th>Ticket状态</th>
                       <th>玩家状态</th>
                       <th>快速工具</th>
                   </tr>";

            while ($row = $result->fetch_assoc())
            {
                $get = $conn->query("SELECT COUNT(online) FROM characters WHERE guid=". $row[$guidString] ." AND online=1;");
                if ($get->data_seek(0) == 0 && $offline == "on")
                {
                    echo '<tr>';
                    echo '<td><a href="?page=tools&selected=tickets&guid=' . $row[$ticketString] . '&database=' . $realm . '">' . $row[$ticketString] . '</td>';
                    echo '<td><a href="?page=tools&selected=tickets&guid=' . $row[$ticketString] . '&database=' . $realm . '">' . $row['name'] . '</td>';
                    echo '<td><a href="?page=tools&selected=tickets&guid=' . $row[$ticketString] . '&database=' . $realm . '">' . substr($row['message'], 0, 15) . '...</td>';
                    echo '<td><a href="?page=tools&selected=tickets&guid=' . $row[$ticketString] . '&database=' . $realm . '">' . date('Y-m-d H:i:s', $row['createtime']) . '</a></td>';

                    if ($row[$closedString] == 1)
                    {
                        echo '<td><font color="red">关闭</font></td>';
                    }
                    else
                    {
                        echo '<td><font color="green">打开</font></td>';
                    }

                    $get = $conn->query("SELECT COUNT(online) FROM characters WHERE guid=". $row[$guidString] ." AND online=1;");
                    if ($get->data_seek(0) > 0)
                    {
                        echo '<td><font color="green">在线</font></td>';
                    }
                    else
                    {
                        echo '<td><font color="red">离线</font></td>';
                    }
                    ?> <td><a href="#" onclick="deleteTicket('<?php echo $row[$ticketString]; ?>', '<?php echo $realm; ?>')">删除</a>
                        &nbsp;
                        <?php if ($row[$closedString] == 1)
                        {
                            ?>
                            <a href="#" onclick="openTicket('<?php echo $row[$ticketString]; ?>', '<?php echo $realm; ?>')">打开</a>
                        <?php
                        }
                        else
                        {
                            ?>
                            <a href="#" onclick="closeTicket('<?php echo $row[$ticketString]; ?>', '<?php echo $realm; ?>')">关闭</a>
                            <?php
                        }
                        ?>
                    </td><?php
                    echo '<tr>';
                }
            }
            echo '</table>';

            break;
        }
        
        case "openTicket":
        {
            $id = $conn->escape_string($_POST['id']);
            $db = $conn->escape_string($_POST['db']);
            $conn->select_db($db);

            $conn->query("UPDATE gm_tickets SET ". $closedString ."=0 WHERE ". $ticketString ."=". $id .";");

            break;
        }
        
        case "savePresetRealm":
        {
            $rid = $conn->escape_string($_POST['rid']);

            if (isset($_COOKIE['presetRealmStatus']))
            {
                setcookie('presetRealmStatus', "", time() - 3600 * 24 * 30 * 3, '/');
                setcookie('presetRealmStatus', $rid, time() + 3600 * 24 * 30 * 3, '/');
            }
            else
            {
                setcookie('presetRealmStatus', $rid, time() + 3600 * 24 * 30 * 3, '/');
            }
            break;
        }
        
    }