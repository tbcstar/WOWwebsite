<?php account::isNotLoggedIn(); ?>
<div id="overlay"></div>
<div id="wrapper">

<?php include "header.php" ?>

<div id="leftcontent">
<div class="box_two">
<?php
 	global $Website;
    $Website->getNews();
	 
	if ($GLOBALS['enableSlideShow']==false && $GLOBALS['news']['enable']==false)  
	{
		buildError("<b>配置文件错误。</b>幻灯片和新闻都不显示，主页将是空的。");
		echo "看起来主页是空的!";
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