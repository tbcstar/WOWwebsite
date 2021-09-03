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
            $title     = mysqli_real_escape_string($conn, $_POST['title']);
            $url       = mysqli_real_escape_string($conn, $_POST['url']);
            $shownWhen = mysqli_real_escape_string($conn, $_POST['shownWhen']);

            if (empty($title) || empty($url) || empty($shownWhen))
            {
                die("请输入所有字段。");
            }

            if (mysqli_query($conn, "INSERT INTO site_links (title, url, shownWhen) VALUES('". $title ."', '". $url ."', '". $shownWhen ."');"))
            {
                $GameServer->logThis("添加 ". $title ." 到菜单");
            }
            else
            {
                $GameServer->logThis("无法添加菜单 - ". mysqli_error($conn));
            }
            
            break;
        }
        
        case "deleteImage":
        {
            $id = mysqli_real_escape_string($conn, $_POST['id']);

            if (mysqli_query($conn, "DELETE FROM slider_images WHERE position=". $id .";"))
            {
                $GameServer->logThis("删除了幻灯片图像");
            }
            else
            {
                $GameServer->logThis("无法删除选定的幻灯片图像 - ". mysqli_error($conn));
            }
            break;
        }

        case "deleteLink":
        {
            $id = mysqli_real_escape_string($conn, $_POST['id']);

            if(mysqli_query($conn, "DELETE FROM site_links WHERE position=". $id .";"))
            {
                $GameServer->logThis("删除了菜单链接");
            }
            else
            {
                $GameServer->logThis("无法删除菜单链接 - ". mysqli_error($conn));
            }
            break;
        }

        case "disablePlugin":
        {
            $foldername = mysqli_real_escape_string($conn, $_POST['foldername']);

            if (mysqli_query($conn, "INSERT INTO disabled_plugins VALUES('". $foldername ."');"))
            {
                include('../../plugins/' . $foldername . '/info.php');
                $GameServer->logThis("禁用了插件 " . $title);
            }
            else
            {
                $GameServer->logThis("无法禁用插件 - ". mysqli_error($conn));   
            }
            break;
        }

        case "enablePlugin":
        {
            $foldername = mysqli_real_escape_string($conn, $_POST['foldername']);

            if (mysqli_query($conn, "DELETE FROM disabled_plugins WHERE foldername='". $foldername ."';"))
            {
                include('../../plugins/' . $foldername . '/info.php');
                $GameServer->logThis("启用插件 -" . $title);
            }
            else
            {
                $GameServer->logThis("无法启用插件 - ". mysqli_error($conn));
            }
            break;
        }

        case "getMenuEditForm":
        {
            $id = mysqli_real_escape_string($conn, $_POST['id']);
            $result = mysqli_query($conn, "SELECT * FROM site_links WHERE position=". $id .";");
            $rows   = mysqli_fetch_assoc($result);
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
            $name = mysqli_real_escape_string($conn, trim($_POST['name']));
            $path = mysqli_real_escape_string($conn, trim($_POST['path']));
            if (mysqli_query($conn, "INSERT INTO template (`name`, `path`) VALUES('". $name ."', '". $path ."');"))
            {
                $GameServer->logThis("安装了模板 ". $_POST['name']);
            }
            else
            {
                $GameServer->logThis("安装模板时出错 ". mysqli_error($conn));
            }
            break;
        }

        case "saveMenu":
        {
            $title     = mysqli_real_escape_string($conn, $_POST['title']);
            $url       = mysqli_real_escape_string($conn, $_POST['url']);
            $shownWhen = mysqli_real_escape_string($conn, $_POST['shownWhen']);
            $id        = mysqli_real_escape_string($conn, $_POST['id']);

            if (empty($title) || empty($url) || empty($shownWhen))
            {
                die("请输入所有字段。");
            }

            if (mysqli_query($conn, "UPDATE site_links SET title='". $title ."', url='". $url ."', shownWhen='". $shownWhen ."' WHERE position=". $id .";"))
            {
                $GameServer->logThis("修改了菜单");
            }
            else
            {
                $GameServer->logThis("无法修改菜单 - ". mysqli_error($conn));
            }

            echo TRUE;
            break;
        }

        case "setTemplate":
        {
            $templateId = mysqli_real_escape_string($conn, $_POST['id']);
            if (mysqli_query($conn, "UPDATE template SET applied='0' WHERE applied='1';") && 
                mysqli_query($conn, "UPDATE template SET applied='1' WHERE id=". $templateId .";"))
            {
                $result = mysqli_query($conn, "SELECT name FROM template WHERE id=". $templateId .";");
                $GameServer->logThis("模板更改为 `". mysqli_fetch_assoc($result)['name'] ."`");
            }
            else
            {
                $GameServer->logThis("无法更改模板 - ". mysqli_error($conn));
            }
            break;
        }

        case "uninstallTemplate":
        {
            $templateId = mysqli_real_escape_string($conn, $_POST['id']);
            $result = mysqli_query($conn, "SELECT name FROM template WHERE id=". $templateId .";");

            if (mysqli_query($conn, "DELETE FROM template WHERE id=". $templateId .";") && 
                mysqli_query($conn, "UPDATE template SET applied='1' ORDER BY id ASC LIMIT 1;"))
            {
                $GameServer->logThis("卸载的模板 - `". mysqli_fetch_assoc($result)['name'] ."`");
            }
            else
            {
                $GameServer->logThis("无法卸载模板 - ". mysqli_error($conn));
            }
            break;
        }

        default:
        {
            header("Location: ../index.php");
            break;
        }
    }