<?php
    global $Account;
    $Account->isNotLoggedIn();
?>

<?php include "header.php" ?>

<div class="container">
<div class="row">
<div class="user-panel logged">
<div class="cp-item wow shake">
<span class="cp-icon ico-acc"></span>
<p>欢迎回来</p>
<p><span><?php echo ucfirst(strtolower($_SESSION['cw_user']));?></span></p>
<?php 
			if (isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=$GLOBALS['adminPanel_minlvl'] && $GLOBALS['adminPanel_enable']==true) 
				echo ' <a href="admin/">(管理面板)</a>';
				
			if (isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=$GLOBALS['staffPanel_minlvl'] && $GLOBALS['staffPanel_enable']==true) 
				echo ' <a href="staff/">(员工面板)</a>';
			?>
</div>
<div class="cp-item wow shake">
<span class="cp-icon ico-coins"></span>
<p>帐户余额</p>
<p>
<span class="coin-gold"></span> <span class="count-gold"><?php echo $Account->loadVP($_SESSION['cw_user']); ?></span>
</p>
</div>
<div class="cp-item wow shake">
<span class="cp-icon ico-cp"></span>
<p>进入</p>
<p><a href="?p=ucp"><span>控制面板</span></a></p>
</div>
<div class="cp-item wow shake last">
<?php if(isset($_SESSION['cw_user'])) { ?>
<a href='?p=logout&last_page=<?php echo $_SERVER["REQUEST_URI"]; ?>'><span class="ico-exit"></span></a>
<?php } ?>
</div>
</div>
</div>
</div>

<?php include "right.php" ?>

<section class="main-section with-sidebar">
<div class="newsbox clearfix">
<div class="newsbox clearfix">
<article class="news2 wow bounceInUp first" style="background-image: url(/images/news/183-699-386.jpg)">
<span class="ico-horn"></span>
<div class="date">2020年10月31日下午2:58</div>
<div class="">
<h3 class="title">
<a href="?p=news/183-Dark+Portal+Opening+Event">
黑暗之门开启事件 </a>
</h3>
<div class="content">
8月11日-格林尼治时间下午7点+2
&nbsp;
在极乐空间的服务器上与怪物和敌对派系战斗数年后，黑暗之门将会打开，让我们的英雄们涌入外域。
&nbsp;
8月11日星期日黑暗之门将向艾泽拉斯所有人开放。60级的玩家将最终能够走进外域并与燃烧军团战斗 </div>
</div>
<div class="readmore">
<div class="fadeout"></div>
<a href="?p=news/183-Dark+Portal+Opening+Event" class="btn">阅读全文</a>
</div>
</article>
<article class="news2 wow bounceInUp " style="background-image: url(/images/news/182-334-392.jpg)">
<span class="ico-horn"></span>
<div class="date">2019年8月7日下午1点50分</div>
<div class="news-content">
<h3 class="title">
<a href="?p=news/182-Important%21+TBC+Release+Announcement%2C+timeline+inside%21">
重要!TBC发布公告，时间线在里面! </a>
</h3>
<div class="content">
问候的旅行者!我们很高兴的宣布我们的时间表为TBC的发布!
以下是我们的时间表:
- 8月8日，Realms进入故障状态 </div>
</div>
<div class="readmore">
<div class="fadeout"></div>
<a href="?p=news/182-Important%21+TBC+Release+Announcement%2C+timeline+inside%21" class="btn">阅读全文</a>
</div>
</article>
<article class="news2 wow bounceInUp " style="background-image: url(/images/news/180-334-392.jpg)">
<span class="ico-horn"></span>
<div class="date">2019年7月13日下午3点17分</div>
<div class="news-content">
<h3 class="title">
<a href="?p=news/180-News%3A">
新闻：</a>
</h3>
<div class="content">
时间到了!本周日下午2:00加入我们，参加我们新的TBC领域的公众压力测试!参与者可以赢得一些s </div>
</div>
<div class="readmore">
<div class="fadeout"></div>
<a href="?p=news/180-News%3A" class="btn">阅读全文</a>
</div>
</article>
</div>
<div class="readmore">
<a href="?p=news">所有新闻</a></div>
</div>
</section>
</div>
</div>
</main>


<footer id="footer">
<div class="container">
<div class="row clearfix">
<div class="column">
<div id="footer-copy" class="wow fadeInUp">
&copy; 2021 - 2022 <br />
<a href="./">TBCstar 时光回溯</a> 
<a class="legals" href="">联系我们-关于我们</a> 
<a class="legals" href="">退款政策/私人政策</a> 
</div></div></div></div>
</div>
</div>
</div>
</footer>
<script type="text/javascript" src="/themes/nefelin/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="/themes/nefelin/js/custom.js"></script>