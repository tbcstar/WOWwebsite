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
<a href="?page=ucp">
控制面板 </a>
<span class="ico-raquo"></span>
<a href="?page=shop">
商城 </a>
<span class="ico-raquo"></span>
<a href="?page=work_list">
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
global $Website, $Account, $Database, $Character;
$service = "revive";

if ( DATA['service'][$service]['price'] == 0 )
{
    echo '<span class="attention">复活是免费的。</span>';
}
else
{ ?>
<span class="attention">恢复成本 
    <?php echo DATA['service'][$service]['price'] . ' ' . $Website->convertCurrency(DATA['service'][$service]['currency']); ?>
    </span><?php
    if ( DATA['service'][$service]['currency'] == "vp" )
    {
        echo "<span class='currency'>Vote Points: ". $Account->loadVP($_SESSION['cw_user']) ."</span>";
    }
    elseif ( DATA['service'][$service]['currency'] == "dp" )
    {
        echo "<span class='currency'>". DATA['donation']['coins_name'] .": ". $Account->loadDP($_SESSION['cw_user']) ."</span>";
    }
} 

$Account->isNotLoggedIn();
$Database->selectDB("webdb");
$num = 0;
$result = $Database->select("realms", "char_db, name", null, "ORDER BY id ASC")->get_result();
while ($row = $result->fetch_assoc())
{
         $acct_id = $Account->getAccountID($_SESSION['cw_user']);
		 $realm = $row['name'];
		 $char_db = $row['char_db'];
		          	
		$Database->selectDB($char_db);
		$result = $Database->select("characters", "name, guid, gender, class, race, level, online", null, "account='$acct_id'")->get_result();
        while ($row = $result->fetch_assoc())
        { ?>
            <div class='charBox'>
            <table width="100%">
            <tr>
                <td width="73">
                    <?php
                    if ( !file_exists("styles/global/images/portraits/". $row['gender'] ."-" .$row['race'] ."-". $row['class'] .".gif") )
                    {
                        echo "<img src=\"styles/" . DATA['template']['path'] . "/images/unknown.png\" />";
                    }
                    else
                    { ?>
                        <img src="styles/global/images/portraits/<?php echo $row['gender'] . "-" . $row['race'] . "-" . $row['class']; ?>.gif" border="none"><?php 
                    } ?>
                </td>

                <td width="160">
                    <h3><?php echo $row['name']; ?></h3>
                    <?php echo $row['level'] ." ". $Character->getRace($row['race']) ." ". $Character->getGender($row['gender']) ." ". $Character->getClass($row['class']);?>
                </td>

                 <td>
                    Realm: <?php echo $realm; 
                    if ( $row['online'] == 1 ) echo "<br/><span class='red_text'>请在尝试解除卡死之前注销。</span>";?>
                </td>

                <td align="right">
                    &nbsp;
                    <input type="submit" value="Revive" 
                        <?php if ( $row['online'] == 0 )
                            { ?>
                                onclick='revive(<?php 
                                    echo $row['guid']; 
                                    ?>, "<?php 
                                    echo $char_db; ?>")' <?php 
                            }
                            else
                            {
                                echo 'disabled="disabled"';
                            } ?> >
                </td>
            </tr>
            </table>
            </div><?php
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