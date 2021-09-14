<?php

$service = $_GET['s'];
$guid = (int)$_GET['guid'];
$realm_id = (int)$_GET['rid'];

$service_title = ucfirst($service." Change");

$service_desc = array(
	'race' 
	=> 
	'<ul>
		<li>您可以根据您的阵营选择对应种族进行改变，但不能改变职业。</li>
		<li>根据您选择的新种族，您的声望、种族技能和出生地都会发生变化。</li>
		<li>服务器的迁移不包含新种族更改。</li>
	</ul>'
,
	'name' 
	=> 
	'<ul>
		<li>除非要求更改名称，否则不能更改名称(费用和限制相同)。</li>
	</ul>'
,
	'appearance' 
	=> 
	'<ul>
		<li>这项服务允许你改变你的性别，你的脸，头发，肤色，发型，名字和其他由性别和种族组合决定的特征。然而，你不能改变你角色的种族。</li>
		<li>如果在此过程中更改角色的名称，则您选择的名称将在创建角色的服务器中可用。</li>
		<li>除非要求对外观进行新的修改(费用和限制相同)，否则外观的修改一旦完成就不能逆转。</li>
	</ul>'
,
	'faction' 
	=> 
	'<ul>
		<li>在这个过程中，你必须选择一个与前一个阵营相反的种族。你不能改变你角色的等级。</li>
		<li>服务器的变化不包括在阵营的变化中。</li>
	</ul>'
	
	,
	
	'unstuck' 
	=> 
	'<ul>
		<li>测试</li>
		<li>测试</li>
	</ul>'
			
);
global $Account, $Website, $Connect, $Character;
if($GLOBALS['service'][$service]['status']!="TRUE") 
	echo "此页面目前不可用。";
else
{
?>
<?php include "headers.php" ?>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/main.js"></script>
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
<a href="?page=shop">
商城 </a>
<span class="ico-raquo"></span>
<a href="?page=work_list">
服务项目 </a>
<span class="ico-raquo"></span>
<div>
<?php echo $service_title; ?> </div>
</div>
<div class="realm_picker">
<div class="">
所在服务器： </div>
<a href="game.tbcstar.com">
时光回溯 </a>
</div>
</div>
<div class="content-box standart">
<div class="content-holder">
<div class="content-frame">
<div class="content">
<h2>选择角色</h2>
<strong class="title">你在服务器上的角色<span>时光回溯 </span>:</strong>
<style>
h4 {
    margin: 1px;
    font-size: 18px;
    color: #71B100;
}


span.accept {
    color: #648434;
    border: 1px solid #9BCC54;
    background: #CDEFA6 url(styles/global/images/typo/accept.png) 10px 50% no-repeat;
}

span.attention, span.notice, span.alert, span.download, span.approved, span.media, span.note, span.cart, span.email, span.doc, span.accept, span.vote, span.currency {
    display: block;
    padding: 8px 10px 8px 34px;
    margin: 15px 0;
}


span.currency {
    color: #B79000;
    border: 1px solid #E7BD72;
    background: #FFF3A3 url(styles/global/images/typo/coins.png) 10px 50% no-repeat;
}

span.attention, span.notice, span.alert, span.download, span.approved, span.media, span.note, span.cart, span.email, span.doc, span.accept, span.vote, span.currency {
    display: block;
    padding: 8px 10px 8px 34px;
    margin: 15px 0;
}


span.attention {
    color: #666;
    border: 1px solid #a8a8a8;
    background: #ccc url(styles/global/images/typo/attention.png) 10px 50% no-repeat;
}

span.attention, span.notice, span.alert, span.download, span.approved, span.media, span.note, span.cart, span.email, span.doc, span.accept, span.vote, span.currency {
    display: block;
    padding: 8px 10px 8px 34px;
    margin: 15px 0;
}


</style>
{alert}

<table class="table">
<tr>
<th>img</th>
<th>名字</th>
<th>等级</th>
<th>种族</th>
<th>职业</th>
</tr>

<div class="box_two_title">确认 <?php echo $service_title; ?></div>
<?php
if($GLOBALS['service'][$service]['price']==0) 
      	echo '<span class="attention">'.$service_title.' 是免费的。</span>';
else
{ ?>
<span class="attention"><?php echo $service_title; ?> 费用 
<?php 
echo $GLOBALS['service'][$service]['price'].' '.$Website->convertCurrency($GLOBALS['service'][$service]['currency']); ?></span>
<?php 
if($GLOBALS['service'][$service]['currency']=="vp")
	echo "<span class='currency'>Vote Points: ".$Account->loadVP($_SESSION['cw_user'])."</span>";
elseif($GLOBALS['service'][$service]['currency']=="dp")
	echo "<span class='currency'>".$GLOBALS['donation']['coins_name'].": ".$Account->loadDP($_SESSION['cw_user'])."</span>";
} 

	$Account->isNotLoggedIn();

	$Connect->selectDB("webdb", $conn);
	$result = $conn->query("SELECT name FROM realms WHERE id='".$realm_id."'");
	$row = $result->fetch_assoc();
	$realm = $row['name'];
	
	$Connect->connectToRealmDB($realm_id);

	$result = $conn->query("SELECT name,guid,gender,class,race,level,online FROM characters WHERE guid='".$guid."'");
	$row = $result->fetch_assoc()
	?>



<tr>
<td><?php if(!file_exists('styles/global/images/portraits/'.$row['gender'].'-'.$row['race'].'-'.$row['class'].'.gif'))
				       echo '<img src="styles/'.$GLOBALS['template']['path'].'/images/unknown.png" />';
					   else 
					   { ?>
                        <img src="styles/global/images/portraits/
					<?php echo $row['gender'].'-'.$row['race'].'-'.$row['class']; ?>.gif" border="none">
                    <?php } ?>
                </td></td>
				

<td><?php echo $row['name']; ?></td>
<td><?php echo $row['level']; ?></td>
<td><?php echo "".$Character->getRace($row['race']); ?></td>
<td><?php echo "".$Character->getClass($row['class']); ?></td>


 <!--<td><?php echo $realm; ?>
					<?php if($row['online']==1)
                   echo "<br/><span class='red_text'>在尝试解卡之前，请先退出游戏。</span>";?>
                </td>-->

</table>
</br>
</div>


<br><br><br><br><br><br><br>
    <h4>条件和免责声明</h4>
    <?php
	echo $service_desc[$service];
	?>
       <input type="submit" class="btn btn-low-yellow" value="Agree & Continue" 
       <?php if($row['online']==0) { ?> 
       onclick='confirmService(<?php echo $row['guid']; ?>,<?php echo $realm_id; ?>,"<?php echo $service; ?>","<?php echo $service_title; ?>"
       ,"<?php echo $row['name']; ?>")' <?php }
       else { echo 'disabled="disabled"'; } ?>>
    <?php
}
?>

<span class="image"></span>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
</div>
<?php include "footer.php" ?>