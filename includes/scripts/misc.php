<?php

require('../ext_scripts_class_loader.php');

global $Account, $Connect, $Server;
$conn = $Connect->connectToDB();

if(isset($_POST['element']) &&$_POST['element'] == 'vote') 
{
   echo '投票积分： '.$Account->loadVP($_POST['account']);
}
elseif(isset($_POST['element']) && $_POST['element'] == 'donate') 
{
   echo $GLOBALS['donation']['coins_name'].': '.$Account->loadDP($_POST['account']);
}
##
#
##
if(isset($_POST['action']) && $_POST['action'] == 'removeComment') 
{
   $Connect->selectDB('webdb');
   mysqli_query($conn, "DELETE FROM news_comments WHERE id=". mysqli_real_escape_string($conn, $_POST['id']) .";");
}
##
#
##
if(isset($_POST['action']) && $_POST['action']=='getComment') 
{
   $Connect->selectDB('webdb');
   $result = mysqli_query($conn, "SELECT `text` FROM news_comments WHERE id='". mysqli_real_escape_string($conn, $_POST['id']) .";");
   $row = mysqli_fetch_assoc($result);
   echo $row['text'];
}
##
#
##
if(isset($_POST['action']) && $_POST['action']=='editComment') 
{
   $content = mysqli_real_escape_string(trim(htmlentities($_POST['content'])));
	
   connect::selectDB('webdb');
   mysqli_query($conn, "UPDATE news_comments SET `text` = '".$content."' WHERE id='". mysqli_real_escape_string($conn, $_POST['id']) .";");
   
   mysqli_query($conn, "INSERT INTO admin_log (full_url, ip, timestamp, action, account, extended_inf) 
   VALUES('/index.php?page=news','".$_SERVER['REMOTE_ADDR']."', '".time()."', '编辑评论', '".$_SESSION['cw_user_id']."', 
   '编辑的评论ID： ".(int)$_POST['id']."')");
}
##
#
##
if(isset($_POST['getTos'])) 
{
   include("../../documents/termsofservice.php");
   echo $tos_message;
}
##
#
##
if(isset($_POST['getRefundPolicy'])) 
{
   include("../../documents/refundpolicy.php");
   echo $rp_message;
}
##
#
##
if(isset($_POST['serverStatus'])) 
{
   	echo '<div class="box_one_title">服务器状态</div>';
	$num = 0;
	if (is_array($GLOBALS['realms']) || is_object($GLOBALS['realms']))
	{
		foreach ($GLOBALS['realms'] as $k => $v) 
		{
			if ($num != 0) 
			{ 
				echo "<hr/>"; 
			}
			$Server->serverStatus($k);
			$num++;
		}
	}
	if ($num == 0) 
	{
		buildError("<b>找不到服务器: </b> 请设置您的数据库并添加您的服务器!",NULL);  
		echo "找不到服务器。";
	}
	unset($num);
	?>
	<hr/>
	<span id="realmlist">设置服务器列表 <?php echo $GLOBALS['connection']['realmlist']; ?></span>
	</div>
	<?php    
}
##
#
##
if(isset($_POST['convertDonationList']))
{
	for ($row = 0; $row < count($GLOBALS['donationList']); $row++)
		{
				$value = mysqli_real_escape_string($conn, $_POST['convertDonationList']);
				if($value == $GLOBALS['donationList'][$row][1])
				{
					echo $GLOBALS['donationList'][$row][2];
					exit();
				}
		}
}

?>