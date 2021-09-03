<?php
    if (!isset($_SESSION) && empty($_SESSION))
        session_start();

    if (isset($_SESSION['cw_staff']) && !isset($_SESSION['cw_staff_id']) && 
        !empty($_SESSION['cw_staff']) && empty($_SESSION['cw_staff_id']))
    {
        exit('似乎缺少一个或多个会话。 由于安全原因，您已断开连接。');
        session_destroy();
     }

    if (isset($_SESSION['cw_admin']) && !isset($_SESSION['cw_admin_id']) &&
        !empty($_SESSION['cw_admin']) && empty($_SESSION['cw_admin_id']))
    {
        exit('似乎缺少一个或多个会话。 由于安全原因，您已断开连接。');
        session_destroy();
    }

    class GameServer
    {

        public function getConnections()
        {
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);

            $result = mysqli_query($conn, "SELECT COUNT(id) AS connections FROM account WHERE online=1;");
            return mysqli_fetch_assoc($result)['connections'];
        }

        public function getPlayersOnline($realmId = 1)
        {
            $conn = $this->connect();
            $this->connectToRealmDB($realmId);
            $result = mysqli_query($conn, "SELECT COUNT(guid) AS online FROM characters WHERE online=1;");
            if ($this->getServerStatus($realmId, false)) 
            {
                return round(mysqli_fetch_assoc($result)['online']);
            }
        }

        public function getUptime($realmId)
        {
            if (!$this->getServerStatus($realmId, false))
            {
                return "";
            }
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);

            $getUp = mysqli_query($conn, "SELECT starttime FROM uptime WHERE realmid=". $realmId ." ORDER BY starttime DESC LIMIT 1;");
            $row   = mysqli_fetch_assoc($getUp);

            $time   = time();
            $uptime = $time - $row['starttime'];

            if ($uptime < 60)
                $string = 'Seconds';
            elseif ($uptime > 60)
            {
                $uptime = $uptime / 60;
                $string = 'Minutes';
            }
            elseif ($uptime > 60)
            {
                $string = 'Hours';
                $uptime = $uptime / 60;
            }
            elseif ($uptime > 24)
                {
                    $string = 'Days';
                    $uptime = $uptime / 24;
                }

            return ceil($uptime) ." ". $string;
        }

        public function getServerStatus($realmId, $showText = true)
        {
            $realmId = mysqli_real_escape_string($conn, $realmId);
            $conn = $this->connect();
            $this->selectDB('webdb', $conn);

            $result = mysqli_query($conn, "SELECT host, port FROM realms WHERE id=". $realmId .";");
            $row    = mysqli_fetch_assoc($result);

            $fp = fsockopen($row['host'], $row['port'], $errno, $errstr, 1);
            if ($showText)
            {
                if (!$fp)
                {
                    return '<font color="#990000">离线</font>';
                }
                else
                {
                    return '<font color="#009933">在线</font>';
                }
            }
            else
            {
                if (!$fp) 
                {
                    return false;
                }
                else
                {
                    return true;
                }
            }
        }

        public function getGMSOnline()
        {
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);
            $result = mysqli_query($conn, "SELECT COUNT(id) AS GMOnline FROM account WHERE username 
                IN (SELECT username FROM account WHERE online=1) AND id IN (SELECT id FROM account_access WHERE gmlevel>1);");

            return mysqli_fetch_assoc($result)['GMOnline'];
        }

        public function getAccountsCreatedToday()
        {
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);
            $result = mysqli_query($conn, "SELECT COUNT(id) AS accountsCreated FROM account WHERE joindate LIKE '%". date("Y-m-d") ."%';");
            $row = mysqli_fetch_assoc($result);
            if ($row['accountsCreated'] == null || empty($row['accountsCreated']))
                $row['accountsCreated'] = 0;

            return $row['accountsCreated'];
        }

        public function getActiveAccounts()
        {
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);
            $result = mysqli_query($conn, "SELECT COUNT(id) AS activeMonth FROM account WHERE last_login LIKE '%". date("Y-m") ."%';");
            $row = mysqli_fetch_assoc($result);
            if ($row['activeMonth'] == null || empty($row['activeMonth'])) $row['activeMonth'] = 0;
            return $row['activeMonth'];
        }

        public function getActiveConnections()
        {
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);
            $result = mysqli_query($conn, "SELECT COUNT(id) AS activeConnections FROM account WHERE online=1;");
            $row = mysqli_fetch_assoc($result);
            if ($row['activeConnections'] == null || empty($row['activeConnections']))
                $row['activeConnections'] = 0;

            return $row['activeConnections'];
        }

        public function getFactionRatio($rid)
        {
            $conn = $this->connect();
            $this->selectDB('webdb', $conn);
            $result = mysqli_query($conn, "SELECT id FROM realms;");
            if (mysqli_num_rows($result) == 0)
            {
                $this->faction_ratio = "Unknown";
            }
            else
            {
                $t   = 0;
                $a   = 0;
                $h   = 0;
                while ($row = mysqli_fetch_assoc($result))
                {
                    $this->connectToRealmDB($row['id']);
                    $result = mysqli_query($conn, "SELECT COUNT(*) AS players FROM characters");
                    $t      = $t + mysqli_fetch_assoc($result)['players'];

                    $result = mysqli_query($conn, "SELECT COUNT(*) AS ally FROM characters WHERE race IN(3,4,7,11,1,22);");
                    $a      = $a + mysqli_fetch_assoc($result)['ally'];

                    $result = mysqli_query($conn, "SELECT COUNT(*) AS horde FROM characters WHERE race IN(2,5,6,8,10,9);");
                    $h      = $h + mysqli_fetch_assoc($result)['horde'];
                }
                $a = ($a / $t) * 100;
                $h = ($h / $t) * 100;
                return '<font color="#0066FF">'. round($a) .'%</font> &nbsp; <font color="#CC0000">'. round($h) .'%</font>';
            }
        }

        public function getAccountsLoggedToday()
        {
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);

            $result = mysqli_query($conn, "SELECT COUNT(*) AS accountsToday FROM account WHERE last_login LIKE '%" . date('Y-m-d') . "%'");
            $row = mysqli_fetch_assoc($result);
            if ($row['accountsToday'] == null || empty($row['accountsToday']))
                $row['accountsToday'] = 0;

            return $row['accountsToday'];
        }

        public function connect()
        {
            return mysqli_connect($GLOBALS['connection']['host'], $GLOBALS['connection']['user'], $GLOBALS['connection']['password']);
        }

        public function connectToRealmDB($realmId)
        {
            $conn = $this->connect();
            $this->selectDB('webdb', $conn);
			$sql="SELECT mysqli_host,mysqli_user,mysqli_pass,char_db FROM realms WHERE id='".(int)$realmid."'";
            $ID = mysqli_real_escape_string($conn, $realmId);
            $getRealmData = mysqli_query($conn, "SELECT mysqli_host, mysqli_user, mysqli_pass, char_db FROM realms WHERE id=". $ID .";");
            if (mysqli_num_rows($getRealmData) > 0)
            {
                $row = mysqli_fetch_assoc($getRealmData);
                if ($row['mysqli_host'] != $GLOBALS['connection']['host'] || 
                    $row['mysqli_user'] != $GLOBALS['connection']['user'] || 
                    $row['mysqli_pass'] != $GLOBALS['connection']['password'])
                {
                    return mysqli_connect($row['mysqli_host'], $row['mysqli_user'], $row['mysqli_pass'])or
                            buildError("<b>数据库连接错误：</b> 无法建立到Realm的连接。错误: " . mysqli_error($conn), NULL);
                }
                else
                {
                    return $this->connect();
                }

                mysqli_select_db($conn, $row['char_db']) or
                        buildError("<b>数据库选择错误:</b> 无法选择Realm数据库。错误: " . mysqli_error($conn), NULL);
            }
        }

        public function selectDB($database, $connection)
        {
            switch ($database)
            {
                default:
                    if(mysqli_set_charset($connection, "utf8")) mysqli_select_db($connection, $database);
                    break;

                case('logondb'):
                    if(mysqli_set_charset($connection, "utf8")) mysqli_select_db($connection, $GLOBALS['connection']['logondb']);
                    break;

                case('webdb'):
                    if(mysqli_set_charset($connection, "utf8")) mysqli_select_db($connection, $GLOBALS['connection']['webdb']);
                    break;

                case('worlddb'):
                    if(mysqli_set_charset($connection, "utf8")) mysqli_select_db($connection, $GLOBALS['connection']['worlddb']);
                    break;
            }
        }

        public function getItemName($id)
        {
            $conn = $this->connect();
            $this->selectDB('worlddb', $conn);

            $result = mysqli_query($conn, "SELECT name FROM item_template WHERE entry=". $id .";");
            $row    = mysqli_fetch_assoc($result);
            return $row['name'];
        }

        public function getAddress()
        {
            return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        public function logThis($action, $extended = NULL)
        {
            $conn = $this->connect();
            $this->selectDB('webdb', $conn);
            $url = $this->getAddress();

            if (isset($_SESSION['cw_admin']))
            {
                $aid = mysqli_real_escape_string($conn, $_SESSION['cw_admin_id']);
            }
            elseif (isset($_SESSION['cw_staff']))
            {
                $aid = mysqli_real_escape_string($conn, $_SESSION['cw_staff_id']);
            }

            $url        = mysqli_real_escape_string($conn, $url);
            $action     = mysqli_real_escape_string($conn, $action);
            $extended   = mysqli_real_escape_string($conn, $extended);


            mysqli_query($conn, "INSERT INTO admin_log (`full_url`, `ip`, `timestamp`, `action`, `account`, `extended_inf`) VALUES 
                ('". $url ."', '". $_SERVER['REMOTE_ADDR'] ."', '". time() ."', '". $action ."', '". $aid ."', '". $extended ."');");
        }

        public function addRealm($name, $desc, $host, $port, $chardb, $sendtype, $rank_user, $rank_pass, $ra_port, $soap_port, $m_host, $m_user, $m_pass)
        {
            $conn      = $this->connect();
            $name      = mysqli_real_escape_string($conn, $name);
            $desc      = mysqli_real_escape_string($conn, $desc);
            $host      = mysqli_real_escape_string($conn, $host);
            $port      = mysqli_real_escape_string($conn, $port);
            $chardb    = mysqli_real_escape_string($conn, $chardb);
            $sendtype  = mysqli_real_escape_string($conn, $sendtype);
            $rank_user = mysqli_real_escape_string($conn, $rank_user);
            $rank_pass = mysqli_real_escape_string($conn, $rank_pass);
            $ra_port   = mysqli_real_escape_string($conn, $ra_port);
            $soap_port = mysqli_real_escape_string($conn, $soap_port);
            $m_host    = mysqli_real_escape_string($conn, $m_host);
            $m_user    = mysqli_real_escape_string($conn, $m_user);
            $m_pass    = mysqli_real_escape_string($conn, $m_pass);

            if (empty($name) || empty($host) || empty($port) || empty($chardb) || empty($rank_user) || empty($rank_pass))
            {
                echo "<pre style='text-align:center;'>
                    <b class='red_text'>
                    请输入所有必需的字段!
                    </b>
                </pre>
            <br/>";
            }
            else
            {
                if (empty($m_host))
                    $m_host = $GLOBALS['connection']['host'];

                if (empty($m_user))
                    $m_host = $GLOBALS['connection']['user'];

                if (empty($m_pass))
                    $m_pass = $GLOBALS['connection']['password'];

                if (empty($ra_port) || $ra_port == null || !isset($ra_port))
                {
                    $ra_port   = "3443";
                    $soap_port = NULL;
                }

                if (empty($soap_port) || $soap_port == null || !isset($soap_port))
                {
                    $ra_port = NULL;
                    $soap_port = "7878";
                }

                $this->selectDB('webdb', $conn);
                if(mysqli_query($conn, "INSERT INTO realms 
                    (name, description, char_db, port, rank_user, rank_pass, ra_port, soap_port, host, sendType, mysqli_host, mysqli_user, mysqli_pass) 
                    VALUES 
                    ('". $name ."', 
                    '". $desc ."', 
                    '". $chardb ."', 
                    ". $port .", 
                    '". $rank_user ."', 
                    '". $rank_pass ."', 
                    '". $ra_port ."', 
                    '". $soap_port ."', 
                    '". $host ."', 
                    '". $sendtype ."', 
                    '". $m_host ."', 
                    '". $m_user ."', 
                    '". $m_pass ."');"))
                {
                    $this->logThis("Added the realm ". $name ."<br/>");

                echo "<pre><h3>&raquo; 成功添加realm `". $name ."`!</h3></pre><br/>";
                }
                else
                {
                    echo "<pre><h3>&raquo; 添加realm时出错 `". mysqli_error($conn) ."`</h3></pre><br/>";
                }

            }
        }

        public function getRealmName($realmId)
        {
            $conn = $this->connect();
            $this->selectDB('webdb', $conn);

            $ID = mysqli_real_escape_string($conn, $realmId);

            $value = "<i>Unknown</i>";

            $result = mysqli_query($conn, "SELECT name FROM realms WHERE id=". $ID .";");
            $row    = mysqli_fetch_assoc($result);

            if (!empty($row['name'])) $value = $row['name'];

            return $value;
        }

        public function checkForNotifications()
        {
            $conn = $this->connect();
            /* 未使用! */
            $this->selectDB('webdb', $conn);



            //检查旧的votelogs
            $old    = time() - 2592000;
            $result = mysqli_query($conn, "SELECT COUNT(*) AS records FROM votelog WHERE `timestamp` <= ". $old .";");

            if (mysqli_data_seek($result, 0) > 1)
            {
                echo '<div class="box_right">
                  <div class="box_right_title">通知</div>';
                echo "你有 " . mysqli_fetch_assoc($result)['records'] . " 30天或更早的votelog记录。因为通常不需要这些。
                    我们建议你清理这些。 ";
                echo '</div>';
            }
        }

        public function serverStatus()
        {
            if (!isset($_COOKIE['presetRealmStatus']))
            {
                $conn = $this->connect();
                $this->selectDB('webdb', $conn);
                $getRealm = mysqli_query($conn, "SELECT id FROM realms ORDER BY id ASC LIMIT 1;");
                $row      = mysqli_fetch_assoc($getRealm);

                $rid = $row['id'];
            }
            else
                $rid = $_COOKIE['presetRealmStatus'];

            echo '选择服务器: <b>' . $this->getRealmName($rid) . '</b> <a href="#" onclick="changePresetRealmStatus()">(Change Realm)</a><hr/>';
            ?>
            <table>
                <tr valign="top">
                    <td width="70%">
                        服务器状态: <br/>
                        运行时长: <br/>
                        在线玩家: <br/>
                    </td>
                    <td>
                        <b>
                            <?php echo $this->getServerStatus($rid); ?><br/>
                            <?php echo $this->getUptime($rid); ?><br/>
                            <?php echo $this->getPlayersOnline($rid); ?><br/>
                        </b>
                    </td>
                </tr>
            </table>
            <hr/>
            <b>总体状态:</b><br/>
            <table>
                <tr valign="top">
                    <td width="70%">
                        在线账户： <br/>
                        今天创建的账户: <br/>
                        活跃账户(本月)
                    </td>
                    <td>
                        <b>
                            <?php echo $this->getActiveConnections(); ?><br/>
                            <?php echo $this->getAccountsCreatedToday(); ?><br/>
                            <?php echo $this->getActiveAccounts(); ?><br/>
                        </b>
                    </td>
                </tr>
            </table>
            <?php
        }
    }
    $GameServer = new GameServer();
    $conn = $GameServer->connect();

    class GameAccount
    {

        public function getAccID($user)
        {

            global $GameServer;
            $conn = $GameServer->connect();
            $GameServer->selectDB('logondb', $conn);

            $user   = mysqli_real_escape_string($conn, $user);
            $result = mysqli_query($conn, "SELECT id FROM account WHERE username='". $user ."';");
            $row    = mysqli_fetch_assoc($result);

            return $row['id'];
        }

        public function getAccName($id)
        {
            global $GameServer;

            $conn = $GameServer->connect();
            $GameServer->selectDB('logondb', $conn);

            $accountId = mysqli_real_escape_string($conn, $id);

            $result = mysqli_query($conn, "SELECT username FROM account WHERE id=". $accountId .";");
            $row    = mysqli_fetch_assoc($result);

            if (empty($row['username']))
                return '<i>Unknown</i>';
            else
                return ucfirst(strtolower($row['username']));
        }

        public function getCharName($id, $realmId)
        {
            global $GameServer;
            $conn = $GameServer->connect();

            $GameServer->connectToRealmDB($realmId);

            $guid = mysqli_real_escape_string($conn, $id);

            $return = "<i>Unknown</i>";

            $result = mysqli_query($conn, "SELECT name FROM characters WHERE guid=". $guid .";");
            if (mysqli_num_rows($result) > 0)
            {
                $row = mysqli_fetch_assoc($result);
                if (!empty($row['name']))
                {
                    return $row['name'];
                    exit;
                }
            }

            return $return;
        }

        public function getEmail($id)
        {
            global $GameServer;
            $conn = $GameServer->connect();

            $accountId = mysqli_real_escape_string($conn, $id);
            $GameServer->selectDB('logondb', $conn);

            $result = mysqli_query($conn, "SELECT email FROM account WHERE id=". $accountId .";");
            $row    = mysqli_fetch_assoc($result);

            return $row['email'];
        }

        public function getVP($id)
        {
            global $GameServer;
            $conn = $GameServer->connect();
            $GameServer->selectDB('webdb', $conn);

            $accountId = mysqli_real_escape_string($conn, $id);

            $result = mysqli_query($conn, "SELECT vp FROM account_data WHERE id=". $accountId .";");
            if (mysqli_num_rows($result) == 0)
                return 0;

            $row = mysqli_fetch_assoc($result);
            return $row['vp'];
        }

        public function getDP($id)
        {
            global $GameServer;
            $conn = $GameServer->connect();
            $GameServer->selectDB('webdb', $conn);

            $accountId = mysqli_real_escape_string($conn, $id);

            $result = mysqli_query($conn, "SELECT dp FROM account_data WHERE id=". $accountId .";");
            if (mysqli_num_rows($result) == 0)
                return 0;

            $row = mysqli_fetch_assoc($result);
            return $row['dp'];
        }

        public function getBan($id)
        {
            global $GameServer;
            $conn = $GameServer->connect();
            $GameServer->selectDB('logondb', $conn);

            $accountId = mysqli_real_escape_string($conn, $id);

            $result = mysqli_query($conn, "SELECT * FROM account_banned WHERE id=". $accountId ." AND active=1 ORDER by bandate DESC LIMIT 1;");
            if (mysqli_num_rows($result) == 0)
                return "<span class='green_text'>Active</span>";

            $row  = mysqli_fetch_assoc($result);
            if ($row['unbandate'] < $row['bandate'])
                $time = "Never";
            else
                $time = date("Y-m-d H:i", $row['unbandate']);

            return
                    "<font size='-4'><b class='red_text'>封禁</b><br/>
                    Unban date: <b>" . $time . "</b><br/>
                    Banned by: <b>" . $row['bannedby'] . "</b><br/>
                    Reason: <b>" . $row['banreason'] . "</b></font>";
        }

        private function downloadFile($url, $path)
        {
            /* Not used! */
            $newfname = $path;
            $file     = fopen($url, "rb");
            if ($file)
            {
                $newf = fopen($newfname, "wb");

                if ($newf)
                {
                    while (!feof($file))
                    {
                        fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                    }
                }
            }

            if ($file)
                fclose($file);

            if ($newf)
                fclose($newf);
        }

    }
    $GameAccount = new GameAccount();

    class GamePage
    {

        public function validateSubPage()
        {
            if (isset($_GET['s']) && !empty($_GET['s']))
                return TRUE;
            else
                return FALSE;
        }

        public function validatePageAccess($page)
        {
            if (isset($_SESSION['cw_staff']) && !isset($_SESSION['cw_admin']))
            {
                if ($GLOBALS['staffPanel_permissions'][$page] != true)
                {
                    header("Location: ?p=notice&e=<h2>未经授权！</h2>
                    您无权查看此页面！");
                }
            }
        }

        public function outputSubPage($panel = null)
        {
            $page    = $_GET['p'];
            $subpage = $_GET['s'];
            $pages   = scandir('../aasp_includes/pages/subpages');
            unset($pages[0], $pages[1]);

            if (!file_exists('../aasp_includes/pages/subpages/' . $page . '-' . $subpage . '.php'))
            {
                include('../aasp_includes/pages/404.php');
            }
            elseif (in_array($page . '-' . $subpage . '.php', $pages))
            {
                include('../aasp_includes/pages/subpages/' . $page . '-' . $subpage . '.php');
            }
            else
            {
                include('../aasp_includes/pages/404.php');
            }
        }

        public function titleLink()
        {
            return '<a href="?p=' . $_GET['p'] . '" title="返回到 ' . ucfirst($_GET['p']) . '">' . ucfirst($_GET['p']) . '</a>';
        }

        public function addSlideImage($upload, $path, $url)
        {
            global $GameServer, $conn;
            $path = mysqli_real_escape_string($conn, $path);
            $url  = mysqli_real_escape_string($conn, $url);

            if (empty($path) || empty($url))
            {
                //No path set, upload image.
                if ($upload['error'] > 0)
                {
                    echo "<span class='red_text'><b>错误：</b> 文件上传不成功!</span>";
                    $abort = true;
                }
                else
                {
                    if ((($upload["type"] == "image/gif") || ($upload["type"] == "image/jpeg") || ($upload["type"] == "image/pjpeg") || ($upload["type"] == "image/png")))
                    {
                        if (file_exists("../styles/global/slideshow/images/" . $upload["name"]))
                        {
                            unlink("../styles/global/slideshow/images/" . $upload["name"]);
                            move_uploaded_file($upload["tmp_name"], "../styles/global/slideshow/images/" . $upload["name"]);
                            $path = "../styles/global/slideshow/images/" . $upload["name"];
                        }
                        else
                        {
                            move_uploaded_file($upload["tmp_name"], "../styles/global/slideshow/images/" . $upload["name"]);
                            $path = "styles/global/slideshow/images/" . $upload["name"];
                        }
                    }
                    else
                    {
                        $abort = true;
                    }
                }
            }
            else
            {
                die("路径/Url不能为空。");
            }

            if (!isset($abort))
            {

                $GameServer->selectDB('webdb', $conn);
                mysqli_query($conn, "INSERT INTO slider_images (`path`, `link`) VALUES('". $path ."','". $url ."');");
            }
        }
    }
    $GamePage = new GamePage();

    class GameCharacter
    {

        public static function getRace($value)
        {
            switch ($value)
            {
                default:
                    return "无类别";
                    break;
                #######
                case(1):
                    return "人类";
                    break;
                #######      
                case(2):
                    return "兽人";
                    break;
                #######
                case(3):
                    return "矮人";
                    break;
                #######
                case(4):
                    return "暗夜精灵";
                    break;
                #######
                case(5):
                    return "亡灵";
                    break;
                #######
                case(6):
                    return "牛头人";
                    break;
                #######
                case(7):
                    return "侏儒";
                    break;
                #######
                case(8):
                    return "巨魔";
                    break;
                #######
                case(9):
                    return "地精";
                    break;
                #######
                case(10):
                    return "血精灵";
                    break;
                #######
                case(11):
                    return "德莱尼";
                    break;
                #######
                case(22):
                    return "狼人";
                    break;
                #######
            }
        }

        public static function getGender($value)
        {
            if ($value == 1)
                return "女性";
            elseif ($value == 0)
                return "男性";
            else
                return "未知";
        }

        public static function getClass($value)
        {
            switch ($value)
            {
                default:
                    return "无类别";
                    break;
                #######
                case(1):
                    return "战士";
                    break;
                #######
                case(2):
                    return "圣骑士";
                    break;
                #######
                case(3):
                    return "猎人";
                    break;
                #######
                case(4):
                    return "盗贼";
                    break;
                #######
                case(5):
                    return "牧师";
                    break;
                #######
                case(6):
                    return "死亡骑士";
                    break;
                #######
                case(7):
                    return "萨满";
                    break;
                #######
                case(8):
                    return "法师";
                    break;
                #######
                case(9):
                    return "术士";
                    break;
                #######
                case(11):
                    return "德鲁伊";
                    break;
                ####### 
                #######
                case(12):
                    return "武僧";
                    break;
                ####### 
            }
        }

    }
    $GameCharacter = new GameCharacter();

    function activeMenu($p)
    {
        if (isset($_GET['p']) && $_GET['p'] == $p)
            echo "style='display:block;'";
    }

    function limit_characters($str, $n)
    {
        $str = preg_replace("/<img[^>]+\>/i", "(image) ", $str);
        if (strlen($str) <= $n)
            return $str;
        else
            return substr($str, 0, $n) . '';
    }

    function stripBBCode($text_to_search)
    {
        $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
        $replace = '';
        return preg_replace($pattern, $replace, $text_to_search);
    }
