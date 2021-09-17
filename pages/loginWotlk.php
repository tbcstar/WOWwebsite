<?php

global $Account, $Database;

if (!isset($_SESSION['cw_user'])) { 
if (isset($_POST['login'])) 
$Account->logIn($_POST['login_username'],$_POST['login_password'],$_SERVER['REQUEST_URI'],$_POST['login_remember']);

?>

<?php include "header.php" ?>

<div class="container">
<div class="row">
<div class="user-panel not-logged">
<button data-target="login-content" class="btn btn-yellow scrollToForm wow rotateInUpLeft">控制面板</button>
&nbsp; or &nbsp;
<button data-target="registration-content" class="btn btn-green scrollToForm wow rotateInUpRight">创建帐号</button>
</div>
</div>
</div>

<?php include "right.php" ?>

<section class="main-section with-sidebar">
<div class="newsbox clearfix">
<div class="newsbox clearfix">
<article class="news2 wow bounceInUp first" style="background-image: url(/images/news/183-699-386.jpg)">
<span class="ico-horn"></span>
<div class="date">2019年8月11日下午2:58</div>
<div class="">
<h3 class="title">
<a href="/news/183-Dark+Portal+Opening+Event">
黑暗之门开启事件 </a>
</h3>
<div class="content">
August 11th - 7:00 PM GMT +2
&nbsp;
After years of battling monsters and rival factions on Elysium&rsquo;s Vanilla servers, The Dark Portal will open and allow our heroes to pour into Outland.
&nbsp;
Sunday, August 11th Dark Portal will open to all of Azeroth. Players who are level 60 will finally be able to walk into outland and fight against the Burning Legion </div>
</div>
<div class="readmore">
<div class="fadeout"></div>
<a href="/news/183-Dark+Portal+Opening+Event" class="btn">阅读全文</a>
</div>
</article>
<article class="news2 wow bounceInUp " style="background-image: url(/images/news/182-334-392.jpg)">
<span class="ico-horn"></span>
<div class="date">August 7, 2019, 1:50 PM</div>
<div class="news-content">
<h3 class="title">
<a href="/news/182-Important%21+TBC+Release+Announcement%2C+timeline+inside%21">
重要!TBC发布公告，时间线在里面!</a>
</h3>
<div class="content">
问候的旅行者!我们很高兴的宣布我们的时间表为TBC的发布!
以下是我们的时间表:
- 8月8日，Realms进入故障状态 </div>
</div>
<div class="readmore">
<div class="fadeout"></div>
<a href="/news/182-Important%21+TBC+Release+Announcement%2C+timeline+inside%21" class="btn">阅读全文</a>
</div>
</article>
<article class="news2 wow bounceInUp " style="background-image: url(/images/news/182-334-392.jpg)">
<span class="ico-horn"></span>
<div class="date">July 13, 2019, 3:17 PM</div>
<div class="news-content">
<h3 class="title">
<a href="/news/180-News%3A">
新闻： </a>
</h3>
<div class="content">
时间到了!本周日下午2:00加入我们，参加我们新的TBC领域的公众压力测试!参与者可以赢得一些s </div>
</div>
<div class="readmore">
<div class="fadeout"></div>
<a href="/news/180-News%3A" class="btn">阅读全文</a>
</div>
</article>
</div>
<div class="readmore">
<a href="#">所有新闻</a></div>
</div>
</section>
</div>
</div>
</main>

<?php 
$Account->isLoggedIn();
if ($_POST['register']) {
	$Account->register($_POST['username'],$_POST['email'],$_POST['password'],$_POST['password_repeat'],$_POST['referer'],$_POST['captcha']);
} 
?>

<section class="section-panel">
<div class="container">
<div class="form-content registration-content active">
<h3 class="title">注册一个账号</h3>
<div class="section-content">


<input type="hidden" value="<?php echo $_GET['id']; ?>" id="referer" />
<span id="username_check" style="display:none;"></span>

<div class="errors"></div>
<div class="row">
<div class="form-group">
<label>E-mail：</label>
<div class="form-control">
<input id="email" type="text" class="inputbox" alt="email" size="38" placeholder="E-mail" onfocus="this.placeholder = ''" onblur="this.placeholder = 'E-mail'" value="<?php echo $_POST['email']; ?>">
</div>
</div>
<div class="form-group">
<label>用户名：</label>
<div class="form-control">
<input id="username" type="text" class="inputbox" alt="username" size="38" maxlength="16" placeholder="Username" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Account'" value="<?php echo $_POST['username']; ?>" onkeyup="checkUsername()"/>
</div>
</div>
<div class="form-group">
<label>密码：</label>
<div class="form-control">
<input id="password" type="password" class="inputbox" alt="password" size="38" maxlength="16" placeholder="Password" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Password'">
</div>
</div>
<div class="form-group">
<label>确认密码：</label>
<div class="form-control">
<input id="password_repeat" type="password" class="inputbox" alt="Repeat the password" size="38" maxlength="16" placeholder="Repeat the Password" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Repeat Password'">
</div>
</div>
</div>
<div class="row">
<div class="form-group text-right captcha">
<div id="greCaptcha"></div> </div>
<div class="form-group text-right">

<input type="submit" class="btn btn-green" value="Sign Up" onclick="register(<?php if($GLOBALS['registration']['captcha']==TRUE)  echo 1;  else  echo 0; ?>)" id="register"/>


</div>
</div>
<div class="row text-center">
<div class="form-link">
<a href="" data-target="login-content" class="showFormContent">我已经有账户了</a>
</div>
</div>
</form>
</div>
</div>
<div class="form-content login-content">
<h3 class="title">登录你的账号</h3>
<div class="section-content">
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="login_form">
<div class="errors"></div>
<div class="row">
<div class="form-group form-group-lg">
<label>用户名：</label>
<div class="form-control">
<input type="text" name="login_username" id='customID1'>
</div>
</div>
<div class="form-group form-group-lg">
<label>密码：</label>
<div class="form-control">
<input type="password" name="login_password">
</div>
</div>
</div>
<div class="row">
<div class="form-group text-right captcha"></div>
<div class="form-group text-right">
<button type="submit" class="btn btn-green" name="login" id='customID2'>Log in</button>
</div>
</div>
<div class="row">
<div class="pull-left form-link" style="background-color: transparent; text-align: left; padding: 16px 9px 20px;">
<a href="" data-target="registration-content" class="showFormContent">
我想注册一个新账户 </a>
</div>
<div class="pull-right form-link " style="background-color: transparent; text-align: right; padding: 16px 9px 20px;">
<a href="#">
密码恢复 </a>
</div>
</div>
</div>
</div>
</div>
</section>
</div>
<footer id="footer">
<div class="container">
<div class="row clearfix">
<div id="footer-copy" class="wow fadeInUp">
&copy; 2021 - 2022 <a href="http://www.tbcstar.com/">TBCstar 时光回溯，亚洲最佳无类别服务器。</a>
</div>
</div>
</div>
</footer>
<script type="text/javascript" src="/themes/nefelin/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="/themes/nefelin/js/custom.js"></script>



<script>
    var input = document.getElementById('customID1');
    var submit = document.getElementById('customID2');
    
    submit.addEventListener('click', function(event){
        input.value = input.value.replace(/ /g,'');
        console.log(input.value);
    });
</script>
</body>
</html>

<?php } ?>

<?php if(isset($_SESSION['cw_user'])) { ?>
<meta http-equiv="refresh" content="0;url=?page=account">
<?php } ?>