<?php require('includes/loader.php'); ?>
<!DOCTYPE">
<html">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $GLOBALS['website_title']; ?> 员工面板</title>
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
                <a href="?p=dashboard">仪表盘</a>
            </ul>
            <?php
            if($GLOBALS['staffPanel_permissions']['Pages']==true)
            {
            ?>     
            <li>页面</li>
            <ul class="hidden" <?php activeMenu('pages'); ?>>
                <a href="?p=pages">所有页面</a>
                <a href="?p=pages&s=new">添加页面</a>
            </ul>
            <?php
            }
            if($GLOBALS['staffPanel_permissions']['News']==true)
            {
            ?>
            <li>新闻</li>
            <ul class="hidden" <?php activeMenu('news'); ?>>
                <a href="?p=news">发布新闻</a>
                <a href="?p=news&s=manage">管理新闻</a>
            </ul>
            <?php
            }
            if($GLOBALS['staffPanel_permissions']['Shop']==true)
            {
            ?>          
            <li>商城</li>
            <ul class="hidden" <?php activeMenu('shop'); ?>>
                <a href="?p=shop">概述</a>
                <a href="?p=shop&s=add">添加物品</a>
                <a href="?p=shop&s=manage">管理物品</a>
                <a href="?p=shop&s=tools">工具</a>
            </ul> 
            <?php
            }
            if($GLOBALS['staffPanel_permissions']['Donations']==true)
            {
            ?>     
            <li>捐赠</li>
            <ul class="hidden" <?php activeMenu('donations'); ?>>
                <a href="?p=donations">概述</a>
                <a href="?p=donations&s=browse">浏览</a>
            </ul> 
            <?php
            }
            if($GLOBALS['staffPanel_permissions']['Logs']==true)
            {
            ?>     
            <li>日志</li>
            <ul class="hidden" <?php activeMenu('logs'); ?>>
                <a href="?p=logs&s=voteshop">投票商店</a>
                <a href="?p=logs&s=donateshop">公益商城</a>
            </ul> 
            <?php
            }
            if($GLOBALS['staffPanel_permissions']['Interface']==true)
            {
            ?>     
            <li>Interface</li>
            <ul class="hidden" <?php activeMenu('interface'); ?>>
                <a href="?p=interface">模板</a>
                <a href="?p=interface&s=menu">菜单</a>
                <a href="?p=interface&s=slideshow">幻灯片</a>
                <a href="?p=interface&s=plugins">插件</a>
            </ul> 
            <?php
            }
            if($GLOBALS['staffPanel_permissions']['Users']==true)
            {
            ?>     
            <li>用户</li>
            <ul class="hidden" <?php activeMenu('users'); ?>>
                <a href="?p=users">概述</a>
                <a href="?p=users&s=manage">管理用户</a>
            </ul> 
            <?php
            }
            if($GLOBALS['staffPanel_permissions']['Realms']==true)
            {
            ?>     
            <li>服务器</li>
            <ul class="hidden" <?php activeMenu('realms'); ?>>
                <a href="?p=realms">新增服务器</a>
                <a href="?p=realms&s=manage">管理服务器</a>
            </ul> 
            <?php
            }
            if($GLOBALS['staffPanel_permissions']['Services']==true)
            {
            ?>     
            <li>服务项目</li>
            <ul class="hidden" <?php activeMenu('services'); ?>>
                <a href="?p=services&s=voting">投票链接</a>
                <a href="?p=services&s=charservice">角色服务</a>
            </ul> 
            <?php
            }
            if($GLOBALS['staffPanel_permissions']['Tools->Tickets']==true || 
            $GLOBALS['staffPanel_permissions']['Tools->Account Access']==true)
            {
            ?>    
            <li>Tools</li>
            <ul class="hidden" <?php activeMenu('tools'); ?>>
                <?php if($GLOBALS['staffPanel_permissions']['Tools->Tickets']==true) {?>
                <a href="?p=tools&s=tickets">Tickets</a>
                <?php } ?>
                <?php if($GLOBALS['staffPanel_permissions']['Tools->Account Access']==true) { ?>
                <a href="?p=tools&s=accountaccess">账户访问</a>
                <?php } ?>
            </ul>  
            <?php
            }
            ?>
        </ul>
    </div>
</div>

<div id="header">
<div id="header_text">
  <?php if(isset($_SESSION['cw_staff'])) { ?> 欢迎  
     <b><?php echo $_SESSION['cw_staff']; ?> </b> 
     <a href="?p=logout"><i>(注销)</i></a> &nbsp; | &nbsp;
     <a href="<?php echo $GLOBALS['website_domain']; ?>" title="View your site">查看您的网站</a>
     <?php } else {
         echo "Please log in.";
     }?>
 </div>
</div>
      
      
<div id="wrapper">
<div id="middlecontent">
<?php if(!isset($_SESSION['cw_staff'])) { ?>  
<br/>
<center>
<h2>请登录</h2>
  <input type="text" placeholder="Username" id="login_username" style="border: 1px solid #ccc;"/><br/> 
  <input type="password" placeholder="Password" id="login_password" style="border: 1px solid #ccc;"/><br/>
  <input type="submit" value="Log in" onclick="login('staff')"/> <br/>
  <div id="login_status"></div>
</center>
 <?php 
 } 
 else 
 { 
 ?>
    <div class="box_right">
    <?php
		if(!isset($_GET['p']))
                 $page = "dashboard";
		 else 
		 { 
			 $page = $_GET['p']; }		   
			 $pages = scandir('../aasp_includes/pages');
			 unset($pages[0],$pages[1]);
			 
			 if (!file_exists('../aasp_includes/pages/'.$page.'.php'))
				 include('../aasp_includes/pages/404.php');
			 elseif(in_array($page.'.php',$pages))
				 include('../aasp_includes/pages/'.$page.'.php');
			 else
				 include('../aasp_includes/pages/404.php');              
		  }
    ?>
     </div>
</div>
    <?php if(isset($_SESSION['cw_staff']))  { ?>
    <div id="rightcontent">
		 <?php if($GLOBALS['forum']['type']=='phpbb' && $GLOBALS['forum']['autoAccountCreate']==TRUE) { ?>
         <div class="box_right">
         <div class="box_right_title">最近的论坛活动</div>
            <table>
                <tr>
                    <th>账户</th>
                    <th>消息</th>
                    <th>主题</th>
                </tr>
			<?php
            $server->selectDB($GLOBALS['forum']['forum_db']);
            global $conn;
            $result = mysqli_query($conn, "SELECT poster_id,post_text,post_time,topic_id FROM phpbb_posts ORDER BY post_id DESC LIMIT 10");
            while($row = mysqli_fetch_assoc($result)) 
			{
                $string = $row['post_text']; 
                //Lets get the username			
                $getUser = mysqli_query($conn, "SELECT username FROM phpbb_users WHERE user_id='".$row['poster_id']."'"); 
				$user = mysqli_fetch_assoc($getUser);
                //Get topic
                $getTopic = mysqli_query($conn, "SELECT topic_title FROM phpbb_topics WHERE topic_id='".$row['topic_id']."'"); 
				$topic = mysqli_fetch_assoc($getTopic);
            ?>
                <tr>
                    <td><a href="http://heroic-wow.net/forum/memberlist.php?mode=viewprofile&u=<?php echo $row['poster_id']; ?>" title="View profile" 
                    target="_blank"><?php echo $user['username']; ?></a></td>
                    <td><?php echo limit_characters(stripBBcode($string));?></td>
                    <td><a href="http://heroic-wow.net/forum/viewtopic.php?t=<?php echo $row['topic_id']?>" title="View this topic" target="_blank">
                    	查看主题</a></td>
                </tr>
            <?php } ?>
        </table>
         </div> 
             <?php } ?>
     <div class="box_right">
            <div class="box_right_title">服务器状态</div>
            <table>
               <tr valign="top">
                   <td>
                        在线玩家：<br/>
                        Active connections: <br/>
                        今天新帐户:<br/>
                   </td>
                   <td>
                   <b>
                       <?php echo $server->players_online;?><br/>
                       <?php echo $server->active_connections;?><br/>
                       <?php echo $server->accounts_today;?><br/>
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
                MySQL Host: <br/>
                MySQL User: <br/>
                MySQL Password: <br/>
                Core Revision: 
               </td>
               <td>
               <b>
               <?php echo $GLOBALS['connection']['host'];?><br/>
               <?php echo $GLOBALS['connection']['user']; ?><br/>
               <?php echo substr($GLOBALS['connection']['password'],0,4); ?>****<br/>
               <?php echo $GLOBALS['current_revision']; ?>
               </b>
               </td>
               <td>
               Logon Database: <br/>
               Website Database: <br />
               World Database: <br/>
               Database Revision: 
               </td>
               <td>
               <b>
               <?php echo $GLOBALS['connection']['logondb']; ?><br/>
               <?php echo $GLOBALS['connection']['webdb']; ?><br/>
               <?php echo $GLOBALS['connection']['worlddb']; ?><br/>
               <?php 
                     $server->selectDB('webdb');
                     $get = mysqli_query($conn, "SELECT version FROM db_version");
                     $row = mysqli_fetch_assoc($get);
                     echo $row['version']; ?>
               </b>
               </td>
           </tr>
    </table>
</div>          
</div>         
  <?php } ?>
</div>               
</div> 
	<?php include('../aasp_includes/javascript_loader.php'); ?>
</body>
</html>