<?php
    define('INIT_SITE', TRUE);
    include('../../includes/misc/headers.php');
    include('../../includes/configuration.php');
    include('../functions.php');

    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();

    $GameServer->selectDB("webdb", $conn);

    # Organized Alphabeticaly

    switch ($_POST['action'])
    {
        case "addLink": 
        {
            $title     = $conn->escape_string($_POST['title']);
            $url       = $conn->escape_string($_POST['url']);
            $shownWhen = $conn->escape_string($_POST['shownWhen']);

            if (empty($title) || empty($url) || empty($shownWhen))
            {
                die("请输入所有字段。");
            }

            if ($conn->query("INSERT INTO site_links (title, url, shownWhen) VALUES('". $title ."', '". $url ."', '". $shownWhen ."');"))
            {
                $GameServer->logThis("添加 ". $title ." 到菜单");
            }
            else
            {
                $GameServer->logThis("无法添加菜单 - ". $conn->error);
                $GameServer->logThis("无法添加菜单 - ". $conn->error);
            }
            
            break;
        }
        
        case "deleteImage":
        {
            $id = $conn->escape_string($_POST['id']);

            if ($conn->query("DELETE FROM slider_images WHERE position=". $id .";"))
            {
                $GameServer->logThis("删除了幻灯片图像");
            }
            else
            {
                $GameServer->logThis("无法删除选定的幻灯片图像 - ". $conn->error);
            }
            break;
        }

        case "deleteLink":
        {
            $id = $conn->escape_string($_POST['id']);

            if($conn->query("DELETE FROM site_links WHERE position=". $id .";"))
            {
                $GameServer->logThis("删除了菜单链接");
            }
            else
            {
                $GameServer->logThis("无法删除菜单链接 - ". $conn->error);
            }
            break;
        }

        case "disablePlugin":
        {
            $foldername = $conn->escape_string($_POST['foldername']);

            if ($conn->query("INSERT INTO disabled_plugins VALUES('". $foldername ."');"))
            {
                include('../../plugins/' . $foldername . '/info.php');
                $GameServer->logThis("禁用了插件 " . $title);
            }
            else
            {
                $GameServer->logThis("无法禁用插件 - ". $conn->error);   
            }
            break;
        }

        case "enablePlugin":
        {
            $foldername = $conn->escape_string($_POST['foldername']);

            if ($conn->query("DELETE FROM disabled_plugins WHERE foldername='". $foldername ."';"))
            {
                include('../../plugins/' . $foldername . '/info.php');
                $GameServer->logThis("启用插件 -" . $title);
            }
            else
            {
                $GameServer->logThis("无法启用插件 - ". $conn->error);
            }
            break;
        }

        case "getMenuEditForm":
        {
            $id = $conn->escape_string($_POST['id']);
            $result = $conn->query("SELECT * FROM site_links WHERE position=". $id .";");
            $rows   = $result->fetch_assoc();
            ?>
            标题<br/>
            <input type="text" id="editlink_title" value="<?php echo $rows['title']; ?>"><br/>
            URL<br/>
            <input type="text" id="editlink_url" value="<?php echo $rows['url']; ?>"><br/>
            显示时间<br/>
            <select id="editlink_shownWhen">
                <option value="always" <?php 
                    if ($rows['shownWhen'] == "always")
                    {
                        echo "selected='selected'";
                    } ?>
                >总是</option>
                <option value="logged" <?php 
                    if ($rows['shownWhen'] == "logged")
                    {
                        echo "selected='selected'";
                    } ?>
                >用户已登录</option>
                <option value="notlogged" <?php 
                    if ($rows['shownWhen'] == "notlogged")
                    {
                        echo "selected='selected'";
                    } ?>
                >用户未登录</option>
            </select><br/>
            <input type="submit" value="保存" onclick="saveMenuLink('<?php echo $rows['position']; ?>')">
            <?php
            break;
        }

        case "installTemplate":
        {
            $name = $conn->escape_string(trim($_POST['name']));
            $path = $conn->escape_string(trim($_POST['path']));
            if ($conn->query("INSERT INTO template (`name`, `path`) VALUES('". $name ."', '". $path ."');"))
            {
                $GameServer->logThis("安装了模板 ". $_POST['name']);
            }
            else
            {
                $GameServer->logThis("安装模板时出错 ". $conn->error);
            }
            break;
        }

        case "saveMenu":
        {
            $title     = $conn->escape_string($_POST['title']);
            $url       = $conn->escape_string($_POST['url']);
            $shownWhen = $conn->escape_string($_POST['shownWhen']);
            $id        = $conn->escape_string($_POST['id']);

            if (empty($title) || empty($url) || empty($shownWhen))
            {
                die("请输入所有字段。");
            }

            if ($conn->query("UPDATE site_links SET title='". $title ."', url='". $url ."', shownWhen='". $shownWhen ."' WHERE position=". $id .";"))
            {
                $GameServer->logThis("修改了菜单");
            }
            else
            {
                $GameServer->logThis("无法修改菜单 - ". $conn->error);
            }

            echo TRUE;
            break;
        }

        case "setTemplate":
        {
            $templateId = $conn->escape_string($_POST['id']);
            if ($conn->query("UPDATE template SET applied='0' WHERE applied='1';") && 
                $conn->query("UPDATE template SET applied='1' WHERE id=". $templateId .";"))
            {
                $result = $conn->query("SELECT name FROM template WHERE id=". $templateId .";");
                $GameServer->logThis("Template Changed To `". $result->fetch_assoc()['name'] ."`");
            }
            else
            {
                $GameServer->logThis("无法更改模板 - ". $conn->error);
            }
            break;
        }

        case "uninstallTemplate":
        {
            $templateId = $conn->escape_string($_POST['id']);
            $result = $conn->query("SELECT name FROM template WHERE id=". $templateId .";");

            if ($conn->query("DELETE FROM template WHERE id=". $templateId .";") && 
                $conn->query("UPDATE template SET applied='1' ORDER BY id ASC LIMIT 1;"))
            {
                $GameServer->logThis("Uninstalled Template - `". $result->fetch_assoc()['name'] ."`");
            }
            else
            {
                $GameServer->logThis("无法卸载模板 - ". $conn->error);
            }
            break;
        }

        default:
        {
            header("Location: ../index.php");
            break;
        }
    }