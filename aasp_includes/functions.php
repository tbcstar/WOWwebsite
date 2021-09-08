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

            $result = $conn->query("SELECT COUNT(id) AS connections FROM account WHERE online=1;");
            return $result->fetch_assoc()['connections'];
        }

        public function getPlayersOnline($realmId = 1)
        {
            $conn = $this->connect();
            $this->connectToRealmDB($realmId);

            $result = $conn->query("SELECT COUNT(guid) AS online FROM characters WHERE online=1;");
            if ($this->getServerStatus($realmId, false)) 
            {
                return round($result->fetch_assoc()['online']);
            }
            else
            {
                return 0;
            }
        }

        public function getUptime($realmId)
        {
            if (!$this->getServerStatus($realmId, false))
            {
                return 0;
            }
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);

            $getUp = $conn->query("SELECT starttime FROM uptime WHERE realmid=". $realmId ." ORDER BY starttime DESC LIMIT 1;");
            $row   = $getUp->fetch_assoc();

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

        public function getServerStatus($realmId, $showText = TRUE)
        {
            $conn = $this->connect();
            $this->selectDB("webdb", $conn);

            $realmId = $conn->escape_string($realmId);

            $result = $conn->query("SELECT host, port FROM realms WHERE id=". $realmId .";");
            $row    = $result->fetch_assoc();

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
                    return TRUE;
                }
            }
        }

        public function getGMSOnline()
        {
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);

            $result = $conn->query("SELECT COUNT(id) AS GMOnline FROM account WHERE username
                IN (SELECT username FROM account WHERE online=1) AND id IN (SELECT id FROM account_access WHERE gmlevel>1);");

            return $result->fetch_assoc()['GMOnline'];
        }

        public function getAccountsCreatedToday()
        {
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);

            $result = $conn->query("SELECT COUNT(id) AS accountsCreated FROM account WHERE joindate LIKE '%". date("Y-m-d") ."%';");
            $row = $result->fetch_assoc();
            if ($row['accountsCreated'] == null || empty($row['accountsCreated']))
                $row['accountsCreated'] = 0;

            return $row['accountsCreated'];
        }

        public function getActiveAccounts()
        {
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);

            $result = $conn->query("SELECT COUNT(id) AS activeMonth FROM account WHERE last_login LIKE '%". date("Y-m") ."%';");
            $row = $result->fetch_assoc();
            if ($row['activeMonth'] == null || empty($row['activeMonth']))
                $row['activeMonth'] = 0;

            return $row['activeMonth'];
        }

        public function getActiveConnections()
        {
            $conn = $this->connect();
            $this->selectDB('logondb', $conn);

            $result = $conn->query("SELECT COUNT(id) AS activeConnections FROM account WHERE online='1';");
            $row = $result->fetch_assoc();
            if (empty($row['activeConnections']))
            {
                $row['activeConnections'] = 0;
            }

            return $row['activeConnections'];
        }

        public function getFactionRatio($rid)
        {
            $conn = $this->connect();
            $this->selectDB("webdb", $conn);

            $result = $conn->query("SELECT id FROM realms;");
            if ($result->num_rows == 0)
            {
                $this->faction_ratio = "Unknown";
            }
            else
            {
                $t   = 0;
                $a   = 0;
                $h   = 0;
                while ($row = $result->fetch_assoc())
                {
                    $this->connectToRealmDB($row['id']);

                    $result = $conn->query("SELECT COUNT(*) AS players FROM characters");
                    $t      = $t + $result->fetch_assoc()['players'];

                    $result = $conn->query("SELECT COUNT(*) AS ally FROM characters WHERE race IN(3,4,7,11,1,22);");
                    $a      = $a + $result->fetch_assoc()['ally'];

                    $result = $conn->query("SELECT COUNT(*) AS horde FROM characters WHERE race IN(2,5,6,8,10,9);");
                    $h      = $h + $result->fetch_assoc()['horde'];
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

            $result = $conn->query("SELECT COUNT(*) AS accountsToday FROM account WHERE last_login LIKE '%" . date('Y-m-d') . "%'");
            $row = $result->fetch_assoc();
            if ($row['accountsToday'] == null || empty($row['accountsToday']))
                $row['accountsToday'] = 0;

            return $row['accountsToday'];
        }

        public function connect()
        {
            if($conn = new mysqli(
                $GLOBALS['connection']['web']['host'], 
                $GLOBALS['connection']['web']['user'], 
                $GLOBALS['connection']['web']['password']))
            {
                $conn->set_charset("UTF8");
                return $conn;
            }
            else
            {
                return false;
            }
        }

        public function connectToRealmDB($realmId)
        {
            $conn = $this->connect();
            $this->selectDB("webdb", $conn);
			$sql="SELECT mysqli_host,mysqli_user,mysqli_pass,char_db FROM realms WHERE id='".(int)$realmid."'";

            $ID = $conn->escape_string($realmId);
            $getRealmData = $conn->query("SELECT mysqli_host, mysqli_user, mysqli_pass, char_db FROM realms WHERE id=". $ID .";");
            if ($getRealmData->num_rows > 0)
            {
                $row = $getRealmData->fetch_assoc();
                if ($row['mysqli_host'] != $GLOBALS['connection']['web']['host'] || 
                    $row['mysqli_user'] != $GLOBALS['connection']['web']['user'] || 
                    $row['mysqli_pass'] != $GLOBALS['connection']['web']['password'])
                {
                    return $connection = new mysqli($row['mysqli_host'], $row['mysqli_user'], $row['mysqli_pass']) 
                        or buildError("<b>数据库连接错误：</b> 无法建立与服务器的连接。错误:". $connection->error, NULL);
                }
                else
                {
                    return $this->connect();
                }

                $conn->select_db($row['char_db']) 
                    or buildError("<b>数据库选择错误:</b> 无法选择服务器数据库。错误: " . $conn->error, NULL);
            }
        }

        public function selectDB($database, $connection)
        {
            switch ($database)
            {
                default:
                    if($connection->set_charset("UTF8")) $connection->select_db($database);
                    break;

                case('logondb'):
                    if($connection->set_charset("UTF8")) $connection->select_db($GLOBALS['connection']['logon']['database']);
                    break;

                case('webdb'):
                    if($connection->set_charset("UTF8")) $connection->select_db($GLOBALS['connection']['web']['database']);
                    break;

                case('worlddb'):
                    if($connection->set_charset("UTF8")) $connection->select_db($GLOBALS['connection']['world']['database']);
                    break;
            }
        }

        public function getItemName($id)
        {
            $conn = $this->connect();
            $this->selectDB('worlddb', $conn);

            $result = $conn->query("SELECT name FROM item_template WHERE entry=". $id .";");
            $row    = $result->fetch_assoc();
            return $row['name'];
        }

        public function getAddress()
        {
            return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        public function logThis($action, $extended = NULL)
        {
            $conn = $this->connect();
            $this->selectDB("webdb", $conn);
            $url = $this->getAddress();

            if (isset($_SESSION['cw_admin']))
            {
                $aid = $conn->escape_string($_SESSION['cw_admin_id']);
            }
            elseif (isset($_SESSION['cw_staff']))
            {
                $aid = $conn->escape_string($_SESSION['cw_staff_id']);
            }

            $url        = $conn->escape_string($url);
            $action     = $conn->escape_string($action);
            $extended   = $conn->escape_string($extended);


            $conn->query("INSERT INTO admin_log (`full_url`, `ip`, `timestamp`, `action`, `account`, `extended_inf`) VALUES 
                ('". $url ."', '". $_SERVER['REMOTE_ADDR'] ."', '". time() ."', '". $action ."', '". $aid ."', '". $extended ."');");
        }

        public function addRealm($name, $desc, $host, $port, $chardb, $sendtype, $rank_user, $rank_pass, $ra_port, $soap_port, $m_host, $m_user, $m_pass)
        {
            $conn      = $this->connect();
            $name      = $conn->escape_string($name);
            $desc      = $conn->escape_string($desc);
            $host      = $conn->escape_string($host);
            $port      = $conn->escape_string($port);
            $chardb    = $conn->escape_string($chardb);
            $sendtype  = $conn->escape_string($sendtype);
            $rank_user = $conn->escape_string($rank_user);
            $rank_pass = $conn->escape_string($rank_pass);
            $ra_port   = $conn->escape_string($ra_port);
            $soap_port = $conn->escape_string($soap_port);
            $m_host    = $conn->escape_string($m_host);
            $m_user    = $conn->escape_string($m_user);
            $m_pass    = $conn->escape_string($m_pass);

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
                    $m_host = $GLOBALS['connection']['web']['host'];

                if (empty($m_user))
                    $m_host = $GLOBALS['connection']['web']['user'];

                if (empty($m_pass))
                    $m_pass = $GLOBALS['connection']['web']['password'];

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

                $this->selectDB("webdb", $conn);
                if($conn->query("INSERT INTO realms 
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
                    echo "<pre><h3>&raquo; 添加服务器时出错 `". $conn->error ."`</h3></pre><br/>";
                }

            }
        }

        public function getRealmName($realmId)
        {
            $conn = $this->connect();
            $this->selectDB("webdb", $conn);

            $ID = $conn->escape_string($realmId);

            $value = "<i>Unknown</i>";

            $result = $conn->query("SELECT name FROM realms WHERE id=". $ID .";");
            $row    = $result->fetch_assoc();

            if (!empty($row['name']))
            {
                $value = $row['name'];
            }

            return $value;
        }

        public function checkForNotifications()
        {
            $conn = $this->connect();
            /* 未使用! */
            $this->selectDB("webdb", $conn);



            //检查旧的votelogs
            $old    = time() - 2592000;
            $result = $conn->query("SELECT COUNT(*) AS records FROM votelog WHERE `timestamp` <= ". $old .";");

            if ($result->data_seek(0) > 1)
            {
                echo '<div class="box_right">
                  <div class="box_right_title">通知</div>';
                echo "你有 " . $result->fetch_assoc()['records'] . " 30天或以上的votelog记录。因为一般来说这些并不是真正需要的。
                     我们建议您清除这些。";
                echo '</div>';
            }
        }

        public function serverStatus()
        {
            if (!isset($_COOKIE['presetRealmStatus']))
            {
                $conn = $this->connect();
                $this->selectDB("webdb", $conn);

                $getRealm = $conn->query("SELECT id FROM realms ORDER BY id ASC LIMIT 1;");
                $row      = $getRealm->fetch_assoc();

                $rid = $row['id'];
            }
            else
                $rid = $_COOKIE['presetRealmStatus'];

            echo "选择服务器: <b>". $this->getRealmName($rid) ."</b><a href='#' onclick='changePresetRealmStatus()'> (Change Realm)</a><hr/>";
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

            $user   = $conn->escape_string($user);
            $result = $conn->query("SELECT id FROM account WHERE username='". $user ."';");
            $row    = $result->fetch_assoc();

            return $row['id'];
        }

        public function getAccName($id)
        {
            global $GameServer;

            $conn = $GameServer->connect();
            $GameServer->selectDB('logondb', $conn);

            $accountId = $conn->escape_string($id);

            $result = $conn->query("SELECT username FROM account WHERE id='". $accountId ."';");
            $row    = $result->fetch_assoc();

            if (empty($row['username']))
            {
                return '<i>Unknown</i>';
            }
            else
            {
                return ucfirst(strtolower($row['username']));
            }
        }

        public function getCharName($id, $realmId)
        {
            global $GameServer;
            $conn = $GameServer->connect();

            $GameServer->connectToRealmDB($realmId);

            $guid = $conn->escape_string($id);

            $return = "<i>Unknown</i>";

            $result = $conn->query("SELECT name FROM characters WHERE guid=". $guid .";");
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
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

            $accountId = $conn->escape_string($id);
            $GameServer->selectDB('logondb', $conn);

            $result = $conn->query("SELECT email FROM account WHERE id=". $accountId .";");
            $row    = $result->fetch_assoc();

            return $row['email'];
        }

        public function getVP($id)
        {
            global $GameServer;
            $conn = $GameServer->connect();
            $GameServer->selectDB('webdb', $conn);

            $accountId = $conn->escape_string($id);

            $result = $conn->query("SELECT vp FROM account_data WHERE id=". $accountId .";");
            if ($result->num_rows == 0)
                return 0;

            $row = $result->fetch_assoc();
            return $row['vp'];
        }

        public function getDP($id)
        {
            global $GameServer;
            $conn = $GameServer->connect();
            $GameServer->selectDB('webdb', $conn);

            $accountId = $conn->escape_string($id);

            $result = $conn->query("SELECT dp FROM account_data WHERE id=". $accountId .";");
            if ($result->num_rows == 0)
                return 0;

            $row = $result->fetch_assoc();
            return $row['dp'];
        }

        public function getBan($id)
        {
            global $GameServer;
            $conn = $GameServer->connect();
            $GameServer->selectDB('logondb', $conn);

            $accountId = $conn->escape_string($id);

            $result = $conn->query("SELECT * FROM account_banned WHERE id=". $accountId ." AND active=1 ORDER by bandate DESC LIMIT 1;");
            if ($result->num_rows == 0)
                return "<span class='green_text'>Active</span>";

            $row  = $result->fetch_assoc();
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
                if ($GLOBALS['staffPanel_permissions'][$page] != TRUE)
                {
                    header("Location: ?page=notice&error=<h2>未经授权！</h2>
                    您无权查看此页面！");
                }
            }
        }

        public function outputSubPage($panel = null)
        {
            $page    = $_GET['page'];
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
            return "<a href='?page=". htmlentities($_GET['page']) ."' title='返回到 ". htmlentities(ucfirst($_GET['page'])) ."'>". htmlentities(ucfirst($_GET['page'])) ."</a>";
        }

        public function addSlideImage($upload, $path, $url)
        {
            global $GameServer, $conn;
            $conn = $GameServer->connect();

            $GameServer->selectDB('webdb', $conn);
            $path = $conn->escape_string($path);
            $url  = $conn->escape_string($url);

            if (empty($path) || empty($url))
            {
                //No path set, upload image.
                if ($upload['error'] > 0)
                {
                    echo "<span class='red_text'><b>错误：</b> 文件上传不成功!</span>";
                    $abort = TRUE;
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
                        $abort = TRUE;
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
                $conn->query("INSERT INTO slider_images (`path`, `link`) VALUES('". $path ."', '". $url ."');");
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
        if (isset($_GET['page']) && $_GET['page'] == $p)
            echo htmlentities("style='display:block;'");
    }

    function limit_characters($str, $n)
    {
        $str = preg_replace("/<img[^>]+\>/i", "(image) ", $str);
        if (strlen($str) <= $n)
            return $str;
        else
            return substr($str, 0, $n). "";
    }

    function stripBBCode($text_to_search)
    {
        $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
        $replace = "";
        return preg_replace($pattern, $replace, $text_to_search);
    }
