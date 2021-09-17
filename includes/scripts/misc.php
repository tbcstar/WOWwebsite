<?php

require "../ext_scripts_class_loader.php";

global $Account, $Database, $Server;

if(isset($_POST['element']) &&$_POST['element'] == 'vote') 
{
   echo '投票积分： '.$Account->loadVP($_POST['account']);
}
elseif(isset($_POST['element']) && $_POST['element'] == 'donate') 
{
   echo DATA['website']['donation']['coins_name'] . ': ' . $Account->loadDP($_POST['account']);
}
##
#
##
if(isset($_POST['action']) && $_POST['action'] == 'removeComment') 
{
   $Database->selectDB("webdb");
    $Database->conn->query("DELETE FROM news_comments WHERE id=". $Database->conn->escape_string($_POST['id']) .";");
}
##
#
##
if(isset($_POST['action']) && $_POST['action']=='getComment') 
{
   $Connect->selectDB("webdb", $conn);
   $result = $conn->query("SELECT `text` FROM news_comments WHERE id='". $conn->escape_string($_POST['id']) .";");
   $row = $result->fetch_assoc();
   echo $row['text'];
}
##
#
##
if(isset($_POST['action']) && $_POST['action']=='editComment') 
{
   $content = mysqli_real_escape_string(trim(htmlentities($_POST['content'])));
	
   $Connect->selectDB("webdb", $conn);
   $conn->query("UPDATE news_comments SET `text` = '".$content."' WHERE id='". $conn->escape_string($_POST['id']) .";");
   
   $conn->query("INSERT INTO admin_log (full_url, ip, timestamp, action, account, extended_inf) 
   VALUES('/index.php?page=news','".$_SERVER['REMOTE_ADDR']."', '".time()."', '编辑评论', '".$_SESSION['cw_user_id']."', 
   '编辑的评论ID： ".(int)$_POST['id']."')");
}
##
#   Terms Of Service
##
if ( isset($_POST['getTos']) )
{
   include "../../documents/termsofservice.php";
   echo $tos_message;
}
##
#   Refund Policy
##
if ( isset($_POST['getRefundPolicy']) )
{
   include "../../documents/refundpolicy.php";
   echo $rp_message;
}
##
#   Server Status
##
if ( isset($_POST['serverStatus']) )
{
   	echo '<div class="box_one_title">服务器状态</div>';
	$num = 0;
	if ( is_array(DATA['realms']) || is_object(DATA['realms']) )
	{
		foreach (DATA['realms'] as $k => $v)
		{
			if ($num != 0) 
			{ 
				echo "<hr/>"; 
			}
			$Server->serverStatus($k);
			$num++;
		}
	}
	if ( $num == 0 )
	{
		buildError("<b>找不到服务器: </b> 请设置您的数据库并添加您的服务器!",NULL);  
		echo "找不到服务器。";
	}
	unset($num);
	?>
	<hr/>
	<span id="realmlist">设置服务器列表 <?php echo DATA['website']['realmlist']; ?></span>
	</div>
	<?php    
}
##
#   Donation List
##
if ( isset($_POST['convertDonationList']) )
{
	for ($row = 0; $row < count(DATA['website']['donationList']); $row++)
		{
				$value = $Database->conn->escape_string($_POST['convertDonationList']);
                if ( $value == DATA['website']['donationList'][$row][1] )
				{
					echo DATA['website']['donationList'][$row][2];
					exit();
				}
		}
}