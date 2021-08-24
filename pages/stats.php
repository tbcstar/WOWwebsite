<?php 
global $Account;
$Account->isNotLoggedIn();
?>
<?php include "headers.php" ?>
<div class="container">
<div class="row">
<ul class="navbar-cp">
<li>
<a href="?p=ucp">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-01.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-01.png" alt="" /> </div>
<p>账户</p>
</a>
</li>
<li>
<a href="?p=shop">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-02.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-02.png" alt="" /> </div>
<p>商城</p>
</a>
</li>
<li>
<a href="?p=donate">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-03.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-03.png" alt="" /> </div>
<p>捐赠充值</p>
</a>
</li>
<li>
<a href="?p=characters">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-04.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-04.png" alt="" /> </div>
<p>角色</p>
</a>
</li>
<li>
<a href="#">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-05.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-05.png" alt="" /> </div>
<p>查找角色</p>
</a>
</li>
<li>
<a class="active" href="?p=stat">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-06.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-06.png" alt="" /> </div>
<p>统计</p>
</a>
</li>
<li>
<a href="?p=vote">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-07.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-07.png" alt="" /> </div>
<p>投票</p>
</a>
</li>
</ul>
</div>
</div>
</header>
<main id="content-wrapper">
<div class="container">
<div class="row">
<div class="column">
<div class="head-content">
<div class="breadcrumbs">
<a href="?p=ucp">
控制面板 </a>
<span class="ico-raquo"></span>
<a href="?p=stat">
统计 </a>
<span class="ico-raquo"></span>
<div>
游戏时长 </div>
</div>
<div class="realm_picker">
<div class="">
所在服务器： </div>
<a href="game.tbcstar.com">
时光回溯 </a>
</div>
</div>
<div class="content-box standart">
<div class="content-holder">
<div class="content-frame">
<div class="content">
</aside>
<?php

######################################################
# SQL连接
######################################################
$db_user = "root";
$db_pass = "A112233a" ;
$db_host = "game.tbcstar.com:3310";

######################################################
# 投票商店网站连接
######################################################

mysql_connect("$db_host","$db_user", "$db_pass");



$arena_top = "characters";

######################################################
# 玩家在线表
######################################################

$player_online = "characters";

$uptime = "auth";


?>
<?php
// Uptime
mysql_select_db("$uptime");
$reponse = mysql_query("SELECT uptime, starttime, maxplayers FROM `uptime` ORDER BY `uptime`.`starttime` DESC") or die(mysql_error());
$donnees = mysql_fetch_array($reponse);
$temps = $donnees['uptime'];
$mx = $donnees['maxplayers'];
$day = floor($temps / 86400);
if($day > 0)
$days = $day.'';
else
$days = '0';
$hours = floor(($temps - ($day * 86400)) / 3600);
if($hours < 10)
$hours = ''.$hours;
$min = floor(($temps - (($hours * 3600) + ($day * 86400))) / 60);
if ($min < 10)
$min = "".$min;

$sec = $temps - ($day * 86400) - ($hours * 3600) - ($min * 60);
if ($sec < 10)
$sec = "".$sec;
// Uptime

// 在线玩家
mysql_select_db("$player_online");
$sql = "SELECT SUM(online) FROM characters";
$sqlquery = mysql_query($sql) or die(mysql_error());
$memb = mysql_result($sqlquery,0,0); 
$asql = "SELECT SUM(online) FROM characters WHERE race IN(1,3,4,7,11)";
$asqlquery = mysql_query($asql) or die(mysql_error());
$amemb = mysql_result($asqlquery,0,0);  

$hsql = "SELECT SUM(online) FROM characters WHERE race IN(2,5,6,8,10)";
$hsqlquery = mysql_query($hsql) or die(mysql_error());
$hmemb = mysql_result($hsqlquery,0,0); 
$chars = mysql_query("SELECT guid FROM characters");
$char = mysql_num_rows($chars);


mysql_select_db("$uptime");
$selalcm = mysql_query("SELECT id FROM account");
$acc = mysql_num_rows($selalcm);


$da = mysql_query("SELECT COUNT(*) FROM account WHERE last_login LIKE '%".date('Y-m-d')."%'");
$daaa = mysql_result($da,0);

$da2 = mysql_query("SELECT COUNT(id) FROM account WHERE online=1");
$daa2 = mysql_result($da2,0);

$da3 = mysql_query("SELECT COUNT(id) FROM account WHERE joindate LIKE '%".date("Y-m-d")."%'");
$daa3 = mysql_result($da3,0);

$da4 = mysql_query("SELECT COUNT(id) FROM account WHERE last_login LIKE '%".date("Y-m")."%'");
$daa4 = mysql_result($da4,0);

// 在线玩家
?>

<section class="main-section with-sidebar">
<div class="newsbox clearfix">
<section class="inner_about">
<div class="table">
<h2>服务器统计信息</h2>
<table class="spoiler">
<p>帐号注册: <?php echo ''.$acc.''; ?></p>
<p>每天唯一玩家：<?php echo ''.$daa4.''; ?></p>
<p>每天创建的账户: <?php echo ''.$daa3.''; ?></p>

<br>
<table class="spoiler">
<center><img src="/themes/nefelin/images/bc.gif" width="50">
<h2>当前伺服器统计</h2></center>
<tr>
<th>伺服器</th>
<th>状态</th>
<th>在线</th>
<th>最大在线</th>
<th>角色</th>
<th>连续运行时间</th>
</tr>
<tr>
<td><a href="#" class="spoiler-button">时光祭坛</a></td>
<td>在线</td>
<td><?php echo $memb; ?></td>
<td><?php echo "$mx"; ?></td>
<td><?php echo ''.$char.''; ?></td>
<td><?php echo "$days"; ?> 天 <?php echo "$hours"; ?> 小时 <?php echo "$min"; ?> 分钟 <?php echo "$sec"; ?> 秒</td>
</tr>
<tr>
<td colspan="6">
<table>
<tr>
<th>联盟</th>
<th>部落</th>
<th>排队中</th>
<th>游戏中</th>
<th>连续运行时间</th>
</tr>
<tr>
<td>
<img src="/images/alliance_small.png" /> <?php echo $amemb; ?> </td>
<td>
<img src="/images/horde_small.png" /> <?php echo $hmemb; ?> </td>
<td>无</td>
<td><?php echo ''.$daa2.''; ?></td>
<td><?php echo "$days"; ?> 天 <?php echo "$hours"; ?> 小时 <?php echo "$min"; ?> 分钟 <?php echo "$sec"; ?> 秒 &nbsp;</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</section>
</div>
</section>
</div>
</div>
</main>
</div>

<?php include "footer.php" ?>