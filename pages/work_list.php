<?php
global $Account, $Connect;
$conn = $Connect->connectToDB();
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
<a class="active" href="?p=characters">
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
<a href="?p=shop">
商城 </a>
<span class="ico-raquo"></span>
<div>
服务项目 </div>
</div>
<div class="realm_picker">
<div class="">
所在服务器：</div>
<a href="game.tbcstar.com">
时光回溯 </a>
</div>
</div>
<h3 class="main-title">编辑你的角色</h3>
<div class="shop-manchar">
<div class="row">
<div class="col">
<a href="?p=service&s=faction">
<div class="item">
<img src="/themes/cp_nefelin/images/manchar-01.jpg" alt="">
<div class="item-content">
改变阵营<div class="price">

<span class="coin-gold"></span>
<span class="numbers"> 350 </span>


</div>
</div>
</div>
</a>
</div>
<div class="col">
<a href="?p=service&s=race">
<div class="item">
<img src="/themes/cp_nefelin/images/manchar-02.jpg" alt="">
<div class="item-content">
改变种族 <div class="price">

<span class="coin-gold"></span>
<span class="numbers"> 250 </span>


</div>
</div>
</div>
</a>
</div>
<div class="col">
<a href="?p=service&s=appearance">
<div class="item">
<img src="/themes/cp_nefelin/images/manchar-03.jpg" alt="">
<div class="item-content">
角色修改 <div class="price">

 <span class="coin-gold"></span>
<span class="numbers"> 150 </span>


</div>
</div>
</div>
</a>
</div>
<div class="col">
<a href="?p=service&s=name">
<div class="item">
<img src="/themes/cp_nefelin/images/manchar-04.jpg" alt="">
<div class="item-content">
修改名字 <div class="price">

<span class="coin-gold"></span>
<span class="numbers"> 250 </span>


</div>
</div>
</div>
</a>
</div>
<div class="col">
<a href="?p=unstuck">
<div class="item">
<img src="/themes/cp_nefelin/images/manchar-06.jpg" alt="">
<div class="item-content">
角色卡死</div>
</div>
</a>
</div>

<div class="col">
<a href="?p=teleport">
<div class="item">
<img src="/themes/cp_nefelin/images/manchar-06.jpg" alt="">
<div class="item-content">
角色传送</div>
</div>
</a>
</div>


<div class="col">
<a href="#">
<div class="item">
<img src="/themes/cp_nefelin/images/manchar-06.jpg" alt="">
<div class="item-content">
角色升级</div>
</div>
</a>
</div>

<div class="col">
<a href="?p=revive">
<div class="item">
<img src="/themes/cp_nefelin/images/manchar-07.jpg" alt="">
<div class="item-content">
角色复活

</div>
</div>
</div>
</a>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
</div>

<?php include "footer.php" ?>