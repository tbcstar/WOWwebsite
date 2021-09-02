<?php
global $Account, $Website, $Connect;
$conn = $Connect->connectToDB();

$Account->isNotLoggedIn();
?>
<?php include "headers.php" ?>
<?php include "menus.php" ?>
<main id="content-wrapper">
<div class="container">
<div class="row">
<div class="column">
<div class="head-content">
<div class="breadcrumbs">
<a href="?p=ucp">
控制面板 </a>
<span class="ico-raquo"></span>
<div>
账户 </div>
</div>
<div class="realm_picker">
<div class="">
所在服务器：</div>
<a href="game.tbcstar.com">Nefelin-WoW </a>
</div>
</div>
<h3 class="main-title">信息</h3>
<div class="general-info">
<div class="item item-1">
<div class="item__info">
<p>E-mail地址</p>
<div><?php echo $Account->getEmail($_SESSION['cw_user']);?></div>
</div>
<a href="?p=settings" class="btn btn-low-yellow">更换</a>
</div>
<div class="item item-2 tfa">
<div class="item__info">
<p>帐户状态</p>
<div><span><?php echo $Account->checkBanStatus($_SESSION['cw_user']);?></span></div>
</div>
<a class="btn btn-low-yellow" href="#">启用</a> </div>
<div class="item item-3">
<div class="item__info">
<p>帐户余额</p>
<div><span class="coin-gold"></span> <span class="count-gold"><?php echo $Account->loadVP($_SESSION['cw_user']); ?></span></div>
</div>
<a href="?p=donate" class="btn btn-low-yellow">添加</a>
</div>
<div class="item item-4">
<div class="item__info">
<p>上次登录</p>
<div><span class="numbers"><?php echo $Account->getJoindate($_SESSION['cw_user']); ?></span></div>
<p>最后IP</p>
<div><span class="numbers">*********</span></div>
</div>
<a href="?p=changepass" class="btn btn-low-yellow">修改密码</a>
</div>
</div>
<section class="extra-info">
</section>

<section class="my-characters">
<h3 class="services character">你在服务器上的角色 <span>时光回溯 </span></h3>
<ul class="character">
<?php 

$Account->isNotLoggedIn();
$Connect->selectDB('webdb', $conn);
$num = 0;
$result = mysql_query('SELECT char_db,name FROM realms ORDER BY id ASC');
while($row = mysql_fetch_assoc($result)) 
{
	$acct_id = $Account->getAccountID($_SESSION['cw_user']);
	$realm = $row['name'];
	$char_db = $row['char_db'];
		          	
	$Connect->selectDB($char_db, $conn);
	$result = mysql_query('SELECT name,guid,gender,class,race,level,online FROM characters WHERE account='.$acct_id);
	while($row = mysql_fetch_assoc($result)) {
	?>
<li class="character_1">
<div class="content">

<img class="class-img" src="/images/class/class-02.png" alt="">

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
<img class="class-img" src="/themes/nefelin/images/race/<?php echo $row['race']; ?>.jpg" border="none">
</li>
<li class="fraction_4">
等级<br>
<span><?php echo $row['level']; ?></span>
</li>
</ul>
<p>在线时长： <span class="time">0 h. 19 m. </span></p>
</div>
<a class="change first" href="?p=work_list">编辑</a> <a class="change last" href="#">出售</a></li>

<?php 

}
}

?>
</ul>
<a href="#" class="btn btn-more-big">显示所有角色</a>

</section>
</div>
</div>
</div>
</main>
</div>
<?php include "footer.php" ?>