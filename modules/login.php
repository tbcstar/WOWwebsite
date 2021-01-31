<?php

if (!isset($_SESSION['cw_user'])) 
{ 
    if (isset($_POST['login']))
        global $Account;
        $Account->logIn($_POST['login_username'],$_POST['login_password'],$_SERVER['REQUEST_URI'],$_POST['login_remember']);
?>
     <div class="box_one">
	 <div class="box_one_title">账户管理</div> 
     <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
     <fieldset style="border: none; margin: 0; padding: 0;">
         <input type="text" placeholder="Username" name="login_username" class="login_input" /><br/>
         <input type="password" placeholder="Password..." name="login_password" class="login_input" style="margin-top: -1px;" /><br/>
         <input type="submit" value="Log In" name="login" style="margin-top: 4px;" /> 
         <input type="checkbox" name="login_remember" checked="checked"/> 记住我
     </fieldset>    
     </form> <br/>
     <table width="100%">
            <tr>
                <td><a href="?p=register">创建账户</a></td>
                <td align="right"><a href="?p=forgotpw">忘记密码？</a></td>
            </tr>
     </table>
     </div>
<?php } ?>


<?php if(isset($_SESSION['cw_user'])) { ?>
<div class="box_one">
<div class="box_one_title">账户管理</div>
<span style="z-index: 99;">欢迎回来 <?php echo $_SESSION['cw_user']; ?>
			<?php 
			if (isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=$GLOBALS['adminPanel_minlvl'] && $GLOBALS['adminPanel_enable']==true) 
				echo ' <a href="admin/">(管理面板)</a>';
				
			if (isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=$GLOBALS['staffPanel_minlvl'] && $GLOBALS['staffPanel_enable']==true) 
				echo ' <a href="staff/">(员工面板)</a>';
			?>
            </span>
			<hr/>
            <input type='button' value='账户面板' onclick='window.location="?p=account"' class="leftbtn">
			<input type='button' value='密码修改'  onclick='window.location="?p=changepass"' class="leftbtn">
            <input type='button' value='投票商店' onclick='window.location="?p=voteshop"' class="leftbtn">  
			<input type='button' value='公益商城'  onclick='window.location="?p=donateshop"' class="leftbtn">
            <input type='button' value='战友招募'  onclick='window.location="?p=raf"' class="leftbtn">
            <input type='button' value='注销'  
            onclick='window.location="?p=logout&last_page=<?php echo $_SERVER["REQUEST_URI"]; ?>"' class="leftbtn">
</div>
			<?php } ?>


