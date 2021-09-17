<?php 
    require "includes/loader.php"; //Load all php scripts
    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $GLOBALS['website_title']; ?> - 员工面板</title>
<link rel="stylesheet" href="../aasp_includes/styles/default/style.css" />
<link rel="stylesheet" href="../aasp_includes/styles/wysiwyg.css" />
<script type="text/javascript" src="../javascript/jquery.js"></script>
</head>

<body>
<div id="overlay"></div>
<div id="loading"><img src="../aasp_includes/styles/default/images/ajax-loader.gif" /></div>
<div id="leftcontent">
        <div id="menu_left">
        <ul>
            <li id="menu_head">菜单</li>
            <li>仪表盘</li>

            <ul class="hidden" <?php activeMenu('dashboard'); ?>>
                <a href="?page=dashboard">仪表盘</a>
            </ul>
            <?php if ($GLOBALS['staffPanel_permissions']['Pages'] == TRUE)
                { ?>     
                    <li>页面</li>
                    <ul class="hidden" <?php activeMenu('pages'); ?>>
                        <a href="?page=pages">所有页面</a>
                        <a href="?page=pages&selected=new">添加页面</a>
            </ul>
            <?php }
                if ($GLOBALS['staffPanel_permissions']['News'] == TRUE)
                { ?>
                    <li>新闻</li>
                    <ul class="hidden" <?php activeMenu('news'); ?>>
                        <a href="?page=news">发布新闻</a>
                        <a href="?page=news&selected=manage">管理新闻</a>
                    </ul>
                <?php }
                if ($GLOBALS['staffPanel_permissions']['Shop'] == TRUE)
                { ?>          
                    <li>商城</li>
                    <ul class="hidden" <?php activeMenu('shop'); ?>>
                        <a href="?page=shop">概述</a>
                        <a href="?page=shop&selected=add">添加物品</a>
                        <a href="?page=shop&selected=manage">管理物品</a>
                        <a href="?page=shop&selected=tools">工具</a>
                    </ul> 
                <?php }
                if ($GLOBALS['staffPanel_permissions']['Donations'] == TRUE)
                {
                    ?>     
                    <li>捐赠</li>
                    <ul class="hidden" <?php activeMenu('donations'); ?>>
                        <a href="?page=donations">概述</a>
                        <a href="?page=donations&selected=browse">浏览</a>
                    </ul> 
                <?php }
                if ($GLOBALS['staffPanel_permissions']['Logs'] == TRUE)
                {
                    ?>     
                    <li>日志</li>
                    <ul class="hidden" <?php activeMenu('logs'); ?>>
                        <a href="?page=logs&selected=voteshop">投票商店</a>
                        <a href="?page=logs&selected=donateshop">公益商城</a>
                    </ul> 
                <?php }
                if ($GLOBALS['staffPanel_permissions']['Interface'] == TRUE)
                {
                    ?>     
                    <li>Interface</li>
                    <ul class="hidden" <?php activeMenu('interface'); ?>>
                        <a href="?page=interface">模板</a>
                        <a href="?page=interface&selected=menu">菜单</a>
                        <a href="?page=interface&selected=slideshow">幻灯片</a>
                        <a href="?page=interface&selected=plugins">插件</a>
                    </ul> 
                <?php }
                if ($GLOBALS['staffPanel_permissions']['Users'] == TRUE)
                {
                    ?>     
                    <li>用户</li>
                    <ul class="hidden" <?php activeMenu('users'); ?>>
                        <a href="?page=users">概述</a>
                        <a href="?page=users&selected=manage">管理用户</a>
                    </ul> 
                <?php }
                if ($GLOBALS['staffPanel_permissions']['Realms'] == TRUE)
                {
                    ?>     
                    <li>服务器</li>
                    <ul class="hidden" <?php activeMenu('realms'); ?>>
                        <a href="?page=realms">新增服务器</a>
                        <a href="?page=realms&selected=manage">管理服务器</a>
                    </ul> 
                <?php }
                if ($GLOBALS['staffPanel_permissions']['Services'] == TRUE)
                {
                    ?>     
                    <li>服务项目</li>
                    <ul class="hidden" <?php activeMenu('services'); ?>>
                        <a href="?page=services&selected=voting">投票链接</a>
                        <a href="?page=services&selected=charservice">角色服务</a>
                    </ul> 
                <?php }
                if ($GLOBALS['staffPanel_permissions']['Tools->Tickets'] == TRUE ||
                        $GLOBALS['staffPanel_permissions']['Tools->Account Access'] == TRUE)
                {
                    ?>    
                    <li>Tools</li>
                    <ul class="hidden" <?php activeMenu('tools'); ?>>
                        <?php if ($GLOBALS['staffPanel_permissions']['Tools->Tickets'] == TRUE)
                        { ?>
                            <a href="?page=tools&selected=tickets">Tickets</a>
                        <?php } ?>
                        <?php if ($GLOBALS['staffPanel_permissions']['Tools->Account Access'] == TRUE)
                        { ?>
                            <a href="?page=tools&selected=accountaccess">账户访问</a>
                    <?php } ?>
                    </ul>  
                <?php } ?>
        </ul>
        </div>
    </div>

<div id="header">
<div id="header_text">
        <?php if (isset($_SESSION['cw_staff']))
            { ?>    
                欢迎  
                <b><?php echo $_SESSION['cw_staff']; ?> </b> 
                <a href="?page=logout"><i>(注销)</i></a> &nbsp; | &nbsp;
                <a href="../" >返回网站</a>
            <?php }
            else
            {
                echo "<a href='../' >返回网站</a> | 请登录。";
            } ?>
</div>
</div>


<div id="wrapper">
<div id="middlecontent">
        <?php if (!isset($_SESSION['cw_staff']))
            { ?>  
                <br/>
                <center>
                    <h2>请登录</h2>
                    <input type="text" placeholder="Username" id="login_username" style="border: 1px solid #ccc;"/><br/> 
                    <input type="password" placeholder="Password" id="login_password" style="border: 1px solid #ccc;"/><br/>
                    <input type="submit" value="Log in" onclick="login('staff')"/> <br/>
                    <div id="login_status"></div>
                </center><?php
                }
                else
                { ?>
                <div class="box_right">
                    <?php
                    if (!isset($_GET['page']))
                        $page = "dashboard";
                    else
                    {
                        $page = $_GET['page'];
                    }
                    $pages = scandir('../aasp_includes/pages');
                    unset($pages[0], $pages[1]);

                    if (!file_exists('../aasp_includes/pages/' . $page . '.php'))
                    {
                        include "../aasp_includes/pages/404.php";
                    }
                    elseif (in_array($page . '.php', $pages))
                    {
                        include "../aasp_includes/pages/". $page .".php";
                    }
                    else
                    {
                        include "../aasp_includes/pages/404.php";
                    }
                } ?>
     </div>
</div>
        <?php if (isset($_SESSION['cw_staff']))
            { ?>
            <div id="rightcontent">
                <?php if ($GLOBALS['forum']['type'] == 'phpbb' && $GLOBALS['forum']['autoAccountCreate'] == TRUE && $page == 'dashboard')
                { ?>
                <div class="box_right">
                <div class="box_right_title">最近的论坛活动</div>
                <table>
                    <tr>
                        <th>账户</th>
                        <th>消息</th>
                        <th>主题</th>
                    </tr>
                    <?php
                    $GameServer->selectDB($GLOBALS['forum']['forum_db'], $conn);
                    $result = $Database->select("phpbb_posts", "poster_id, post_text, post_time, topic_id", null, null, "ORDER BY post_id DESC LIMIT 10");
                    $result = $result->get_result();
                    while ($row = $result->fetch_assoc())
                    {
                        $string   = $row['post_text'];
                        //Lets get the username			
                        $getUser  = $Database->select("phpbb_users", "username", null, "user_id=". $row['poster_id']);
                        $user     = $getUser->get_result()->fetch_assoc();
                        //Get topic
                        $getTopic = $Database->select("phpbb_topics", "topic_title", null, "topic_id=". $row['topic_id']);
                        $topic    = $getTopic->fetch_assoc(); ?>
                        <tr>
                        <td>
                            <a href="http://www.tbcstar.com/forum/memberlist.php?mode=viewprofile&u=<?php echo $row['poster_id']; ?>" title="View profile" target="_blank">
                               <?php echo $user['username']; ?>
                           </a>
                        </td>
                        <td><?php echo limit_characters(stripBBcode($string)); ?></td>
                        <td><a href="http://www.tbcstar.com/forum/viewtopic.php?t=<?php echo $row['topic_id'] ?>" title="View this topic" target="_blank">查看主题</a></td>
                        </tr><?php } ?>
                </table>
                </div><?php } ?>
                <div class="box_right">
                <div class="box_right_title">服务器状态 - <b><?php echo $GameServer->getServerStatus(1, TRUE); ?></b></div>
                <table>
                <tr valign="top">
                    <td>
                        在线玩家: <br/>
                        Active connections: <br/>
                        今天新帐户: <br/>
                    </td>
                    <td>
                        <b>
                        <?php echo $GameServer->getPlayersOnline(); ?><br/>
                        <?php echo $GameServer->getActiveConnections(); ?><br/>
                        <?php echo $GameServer->getAccountsCreatedToday(); ?><br/>
                        </b>
                    </td>
                </tr>
                </table>
                </div>

                <div class="box_right">
                <div class="box_right_title">网站配置</div>
                <table>
                <tr valign="top">
                    <td>
                    <tr>
                        <td>MySQL Host:</td>
                        <td>MySQL User:</td>
                        <td>MySQL Password:</td>
                    </tr>
                    </td>
                    <td>
                    <tr style='font-weight: bold;'>
                        <td><?php echo $GLOBALS['connection']['host']; ?></td>
                        <td><?php echo $GLOBALS['connection']['user']; ?></td>
                        <td>****<br/></td>
                    </tr>
                    </td>
                    <td>
                    <tr>
                        <td>Logon Database:</td>
                        <td>Website Database:</td>
                        <td>World Database:</td>
                        <td>Db Rev:</td>
                    </tr>                                      
                    </td>
                    <td>
                    <tr style="font-weight: bold;">
                        <td><?php echo $GLOBALS['connection']['logondb']; ?></td>
                        <td><?php echo $GLOBALS['connection']['webdb']; ?></td>
                        <td><?php echo $GLOBALS['connection']['worlddb']; ?></td>
                        <td><?php
                            $GameServer->selectDB("webdb", $conn);
                            $get = $Database->select("db_version", "version");
                            $row = $get->get_result()->fetch_assoc();
                            if ($row['version'] == null || empty($row['version'])) $row['version'] = '1.0';
                            echo $row['version']; ?>        
                        </td>
                    </tr>
                    </td>
                </tr>
                </table>
                </div>          
            </div>     
    <?php } ?>
</div>               
</div> 
<?php include "../aasp_includes/javascript_loader.php";
    if ( !isset( $_SESSION['cw_admin'] ) )
    {?>
        <script type="text/javascript">
            document.onkeydown = function (event)
            {
                var key_press = String.fromCharCode(event.keyCode);
                var key_code = event.keyCode;
                if (key_code == 13)
                    login("staff");
            }
        </script>
    <?php } ?>
</body>
</html> 