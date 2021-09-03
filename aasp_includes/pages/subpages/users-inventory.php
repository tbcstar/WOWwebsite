<?php
    global $GamePage, $GameServer, $GameAccount; 
    $conn = $GameServer->connect();
?>
<div class="box_right_title">
    <?php echo $GamePage->titleLink(); ?> &raquo; 角色库存</div>
显示角色明细 
<a href="?p=users&s=viewchar&guid=<?php echo $_GET['guid']; ?>&rid=<?php echo $_GET['rid']; ?>">
	<?php echo $GameAccount->getCharName($_GET['guid'], $_GET['rid']); ?>
</a>
<hr/>
过滤器:
	   	<a href="?p=users&s=inventory&guid=<?php echo $_GET['guid']; ?>&rid=<?php echo $_GET['rid']; ?>&f=equip">
			<?php 
				if (isset($_GET['f']) && $_GET['f'] == "equip")
					echo "<b>"; ?>装备物品</a><?php 
				if (isset($_GET['f']) && $_GET['f'] == "equip")
					echo "</b>"; 
			?>
		</a> 

    	&nbsp; | &nbsp; 

    	<a href="?p=users&s=inventory&guid=<?php echo $_GET['guid']; ?>&rid=<?php echo $_GET['rid']; ?>&f=bank">
			<?php 
				if (isset($_GET['f']) && $_GET['f'] == "bank")
					echo "<b>"; ?>银行物品<?php 
				if (isset($_GET['f']) && $_GET['f'] == "bank")
					echo "</b>"; 
			?>
		</a> 

    	&nbsp; | &nbsp; 

    	<a href="?p=users&s=inventory&guid=<?php echo $_GET['guid']; ?>&rid=<?php echo $_GET['rid']; ?>&f=keyring">
		<?php 
			if (isset($_GET['f']) && $_GET['f'] == "keyring")
				echo "<b>"; ?>Items in keyring<?php 
			if (isset($_GET['f']) && $_GET['f'] == "keyring")
				echo "</b>"; 
		?>
        </a>

     	&nbsp; | &nbsp; 

     	<a href="?p=users&s=inventory&guid=<?php echo $_GET['guid']; ?>&rid=<?php echo $_GET['rid']; ?>&f=currency">
			<?php 
				if (isset($_GET['f']) && $_GET['f'] == "currency")
					echo "<b>"; ?>游戏币<?php 
				if (isset($_GET['f']) && $_GET['f'] == "currency")
					echo "</b>"; 
			?>
		</a> 

     	&nbsp; | &nbsp; 

     	<a href="?p=users&s=inventory&guid=<?php echo $_GET['guid']; ?>&rid=<?php echo $_GET['rid']; ?>">
			<?php 
				if(!isset($_GET['f'])) 
					echo "<b>"; ?>所有物品<?php 
				if(!isset($_GET['f'])) 
					echo "</b>"; 
			?>
		</a> 
<p/>
<?php
$GameServer->connectToRealmDB($_GET['rid']);
$equip_array = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18);

$result = mysqli_query($conn, "SELECT guid, itemEntry, count FROM item_instance WHERE owner_guid=". mysqli_real_escape_string($conn, $_GET['guid']) .";");
if(mysqli_num_rows($result) == 0)
{
	echo "没有发现任何物品!";
}
else
{
	echo "<table cellspacing='3' cellpadding='5'>";
	while($row = mysqli_fetch_assoc($result)) 
	{
		$entry = $row['itemEntry'];

		if(isset($_GET['f']))
		{
			if($_GET['f'] == 'equip') 
			{
				$getPos = mysqli_query($conn, "SELECT slot, bag FROM character_inventory 
                WHERE item='". $row['guid'] ."' AND bag='0' AND slot RANGE(0,18) AND guid=". mysqli_real_escape_string($conn, $_GET['guid']) .";");
			}
			elseif($_GET['f'] == 'bank') 
			{
				$getPos = mysqli_query($conn, "SELECT slot, bag FROM character_inventory 
                WHERE item='". $row['guid'] ."' AND slot>=39 AND slot<=73;");
			}
			elseif($_GET['f'] == 'keyring') 
			{
				$getPos = mysqli_query($conn, "SELECT slot, bag FROM character_inventory 
                WHERE item='". $row['guid'] ."' AND slot>=86 AND slot<=117;");
			}
			elseif($_GET['f'] == 'currency') 
			{
				$getPos = mysqli_query($conn, "SELECT slot, bag FROM character_inventory 
                WHERE item='". $row['guid'] ."' AND slot>=118 AND slot<=135;");
			}
		}
		else
		{
			$getPos = mysqli_query($conn, "SELECT slot,bag FROM character_inventory WHERE item='".$row['guid']."'");
		}

		if(mysqli_data_seek($getPos,0)>0)
		{
			$pos = mysqli_fetch_assoc($getPos);

			$GameServer->selectDB('worlddb');
			$get = mysqli_query($conn, "SELECT name,entry,quality,displayid FROM item_template WHERE entry='".$entry."'");
			$r = mysqli_fetch_assoc($get);

			 $GameServer->selectDB('webdb');
			 $getIcon = mysqli_query($conn, "SELECT icon FROM item_icons WHERE displayid='".$r['displayid']."'");
			 if(mysqli_num_rows($getIcon)==0) 
			 {
				 //发现没有图标。也许灾难项目。从wowhead获得图标。
				 $sxml = new SimpleXmlElement(file_get_contents("http://www.wowhead.com/item=". $entry ."&xml"));

				  $icon = strtolower(mysqli_real_escape_string($conn, $sxml->item->icon));
				  //现在我们已经装载好了。将其添加到数据库中以备将来使用。
				  //注意，WoWHead XML非常慢。这就是为什么我们把它加到db中的主要原因。
				  mysqli_query($conn, "INSERT INTO item_icons VALUES('". $row['displayid'] ."', '". $icon ."');");
			 }
			 else 
			 {
			   $iconrow = mysqli_fetch_assoc($getIcon);
			   $icon = strtolower($iconrow['icon']);
			 }

			$GameServer->connectToRealmDB($_GET['rid']);

			?>
				<tr bgcolor="#e9e9e9">
					<td width="36"><img src="http://static.wowhead.com/images/wow/icons/medium/<?php echo $icon; ?>.jpg"></td>
					<td>
						<a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $r['entry']; ?>" title="" target="_blank"><?php echo $r['name']; ?></a>
					</td>
					<td>x<?php echo $row['count']; ?> 

					<?php 
					if(!isset($_GET['f']))
					{
						if (in_array($pos['slot'], $equip_array) && $pos['bag'] == 0)   echo "(Equipped)";
                        if ($pos['slot'] >= 39 && $pos['slot'] <= 73)                   echo "(Bank)";
                        if ($pos['slot'] >= 86 && $pos['slot'] <= 117)                  echo "(Keyring)";
                        if ($pos['slot'] >= 118 && $pos['slot'] <= 135)                 echo "(Currency)";
					}
					?>
	            </td>
	        </tr>
	    <?php
	 	}
	}
 echo "</table>";
}
?>