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
<div>
统计 </div>
</div>
<div class="realm_picker">
<div class="">
所在服务器：</div>
<a href="game.tbcstar.com">
时光回溯 </a>
</div>
</div>
<h3 class="main-title">游戏统计</h3>
<div class="statistic">
<div class="row">
<div class="col">
<div class="item">
</div>
</div>
<div class="col">
<div class="item">
<img src="/themes/cp_nefelin/images/stat-type-02.jpg" alt="">
<div class="item-content">
<div>PvE统计</div>
<a href="#" class="btn btn-low-green">查看</a>
</div>
</div>
</div>
<div class="col">
<div class="item">
<img src="/themes/cp_nefelin/images/stat-type-03.jpg" alt="">
<div class="item-content">
<div>击杀数</div>
<a href="?p=kills" class="btn btn-low-green">查看</a>
</div>
</div>
</div>
<div class="col">
<div class="item">
<img src="/themes/cp_nefelin/images/stat-type-04.jpg" alt="">
<div class="item-content">
<div>游戏时长</div>
<a href="?p=stats" class="btn btn-low-green">查看</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
</div>

<?php include "footer.php" ?>