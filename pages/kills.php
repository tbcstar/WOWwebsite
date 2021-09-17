<?php 
    global $Account, $Database;
    $Account->isNotLoggedIn();
?>
<?php include "headers.php" ?>
<div class="container">
<div class="row">
<ul class="navbar-cp">
<li>
<a href="?page=ucp">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-01.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-01.png" alt="" /> </div>
<p>账户</p>
</a>
</li>
<li>
<a href="?page=shop">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-02.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-02.png" alt="" /> </div>
<p>商城</p>
</a>
</li>
<li>
<a href="?page=donate">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-03.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-03.png" alt="" /> </div>
<p>捐赠充值</p>
</a>
</li>
<li>
<a href="?page=characters">
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
<a class="active" href="?page=stat">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-06.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-06.png" alt="" /> </div>
<p>统计</p>
</a>
</li>
<li>
<a href="?page=vote">
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
<a href="?page=ucp">
控制面板 </a>
<span class="ico-raquo"></span>
<a href="?page=stat">
统计 </a>
<span class="ico-raquo"></span>
<div>
荣誉排名 </div>
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
<h2>荣誉排名</h2>
<br />

<?php

######################################################
# SQL连接
######################################################
$db_user = "root";
$db_pass = "A112233a!" ;
$db_host = "game.tbcstar.com";

######################################################
# 投票商店网站连接
######################################################

mysqli_connect("$db_host","$db_user", "$db_pass");



$arena_top = "characters";

######################################################
# 玩家在线表
######################################################

$player_online = "characters";

$uptime = "auth";


?>

<?php
$Database->conn->select_db("$arena_top");
$a=0;
$result = $Database->select( `name`, `race`, `class`, `gender`, `level`, totalKills, totalHonorPoints, totaltime FROM `characters` ORDER BY `totalKills` DESC LIMIT 5;");

$msg = $result->num_rows;
 if (!$msg){ 
     echo '
<table class="table">
<tr>
<th>名次</th>
<th>名字</th>
<th>等级</th>
<th>阵营</th>
<th>总击杀数</th>
</tr>
<center><br />不是PVP前5名！</center>
';}else{
echo '
<table class="table">
<tr>
<th>名次</th>
<th>名字</th>
<th>等级</th>
<th>阵营</th>
<th>总击杀数</th>
</tr>
';
 while ($row = $result->fetch_array())

   {
	   
if ($a<=5)
$a=$a+1;

      if($row['race']=="1" or $row['race']=="3" or $row['race']=="4" or $row['race']=="7" or  $row['race']=="11")
       {$faction = "<img src='images/icons/faction_alliance.gif' width='20' height='20' alt='Aliance'/>";}
       else{$faction = "<img src='images/icons/faction_horde.gif' width='20' height='20' alt='Horde'/>";}
	   
	   if($row['race']=="1" or $row['race']=="3" or $row['race']=="4" or $row['race']=="7" or  $row['race']=="11")
       {$faction2 = "联盟";}
       else{$faction2 = "部落";}
   
echo '


<tr>
<td>'.$a.'</td>
<td>
<div class="pull-right">'.$faction.'</div>
<a href="跳转到角色详细页面（参考https://cp.elysium-project.org/armory/char-1-5326438）">'.$row['name'].'</a> </td>
<td>'.$row['level'].'</td>
<td>'.$faction2.'</td>
<td>
'.$row['totalKills'].' </td>
</tr>

';}}
?>
</table>
</div>
<span class="image"></span>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
</div>

<?php include "footer.php" ?>