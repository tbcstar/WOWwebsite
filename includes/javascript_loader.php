<script type="text/javascript" src="javascript/main.js"></script>
<script type="text/javascript" src="javascript/jquery.js"></script>
<?php
####购物车####
if($_GET['page'] ==' donateshop') 
{ ?>
<script type="text/javascript">
		$(document).ready(function()
		{
			loadMiniCart("donateCart");
		});
</script>
<?php }

if($_GET['page']=='voteshop') 
{ ?>
	<script type="text/javascript">
            $(document).ready(function()
            {
                loadMiniCart("voteCart");
            });
    </script>
<?php } 


{
?>
	
<?php 
}
if($GLOBALS['core_expansion'] > 2)
{
	echo '<script type="text/javascript" src="/javascript/tooltip.js"></script>';
}
else
{
	echo '<script type="text/javascript" src="/javascript/tooltip.js"></script>';
}

####CURSOR TRACKER####
if($_GET['page'] == 'donateshop' || $_GET['page'] == 'voteshop') 
{ 
	?>
	<script type="text/javascript">
	$(document).ready(function() {
		$(document).mousemove(function(e)
		{
		   mouseY = e.pageY;
      	});
	});
	</script><?php 
}

####FACEBOOK####
if($GLOBALS['social']['enableFacebookModule'] == TRUE) 
{  
	?>
	<script type="text/javascript">
		$(document).ready(function() 
		{
			var box_width_one = $(".box_one").width();
			$("#fb").attr('width', box_width_one);
		});
	</script>
	<?php 
}

####服务器状态######
if($GLOBALS['serverStatus']['enable'] == TRUE) 
{
	?>
	<script type="text/javascript">
        $(document).ready(function() 
        {
            $.post("includes/scripts/misc.php", { serverStatus: TRUE },
           function(data) 
           {
               $("#server_status").html(data);
               $(".srv_status_po").hover(function() 
               {
                    $(".srv_status_text").fadeIn("fast");
                }, 
                function() 
                {
                    $(".srv_status_text").fadeOut("fast");
                });
      		});
        });
    </script>
	<?php 
}
global $Plugins;
$Plugins->load('javascript');
	
?>

