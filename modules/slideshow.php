<?php

if(!isset($_GET['page']))
	$page = "home";
else
	$page = $_GET['page'];
	
if ($GLOBALS['enableSlideShow'] == TRUE && !isset($_COOKIE['hideslider']) && $_GET['page']=='home') 
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