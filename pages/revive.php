<?php include "headers.php" ?>
<?php include "menus.php" ?>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/main.js"></script>
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
角色复活 </div>
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
<strong class="title">你在服务器上的角色<span>Nefelin </span>:</strong>

{alert}

<table class="table">
<tr>
<th>头像</th>
<th>名字</th>
<th>等级</th>
<th>阵营</th>
<th>职业</th>
<th>游戏时长</th>
<th>&nbsp;</th>
</tr>
<?php 
global $Website, $Account, $Connect, $Character;
$conn = $Connect->connectToDB();
$service = "revive";

if($GLOBALS['service'][$service]['price']==0) 
      echo '<span class="attention">复活是免费的。</span>';
else
{ ?>
<span class="attention">恢复成本 
<?php 
echo $GLOBALS['service'][$service]['price'].' '.$Website->convertCurrency($GLOBALS['service'][$service]['currency']); ?></span>
<?php 
if($GLOBALS['service'][$service]['currency']=="vp")
	echo "<span class='currency'>投票积分： ".$Account->loadVP($_SESSION['cw_user'])."</span>";
elseif($GLOBALS['service'][$service]['currency']=="dp")
	echo "<span class='currency'>".$GLOBALS['donation']['coins_name'].": ".$Account->loadDP($_SESSION['cw_user'])."</span>";
} 

$Account->isNotLoggedIn();
$Connect->selectDB('webdb', $conn);
$num = 0;
$result = mysqli_query($conn, 'SELECT char_db, name FROM realms ORDER BY id ASC;');
while($row = mysqli_fetch_assoc($result)) 
{
         $acct_id = $Account->getAccountID($_SESSION['cw_user']);
		 $realm = $row['name'];
		 $char_db = $row['char_db'];
		          	
		$Connect->selectDB($char_db, $conn);
		$result = mysqli_query($conn, "SELECT name, guid, gender, class, race, level, online FROM characters WHERE account=". $acct_id .";");
        while ($row = mysqli_fetch_assoc($result))
		{
	
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
<td>0 h. 19 m. 34 s.</td>

 <!--<td><?php echo $realm; ?>
					<?php if($row['online']==1)
                   echo "<br/><span class='red_text'>在尝试解除卡死之前，请先登出。</span>";?>
                </td>-->
                
				
				
				 <td align="right"> &nbsp; <input type="submit" class="btn btn-low-yellow" value="复活" 
				   <?php if($row['online']==0) { ?> 
                   onclick='revive(<?php echo $row['guid']; ?>,"<?php echo $char_db; ?>")' <?php }
                   else { echo 'disabled="disabled"'; } ?>>
               </td>
	
</tr>


	<?php 
		$num++;
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