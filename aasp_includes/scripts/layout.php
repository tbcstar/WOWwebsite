<?php
    define('INIT_SITE', TRUE);
    include "../../includes/misc/headers.php";
    include "../../includes/configuration.php";
    include "../functions.php";

    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();

    $GameServer->selectDB("webdb", $conn);

    # Organized Alphabeticaly

    switch ($_POST['action'])
    {
        case "addLink": 
        {
            $title     = $Database->conn->escape_string($_POST['title']);
            $url       = $Database->conn->escape_string($_POST['url']);
            $shownWhen = $Database->conn->escape_string($_POST['shownWhen']);

            if (empty($title) || empty($url) || empty($shownWhen))
            {
                die("请输入所有字段。");
            }

            if ($Database->conn->query("INSERT INTO site_links (title, url, shownWhen) VALUES
                ('$title', '$url', '$shownWhen');"))
            {
                $GameServer->logThis("添加 ". $title ." 到菜单");
            }
            else
            {
                $GameServer->logThis("无法添加菜单 - ". $Database->conn->error);
            }
            
            break;
        }
        
        case "deleteImage":
        {
            $id = $Database->conn->escape_string($_POST['id']);

            if ($Database->conn->query("DELETE FROM slider_images WHERE position=$id;"))
            {
                $GameServer->logThis("删除了幻灯片图像");
            }
            else
            {
                $GameServer->logThis("无法删除选定的幻灯片图像 - ". $Database->conn->error);
            }
            break;
        }

        case "deleteLink":
        {
            $id = $Database->conn->escape_string($_POST['id']);

            if($Database->conn->query("DELETE FROM site_links WHERE position=$id;"))
            {
                $GameServer->logThis("删除了菜单链接");
            }
            else
            {
                $GameServer->logThis("无法删除菜单链接 - ". $Database->conn->error);
            }
            break;
        }

        case "disablePlugin":
        {
            $foldername = $Database->conn->escape_string($_POST['foldername']);

            if ($Database->conn->query("INSERT INTO disabled_plugins VALUES('$foldername');"))
            {
                include "../../plugins/" . $foldername . "/info.php";
                $GameServer->logThis("禁用了插件 " . $title);
            }
            else
            {
                $GameServer->logThis("无法禁用插件 - ". $Database->conn->error);   
            }
            break;
        }

        case "enablePlugin":
        {
            $foldername = $Database->conn->escape_string($_POST['foldername']);

            if ($Database->conn->query("DELETE FROM disabled_plugins WHERE foldername='$foldername';"))
            {
                include "../../plugins/" . $foldername . "/info.php";
                $GameServer->logThis("启用插件 -" . $title);
            }
            else
            {
                $GameServer->logThis("无法启用插件 - ". $Database->conn->error);
            }
            break;
        }

        case "getMenuEditForm":
        {
            $id = $Database->conn->escape_string($_POST['id']);
            $result = $Database->select("site_links", null, null, "position=$id")->get_result();
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
            $name = $Database->conn->escape_string(trim($_POST['name']));
            $path = $Database->conn->escape_string(trim($_POST['path']));
            if ($Database->conn->query("INSERT INTO template (`name`, `path`) VALUES('$name', '$path');"))
            {
                $GameServer->logThis("安装了模板 ". $_POST['name']);
            }
            else
            {
                $GameServer->logThis("安装模板时出错 ". $Database->conn->error);
            }
            break;
        }

        case "saveMenu":
        {
            $title     = $Database->conn->escape_string($_POST['title']);
            $url       = $Database->conn->escape_string($_POST['url']);
            $shownWhen = $Database->conn->escape_string($_POST['shownWhen']);
            $id        = $Database->conn->escape_string($_POST['id']);

            if (empty($title) || empty($url) || empty($shownWhen))
            {
                die("请输入所有字段。");
            }

            if ($Database->conn->query("UPDATE site_links SET title='$title', url='$url', shownWhen='$shownWhen' WHERE position=$position;"))
            {
                $GameServer->logThis("修改了菜单");
            }
            else
            {
                $GameServer->logThis("无法修改菜单 - ". $Database->conn->error);
            }

            echo TRUE;
            break;
        }

        case "setTemplate":
        {
            $templateId = $Database->conn->escape_string($_POST['id']);
            if ($Database->conn->query("UPDATE template SET applied='0' WHERE applied='1';") && 
                $Database->conn->query("UPDATE template SET applied='1' WHERE id=". $templateId .";"))
            {
                $result = $Database->select("template", "name", null, "id=$templateId")->get_result();
                $GameServer->logThis("Template Changed To `". $result->fetch_assoc()['name'] ."`");
            }
            else
            {
                $GameServer->logThis("无法更改模板 - ". $Database->conn->error);
            }
            break;
        }

        case "uninstallTemplate":
        {
            $templateId = $Database->conn->escape_string($_POST['id']);
            $result = $Database->select("template", "name", null, "id=$templateId")->get_result();

            if ($Database->conn->query("DELETE FROM template WHERE id=$templateId;") && 
                $Database->conn->query("UPDATE template SET applied='1' ORDER BY id ASC LIMIT 1;"))
            {
                $GameServer->logThis("Uninstalled Template - `". $result->fetch_assoc()['name'] ."`");
            }
            else
            {
                $GameServer->logThis("无法卸载模板 - ". $Database->conn->error);
            }
            break;
        }

        default:
        {
            header("Location: ../index.php");
            break;
        }
    }