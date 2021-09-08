<?php include "headers.php" ?>
<style>
.orange_tooltip { color: #FF8000; }
.purple_tooltip { color: #A335EE; }
.blue_tooltip { color: #0070DD; }
.green_tooltip { color: #1EFF00; }
.gray_tooltip { color: #9D9D9D; }
.white_tooltip { color: #fff; }
.gold_tooltip { color: #E5CC80; } 

.warrior_color { color: #C69B6D; }
.paladin_color { color: #F48CBA; }
.hunter_color { color: #AAD372; }
.rogue_color { color: #FFF468; }
.priest_color { color: #FFF; }
.dk_color { color: #C41E3B; }
.shaman_color { color: #2359FF; }
.mage_color { color: #68CCEF; }
.warlock_color { color: #9382C9; }
.druid_color { color: #FF7C0A; }
</style>
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
<a class="active" href="?page=shop">
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
<a href="?page=characters">
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
商品 </div>
</div>
<div class="realm_picker">
<div class="">
所在服务器： </div>
<a href="game.tbcstar.com">
时光回溯 </a>
</div>
</div>


{alert}


<?php 
 global $Account, $Shop, $Connect;
 $conn = $Connect->connectToDB();
 $Account->isNotLoggedIn();

 /* 声明一些通用变量 */ 
 $shopPage = $conn->escape_string($_GET['p']);
 $shopVar = "vote";
 $shopCurrency = "Vote Points";
 
 $selected = 'selected="selected"';
 ///////////////////////////////
 ?>
<div class='box_two_title'>投票商店

<div id='cartHolder' onclick='window.location="?page=cart"'>加载购物车...</div> 
        <div id='cartArrow'>
        <img src='styles/default/images/arrow.png' border='none'/></div>
</div>

<?php
if($GLOBALS[$shopVar.'Shop']['enableShop']==FALSE)
	echo "<span class='attention'><b>注意！</b>商城暂未开放。请稍后再来。</span>";
else 
{
?>

<span class='currency'><?php echo $shopCurrency; ?>: 
<?php echo $Account->loadVP($_SESSION['cw_user']); ?></span>
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
<center> 


<section class="main-section">
<div id="yw0" class="list-view">
<div class="shop-items clearfix">
<div class="items">

 
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
</center> 


</div>
</section>
</div>
</div>
</div>
</main>
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