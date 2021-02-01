<div id="overlay"></div>
<div id="wrapper">

<?php include "header.php" ?>

<div id="leftcontent">
<div class="box_two">

<?php
global $Account, $Connect;
?>

<div class='box_two_title'>忘记密码</div>
<?php 
$Account->isLoggedIn();
if (isset($_POST['forgotpw'])) 
	$Account->forgotPW($_POST['forgot_username'],$_POST['forgot_email']);

if(isset($_GET['code']) || isset($_GET['account'])) {
 if (!isset($_GET['code']) || !isset($_GET['account']))
	 echo "<b class='red_text'>链接错误，缺少一个或多个必需的值。</b>";
 else 
 {
	 $Connect->selectDB('webdb');
	 $code = mysqli_real_escape_string($conn, $_GET['code']); $account = mysqli_real_escape_string($conn, $_GET['account']);
	 $result = mysqli_query($conn, "SELECT COUNT('id') FROM password_reset WHERE code='".$code."' AND account_id='".$account."'");
	 if (mysqli_data_seek($result,0)==0)
		 echo "<b class='red_text'>指定的值与数据库中的值不匹配。</b>";
	 else 
	 {
		 $newPass = RandomString();
		 echo "<b class='yellow_text'>您的新密码是: ".$newPass." <br/><br/>请登录并更改您的密码。</b>";
		 mysqli_query($conn, "DELETE FROM password_reset WHERE account_id = '".$account."'");
		 $account_name = $Account->getAccountName($account);
		 
		 $Account->changePassword($account_name,$newPass);
		 
		 $ignoreForgotForm = true;
	 }
 }
}
if (!isset($ignoreForgotForm)) { ?> 
要重置密码，请输入您的用户名和注册的电子邮件地址。您将收到一封包含重置密码链接的电子邮件。<br/><br/>

<form action="?p=forgotpw" method="post">
<table width="80%">
    <tr>
         <td align="right">用户名：</td> 
         <td><input type="text" name="forgot_username" /></td>
    </tr>
    <tr>
         <td align="right">E-mail:</td> 
         <td><input type="text" name="forgot_email" /></td>
    </tr>
    <tr>
         <td></td>
         <td><hr/></td>
    </tr>
    <tr>
         <td></td>
         <td><input type="submit" value="OK!" name="forgotpw" /></td>
    </tr>
</table>
</form> 
<?php } ?>
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