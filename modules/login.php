<?php

if ( !isset($_SESSION['cw_user']) )
{ ?>
    <h4>帐户管理</h4><?php
    if ( isset($_POST['login']) )
    { 
        global $Account;
        $Account->logIn($_POST['login_username'], $_POST['login_password'], $_SERVER['REQUEST_URI'], $_POST['login_remember']);
    }
    ?><form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input class="form-control" type="text" placeholder="Username..." name="login_username" /><br/>
        </div>
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input class="form-control" type="password" placeholder="Password..." name="login_password" /><br/>
        </div>
        <div class="form-group">
            <input class="form-control btn btn-default" type="submit" value="Log In" name="login" /> 
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="login_remember" checked="checked"/> 记住账号</label>
        </div>
        <small><a href="?page=register">创建一个帐户</a></small><br>
        <small><a href="?page=forgotpw">忘记密码？</a></small>
    </form><?php
} ?>


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
            <input type='button' value='账户面板' onclick='window.location="?page=account"' class="leftbtn">
			<input type='button' value='密码修改'  onclick='window.location="?page=changepass"' class="leftbtn">
            <input type='button' value='投票商店' onclick='window.location="?page=voteshop"' class="leftbtn">  
			<input type='button' value='公益商城'  onclick='window.location="?page=donateshop"' class="leftbtn">
            <input type='button' value='战友招募'  onclick='window.location="?page=raf"' class="leftbtn">
            <input type='button' value='注销'  
            onclick='window.location="?page=logout&last_page=<?php echo $_SERVER["REQUEST_URI"]; ?>"' class="leftbtn">
</div>
			<?php } ?>


