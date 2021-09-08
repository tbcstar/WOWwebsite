<div id="overlay"></div>
<div id="wrapper">

<?php include "header.php" ?>

<div id="leftcontent">
<div class="box_two">
<?php 
global $Account, $Shop, $Connect;
$conn = $Connect->connectToDB();
$Account->isNotLoggedIn();

 /* 声明一些通用变量 */ 
 $shopPage = $conn->escape_string($_GET['page']);
 $shopVar = "donate";
 $shopCurrency = $GLOBALS['donation']['coins_name'];
 
 $selected = 'selected="selected"';
 ///////////////////////////////
 ?>
<div class='box_two_title'>公益商城

<div id='cartHolder' onclick='window.location="?page=cart"'>加载购物车...</div> 
        <div id='cartArrow'>
        <img src='styles/default/images/arrow.png' border='none'/></div>
</div>

<?php
if($GLOBALS[$shopVar.'Shop']['enableShop']==FALSE)
	echo "<span class='attention'><b>注意！ </b>商城还未开放。请稍后再来。</span>";
else 
{
?>

<span class='currency'><?php echo $shopCurrency; ?>: 
<?php echo $Account->loadDP($_SESSION['cw_user']); ?></span>
<?php if (!isset($_GET['search'])) { $inputValue = "搜索物品..."; } else { $inputValue = $_GET['search_value']; } 

if($GLOBALS[$shopVar.'Shop']['shopType']==1)
{
	//Search enabled.
?>
<center>
        <form action="?page=<?php echo $shopPage; ?>" method="get">
        <input type="hidden" name="p" value="<?php echo $shopPage; ?>">
        <table> <tr valign="middle">
            <td><input type="text" onclick="this.value=''" value="<?php echo $inputValue; ?>" name="search_value"></td>          
            <td><input type="submit" value="Search" name="search"></td>
            <tr>
        </table>
        <?php if($GLOBALS[$shopVar.'Shop']['enableAdvancedSearch']==TRUE) { ?> <br/>
        高级搜索<br/>
		<table width="56%">
		                   <tr>	<td>	
                            <select name="q" style="width: 100%">
                                <option>--品质--</option>
                                <option value="0" <?php if(isset($_GET['q']) && $_GET['q']==0 && $_GET['q']!="--Quality--" 
								&& isset($_GET['q'])) 
								{ echo $selected; } ?>>
                                Poor</option>
                                <option value="1" <?php if(isset($_GET['q']) && $_GET['q']==1) { echo $selected; } ?>>白色</option>
                                <option value="2" <?php if(isset($_GET['q']) && $_GET['q']==2) { echo $selected; } ?>>绿色</option>
                                <option value="3" <?php if(isset($_GET['q']) && $_GET['q']==3) { echo $selected; } ?>>蓝色</option>
                                <option value="4" <?php if(isset($_GET['q']) && $_GET['q']==4) { echo $selected; } ?>>紫色</option>
                                <option value="5" <?php if(isset($_GET['q']) && $_GET['q']==5) { echo $selected; } ?>>橙色</option>
                                <option value="7" <?php if(isset($_GET['q']) && $_GET['q']==7) { echo $selected; } ?>>传家宝</option>
                            </select>	
                           </td>
                           <td>	<select name="r" style="width: 100%">
                                    <option>--结果--</option>
                                    <option value="50" <?php if(isset($_GET['r']) && $_GET['r']==50) { echo $selected; }?>>50</option>
                                    <option value="100" <?php if(isset($_GET['r']) && $_GET['r']==100) { echo $selected; }?>>100</option>
                                    <option value="150" <?php if(isset($_GET['r']) && $_GET['r']==150) { echo $selected; }?>>150</option>
                                    <option value="200" <?php if(isset($_GET['r']) && $_GET['r']==200) { echo $selected; }?>>200</option>
                            </select>	
                        
                            </td>
                           	</tr>
                            <tr>	
                            <td>	
								<select name="t" style="width: 100%">
                                <option>--类型--</option>
                                <option value="0" <?php if(isset($_GET['t']) && $_GET['t']==0 && $_GET['t']!="--Type--"
								&& isset($_GET['q'])) 
								{ echo $selected; } ?>>消耗品</option>
                                <option value="1" <?php if(isset($_GET['t']) && $_GET['t']==1) { echo $selected; } ?>>背包</option>
                                <option value="2" <?php if(isset($_GET['t']) && $_GET['t']==2) { echo $selected; } ?>>武器</option>
                                <option value="3" <?php if(isset($_GET['t']) && $_GET['t']==3) { echo $selected; } ?>>珠宝</option>
                                <option value="4" <?php if(isset($_GET['t']) && $_GET['t']==4) { echo $selected; } ?>>护甲</option>
                                <option value="15" <?php if(isset($_GET['t']) && $_GET['t']==15) { echo $selected; } ?>>其它</option>
                                <option value="16"<?php if(isset($_GET['t']) && $_GET['t']==16) { echo $selected; } ?>>图腾</option>
                                <option value="15-5" <?php if(isset($_GET['t']) && $_GET['t']=="15-5") { echo $selected; } ?>>坐骑</option>
                                <option value="15-2" <?php if(isset($_GET['t']) && $_GET['t']=="15-2") { echo $selected; } ?>>宠物</option>
                                </select>	
                           </td> 
                           <td>	 
                                <input type="checkbox" name="st"  value="8"/> Heroic
                            </td>
                           	</tr>
                            <tr>
                                <td>
                                <select name="f" style="width: 100%">
                                    <option>--阵营--</option>
                                    <option value="1" <?php if(isset($_GET['f']) && $_GET['f']==1) { echo $selected; }?>>部落</option>
                                    <option value="2" <?php if(isset($_GET['f']) && $_GET['f']==2) { echo $selected; }?>>联盟</option>
                                </select>
                                </td>
                                <td>
                                <select name="c" style="width: 100%">
                                    <option>--职业--</option>
                                    <option value="1" <?php if(isset($_GET['c']) && $_GET['c']==1) { echo $selected; }?>>战士</option>
                                    <option value="2" <?php if(isset($_GET['c']) && $_GET['c']==2) { echo $selected; }?>>圣骑士</option>
                                    <option value="4" <?php if(isset($_GET['c']) && $_GET['c']==4) { echo $selected; }?>>猎人</option>
                                    <option value="8" <?php if(isset($_GET['c']) && $_GET['c']==8) { echo $selected; }?>>盗贼</option>
                                    <option value="16" <?php if(isset($_GET['c']) && $_GET['c']==16) { echo $selected; }?>>牧师</option>
                                    <option value="32" <?php if(isset($_GET['c']) && $_GET['c']==32) { echo $selected; }?>>死亡骑士</option>
                                    <option value="64" <?php if(isset($_GET['c']) && $_GET['c']==64) { echo $selected; }?>>萨满</option>
                                    <option value="128" <?php if(isset($_GET['c']) && $_GET['c']==128) { echo $selected; }?>>法师</option>
                                    <option value="256" <?php if(isset($_GET['c']) && $_GET['c']==256) { echo $selected; }?>>术士</option>
                                    <option value="1024" <?php if(isset($_GET['c']) && $_GET['c']==1024) { echo $selected; }?>>德鲁伊</option>
                                </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                            <select name="ilfrom" style="width: 100%">
                                            <option>--物品等级--</option>
                                            <?php
											    for ($i = 1; $i <= $GLOBALS['maxItemLevel']; $i++) 
												{
													 if($_GET['ilfrom']==$i)
														 echo "<option selected='selected'>";
													 else
														 echo "<option>";

													echo $i."</option>";
												}
											?>
                                    </select>	
                                </td>
                                <td>
                                            <select name="ilto" style="width: 100%">
                                            <option>--物品等级--</option>
                                            <?php
											    for ($i = $GLOBALS['maxItemLevel']; $i >= 1; $i--) 
												{
													 if($_GET['ilto']==$i)
														 echo "<option selected='selected'>";
													 else
														 echo "<option>";

													echo $i."</option>";
												}
											?>
                                    </select>	
                                </td>
                            </tr>
        </table>
		<?php } ?>
        </form><br/>
</center> 
<?php 
if (isset($_GET['search'])) {
		$Shop->search($_GET['search_value'],$shopVar,$_GET['q'],$_GET['t'],$_GET['ilfrom'],$_GET['ilto'],$_GET['r'],$_GET['f'],$_GET['c'],$_GET['st']);
	}
}
elseif($GLOBALS[$shopVar.'Shop']['shopType']==2)
{
	//List all items.
		$Shop->listAll($shopVar);	
	}
}
?>
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