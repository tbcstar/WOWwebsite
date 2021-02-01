<div id="overlay"></div>
<div id="wrapper">

<?php include "header.php" ?>

<div id="leftcontent">
<div class="box_two">

<?php
global $Account;
$Account->isNotLoggedIn(); ?>

<div class='box_two_title'>战友招募</div>
<b class='yellow_text'>你的招募链接</b> <div id="raf_box">
                  <?php echo $GLOBALS['website_domain']."?p=register&id=".$Account->getAccountID($_SESSION['cw_user']); ?>
</div><br/>
<h4 class='blue_text'>它是如何工作的？</h4>

很简单!只要复制上面的链接，并发送给你的朋友。如果他们使用你的推荐链接创建一个帐户，你们俩可以冒险进入艾泽拉斯以更快的升级速度，声望，和更多!
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