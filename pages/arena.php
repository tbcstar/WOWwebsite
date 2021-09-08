<?php 
global $Account;
$Account->isNotLoggedIn();
$Connect->selectDB('webdb', $conn);
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
竞技场排名 </div>
</div>
<div class="realm_picker">
<div class="">
服务器: </div>
<a href="#">
时光回溯 </a>
</div>
</div>
<div class="content-box standart">
<div class="content-holder">
<div class="content-frame">
<div class="content">
<h2>竞技场排名</h2>
<br />



<?php

######################################################
# SQL连接
######################################################
$host = "game.tbcstar.com:3310";
$user = "root";
$pass = "A112233a"; 
$mangoscharacters = "characters";
$mangosrealm = "auth";
?>

<?


$j=1;
        $teamType = array(
                '2' => '2x2',
                '3' => '3x3',
                '5' => '5x5'
				);
				
$connect = new mysqli($host,$user,$pass) OR DIE("'Can't connect with $host"); 
$conn->select_db($mangoscharacters,$connect) or die($conn->error()); 


if(!isset($_GET['guid'])){

$sql = $conn->query("SELECT * FROM `arena_team` ORDER by `name`");

echo "

<table class='table'>
<tr>
<th>队伍名称</th>
<th>命令类型</th>
<th>队长</th>
<th>阵营</th>
<th>评分</th>
</tr>
";
while ($row = mysqli_fetch_array($sql)){
$query_num = $conn->query("SELECT COUNT(*) FROM `arena_team_member` WHERE `arenaTeamId`='$row[arenaTeamId]'");
$gleader = "SELECT name,race FROM `characters` WHERE `guid`='$row[captainGuid]'";
$myrow = mysqli_fetch_array($conn->query($gleader));
$top = $conn->query("SELECT * FROM `arena_team_stats` WHERE `arenaTeamId`='$row[arenaTeamId]'");
$toprow = mysqli_fetch_array($top);

if($myrow['race']=="1" or $myrow['race']=="3" or $myrow['race']=="4" or $myrow['race']=="7" or  $myrow['race']=="11"){
	
	$faction = "alliance_small";
	}else{
	$faction = "horde_small";}



echo "

<tr>
<td><a href=?page=arena&guid=".$row['arenaTeamId'].">".$row['name']."</a></td>
<td><center>".$teamType[$row['type']]."</center></td>
<td><center><a href=?page=arena&player=".$row['captainGuid'].">".$myrow['name']."</a></center></td>
<td><center><img src=images/".$faction.".png></center></td>
<td>".$row['rating']."</td>
</tr>
";

}
echo "</table></center><br><br>";
}

if (@$_GET['guid'] ) { 

$name = "SELECT * FROM `arena_team` WHERE `arenaTeamId`='$_GET[guid]'";
$nrow = mysqli_fetch_array($conn->query($name));
$top = "SELECT * FROM `arena_team` WHERE `arenaTeamId`='$_GET[guid]'";
$trow = mysqli_fetch_array($conn->query($top));
$member = "SELECT * FROM `arena_team_member` WHERE `arenaTeamId`='$_GET[guid]'";
$mrow = mysqli_fetch_array($conn->query($member));

$sql = $conn->query("SELECT * FROM `characters`, `arena_team_member` WHERE `characters`.`guid`=`arena_team_member`.`guid` and `arenaTeamId` = '".$_GET["guid"]."' ");
$row = mysqli_fetch_array($sql);
$data = explode(' ',$row['data']);
$lvl = $data[$ver];	
$gender = dechex($data[36]);
$gender = str_pad($gender,8, 0, STR_PAD_LEFT);
$gender = $gender{3};
$guid = $row['guid'];
$race = $row['race'];
$class = $row['class'];
$online = $row['online'];
$j=1;

echo "<center>
<table border=0 width=60%>
<tr>
<td>
<table border=1 width=100%>
<tr><td>队伍名称</td><td  >".$nrow['name']."</td></tr>
<tr><td>评分</td><td  >".$trow['rating']."</td></tr>
<tr><td>命令类型</td><td  >".$teamType[$nrow['type']]."</td></tr>
<tr><td colspan=2 >本周统计数据</td></tr>
<tr><td>Played: ".$trow['weekGames']."</td><td  >Won: ".$trow['weekWins']."</td></tr>
<tr><td colspan=2 >统计</td></tr>
<tr><td>Played: ".$trow['seasonGames']."</td><td  >Won: ".$trow['seasonWins']."</td></tr>


</table>
";

echo "<table border=1 width=100%>
<tr>
<td align=center>#</td>
<td align=center>玩家名字</td>
<td align=center>种族</td>
<td align=center>职业</td>
<td align=center>本周游戏</td>
<td align=center>本周胜利</td>
<td align=center>本赛季</td>
<td align=center>赛季胜利</td>
<td align=center>个人评分</td>
<td align=center>状态</td>
</tr>
";

echo "<tr>
<td valign=center width=3%>$j</td>
<td align=center valign=center width=20%><a href='?page=arena&player=".$guid."' style='color: #ff9900; font-family : Geneva; text-decoration : none;'>".$row['name']."</a></td>
<td align=center valign=center width=7%><img src=/themes/nefelin/images/race/".$race.".jpg></td>
<td align=center valign=center width=7%><img src=/themes/nefelin/images/class/".$class.".gif></td>
<td align=center width=20%>".$mrow['weekGames']."</td>
<td valign=center width=20%>".$mrow['weekWins']."</td>
<td valign=center width=10%>".$mrow['seasonGames']."</td>
<td valign=center width=10%>".$mrow['seasonWins']."</td>
<td valign=center width=10%>".$mrow['personalRating']."</td>
<td valign=center width=10%><center><img src='/themes/nefelin/images/".$online.".png' height='18' width='18'></center></td>
</tr>
";


echo "</table></td></tr></table></center><br><br><br>";

} 

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