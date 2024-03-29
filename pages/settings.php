<?php include "headers.php" ?>
<div class="container">
<div class="row">
<ul class="navbar-cp">
<li>
<a class="active" href="?p=ucp">
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
<a href="https://www.tbcstar.com/armory">
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

<div>
修改邮箱地址 </div>
</div>
<div class="realm_picker">
<div class="">
所在服务器：</div>
<a href="game.tbcstar.com">
时光回溯 </a>
</div>
</div>
<?php account::isNotLoggedIn(); 
if (isset($_POST['save'])) {
	account::changeEmail($_POST['email'],$_POST['current_pass']);
}
?><br /><br /><br />
<div class="content-box main">
<div class="content-holder">
<div class="content-frame">
<div class="content">
<h2>修改邮箱地址</h2>
<form action="?p=settings" method="post">
<div class="row">
<label for="PasswordForm_password">邮箱地址</label>:<br />
<input class="default" name="email" type="text" value="<?php echo account::getEmail($_SESSION['cw_user']); ?>" /> </div>
<div class="row">
<label for="PasswordForm_password">当前密码</label>:<br />
<input class="default" type="password" name="current_pass"/> </div>
</div>
</div>

<div class="row">
<input class="btn btn-yellow" type="submit" name="save" value="保存" /> </div>
</form> 
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