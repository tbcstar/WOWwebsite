<?php
    require "includes/loader.php";
    global $GameServer;
    $conn = $GameServer->connect();
?>
<!DOCTYPE>
<html>
	<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo DATA['website']['title']; ?> 管理面板</title>
	<link rel="stylesheet" href="/aasp_includes/styles/default/style.css" />
	<link rel="stylesheet" href="/aasp_includes/styles/wysiwyg.css" />
	<script type="text/javascript" src="/aasp_includes/js/jquery.min.js"></script>
	<script type="text/javascript" src="/javascript/jquery.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/account.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/interface.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/account.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/server.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/news.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/logs.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/shop.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/wysiwyg.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/wysiwyg/wysiwyg.image.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/wysiwyg/wysiwyg.link.js"></script>
	<script type="text/javascript" src="/aasp_includes/js/wysiwyg/wysiwyg.table.js"></script>

	</head>

	<body>
	<div id="overlay"></div>
	<div id="loading"><img src="/aasp_includes/styles/default/images/ajax-loader.gif" /></div>
	<div id="leftcontent">
		<div id="menu_left">
			<ul>
				<li id="menu_head">菜单</li>

				<li>仪表盘</li>
					<ul class="hidden" <?php activeMenu('dashboard'); ?>>
						<a href="?page=dashboard">控制面板</a>
						<a href="?page=updates">更新</a>
					</ul>

				<li>页面</li>
					<ul class="hidden" <?php activeMenu('pages'); ?>>
						<a href="?page=pages">所有页面</a>
						<a href="?page=pages&selected=new">添加新页面</a>
					</ul>

				<li>新闻</li>
					<ul class="hidden" <?php activeMenu('news'); ?>>
						<a href="?page=news">发布新闻</a>
						<a href="?page=news&selected=manage">管理新闻</a>
					</ul>

				<li>商城</li>
					<ul class="hidden" <?php activeMenu('shop'); ?>>
						<a href="?page=shop">总览</a>
						<a href="?page=shop&selected=add">添加物品</a>
						<a href="?page=shop&selected=manage">管理物品</a>
						<a href="?page=shop&selected=tools">工具</a>
					</ul>

				<li>捐赠</li>
					<ul class="hidden" <?php activeMenu('donations'); ?>>
						<a href="?page=donations">总览</a>
						<a href="?page=donations&selected=browse">浏览</a>
					</ul>

				<li>日志</li>
					<ul class="hidden" <?php activeMenu('logs'); ?>>
						<a href="?page=logs&selected=voteshop">投票商店</a>
						<a href="?page=logs&selected=donateshop">公益商城</a>
						<a href="?page=logs&selected=admin">管理面板</a>
					</ul>

				<li>Interface</li>
					<ul class="hidden" <?php activeMenu('interface'); ?>>
						<a href="?page=interface">模板</a>
						<a href="?page=interface&selected=menu">菜单</a>
						<a href="?page=interface&selected=slideshow">幻灯片</a>
						<a href="?page=interface&selected=plugins">插件</a>
					</ul>

				<li>用户</li>
					<ul class="hidden" <?php activeMenu('users'); ?>>
						<a href="?page=users">总览</a>
						<a href="?page=users&selected=manage">管理用户</a>
					</ul>

				<li>服务器</li>
					<ul class="hidden" <?php activeMenu('realms'); ?>>
						<a href="?page=realms">添加服务器</a>
						<a href="?page=realms&selected=manage">服务器管理</a>
					</ul>

				<li>服务项目</li>
					<ul class="hidden" <?php activeMenu('services'); ?>>
						<a href="?page=services&selected=voting">投票链接</a>
						<a href="?page=services&selected=charservice">角色服务</a>
					</ul>

				<li>工具</li>
					<ul class="hidden" <?php activeMenu('tools'); ?>>
						<a href="?page=tools&selected=tickets">工单</a>
						<a href="?page=tools&selected=accountaccess">账号访问</a>
					</ul>      
			</ul>
		</div>
	</div>

        <div id="header">
            <div id="header_text">
                <?php if (isset($_SESSION['cw_admin']))
                    { ?> 欢迎  
                        <b><?php echo $_SESSION['cw_admin']; ?> </b> 
                        <a href="?page=logout"><i>(Log out)</i></a> &nbsp; | &nbsp;
                        <a href="../">返回网站</a>
                        <?php
                    }
                    else
                    {
                        echo "<a href='../'>返回网站</a> | 请登录。";
                    }
                ?>
            </div>
        </div>

        <div id="wrapper">
            <div id="middlecontent">
<?php if (!isset($_SESSION['cw_admin']))
    { ?>  
                        <br/>
                        <center>
                            <h2>请登录</h2>
                            <input type="text" placeholder="Username" id="login_username" style="border: 1px solid #ccc;"/><br/> 
                            <input type="password" placeholder="Password" id="login_password" style="border: 1px solid #ccc;"/><br/>
                            <input type="submit" value="Log in" onclick="login('admin')"/> <br/>
                            <div id="login_status"></div>
                        </center>
                            <?php
                        }
                        else
                        {
                            ?>
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
                                include "../aasp_includes/pages/" . $page . ".php";
                            }
                            else
                            {
                                include "../aasp_includes/pages/404.php";
                            }
                        }
                    ?>
            <?php if (DATA['website']['forum']['type'] == "phpbb" && DATA['website']['forum']['auto_account_create'] == TRUE && $page == 'dashboard')
    { ?>
                            <div class="box_right">
                                <div class="box_right_title">最近的论坛活动</div>
                                <table width="100%">
                                    <tr>
                                        <th>账户</th>
                                        <th>标题</th>
                                        <th>消息</th>
                                        <th>主题</th>
                                    </tr>
                                    <?php
                                    $GameServer->selectDB(DATA['website']['forum']['db']);
                                    $result = $Database->select("phpbb_posts", "poster_id, post_text, post_time, topic_id", null,null,"ORDER BY post_id DESC LIMIT 10")->get_result();
                                    while ($row    = $result->fetch_assoc())
                                    {
                                        $string   = $row['post_text'];
                                        //Lets get the username     
                                        $getUser  = $Database->select("phpbb_users", "username", null, "user_id=". $row['poster_id'])->get_result();
                                        $user     = $getUser->fetch_assoc();
                                        //Get topic
                                        $getTopic = $Database->select("phpbb_topics", "topic_title", null, "topic_id=". $row['topic_id'])->get_result();
                                        $topic    = $getTopic->fetch_assoc();
                                        ?>
                                        <tr class="center">
                                            <td><a href="http://www.tbcstar.com/forum/memberlist.php?mode=viewprofile&u=<?php echo $row['poster_id']; ?>" title="View profile" 
                                                   target="_blank"><?php echo $user['username']; ?></a></td>
                                            <td><?php echo $topic['topic_title']; ?></td>
                                            <td><?php echo limit_characters(strip_tags($string), 75); ?>...</td>
                                            <td><a href="<?php echo DATA['website']['domain'] . substr(DATA['website']['forum']['path'], 1); ?>viewtopic.php?t=<?php echo $row['topic_id'] ?>" 
                                                   title="View this topic" target="_blank">
                                                    查看主题</a></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div> 
    <?php } ?>
                </div>

            </div>
    <?php if ( isset($_SESSION['cw_admin']) )
    { ?>
                    <div id="rightcontent">
                        <div class="box_right">
                            <div class="box_right_title">服务器状态</div>
        <?php $GameServer->serverStatus(); ?>
                        </div>    

                        <div class="box_right">
                            <div class="box_right_title">网站配置</div>
                            <table>
                                <tr valign="top">
                                    <td>

                                    <tr>
                                        <td>MySQL Host: </td>
                                        <td>MySQL User: </td>
                                        <td>MySQL Password: </td>
                                    </tr>

                                    </td>
                                    <td>

                                    <tr style="font-weight: bold;">
                                        <td><?php echo DATA['website']['connection']['host']; ?></td>
                                        <td><?php echo DATA['website']['connection']['username']; ?></td>
                                        <td>****<br/></td>
                                    </tr>

                                    </td>
                                    <td>

                                    <tr>
                                        <td>Logon Database: </td>
                                        <td>Website Database: </td>
                                        <td>World Database: </td>
                                        <td>Db Rev: </td>
                                    </tr>

                                    </td>
                                    <td>
                                    <tr style="font-weight: bold;">
                                        <td><?php echo DATA['logon']['database']; ?></td>
                                        <td><?php echo DATA['website']['connection']['name']; ?></td>
                                        <td><?php echo DATA['world']['database']; ?></td>
                                        <td>
                                            <?php
                                                $GameServer->selectDB("webdb");
                                                $get = $Database->select("db_version", "version")->get_result();
                                                $row = $get->fetch_assoc();
                                                if ($row['version'] == null || empty($row['version'])) $row['version'] = '1.0';
                                                echo $row['version'];
                                            ?>
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
<?php
    include "../aasp_includes/javascript_loader.php";
    if (!isset($_SESSION['cw_admin']))
    {
        ?>
            <script type="text/javascript">
                document.onkeydown = function (event)
                {
                    var key_press = String.fromCharCode(event.keyCode);
                    var key_code = event.keyCode;
                    if (key_code == 13)
                    {
                        login('admin')
                    }
                }
            </script>
    <?php } ?>
</body>
</html>