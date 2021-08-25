<div id="overlay"></div>
<div id="wrapper">

<?php include "header.php" ?>

<div id="leftcontent">
<div class="box_two">

<div class='box_two_title'更改密码</div>
<?php
	global $Account;
    $Account->isNotLoggedIn();
    if (isset($_POST['change_password']))
    {
        $Account->changePass($_POST['current_password'], $_POST['new_password'], $_POST['new_password_repeat']);
    }
?>
<form method="POST">
<table width="70%">
    	<tr>
            <td>当前密码:</td> 
            <td><input type="password" name="current_password" class="input_text"/></td>
        </tr> 

        <tr>
           <td></td> 
           <td><hr/></td>
        </tr>

        <tr>
            <td>新的密码:</td> 
            <td><input type="password" name="new_password" class="input_text"/></td>
        </tr> 
        <tr>
            <td>重复新密码:</td> 
            <td><input type="password" name="new_password_repeat" class="input_text"/></td>
        </tr>

        <tr>
           <td></td> 
            <td><input type="submit" value="Change Password" name="change_password" /></td>
       </tr>                
</table>                 
</form>

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