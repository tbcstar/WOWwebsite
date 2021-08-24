<?php
global $Account;
?>

<?php if (!isset($_SESSION['cw_user'])) { 
 	  if (isset($_POST['login'])) 
	  	$Account->logIn($_POST['login_username'],$_POST['login_password'],$_SERVER['REQUEST_URI'],$_POST['login_remember']);
?>

<link href="css/jquery.fancybox.css" rel="stylesheet">

<link href="css/animate.css" rel="stylesheet">

<link href="css/style.css" rel="stylesheet">
<style>
.game-versions {
    font-size: 40px;
    font-family: frizquadratac,sans-serif;
    color: #fff;
    text-decoration: none;
}

.game-versions .game-versions-text {
    position: relative;
    top: 10px;
	}
</style>
<body>
<div class="wrapper">

<header id="header">
<section class="section home" id="home">
<div class="container">
<div class="navbar-toggle">
<span class="burger"></span>
</div>
<div class="slogan">
开放Beta测试
</div>

<nav class="navbar clearfix" role="navigation">
<ul class="nav navbar-nav clearfix">
<li class="active">
<a href="#home"><span class="text">首页</span><span class="ico"><img src="/images/nav-side.png" alt=""></span></a>
</li>
<li>
<a href="#about"><span class="text">介绍</span><span class="ico"><img src="/images/nav-side-03.png" alt=""></span></a>
</li>
<li>
<a href="#classes"><span class="text">为什么使用渐进模式？</span><span class="ico"><img src="/images/nav-side-04.png" alt=""></span></a>
</li>
<li>
<a href="#why"><span class="text">为什么选择TBC版本？</span><span class="ico"><img src="/images/nav-side-05.png" alt=""></span></a>
</li>
<li>
<a href="#raids"><span class="text">独特系统</span><span class="ico"><img src="/images/nav-side-06.png" alt=""></span></a>
</li>
<li>
<a href="#betakey"><span class="text">注册账号</span><span class="ico"><img src="/images/nav-side-07.png" alt=""></span></a>
</li>
</ul>
<div class="socials">
<a href="#" class="wow bounceInLeft"><img src="/images/ico-fb.png" alt=""></a>
<a href="#" class="wow bounceInRight"><img src="/images/ico-tw.png" alt=""></a>
<a href="#" class="wow bounceInLeft"><img src="/images/ico-vk.png" alt=""></a>
<a href="#" class="wow bounceInRight"><img src="/images/ico-yt.png" alt=""></a>
</div>
<!--<div class="languages wow lightSpeedIn">
<a href="/main/index.html" class="en active"></a>
<a href="/ru/main/index.html" class="ru"></a>
</div>-->
</nav>
<div class="brand">
<a class="logo" href=""><img src="/images/nefelin_logo_tbc.png" class="wow swing" alt="logo" role="banner" style="visibility: visible; animation-name: swing;"></a>
</div>
<div class="video">
<a href="https://www.youtube.com/watch?v=IBHL_-biMrQ" class="various fancybox-media play wow flip"><img src="/images/play.png" alt=""></a>
</div>
<div class="buttons clearfix">
<div class="rcol text-left wow bounceInLeft"><a href="#" class="btn scrollToForm" data-target="betakey-content">B站视频</a></div>
<div class="rcol text-right wow bounceInRight"><a href="https://qm.qq.com/cgi-bin/qm/qr?k=nWGwF-IsoA_fpk7CiTztc8iiI6VzRs5n&jump_from=webapi" class="btn">进入Q群</a></div>
</div>
</div>
</section>
</header>

<main id="content-wrapper">
<section class="section about" id="about">
<div class="container">
<h3 class="section-title wow pulse">关于我们的项目</h3>
<article class="section-text wow flipInX">
<p>独特的无类别玩法，<strong>TBCstar团队</strong>已经探索并征服了浩瀚的魔兽世界。我们对这个游戏的热爱也与日俱增。事实上，我们非常想要恢复那些在魔兽世界第一个资料片中体验到的情感和时刻... <strong>燃烧的远征</strong>.</p>
<p>同时，我们还希望有一些非常特别的玩法和创意……所以我们决定自己动手!现在，经过5年的不断的开发和内部测试，我们终于准备好介绍我们的项目……给你。</p>
</article>
<div class="row items">
<div class="col wow wobble">
<div class="block">
<div class="img">
<img src="/images/pref/pref-02.png" alt="">
<img src="/images/pref/pref-hov-02.png" class="hov" alt="">
</div>
<h4 class="title">寻路</h4>
<div class="text">每个单位(生物，npc，宠物)计算路径到达他们的目标而不被纹理，通过柱子等。</div>
</div>
</div>
<div class="col wow tada">
<div class="block">
<div class="img">
<img src="/images/pref/pref-11.png" alt="">
<img src="/images/pref/pref-hov-11.png" class="hov" alt="">
</div>
<h4 class="title">角色XP</h4>
<div class="text">1-58的升级经验是双倍，58-70只有周末是双倍XP，这对很多玩家来说是很友好的设置。</div>
</div>
</div>




<div class="col wow tada">
<div class="block">
<div class="img">
<img src="/images/pref/pref-07.png" alt="">
<img src="/images/pref/pref-hov-07.png" class="hov" alt="">
</div>
<h4 class="title">自动备份系统</h4>
<div class="text">每天从主服务器传输备份到其他服务器。它每天备份完整的服务器和你所有的角色。</div>
</div>
</div>
<div class="col wow wobble">
<div class="block">
<div class="img">
<img src="/images/pref/pref-08.png" alt="">
<img src="/images/pref/pref-hov-08.png" class="hov" alt="">
</div>
<h4 class="title">DDOS保护</h4>
<div class="text">我们在服务器保护方面有丰富的经验，所以你可以确保服务器的正常运行时间为99.9%。</div>
</div>
</div>
<div class="col wow tada">
<div class="block">
<div class="img">
<img src="/images/pref/pref-09.png" alt="">
<img src="/images/pref/pref-hov-09.png" class="hov" alt="">
</div>
<h4 class="title">低延迟</h4>
<div class="text">我们的服务器使用BGP双线接入，极大的降低了不同运营商连接游戏的延迟。</div>
</div>
</div>
<div class="col wow wobble">
<div class="block">
<div class="img">
<img src="/images/pref/pref-10.png" alt="">
<img src="/images/pref/pref-hov-10.png" class="hov" alt="">
</div>
<h4 class="title">趣味玩法</h4>
<div class="text">我们用爱发电，并将一些独特、有趣的玩法带到我们的世界中，并期待你们的喜欢。</div>
</div>
</div>
</div>
</div>
</section>
<section class="section classes" id="classes">
<div class="container">
<div class="text-block">
<h3 class="section-title wow pulse">为什么使用渐进模式？</h3>
<article class="section-text wow flipInX">
<p>首先，我们回答这个问题:“什么是渐进模式?”并不会马上开放TBC所有内容，这意味着你将不能立即进入黑暗神殿或太阳井高地。</p>
<p>我们相信这种时间线的发布方式可以让我们更彻底地享受游戏乐趣，并提供最好的内容体验。</p>
</article>
</div>
<div class="row">
<div class="class-tabs">

<ul class="nav-tabs clearfix" role="tablist">
<li role="presentation" class="active wow bounceInLeft">
<a href="#class-warrior" aria-controls="class-warrior" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/class/class-01.png" alt="">
<img src="/images/class/class-hov-01.png" class="hov" alt="">
</div>
战士
</a>
</li>
<li role="presentation" class="wow bounceInDown">
<a href="#class-hunter" aria-controls="class-hunter" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/class/class-02.png" alt="">
<img src="/images/class/class-hov-02.png" class="hov" alt="">
</div>
猎人
</a>
</li>
<li role="presentation" class="wow bounceInLeft">
<a href="#class-priest" aria-controls="class-priest" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/class/class-03.png" alt="">
<img src="/images/class/class-hov-03.png" class="hov" alt="">
</div>
牧师
</a>
</li>
<li role="presentation" class="wow bounceInUp">
<a href="#class-shaman" aria-controls="class-shaman" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/class/class-04.png" alt="">
<img src="/images/class/class-hov-04.png" class="hov" alt="">
</div>
萨满
</a>
</li>
<li role="presentation" class="wow bounceIn">
<a href="#class-warlock" aria-controls="class-warlock" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/class/class-05.png" alt="">
<img src="/images/class/class-hov-05.png" class="hov" alt="">
</div>
术士
</a>
</li>
<li role="presentation" class="wow bounceInUp">
<a href="#class-druid" aria-controls="class-druid" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/class/class-06.png" alt="">
<img src="/images/class/class-hov-06.png" class="hov" alt="">
</div>
德鲁伊
</a>
</li>
<li role="presentation" class="wow bounceInLeft">
<a href="#class-paladin" aria-controls="class-paladin" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/class/class-07.png" alt="">
<img src="/images/class/class-hov-07.png" class="hov" alt="">
</div>
圣骑士
</a>
</li>
<li role="presentation" class="wow bounceInDown">
<a href="#class-rogue" aria-controls="class-rogue" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/class/class-08.png" alt="">
<img src="/images/class/class-hov-08.png" class="hov" alt="">
</div>
盗贼
</a>
</li>
<li role="presentation" class="wow bounceInRight">
<a href="#class-mage" aria-controls="class-mage" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/class/class-09.png" alt="">
<img src="/images/class/class-hov-09.png" class="hov" alt="">
</div>
法师
</a>
</li>
</ul>
</div>
</div>
<div class="row tabs-row">

<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="class-warrior">
<div class="class-item">
<img src="/images/class/warrior-01.png" alt="">
T <span class="numbers">4</span>
</div>
<div class="class-item">
<img src="/images/class/warrior-02.png" alt="">
T <span class="numbers">5</span>
</div>
<div class="class-item">
<img src="/images/class/warrior-03.png" alt="">
T <span class="numbers">6</span>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="class-hunter">
<div class="class-item">
<img src="/images/class/hunter-01.png" alt="">
T <span class="numbers">4</span>
</div>
<div class="class-item">
<img src="/images/class/hunter-02.png" alt="">
T <span class="numbers">5</span>
</div>
<div class="class-item">
<img src="/images/class/hunter-03.png" alt="">
T <span class="numbers">6</span>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="class-priest">
<div class="class-item">
<img src="/images/class/priest-01.png" alt="">
T <span class="numbers">4</span>
</div>
<div class="class-item">
<img src="/images/class/priest-02.png" alt="">
T <span class="numbers">5</span>
</div>
<div class="class-item">
<img src="/images/class/priest-03.png" alt="">
T <span class="numbers">6</span>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="class-shaman">
<div class="class-item">
<img src="/images/class/shaman-01.png" alt="">
T <span class="numbers">4</span>
</div>
<div class="class-item">
<img src="/images/class/shaman-02.png" alt="">
T <span class="numbers">5</span>
</div>
<div class="class-item">
<img src="/images/class/shaman-03.png" alt="">
T <span class="numbers">6</span>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="class-warlock">
<div class="class-item">
<img src="/images/class/warlock-01.png" alt="">
T <span class="numbers">4</span>
</div>
<div class="class-item">
<img src="/images/class/warlock-02.png" alt="">
T <span class="numbers">5</span>
</div>
<div class="class-item">
<img src="/images/class/warlock-03.png" alt="">
T <span class="numbers">6</span>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="class-druid">
<div class="class-item">
<img src="/images/class/druid-01.png" alt="">
T <span class="numbers">4</span>
</div>
<div class="class-item">
<img src="/images/class/druid-02.png" alt="">
T <span class="numbers">5</span>
</div>
<div class="class-item">
<img src="/images/class/druid-03.png" alt="">
T <span class="numbers">6</span>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="class-paladin">
<div class="class-item">
<img src="/images/class/paladin-01.png" alt="">
T <span class="numbers">4</span>
</div>
<div class="class-item">
<img src="/images/class/paladin-02.png" alt="">
T <span class="numbers">5</span>
</div>
<div class="class-item">
<img src="/images/class/paladin-03.png" alt="">
T <span class="numbers">6</span>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="class-rogue">
<div class="class-item">
<img src="/images/class/rogue-01-new.png" alt="">
T <span class="numbers">4</span>
</div>
<div class="class-item">
<img src="/images/class/rogue-02-new.png" alt="">
T <span class="numbers">5</span>
</div>
<div class="class-item">
<img src="/images/class/rogue-03-new.png" alt="">
T <span class="numbers">6</span>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="class-mage">
<div class="class-item">
<img src="/images/class/mage-01.png" alt="">
T <span class="numbers">4</span>
</div>
<div class="class-item">
<img src="/images/class/mage-02.png" alt="">
T <span class="numbers">5</span>
</div>
<div class="class-item">
<img src="/images/class/mage-03.png" alt="">
 T <span class="numbers">6</span>
</div>
</div>
</div>
</div>
</div>
</section>
<section class="section why" id="why">
<div class="container">
<h3 class="section-title wow pulse">为什么选择TBC版本？</h3>
<article class="section-text wow flipInX">
<p>通过不同论坛和投票统计。许多玩家认为燃烧的远征是魔兽世界历史上最好的资料片。</p>
<p>它的职业平衡，令人兴奋的战场、副本、竞技场和独特而美丽的环境，以及许多有趣的经历都为这一版本增添了色彩。</p>
</article>
<div class="why-items">
<div class="col wow bounceInRight">
<div class="img">
<img src="/images/why/why-01.png" alt="">
<img src="/images/why/why-hov-01.png" class="hov" alt="">
</div>
<div class="text">团队副本</div>
</div>
<div class="col wow bounceInRight">
<div class="img">
<img src="/images/why/why-02.png" alt="">
<img src="/images/why/why-hov-02.png" class="hov" alt="">
</div>
<div class="text">70级上限</div>
</div>
<div class="col wow bounceInLeft">
<div class="img">
<img src="/images/why/why-03.png" alt="">
<img src="/images/why/why-hov-03.png" class="hov" alt="">
</div>
<div class="text">竞技场</div>
</div>
<div class="col wow bounceInLeft">
<div class="img">
<img src="/images/why/why-04.png" alt="">
<img src="/images/why/why-hov-04.png" class="hov" alt="">
</div>
<div class="text">飞行坐骑</div>
</div>
<div class="col wow bounceInUp">
<div class="img">
<img src="/images/why/why-05.png" alt="">
<img src="/images/why/why-hov-05.png" class="hov" alt="">
</div>
<div class="text">血精灵/德莱尼</div>
</div>
<div class="col wow bounceInUp">
<div class="img">
<img src="/images/why/why-06.png" alt="">
<img src="/images/why/why-hov-06.png" class="hov" alt="">
</div>
<div class="text">狼人/地精</div>
</div>
<div class="col wow bounceInDown">
<div class="img">
<img src="/images/why/why-07.png" alt="">
<img src="/images/why/why-hov-07.png" class="hov" alt="">
</div>
<div class="text">新的战场</div>
</div>
<div class="col wow bounceInUp">
<div class="img">
<img src="/images/why/why-08.png" alt="">
<img src="/images/why/why-hov-08.png" class="hov" alt="">
</div>
<div class="text">新的地图</div>
</div>
</div>
</div>
</section>
<section class="section raids" id="raids">
<div class="container">
<h3 class="section-title">TBCstar独特系统</h3>
<div class="row">
<div class="raid-tabs">

<ul class="nav-tabs clearfix" role="tablist">
<li role="presentation" class="active wow bounceInRight">
<a href="#raid-karazhan" aria-controls="raid-karazhan" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/raid/raids-01.png" alt="">
<img src="/images/raid/raids-hov-01.png" class="hov" alt="">
</div>
</a>
</li>
<li role="presentation" class="wow bounceInDown">
<a href="#raid-zulaman" aria-controls="raid-zulaman" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/raid/raids-02.png" alt="">
<img src="/images/raid/raids-hov-02.png" class="hov" alt="">
</div>
</a>
</li>
<li role="presentation" class="wow bounceInLeft">
<a href="#raid-gruul" aria-controls="raid-gruul" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/raid/raids-03.png" alt="">
<img src="/images/raid/raids-hov-03.png" class="hov" alt="">
</div>
</a>
</li>
<li role="presentation" class="wow bounceInUp">
<a href="#raid-magtheridon" aria-controls="raid-magtheridon" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/raid/raids-04.png" alt="">
<img src="/images/raid/raids-hov-04.png" class="hov" alt="">
</div>
</a>
</li>
<li role="presentation" class="wow bounceIn">
<a href="#raid-serpentshrine" aria-controls="raid-serpentshrine" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/raid/raids-05.png" alt="">
<img src="/images/raid/raids-hov-05.png" class="hov" alt="">
</div>
</a>
</li>
<li role="presentation" class="wow bounceInUp">
<a href="#raid-tempest-keep" aria-controls="raid-tempest-keep" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/raid/raids-06.png" alt="">
<img src="/images/raid/raids-hov-06.png" class="hov" alt="">
</div>
</a>
</li>
<li role="presentation" class="wow bounceInLeft">
<a href="#raid-mount-hyjal" aria-controls="raid-mount-hyjal" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/raid/raids-07.png" alt="">
<img src="/images/raid/raids-hov-07.png" class="hov" alt="">
</div>
</a>
</li>
<li role="presentation" class="wow bounceInDown">
<a href="#raid-black-temple" aria-controls="raid-black-temple" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/raid/raids-08.png" alt="">
<img src="/images/raid/raids-hov-08.png" class="hov" alt="">
</div>
</a>
</li>
<li role="presentation" class="wow bounceInRight">
<a href="#raid-sunwell" aria-controls="raid-sunwell" role="tab" data-toggle="tab">
<div class="img">
<img src="/images/raid/raids-09.png" alt="">
<img src="/images/raid/raids-hov-09.png" class="hov" alt="">
</div>
</a>
</li>
</ul>
</div>
</div>
<div class="row tabs-row">

<div class="tab-content">
<div role="tabpanel" class="tab-pane clearfix active" id="raid-karazhan">
<div class="place wow flip">
<h3 class="title">无类别模式</h3>
<img src="/images/raid/img-raid-Classless.png" alt="">
<div class="text">您会从所有职业中获得一组随机的技能，这让您面临挑战，您将根据已有技能来搭配组合。你获得的技能是完全随机的，但你可以从任何职业中选择您想要的天赋来增强你的技能组合。</div>
</div>
<div class="place-raids wow rotateIn">
<div class="col">
<img src="/images/raid/raid-attumen-the-hunsman.png" alt="">
<p>猎手阿图门</p>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane clearfix" id="raid-zulaman">
<div class="place">
<h3 class="title">证券市场</h3>
<img src="/images/raid/img-raid-zulaman.png" alt="">
<div class="text">在这里很多魔兽世界里的势力/阵营完成了A股上市。除了声望以外，玩家还能在金融市场和各大势力进行互动，是韭菜还是股神，就看你的天赋啦！</div>
</div>
<div class="place-raids">
<div class="col">
<img src="/images/raid/raid-nalorakk.png" alt="">
<p>纳洛拉克</p>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane clearfix" id="raid-gruul">
<div class="place">
<h3 class="title">命运骰子</h3>
<img src="/images/raid/img-raid-gruul.png" alt="">
<div class="text">当对角色随机获取的技能不满意的时候，你是时候掷出命运的骰子，来完成你的逆转。</div>
</div>
<div class="place-raids">
<div class="col">
<img src="/images/raid/raid-high-king-mauglar.png" alt="">
<p>莫加尔大王</p>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane clearfix" id="raid-magtheridon">
<div class="place">
<h3 class="title">游戏商城</h3>
<img src="/images/raid/img-raid-magtheridon.png" alt="">
<div class="text">如同9.0版本里的商城一样，我们的宠物、坐骑、趣味道具...更加丰富。</div>
</div>
<div class="place-raids">
<div class="col">
<img src="/images/raid/raid-magtheridon.png" alt="">
<p>玛瑟里顿</p>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane clearfix" id="raid-serpentshrine">
<div class="place">
<h3 class="title">主城传送</h3>
<img src="/images/raid/img-raid-serpentshrine-cavern.png" alt="">
<div class="text">便捷的功能，但我们的视觉效果是更惊艳的。</div>
</div>
<div class="place-raids">
<div class="col">
<img src="/images/raid/raid-hydros-the-unstable.png" alt="">
<p>不稳定的海度斯</p>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane clearfix" id="raid-tempest-keep">
<div class="place">
<h3 class="title">工会领地</h3>
<img src="/images/raid/img-raid-tempest-keep.png" alt="">
<div class="text">作为一个庞大的势力，怎么能没有自己的领土。</div>
</div>
<div class="place-raids">
<div class="col">
<img src="/images/raid/raid-alar.gif" alt="">
<p>奥的灰烬</p>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane clearfix" id="raid-mount-hyjal">
<div class="place">
<h3 class="title">竞技场观战</h3>
<img src="/images/raid/img-raid-the-battle-for-mount-hyjal.png" alt="">
<div class="text">热爱竞技的你，绝不会错过每一场热血的战斗。</div>
</div>
<div class="place-raids">
<div class="col">
<img src="/images/raid/raid-rage-winterchill.png" alt="">
<p>雷基·冬寒</p>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane clearfix" id="raid-black-temple">
<div class="place">
<h3 class="title">幻化系统</h3>
<img src="/images/raid/img-raid-black-temple.png" alt="">
<div class="text">虽然版本只开放到了TBC，但幻化内容却是包含到了7.0的内容，怎么样，惊喜吗，开心吗？</div>
</div>
<div class="place-raids">
<div class="col">
<img src="/images/raid/raid-high-warlord-najentus.png" alt="">
<p>高阶督军纳因图斯</p>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane clearfix" id="raid-sunwell">
<div class="place">
<h3 class="title">更多趣味玩法</h3>
<img src="/images/raid/img-raid-the-sunwell.png" alt="">
<div class="text">内容太多不想写了，让我们进游戏里去发现吧。</div>
</div>
<div class="place-raids">
<div class="col">
<img src="/images/raid/raid-kalecgos.png" alt="">
<p>卡雷苟斯</p>
</div>
</div>
</div>
</section>
</main> 


<footer id="footer"> 
<section class="section betakey" id="betakey"> 
<div class="section-panel"> <div class="container">  
<div class="form-content registration-content"> 

<h4 class="section-title text-center">登录到你的帐户</h4> 
<div class="section-content wow lightSpeedIn animated" style="visibility: visible; animation-name: lightSpeedIn;"> 
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="login_form">
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

 <div class="form-group text-left">
 <input type="submit" name="login" value="登录">
 <!--<button type="submit" class="btn btn-yellow">Log in</button> -->
 </div>
 </div>
 <div class="row text-center"> 
 <div class="form-link">
 
 <a href="" data-target="betakey-content" class="showFormContent">我想注册一个新账户</a>
 </div>
 </div>
 </form> 
 </div>
 </div>
 
 <div class="form-content betakey-content active"> 
 
 <h3 class="section-title text-center wow pulse" style="visibility: visible; animation-name: pulse;">注册账户</h3>
<article class="section-text text-center wow flipInY" style="visibility: visible; animation-name: flipInY;"> 
<p>公开beta测试的主要任务是识别尽可能多的bug，以便我们可以在发布之前修复它们。为了参加公开测试，请在下面注册一个账号。 <br> 如果您想要完全参与bug测试，我们恳请您注册账号参与测试。</p></article> 
 <h4 class="section-title text-center">我想注册一个账户</h4> 
 <div class="section-content wow lightSpeedIn" style="visibility: visible; animation-name: lightSpeedIn;"> 
 <form action="/includes/scripts/register.php" id=info method="post"> 
<div class="row">
<div class="form-group">
<label>E-mail：</label>
<div class="form-control">
<input type="text" name="Account[email]">
</div>
</div>
<div class="form-group">
<label>用户名：</label>
<div class="form-control">
<input type="text" name="Account[username]">
</div>
</div>
<div class="form-group">
<label>密码：</label>
<div class="form-control">
<input type="password" name="Account[password]">
</div>
</div>
<div class="form-group">
<label>确认密码：</label>
<div class="form-control">
<input type="password" name="Account[password2]">
</div>
</div>
</div>
 <div class="row">

 <div class="form-group text-center"> 
 <button type="submit" class="btn btn-yellow">注册</button>
 </div>
 </div>
 <div class="row text-center">
 <div class="form-link">
<a href="" data-target="registration-content" class="showFormContent">我已经有一个账户了</a>
 </div>
 </div>
 </form> 
 </div>
 </div>
 </div>
 </div>
 
 <div class="copyrights"> <div class="container"> <div class="go-forum text-center"><a href="" class="btn">前往论坛</a></div><div class="row"> <div class="copy"> © 2021 时光回溯 <a href="">tbcstar.com</a> </div><div class="u wow fadeInUp" style="visibility: hidden; animation-name: none;"> </div></div></div></div></section> </footer>


<script src="/css/js/jquery-2.1.0.min.js" type="text/javascript"></script>
<script src="/css/js/modernizr.custom.js" type="text/javascript"></script>
<script src="/css/js/jquery.easing.js" type="text/javascript"></script>
<script src="/css/js/jquery.fancybox.js" type="text/javascript"></script>
<script src="/css/js/jquery.fancybox-media.js" type="text/javascript"></script>
<script src="/css/js/wow.min.js" type="text/javascript"></script>
<script src="/css/js/tab.js" type="text/javascript"></script>
<script src="/javascript/custom.js" type="text/javascript"></script>

<?php } ?>


<?php if(isset($_SESSION['cw_user'])) { ?>
<meta http-equiv="refresh" content="0;url=?p=account">
<?php } ?>