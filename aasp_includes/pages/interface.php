<?php 

    global $GameServer, $GamePage;
    $conn = $GameServer->connect();
    $GameServer->selectDB('webdb', $conn);
	
	$GamePage->validatePageAccess('Interface');
	
    if($GamePage->validateSubPage() == TRUE) 
    {
		$GamePage->outputSubPage();
	} 
    else 
    {
?>
<div class="box_right_title">网站模板</div>          
    
	在这里，你可以选择启用那个模板，也可以在这里安装新的模板。<br/><br/>
    <h3>选择模板</h3>
        <select id="choose_template">
        <?php
            $result = $conn->query("SELECT * FROM template ORDER BY id ASC;");
            while ($row = $result->fetch_assoc())
            {
                if($row['applied'] == 1)
                {
                    echo "<option selected='selected' value='".$row['id']."'>[Active] ";
                }
                else
                {
                    echo "<option value='".$row['id']."'>";
                }

                echo $row['name']."</option>";
            }
        ?>
        </select>
        <input type="submit" value="Save" onclick="setTemplate()"/><hr/><p/>
        
        <h3>安装一个新模板</h3>
        <a href="#" onclick="templateInstallGuide()">如何在你的网站上安装新模板</a><br/><br/><br/>
        模板的路径<br/>
        <input type="text" id="installtemplate_path"/><br/>
        选择一个名字<br/>
        <input type="text" id="installtemplate_name"/><br/>
        <input type="submit" value="安装" onclick="installTemplate()"/>
        <hr/>
        <p/>
        
        <h3>卸载一个模板</h3>
        <select id="uninstall_template_id">
        <?php
            $result = $conn->query("SELECT * FROM template ORDER BY id ASC;");
            while ($row = $result->fetch_assoc())
            {
                if($row['applied'] == 1)
                {
                    echo "<option selected='selected' value='".$row['id']."'>[Active] ";
                }
                else
                {
                    echo "<option value='".$row['id']."'>";
                }

                echo $row['name']."</option>";
            }
        ?>
        </select>
        <input type="submit" value="卸载" onclick="uninstallTemplate()"/> 
 <?php } ?>