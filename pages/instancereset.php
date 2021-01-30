<div id="overlay"></div>
<div id="wrapper">

<?php include "header.php" ?>

<div id="leftcontent">
<div class="box_two">

<div class='box_two_title'>副本重置</div>
让你重置你的角色的副本CD。<hr/>
<?php
account::isNotLoggedIn();

$service = "reset";

if($GLOBALS['service'][$service]['price']==0) 
      echo '<span class="attention">副本重置是免费的。</span>';
else
{ ?>
<span class="attention">重置副本费用 
<?php 
echo $GLOBALS['service'][$service]['price'].' '.website::convertCurrency($GLOBALS['service'][$service]['currency']); ?></span>
<?php 
if($GLOBALS['service'][$service]['currency']=="vp")
	echo "<span class='currency'>投票积分：".account::loadVP($_SESSION['cw_user'])."</span>";
elseif($GLOBALS['service'][$service]['currency']=="dp")
	echo "<span class='currency'>".$GLOBALS['donation']['coins_name'].": ".account::loadDP($_SESSION['cw_user'])."</span>";
} 

if (isset($_POST['ir_step1']) || isset($_POST['ir_step2'])) 
	echo '选择服务器： <b>'.server::getRealmName($_POST['ir_realm']).'</b><br/><br/>';
else
{
?>
选择服务器：
&nbsp;
<form action="?p=instancereset" method="post">
<table>
<tr>
<td>
<select name="ir_realm">
	 <?php
	 $result = mysql_query("SELECT name,char_db FROM realms");
	 while($row = mysql_fetch_assoc($result))
	 {
		 if(isset($_POST['ir_realm']) && $_POST['ir_realm'] == $row['char_db'])
		 	echo '<option value="'.$row['char_db'].'" selected>';
		 else
		 	echo '<option value="'.$row['char_db'].'">';
		 echo $row['name'].'</option>';
	 }
	 ?>
</select>
</td>
<td>
<?php
if(!isset($_POST['ir_step1']) && !isset($_POST['ir_step2']) && !isset($_POST['ir_step3']))
	echo '<input type="submit" value="Continue" name="ir_step1">';
?>
</td>
</tr>
</table>
</form>
<?php
}
if(isset($_POST['ir_step1']) || isset($_POST['ir_step2']) || isset($_POST['ir_step3']))
{
	if (isset($_POST['ir_step2'])) 
		echo '选择角色:<b>'.character::getCharName($_POST['ir_char'],server::getRealmId($_POST['ir_realm']))
		.'</b><br/><br/>';
else
{		
?>
选择角色： 
&nbsp;
<form action="?p=instancereset" method="post">
<table>
<tr>
<td>
<input type="hidden" name="ir_realm" value="<?php echo $_POST['ir_realm']; ?>">
<select name="ir_char">
	 <?php
	 $acc_id = account::getAccountID($_SESSION['username']);
	 connect::selectDB($_POST['ir_realm']);
	 $result = mysql_query("SELECT name,guid FROM characters WHERE account='".$acc_id."'");
	 
	 while($row = mysql_fetch_assoc($result))
	 {
		if(isset($_POST['ir_char']) && $_POST['ir_char'] == $row['guid'])
		 	echo '<option value="'.$row['guid'].'" selected>';
		else
			echo '<option value="'.$row['guid'].'">';	
			
		echo $row['name'].'</option>'; 
	 }
	 ?>
</select>
</td>
<td>
<?php
if(!isset($_POST['ir_step2']) && !isset($_POST['ir_step3']))
	echo '<input type="submit" value="Continue" name="ir_step2">';
?>
</td>
</tr>
</table>
</form>
<?php	
	}
}
if(isset($_POST['ir_step2']) || isset($_POST['ir_step3']))
{
?>
选择副本：
&nbsp;
<form action="?p=instancereset" method="post">
<table>
<tr>
<td>
<input type="hidden" name="ir_realm" value="<?php echo $_POST['ir_realm']; ?>">
<input type="hidden" name="ir_char" value="<?php echo $_POST['ir_char']; ?>">
<select name="ir_instance">
	 <?php
	 $guid = (int)$_POST['ir_char'];
	 connect::selectDB($_POST['ir_realm']);
			
	 $result = mysql_query("SELECT instance FROM character_instance WHERE guid='".$guid."' AND permanent=1");
	 if (mysql_num_rows($result)==0) 
	 {
		 echo "<option value='#'>没有副本需要重置！</option>";
		 $nope = true;
	 }
	 else
	 {
		 while($row = mysql_fetch_assoc($result)) 
		 {
			 $getI = mysql_query("SELECT id, map, difficulty FROM instance WHERE id='".$row['instance']."'");
			 $instance = mysql_fetch_assoc($getI); 
			 
			 connect::selectDB('webdb');
			 $getName = mysql_query("SELECT name FROM instance_data WHERE map='".$instance['map']."'");
			 $name = mysql_fetch_assoc($getName);
			 
			 if(empty($name['name']))
			 	$name = "Unknown Instance";
			 else
			 	$name = $name['name'];	
				
			 if ($instance['difficulty']==0)
				 $difficulty = "10-man Normal";
			 elseif($instance['difficulty']==1)
				 $difficulty = "25-man Normal";
			 elseif($instance['difficulty']==2)
				 $difficulty = "10-man Heroic";
			 elseif($instance['difficulty']==3)
				 $difficulty = "25-man Heroic";
			 
			 echo '<option value="'.$instance['id'].'">'.$name.' <i>('.$difficulty.')</i></option>';
	 }
 }
?>
</select>
</td>
<td>
<?php
if(!isset($_POST['ir_step1']) && !isset($nope))
	echo '<input type="submit" value="Reset Instance" name="ir_step3">';
?>
</td>
</tr>
</table>
</form>
<?php	
}

if(isset($_POST['ir_step3']))
{
	$guid = (int)$_POST['ir_char'];
	$instance = (int)$_POST['ir_instance'];
	
	if($GLOBALS['service'][$service]['currency']=="vp")
		if(account::hasVP($_SESSION['cw_user'],$GLOBALS['service'][$service]['price'])==FALSE)
			echo '<span class="alert">你没有足够的投票积分！';
		else
		{
			connect::selectDB($_POST['ir_realm']);
			mysql_query("DELETE FROM instance WHERE id='".$instance."'");
			account::deductVP(account::getAccountID($_SESSION['cw_user']),$GLOBALS['service'][$service]['price']);
			echo '<span class="approved">副本CD已重置！</span>';
		}
	elseif($GLOBALS['service'][$service]['currency']=="dp")
		if(account::hasDP($_SESSION['cw_user'],$GLOBALS['service'][$service]['price'])==FALSE)
			echo '<span class="alert">你的捐赠积分不够'.$GLOBALS['donation']['coins_name'];
		else
		{
			connect::selectDB($_POST['ir_realm']);
			mysql_query("DELETE FROM instance WHERE id='".$instance."'");
			account::deductDP(account::getAccountID($_SESSION['cw_user']),$GLOBALS['service'][$service]['price']);
			echo '<span class="approved">副本CD已重置！</span>';
			
			account::logThis("Performed an Instance reset on ".character::getCharName($guid,server::getRealmId($_POST['ir_realm'])),"instancereset",
			server::getRealmId($_POST['ir_realm']));
		}
}
?>
<br/>
<a href="?p=instancereset">重新开始</a>

</div>
<div id="footer">
{footer}
</div>
</div>

<div id="rightcontent">     
{login}          
{serverstatus}  			
</div>
</div>
