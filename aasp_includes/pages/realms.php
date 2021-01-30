<?php
 	$server->selectDB('webdb'); 
 	$page = new page;
	
	$page->validatePageAccess('Realms');
	
    if($page->validateSubPage() == TRUE) {
		$page->outputSubPage();
	} else {
?>
<div class='box_right_title'>新服务器</div>
<?php if(isset($_POST['add_realm'])) {
	$server->addRealm($_POST['realm_id'],$_POST['realm_name'],$_POST['realm_desc'],$_POST['realm_host'],$_POST['realm_port']
			,$_POST['realm_chardb'],$_POST['realm_sendtype'],$_POST['realm_rank_username'],
			$_POST['realm_rank_password'],$_POST['realm_ra_port'],$_POST['realm_soap_port'],$_POST['realm_a_host']
			,$_POST['realm_a_user'],$_POST['realm_a_pass']);	
}?>

                        <form action="?p=realms" method="post" style="line-height: 15px;">
                        <b>服务器常规信息</b><hr/>
                        服务器ID：<br/>
                        <input type="text" name="realm_id" placeholder="默认: 1"/> <br/>
                        <i class='blue_text'>这个ID必须与您在Realm中的realmlist表中指定的ID相同。
                        					 否则正常运行时间将不起作用。</i><br/>
                        服务器名称：<br/>
                        <input type="text" name="realm_name" placeholder="默认: 时光回溯"/> <br/>
                        （可选）服务器说明：<br/>
                        <input type="text" name="realm_desc" placeholder="默认: Blizzlike 3x"/> <br/>
                        服务器端口：<br/>
                        <input type="text" name="realm_port" placeholder="默认: 8085"/> <br/>
                        主机：(IP 或 域名)<br/>
                        <input type="text" name="realm_host" placeholder="默认: 127.0.0.1"/> <br/>
                        
                        <br/>
                        <b>远程控制台信息</b> <i>（投票&捐赠商店）</i><hr/>
                        远程控制台<i>（以后你可以随时修改）</i>: <br/>
                        <select name="realm_sendtype">
                                 <option value="ra">RA</option>
                                 <option value="soap">SOAP</option>
                        </select><br/>
                        <i class='blue_text'>指定一个级别3的GM帐户(用于远程控制台)<br/>
                        提示:不要使用你的管理帐户。使用3级帐户。</i><br/>
                        用户名： <br/>
                        <input type="text" name="realm_rank_username" placeholder="Default: rauser"/> <br/>
                        密码：<br/>
                        <input type="password" name="realm_rank_password" placeholder="Default: rapassword"/> <br/>
                        RA port: <i>如果您选择了SOAP，则可以忽略)</i> <br/>
                        <input type="text" name="realm_ra_port" placeholder="Default: 3443"/> <br/>
                        SOAP port: <i>(如果你选择了RA，可以忽略)</i> <br/>
                        <input type="text" name="realm_soap_port" placeholder="Default: 7878"/> <br/>
                        <br/>
                        <b>MySQL信息</b> <i>(如果留空，设置将从配置文件中复制)</i><hr/>
                        MySQL Host: <br/>
                        <input type="text" name="realm_m_host" placeholder="Default: 127.0.0.1"/><br/>
                        MySQL User: <br/>
                        <input type="text" name="realm_m_user" placeholder="Default: root"/><br/>
                        MySQL Password: <br/>
                        <input type="text" name="realm_m_pass" placeholder="Default: ascent"/><br/>
                        Character Database: <br/>
                        <input type="text" name="realm_chardb" placeholder="Default: characters"/> <br/>
                        <hr/>
                        <input type="submit" value="添加" name="add_realm" />                     
                        </form>
<?php } ?>
