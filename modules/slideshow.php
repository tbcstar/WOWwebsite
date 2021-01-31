<?php

if(!isset($_GET['p']))
	$page = "home";
else
	$page = $_GET['p'];
	
if ($GLOBALS['enableSlideShow'] == TRUE && !isset($_COOKIE['hideslider']) && $_GET['p']=='home') 
{ 
	global $Website;?>
<div class="main_view">
    <div class="window">
        <div class="image_reel">
        		<?php $Website->getSlideShowImages(); ?>
        </div>
    </div>
    <div class="paging">
        <?php $Website->getSlideShowImageNumbers(); ?>
    </div>
</div>
<?php } ?>