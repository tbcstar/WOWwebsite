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
<div>
服务项目 </div>
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
<h2>查找角色</h2>
<strong class="title">你在服务器上的角色 <span>Nefelin </span>:</strong>
<style>
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
<th>头像</th>
<th>名字</th>
<th>等级</th>
<th>阵营</th>
<th>职业</th>
<th>&nbsp;</th>
</tr>
</br>
<?php
global $Account, $Database, $Character, $Website;
$service = $_GET['s'];

$service_title = ucfirst($service." Change");

if ( DATA['service'][$service]['status'] != true )
{
	echo "此页面目前不可用。";
}
else
{
	if(isset($_GET['service'])&&$_GET['service']=='applied')
	{
		echo '<div class="box_two_title">应用服务！</div>';
		echo '你的服务已经应用到你刚刚选择的角色。您可能需要重新登录您的帐户，以注意任何变化。';
		echo '<p/>如果您需要任何帮助，此操作已被记录在我们的数据库中。';
	}
	else
	{
?>


选择您希望将此服务应用于哪个角色。
<?php
if ( DATA['service'][$service]['price'] == 0 )
{
    echo '<span class="attention">'.$service_title.' 是免费的。</span>';
}
else
{ ?>
<span class="attention"><?php echo $service_title; ?> 费用
<?php 
echo DATA['service'][$service]['price'] . ' ' . $Website->convertCurrency(DATA['service'][$service]['currency']); ?></span>
<?php 
if ( DATA['service'][$service]['currency'] == "vp" )
{
    echo "<span class='currency'>投票积分：".$Account->loadVP($_SESSION['cw_user'])."</span>";
}
    elseif ( DATA['service'][$service]['currency'] == "dp" )
{
    echo "<span class='currency'>" . DATA['website']['donation']['coins_name'] . ": " . $Account->loadDP($_SESSION['cw_user']) . "</span>";
}
} 

$Account->isNotLoggedIn();
$Database->selectDB("webdb");
$num = 0;
$result = $Database->select("realms", "char_db, name, id", null, null, "ORDER BY id ASC")->get_result();
while($row = $result->fetch_assoc()) 
{
        $acct_id = $Account->getAccountID($_SESSION['cw_user']);
		$realm = $row['name'];
		$char_db = $row['char_db'];
		$realm_id = $row['id'];
		          	
		$Database->selectDB($char_db);
        $result = $Database->select("characters", null, null, "account='$acct_id'")->get_result();
        while ($row = $result->fetch_assoc())
        { ?>
            <div class='charBox'>
            <table width="100%">
            <tr>
                <td width="73">
                    <?php
                    if ( !file_exists("styles/global/images/portraits/". $row['gender'] ."-". $row['race'] ."-". $row['class'] .".gif") )
                    {
                        echo "<img src=\"styles/". DATA['template']['path'] ."/images/unknown.png\" />";
                    }
                    else
                    { ?>
                        <img src="styles/global/images/portraits/<?php echo $row['gender'] ."-". $row['race'] ."-". $row['class']; ?>.gif" border="none"><?php
                    } ?>
                </td>

                <td width="160">
                    <h3><?php echo $row['name']; ?></h3>
                    <?php echo $row['level'] ." ". $Character->getRace($row['race']) ." ". $Character->getGender($row['gender']) ." ". $Character->getClass($row['class']);?>
                </td>

                <td>
                    Realm: <?php echo $realm;
                    if ( $row['online'] == 1 )
                    {
                        echo "<br/><span class='red_text'>请在使用此服务前注销。</span>";
                    } ?>
                </td>

                <td align="right">
                    &nbsp; 
                    <input type="submit" value="选择" <?php 
                        if ($row['online'] == 0)
                        { ?> 
                            onclick='nstepService(
                                <?php echo $row['guid']; ?>,<?php echo $realm_id; ?>, "<?php echo $service; ?>", "<?php echo $service_title; ?>", "<?php echo $row['name']; ?>")' <?php 
                        }
                        else
                        {
                            echo 'disabled="disabled"';
                        } ?>>
                </td>
            </tr>                         
            </table>
            </div>
	        <?php 
		    $num++;
	    }
    }
  }
}
?>
</table>
</br>
</div>
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