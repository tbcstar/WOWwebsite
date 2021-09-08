<?php
global $Account;
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
<a class="active" href="?page=shop">
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
商城 </div>
</div>
<div class="realm_picker">
<div class="">
所在服务器： </div>
<a href="game.tbcstar.com">
时光回溯 </a>
</div>
</div>
<div class="shop-categories en">
<div class="row">
<div class="col">
<div class="item item-1">
<div class="row">购买物品</div>
<a href="?page=voteshop" class="btn btn-low-yellow">现在开始</a>
</div>
</div>
<div class="col" style="opacity:0.1">
<div class="item item-2">
<div class="row">角色拍卖</div>
<a href="#" class="btn btn-low-yellow">现在开始</a>
</div>
</div>
<div class="col" style="opacity:0.1">
<div class="item item-6">
<div class="row">黄金交易所</div>
<a href="#" class="btn btn-low-yellow">现在开始</a>
</div>
</div>
<div class="col half">
<div class="item item-4">
<div class="row">编辑你的角色</div>
<a href="?page=work_list" class="btn btn-low-yellow">现在开始</a>
</div>
</div>
<div class="col half" style="opacity:0.1">
<div class="item item-5">
<div class="row">编辑公会</div>
<a href="javascript:void(0)" class="btn btn-low-yellow">现在开始</a>
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