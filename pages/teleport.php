<?php 
global $Account, $Website, $Connect;
$conn = $Connect->connectToDB();
$Account->isNotLoggedIn(); ?>
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
<a href="?p=work_list">
服务项目 </a>
<span class="ico-raquo"></span>
<div>
角色恢复 </div>
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
<strong class="title">你在服务器上的角色 <span>时光回溯 </span>:</strong>

{alert}

<table class="table">
<tr>
<th>img2</th>
<th>img</th>
<th>名字</th>
<th>等级</th>
<th>阵营</th>
<th>职业</th>
<th>在线时长</th>
<th>&nbsp;</th>
</tr>
<?php 

$service = "teleport";

if($GLOBALS['service'][$service]['price']==0) 
      echo '<span class="attention">传送是免费的。</span>';
else
{ ?>
<span class="attention">传送费用 
<?php 
echo $GLOBALS['service'][$service]['price'].' '.$Website->convertCurrency($GLOBALS['service'][$service]['currency']); ?></span>
<?php 
if($GLOBALS['service'][$service]['currency']=="vp")
	echo "<span class='currency'>投票积分：".$Account->loadVP($_SESSION['cw_user'])."</span>";
elseif($GLOBALS['service'][$service]['currency']=="dp")
	echo "<span class='currency'>".$GLOBALS['donation']['coins_name'].": ".$Account->loadDP($_SESSION['cw_user'])."</span>";
} ?>
<hr/>
<h3 id="choosechar">选择角色</h3> 
<?php
$Connect->selectDB('webdb', $conn);
$result = $conn->query("SELECT char_db, name FROM realms ORDER BY id ASC;");
while ($row = $result->fetch_assoc())
{
        $acct_id = $Account->getAccountID($_SESSION['cw_user']);
		$realm   = $row['name'];
		$char_db = $row['char_db'];
		          	
		$Connect->selectDB($char_db, $conn);
        $result = $conn->query("SELECT name, guid, gender, class, race, level, online FROM characters WHERE account=". $acct_id .";");
        while ($row = $result->fetch_assoc())
		{
	?>
   

<div class='charBox' style="cursor:pointer;" id="<?php echo $row['guid'].'*'.$char_db; ?>"<?php if ($row['online'] != 1) { ?> onclick="selectChar('<?php echo $row['guid'].'*'.$char_db; ?>',this)"<?php } ?>>


	<table>
<tr>
<td><?php if(!file_exists('styles/global/images/portraits/'.$row['gender'].'-'.$row['race'].'-'.$row['class'].'.gif'))
				       echo '<img src="styles/'.$GLOBALS['template']['path'].'/images/unknown.png" />';
					   else 
					   { ?>
				   <img src="styles/global/images/portraits/<?php echo $row['gender'].'-'.$row['race'].'-'.$row['class']; ?>.gif" border="none">
                </td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['level']; ?></td>
<td><?php echo "".$Character->getRace($row['race']); ?></td>
<td><?php echo "".$Character->getClass($row['class']); ?></td>
<td></td>
<td></td>
<td><?php } ?>继续</a></td>
</tr>

</div>  
	</table>
	<?php } ?>
<br/>&nbsp;
    <span id="teleport_to" style="display:none;">  
     
    </span>      
</div>

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
<?php
}
?>