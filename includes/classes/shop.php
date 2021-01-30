<?php
class shop {
	
	public function search($value,$shop,$quality,$type,$ilevelfrom,$ilevelto,$results,$faction,$class,$subtype) 
	{
		connect::selectDB('webdb');
		if ($shop=='vote') 
			$shopGlobalVar = $GLOBALS['voteShop']; 
		elseif($shop=='donate') 
			$shopGlobalVar = $GLOBALS['donateShop']; 
		
		$value = mysql_real_escape_string($value);
		$shop = mysql_real_escape_string($shop);
		$quality = (int)$quality;
		$ilevelfrom = (int)$ilevelfrom;
		$ilevelto = (int)$ilevelto;
		$results = (int)$results;
		$faction = (int)$faction;
		$class = (int)$class;
		$type = mysql_real_escape_string($type);
		$subtype = mysql_real_escape_string($subtype);
		
		if($value=="搜索物品...") 
			$value = "";
		
		$advanced = NULL;
		
		####高级搜索
		if($GLOBALS[$shop.'Shop']['enableAdvancedSearch']==TRUE) 
		{
			if($quality!="--品质--") 
				$advanced.=" AND quality='".$quality."'";
			
			if($type!="--类型--") {
				if($type=="15-5" || $type=="15-5")  {
					//坐骑或宠物
					$type = explode('-',$type);
					
					$advanced.=" AND type='".$type[0]."' AND subtype='".$type[1]."'";
				} else 
					$advanced.=" AND type='".$type."'";
			} 
			
			if($faction!="--阵营--") 
				$advanced.=" AND faction='".$faction."'";
			
			if($class!="--种族--") 
				$advanced.=" AND class='".$class."'"; 
			
			if($ilevelfrom!="--物品等级--") 
				$advanced.=" AND itemlevel>='".$ilevelfrom."'";
			
			if($ilevelto!="--物品等级--") 
				$advanced.=" AND itemlevel<='".$ilevelto."'";

			$count = mysql_query("SELECT COUNT(id) FROM shopitems WHERE name LIKE '%".$value."%' 
								  AND in_shop = '".$shop."' ".$advanced);
		
				if(mysql_result($count,0)==0) 
					$count = 0;
				 else 
					$count = mysql_result($count,0);
				
			
			if($results!="--结果--") 
				$advanced.=" ORDER BY name ASC LIMIT ".$results;
			 else 
				$advanced.=" ORDER BY name ASC LIMIT 250";
		}
		$result = mysql_query("SELECT entry,displayid,name,quality,price,faction,class
		FROM shopitems WHERE name LIKE '%".$value."%' 
		AND in_shop = '".mysql_real_escape_string($shop)."' ".$advanced);
		
		if($results!="--结果--") 
			$limited = $results;
		 else 
			$limited = mysql_num_rows($result);
		
	    echo "<div class='shopBox'><b>".$count."</b> 找到的结果。 (".$limited." displayed)</div>";
		
		if (mysql_num_rows($result)==0) 
			echo '<b class="red_text">没有找到结果!</b><br/>';
		 else 
		 {
			while($row = mysql_fetch_assoc($result)) 
			{
				$entry = $row['entry'];
				
				switch($row['quality']) {
					default:
					        $class="白色";
					break;
					case(0):
					        $class="灰色";
					break;
					case(2):
					        $class="绿色";
					break;
					case(3):
					        $class="蓝色";
					break;
					case(4):
					        $class="紫色";
					break;
					case(5):
					        $class="橙色";
					break;
					
					case(6):
					        $class="gold";
					break;
					
					case(7):
					        $class="gold";
					break;
				}
				
				 $getIcon = mysql_query("SELECT icon FROM item_icons WHERE displayid='".$row['displayid']."'");
				 if(mysql_num_rows($getIcon)==0) 
				 {
					 //发现没有图标。也许灾难项目。从wowhead获取图标。
					 $sxml = new SimpleXmlElement(file_get_contents('http://www.wowhead.com/item='.$entry.'&xml'));
					  
					  $icon = strtolower(mysql_real_escape_string($sxml->item->icon));
					  //现在我们已经加载了它。将其添加到数据库中供以后使用。
					  //注意，WoWHead XML非常慢。这就是我们将其添加到数据库中的主要原因。
					  mysql_query("INSERT INTO item_icons VALUES('".$row['displayid']."','".$icon."')");
				 }
				 else 
				 {
				   $iconrow = mysql_fetch_assoc($getIcon);
				   $icon = strtolower($iconrow['icon']);
				 }
				?>
				
			

<div class="col" id="item-<?php echo $entry; ?>">
<div class="item background" style="background-image: url('/images/shop/mounts/snow_hippogryph.png');">
<div class="image" rel="50818">
<img src="http://static.wowhead.com/images/wow/icons/medium/<?php echo $icon; ?>.jpg" alt="" /> <br><br>
<input type="button" class="btn" value="Add to cart" onclick="addCartItem(<?php echo $entry; ?>,'<?php echo $shop; ?>Cart', '<?php echo $shop; ?>',this)">
								   
								   </div>
<div class="name quality-4">
<span><a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $entry; ?>" 
                                       class="<?php echo $class; ?>_tooltip" target="_blank">
                                       <?php echo $row['name']; ?></a></span>
</div>
<div class="price">
<div style="display:none;" id="status-<?php echo $entry; ?>" class="green_text">
						   物品已添加到您的购物车
						   </div>
<span class="coin-gold"></span>


<?php echo $row["price"]; ?> 
 <?php 
								   if ($shop=="donate") 
								   	   echo $GLOBALS['donation']['coins_name'];
								   else 
									   echo '投票积分';   
								   ?></div>

</div>
</div>

			

                <?php
			}
		}
	}
	
	public function listAll($shop)
	{
		connect::selectDB('webdb');
		$shop = mysql_real_escape_string($shop);
		
		$result = mysql_query("SELECT entry,displayid,name,quality,price,faction,class
		FROM shopitems WHERE in_shop = '".$shop."'");
		
		if(mysql_num_rows($result)==0)
			echo '在商城中找不到任何物品。';
		else
		{
			while($row = mysql_fetch_assoc($result))
			{
				$entry = $row['entry'];
				$getIcon = mysql_query("SELECT icon FROM item_icons WHERE displayid='".$row['displayid']."'");
				 if(mysql_num_rows($getIcon)==0) 
				 {
					 //发现没有图标。也许灾难项目。从wowhead获取图标。
					 $sxml = new SimpleXmlElement(file_get_contents('http://www.wowhead.com/item='.$entry.'&xml'));
					  
					  $icon = strtolower(mysql_real_escape_string($sxml->item->icon));
					  //现在我们已经加载了它。将其添加到数据库中供以后使用。
					  //注意，WoWHead XML非常慢。这就是我们将其添加到数据库中的主要原因。
					  mysql_query("INSERT INTO item_icons VALUES('".$row['displayid']."','".$icon."')");
				 }
				 else 
				 {
				   $iconrow = mysql_fetch_assoc($getIcon);
				   $icon = strtolower($iconrow['icon']);
				 }
				?>
                <div class="shopBox" id="item-<?php echo $entry; ?>"> 
                   <table>
                          <tr> 
                           <td>
                            <div class="iconmedium icon" rel="50818">
                                 <ins style="background-image: url('http://static.wowhead.com/images/wow/icons/medium/<?php echo $icon; ?>.jpg');">
                                 </ins>
                                 <del></del>
                                 </div>
                           </td>
                               <td width="380">
                               <a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $entry; ?>" 
                               class="<?php echo $class; ?>_tooltip" target="_blank">
                               <?php echo $row['name']; ?></a></td>
                               <td align="right" width="350">
                               <?php if($row['faction']==2) {
                                 echo "<span class='blue_text'>仅联盟</span>";  
                                 if($row['class']!="-1")
                                 echo "<br/>";
                               } elseif($row['faction']==1) {
                                 echo "<span class='red_text'>仅部落</span>"; 
                                 if($row['class']!="-1")
                                 echo "<br/>";
                               }
                               
                               if($row['class']!="-1") {
                                 echo shop::getClassMask($row['class']);
                               }
                               
                               if(isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=5)
                               {
                             ?>
                             <font size="-2">
                             ( <a 
                             onclick="editShopItem('<?php echo $entry; ?>','<?php echo $shop; ?>','<?php echo $row['price']; ?>')">编辑</a> | 
                             <a
                             onclick="removeShopItem('<?php echo $entry; ?>','<?php echo $shop; ?>')">移除</a> )
                             </font>
                             &nbsp; &nbsp; &nbsp; &nbsp;   
                             <?php
                               }
                               
                               ?>
                               <font class="shopItemPrice"><?php echo $row["price"]; ?> 
                               <?php if ($shop=="donate") {
                                 echo $GLOBALS['donation']['coins_name'];
                               } else {
                                echo '投票积分';   
                               }?></font>
                         
                       <div style="display:none;" id="status-<?php echo $entry; ?>" class="green_text">
                       物品已添加到您的购物车
                       </div></td>
                               <td><input type="button" value="Add to cart" 
                               onclick="addCartItem(<?php echo $entry; ?>,'<?php echo $shop; ?>Cart',
                               '<?php echo $shop; ?>',this)"> 
                               
                               </td> 
                           </tr> 
                    </table> 
                </div>
                <?php
			}
		}
		
	}
	

	public function logItem($shop,$entry,$char_id,$account,$realm_id,$amount) 
	{
		connect::selectDB('webdb');
		date_default_timezone_set($GLOBALS['timezone']);
		mysql_query("INSERT INTO shoplog VALUES ('','".(int)$entry."','".(int)$char_id."','".date("Y-m-d H:i:s")."',
		'".$_SERVER['REMOTE_ADDR']."','".mysql_real_escape_string($shop)."','".(int)$account."','".(int)$realm_id."','".(int)$amount."')");
	}
	
	public static function getClassMask($classID) {
		
		switch((int)$classID) {

			case(1):
			 return "<span class='warrior_color'>战士</span> <br/>";
			break;
			case(2):
			 return "<span class='paladin_color'>圣骑士</span> <br/>";
			break;
			case(4):
			 return "<span class='hunter_color'>猎人</span> <br/>";
			break;
			case(8):
			 return "<span class='rogue_color'>盗贼</span> <br/>";
			break;
			case(16):
			 return "<span class='priest_color'>牧师</span> <br/>";
			break;
			case(32):
			 return "<span class='dk_color'>死亡骑士</span> <br/>";
			break;
			case(64):
			 return "<span class='shaman_color'>萨满</span> <br/>";
			break;
			case(128):
			 return "<span class='mage_color'>法师</span> <br/>";
			break;
			case(256):
			 return "<span class='warlock_color'>术士</span> <br/>";
			break;
			case(1024):
			 return "<span class='druid_color'>德鲁伊</span> <br/>";
			break;
		}
		
	}
}

?>