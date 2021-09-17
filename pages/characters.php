<?php 
global $Account, $Website, $Connect, $Character;
$conn = $Connect->connectToDB();
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
<a class="active" href="?page=characters">
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
<a href="?page=stat">
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
<div>
你的角色 </div>
</div>
<div class="realm_picker">
<div class="">
所在服务器： </div>
<a href="game.tbcstar.com">
时光祭坛 </a>
</div>
</div>


<section class="my-characters">
<h3 class="services character">你在服务器上的角色 <span>时光回溯</span></h3>
<ul class="character">
<?php 

$Account->isNotLoggedIn();
$Connect->selectDB("webdb", $conn);
$num = 0;
$result = $conn->query('SELECT char_db,name FROM realms ORDER BY id ASC');
while($row = $result->fetch_assoc()) 
{
	$acct_id = $Account->getAccountID($_SESSION['cw_user']);
	$realm = $row['name'];
	$char_db = $row['char_db'];
		          	
	$Connect->selectDB($char_db);
	$result = $conn->query('SELECT name,guid,gender,class,race,level,online FROM characters WHERE account='.$acct_id);
	while($row = $result->fetch_assoc()) {
	?>
<li class="character_1">
<div class="content">

<img class="class-img" src="/images/class/<?php echo $row['class']; ?>.png" alt="">

<h5><a href="#"><?php echo $row['name']; ?></a></h5>
<ul class="fraction">
<li class="fraction_1">
阵营<br>
<span class="alliance">
联盟 </span>
</li>
<li class="fraction_2">
职业<br>
<img class="class-img" src="/images/class/<?php echo $row['class']; ?>.gif" border="none">
</li>
<li class="fraction_3">
种族<br>
<img class="class-img" src="/images/race/<?php echo $row['race']; ?>.gif" border="none">
</li>
<li class="fraction_4">
等级<br>
<span><?php echo $row['level']; ?></span>
</li>
</ul>
<p>游戏时间:<span class="time">0 h. 19 m. </span></p>
</div>
<a class="change first" href="?page=work_list">编辑</a> <a class="change last" href="#">出售</a></li>

<?php 

}
}

?>
</ul>
<a href="?page=characters" class="btn btn-more-big">显示所有角色</a>

</section>
</div>
</div>
</div>
</main>
</div>
<?php include "footer.php" ?>