<?php 
global $Account, $Website;
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
<a href="?p=stat">
<div class="nav-img">
<img src="/themes/cp_nefelin/images/cp-nav-06.png" alt="" /> <img class="hov" src="/themes/cp_nefelin/images/cp-nav-hov-06.png" alt="" /> </div>
<p>统计</p>
</a>
</li>
<li>
<a class="active" href="?p=vote">
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
<div>
为服务器投票 </div>
</div>
<div class="realm_picker">
<div class="">
所在服务器：</div>
<a href="game.tbcstar.com">
时光回溯 </a>
</div>
</div>
<ul class="vote-nav clearfix">
<li>
<span class="vote-icon"></span>
<p><span><span class="numbers">每票</span>投票积分</span>2点</p>
</li>
<li class="middle">
<span class="vote-icon"></span>
<p>You can't vote more <br>than <span><span class="numbers">2</span> time in <span class="numbers">24</span> hours</span></p>
</li>
<li>
<span class="vote-icon"></span>
<p>To receive the coins you need<br>to write <span>the name of your account</span> (if needed)</p>
</li>
</ul>
<h3 class="main-title">选择TOP进行投票</h3>
<div class="vote">
<div class="row">

<h4 class="yellow_text">投票积分：<?php echo $Account->loadVP($_SESSION['cw_user']); ?></h4>

<?php $Website->loadVotingLinks(); ?>

</div>
</div>
</div>
</div>
</div>
</main>
</div>


<?php include "footer.php" ?>