<?php
    if (!isset($_SESSION) && empty($_SESSION))
    {
        session_start();
    }

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

    include_once "../includes/misc/connect.php";
    class GameServer extends Database
    {
        public $conn = null;

        public function __construct()
        {
            $this->conn = $this->connect();
        }

        public function getConnections()
        {
            $this->selectDB("logondb");

            $result = $this->select("account", "COUNT(id) AS connections", null, "online=1")->get_result();
            return $result->fetch_assoc()['connections'];
        }

        public function getPlayersOnline($realmId = 1)
        {
            $this->realm($realmId);

            $result = $this->select("characters", "COUNT(guid) AS online", null, "online=1")->get_result();
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
            $this->selectDB("logondb");

            $getUp = $this->select("uptime", "starttime", null, "realmid=$realmId ORDER BY starttime DESC LIMIT 1")->get_result();
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
            $this->selectDB("webdb");

            $realmId = $this->conn->escape_string($realmId);

            $result = $this->select("realms", "host, port", null, "id=$realmId")->get_result();
            $row    = $result->fetch_assoc();

            $fp = fsockopen($row['host'], $row['port'], $errno, $errstr, 1);
            if ( $showText )
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
            $this->selectDB("logondb");

            $result = $this->select("account", "COUNT(id) AS GMOnline", null, "username 
                IN (SELECT username FROM account WHERE online=1) AND id IN (SELECT id FROM account_access WHERE gmlevel>1)")->get_result();

            return $result->fetch_assoc()['GMOnline'];
        }

        public function getAccountsCreatedToday()
        {
            $this->selectDB("logondb");

            $result = $this->select("account", "COUNT(id) AS accountsCreated", null, "joindate LIKE '%". date("Y-m-d") ."%'")->get_result();
            $row = $result->fetch_assoc();
            if ($row['accountsCreated'] == null || empty($row['accountsCreated']))
                $row['accountsCreated'] = 0;

            return $row['accountsCreated'];
        }

        public function getActiveAccounts()
        {
            $this->selectDB("logondb");

            $result = $this->select("account", "COUNT(id) AS activeMonth", null, "last_login LIKE '%". date("Y-m") ."%'")->get_result();
            $row = $result->fetch_assoc();
            if ($row['activeMonth'] == null || empty($row['activeMonth']))
                $row['activeMonth'] = 0;

            return $row['activeMonth'];
        }

        public function getActiveConnections()
        {
            $this->selectDB("logondb");

            $result = $this->select("account", "COUNT(id) AS activeConnections", null, "online='1'")->get_result();
            $row = $result->fetch_assoc();
            if (empty($row['activeConnections']))
            {
                $row['activeConnections'] = 0;
            }

            return $row['activeConnections'];
        }

        public function getFactionRatio($rid)
        {
            $this->selectDB("webdb");

            $result = $this->select("realms", "id")->get_result();
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
                    $this->realm($row['id']);

                    $result = $this->select("characters", "COUNT(*) AS players")->get_result();
                    $t      = $t + $result->fetch_assoc()['players'];

                    $result = $this->select("characters", "COUNT(*) AS ally", null, "race IN(3,4,7,11,1,22)")->get_result();
                    $a      = $a + $result->fetch_assoc()['ally'];

                    $result = $this->select("characters", "COUNT(*) AS horde", null, "race IN(2,5,6,8,10,9)")->get_result();
                    $h      = $h + $result->fetch_assoc()['horde'];
                }
                $a = ($a / $t) * 100;
                $h = ($h / $t) * 100;
                return '<font color="#0066FF">'. round($a) .'%</font> &nbsp; <font color="#CC0000">'. round($h) .'%</font>';
            }
        }

        public function getAccountsLoggedToday()
        {
            $this->selectDB("logondb");

            $result = $this->select("account", "COUNT(*) AS accountsToday", null, "last_login LIKE '%" . date('Y-m-d') . "%'")->get_result();
            $row = $result->fetch_assoc();
            if ($row['accountsToday'] == null || empty($row['accountsToday']))
                $row['accountsToday'] = 0;

            return $row['accountsToday'];
        }

        public function getItemName($id)
        {
            $this->selectDB("logondb");

            $result = $this->select("item_template", "name", null, "entry=$id")->get_result();
            $row    = $result->fetch_assoc();
            return $row['name'];
        }

        public function getAddress()
        {
            return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        public function logThis($action, $extended = NULL)
        {
            $this->selectDB("webdb");
            $url = $this->getAddress();

            if (isset($_SESSION['cw_admin']))
            {
                $aid = $this->conn->escape_string($_SESSION['cw_admin_id']);
            }
            elseif (isset($_SESSION['cw_staff']))
            {
                $aid = $this->conn->escape_string($_SESSION['cw_staff_id']);
            }

            $url        = $this->conn->escape_string($url);
            $action     = $this->conn->escape_string($action);
            $extended   = $this->conn->escape_string($extended);


            $this->conn->query("INSERT INTO admin_log (`full_url`, `ip`, `timestamp`, `action`, `account`, `extended_inf`) VALUES 
                ('". $url ."', '". $_SERVER['REMOTE_ADDR'] ."', '". time() ."', '". $action ."', '". $aid ."', '". $extended ."');");
        }

        public function addRealm($name, $desc, $host, $port, $chardb, $sendtype, $rank_user, $rank_pass, $ra_port, $soap_port, $m_host, $m_user, $m_pass)
        {
            $name      = $this->conn->escape_string($name);
            $desc      = $this->conn->escape_string($desc);
            $host      = $this->conn->escape_string($host);
            $port      = $this->conn->escape_string($port);
            $chardb    = $this->conn->escape_string($chardb);
            $sendtype  = $this->conn->escape_string($sendtype);
            $rank_user = $this->conn->escape_string($rank_user);
            $rank_pass = $this->conn->escape_string($rank_pass);
            $ra_port   = $this->conn->escape_string($ra_port);
            $soap_port = $this->conn->escape_string($soap_port);
            $m_host    = $this->conn->escape_string($m_host);
            $m_user    = $this->conn->escape_string($m_user);
            $m_pass    = $this->conn->escape_string($m_pass);

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
                    $m_host = DATA['website']['connection']['host'];

                if (empty($m_user))
                    $m_host = DATA['website']['connection']['user'];

                if (empty($m_pass))
                    $m_pass = DATA['website']['connection']['password'];

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

                $this->selectDB("webdb");
                if($this->conn->query("INSERT INTO realms 
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
                    echo "<pre><h3>&raquo; 添加服务器时出错 `". $this->conn->error ."`</h3></pre><br/>";
                }

            }
        }

        public function getRealmName($realmId)
        {
            $this->selectDB("webdb");

            $ID = $this->conn->escape_string($realmId);

            $value = "<i>Unknown</i>";

            $result = $this->select("realms", "name", null, "id=$ID")->get_result();
            $row    = $result->fetch_assoc();

            if (!empty($row['name']))
            {
                $value = $row['name'];
            }

            return $value;
        }

        public function checkForNotifications()
        {
            /* 未使用! */
            $this->selectDB("webdb");



            //检查旧的votelogs
            $old    = time() - 2592000;
            $result = $this->select("votelog", "COUNT(*) AS records", null, "`timestamp` <= $old")->get_result();

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

                $this->selectDB("webdb");

                $getRealm = $this->select("realms", "id", null, null, "ORDER BY id ASC LIMIT 1")->get_result();
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

    class GameAccount extends GameServer
    {

        public function getAccID($user)
        {

            $this->selectDB("logondb");

            $user   = $this->conn->escape_string($user);
            $result = $this->select("account", "id", null, "username='$user'")->get_result();
            $row    = $result->fetch_assoc();

            return $row['id'];
        }

        public function getAccName($id)
        {
            $this->selectDB("logondb");

            $accountId = $this->conn->escape_string($id);

            $result = $this->select("account", "username", null, "id='$accountId'")->get_result();
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

            $GameServer->realm($realmId);

            $guid = $this->conn->escape_string($id);

            $return = "<i>Unknown</i>";

            $result = $this->select("characters", "name", null, "guid=$guid")->get_result();
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

            $accountId = $this->conn->escape_string($id);
            $GameServer->selectDB("logondb");

            $result = $this->select("account", "email", null, "id=$accountId")->get_result();
            $row    = $result->fetch_assoc();

            return $row['email'];
        }

        public function getVP($id)
        {
            $GameServer->selectDB("webdb");

            $accountId = $this->conn->escape_string($id);

            $result = $this->select("account_data", "vp", null, "id=$accountId")->get_result();
            if ($result->num_rows == 0) return 0;

            $row = $result->fetch_assoc();
            return $row['vp'];
        }

        public function getDP($id)
        {
            $GameServer->selectDB("webdb");

            $accountId = $this->conn->escape_string($id);

            $result = $this->select("account_data", "dp", null, "id=$accountId")->get_result();
            if ($result->num_rows == 0)
                return 0;

            $row = $result->fetch_assoc();
            return $row['dp'];
        }

        public function getBan($id)
        {
            $GameServer->selectDB("logondb");

            $accountId = $this->conn->escape_string($id);

            $result = $this->select("account_banned", null, null, "id=$accountId AND active=1 ORDER by bandate DESC LIMIT 1")->get_result();
            if ($result->num_rows == 0) return "<span class='green_text'>Active</span>";

            $row  = $result->fetch_assoc();
            if ($row['unbandate'] < $row['bandate']) $time = "Never";
            else $time = date("Y-m-d H:i", $row['unbandate']);

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

            if ($file) fclose($file);

            if ($newf) fclose($newf);
        }

    }
    $GameAccount = new GameAccount();

    class GamePage
    {

        public function validateSubPage()
        {
            if (isset($_GET['s']) && !empty($_GET['s'])) return TRUE;
            else return FALSE;
        }

        public function validatePageAccess($page)
        {
            if (isset($_SESSION['cw_staff']) && !isset($_SESSION['cw_admin']))
            {
                if ( DATA['staff']['permissions'][$page] != TRUE )
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
                include "../aasp_includes/pages/404.php";
            }
            elseif (in_array($page . '-' . $subpage . '.php', $pages))
            {
                include "../aasp_includes/pages/subpages/". $page . "-" . $subpage .".php";
            }
            else
            {
                include "../aasp_includes/pages/404.php";
            }
        }

        public function titleLink()
        {
            return "<a href='?page=". htmlentities($_GET['page']) ."' title='返回到 ". htmlentities(ucfirst($_GET['page'])) ."'>". htmlentities(ucfirst($_GET['page'])) ."</a>";
        }

        public function addSlideImage($upload, $path, $url)
        {

            $GameServer->selectDB("webdb");
            $path = $this->conn->escape_string($path);
            $url  = $this->conn->escape_string($url);

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

                $GameServer->selectDB("webdb", $conn);
                $this->conn->query("INSERT INTO slider_images (`path`, `link`) VALUES('". $path ."', '". $url ."');");
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
