<?php 

#################
# Not finished. #
#################

global $Support;

?>
<div class='box_two_title'>支持帮助</div>
<?php exit('This page was never completed.'); ?>
<table class='splashWebformLink'>
       <tr>
           <td>
           <a href="?page=support&do=email">
           <span class="splashWebformLogo"></span>
           <span class="webformText">电子邮件支持</span></a>
           </td>
           <td>
           <a href="?page=support&do=faq">
           <span class="splashWebformLogo"></span>
           <span class="webformText">FAQ</span></a>
           </td>
       </tr>
</table> 
<?php 
if (isset($_GET['do']) && $_GET['do']=="email")
	$Support->loadEmailForm();
?>      