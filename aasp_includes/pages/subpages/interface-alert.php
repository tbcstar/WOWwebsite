<?php 
	global $Page; 
	
	if(isset($_POST['update_alert']))
	{
		$alert_enable = $_POST['alert_enable'];
		$alert_message = trim($_POST['alert_message']);

		$alert_enable = ($alert_enable == "on") ? "TRUE" : "false";

		$file_content = "<?php

						$alert_enabled = ". $alert_enable .";

						$alert_message = \"". $alert_message ."\";

						?>
						";
		
		$fp = fopen('../documents/alert.php', 'w');
		if(fwrite($fp, $file_content))
		{
			$msg = "警报消息已更新!";
		}
		else
		{
			$msg = "[失败]无法写入文件!";
		}

		fclose($fp);
	}

	include('../documents/alert.php');
?>
<div class="box_right_title"><?php echo $Page->titleLink(); ?> &raquo; 告警信息</div>
<form action="?page=interface&selected=alert" method="post">
<table>
	<tr>
    	<td>启用告警消息</td>
        <td><input name="alert_enable" type="checkbox" <?php if ($alert_enabled == TRUE) echo 'checked'; ?> /></td>
    </tr>
    <tr>
    	<td>告警信息</td>
        <td><textarea name="alert_message" cols="60" rows="3"><?php echo $alert_message; ?></textarea>
    </tr>
    <tr>
    	<td></td>
        <td><input type="submit" value="Save" name="update_alert">
        <?php
			if(isset($msg))
			{
				echo $msg;
			}
		?>
        </td>
    </tr>
</table>
</td>