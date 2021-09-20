<?php include "headers.php" ?>

<?php include "menus.php" ?>

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
购买物品 </div>
</div>
<div class="realm_picker">
<div class="">
服务器: </div>
<a href="game.tbcstar.com">
时光回溯 </a>
</div>
</div>
<div class="content-box info">
<div class="content-holder">
<div class="content-frame">
<div class="content">
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
<?php
echo '<span class="currency">投票积分： '.account::loadVP($_SESSION['cw_user']).'<br/>
'.$GLOBALS['donation']['coins_name'].': '.account::loadDP($_SESSION['cw_user']).'
</span>';

if(isset($_GET['return']) && $_GET['return']=="true")
	echo "<span class='accept'>物品已发送到选定的角色!</span>";
elseif(isset($_GET['return']) && $_GET['return']!="true")
	echo "<span class='alert'>".$_GET['return']."</span>";

account::isNotLoggedIn();
connect::selectDB('webdb');

$counter = 0;
$totalDP = 0;
$totalVP = 0;

if(isset($_SESSION['donateCart']) && !empty($_SESSION['donateCart'])) 
{
	$counter = 1;
	
	echo '<h3>公益商城</h3>';
	
	$sql = "SELECT * FROM shopitems WHERE entry IN(";
	foreach($_SESSION['donateCart'] as $entry => $value) {
		if($_SESSION['donateCart'][$entry]['quantity']!=0) {
		  $sql .= $entry. ',';
		  
		  connect::selectDB($GLOBALS['connection']['worlddb']);
		  $result = mysql_query("SELECT maxcount FROM item_template WHERE entry='".$entry."' AND maxcount>0");
		  if(mysql_result($result,0)!=0)
			  $_SESSION['donateCart'][$entry]['quantity']=1;
		  
		   connect::selectDB($GLOBALS['connection']['webdb']);
		}
	  }
	  
	  $sql = substr($sql,0,-1) . ") AND in_shop='donate' ORDER BY `itemlevel` ASC";

      $query = mysql_query($sql);
?>
<table width="100%" >
<tr id="cartHead"><th>名称</th><th>数量</th><th>价格</th><th>结算</th></tr>
<?php
while($row = mysql_fetch_array($query)) 
{
	?><tr align="center">
        <td><a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>"><?php echo $row['name']; ?></a></td>
        <td><input type="text" value="<?php echo $_SESSION['donateCart'][$row['entry']]['quantity']; ?>" style="width: 30px; text-align: center;"
        onFocus="$(this).next('.quantitySave').fadeIn()" id="donateCartQuantity-<?php echo $row['entry']; ?>" />
        <div class="quantitySave" style="display:none;">
        <a href="#" onclick="saveItemQuantityInCart('donateCart',<?php echo $row['entry']; ?>)">保存</a>
        </div>
        </td>
        <td><?php echo $_SESSION['donateCart'][$row['entry']]['quantity'] * $row['price']; ?> 
		<?php echo $GLOBALS['donation']['coins_name']; ?></td>
        <td><a href="#" onclick="removeItemFromCart('donateCart',<?php echo $row['entry']; ?>)">移除</a></td>
    </tr>
    <?php
	$totalDP = $totalDP + ( $_SESSION['donateCart'][$row['entry']]['quantity'] * $row['price'] );
}
?>
</table>
<?php 
} 
if(isset($_SESSION['voteCart']) && !empty($_SESSION['voteCart'])) 
{
	$counter = 1;

	 echo '<br>投票商店<br>';
	$sql = "SELECT * FROM shopitems WHERE entry IN(";
	foreach($_SESSION['voteCart'] as $entry => $value) {
		if($_SESSION['voteCart'][$entry]['quantity']!=0) {
		  $sql .= $entry. ',';
		  connect::selectDB($GLOBALS['connection']['worlddb']);
		  $result = mysql_query("SELECT maxcount FROM item_template WHERE entry='".$entry."' AND maxcount>0");
		  if(mysql_result($result,0)!=0)
			  $_SESSION['voteCart'][$entry]['quantity']=1;

		   connect::selectDB($GLOBALS['connection']['webdb']);
		}
	  }
	  
	  $sql = substr($sql,0,-1) . ") AND in_shop='vote' ORDER BY `itemlevel` ASC";

$query = mysql_query($sql);
?>
<table width="100%" >
<tr id="cartHead"><th>名称</th><th>数量</th><th>价格</th><th>结算</th></tr>
<?php
while($row = mysql_fetch_array($query)) {
	?><tr align="center">
        <td><a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>"><?php echo $row['name']; ?></a></td>
        <td><input type="text" value="<?php echo $_SESSION['voteCart'][$row['entry']]['quantity']; ?>" style="width: 30px; text-align: center;"
        onFocus="$(this).next('.quantitySave').fadeIn()" id="voteCartQuantity-<?php echo $row['entry']; ?>" />
        <div class="quantitySave" style="display:none;">
        <a href="#" onclick="saveItemQuantityInCart('voteCart',<?php echo $row['entry']; ?>)">保存</a>
        </div>
        </td>
        <td><?php echo $_SESSION['voteCart'][$row['entry']]['quantity'] * $row['price']; ?> 投票积分</td>
        <td><a href="#" onclick="removeItemFromCart('voteCart',<?php echo $row['entry']; ?>)">移除</a></td>
    </tr>
    <?php
	$totalVP = $totalVP + ( $_SESSION['voteCart'][$row['entry']]['quantity'] * $row['price'] );
}
?>
</table>
<?php
}
?>
<br/>合计：<?php echo $totalVP; ?> 投票积分，<?php echo $totalDP.' '.$GLOBALS['donation']['coins_name']; ?>
<hr/>
<?php
if(isset($_SESSION['donateCart']) && !empty($_SESSION['donateCart']) || isset($_SESSION['voteCart']) 
&& !empty($_SESSION['voteCart'])) 
{	?>
	<input class="btn" type='submit' value='Clear Cart' onclick='clearCart()'>
   <center>
    
    
	 <select id="checkout_values">
     <?php
	     account::getCharactersForShop($_SESSION['cw_user']);
	 ?>
     </select>
     <input class="btn" type='submit' value='Checkout'  onclick='checkout()'></td>
     

     </center>
     
	<?php
}

if($counter==0)
	echo "<span class='attention'>您的购物车是空的</span>";

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