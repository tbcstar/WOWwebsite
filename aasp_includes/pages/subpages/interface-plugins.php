<?php $page = new page;
	  $server = new server; ?>
<div class="box_right_title">插件</div>
<table>
	<tr>
    	<th>名称</th>
        <th>描述</th>
        <th>作者</th>
        <th>创建日期</th>
        <th>状态</th>
    </tr>
<?php
	$bad = array('.','..','index.html');
	
	$folder = scandir('../plugins/');
	foreach($folder as $folderName)
	{
		if(!in_array($folderName,$bad))
		{
			if(file_exists('../plugins/'.$folderName.'/info.php'))
			{
				include('../plugins/'.$folderName.'/info.php');
				?> <tr class="center" onclick="window.location='?p=interface&s=viewplugin&plugin=<?php echo $folderName; ?>'"> <?php
					echo '<td><a href="?p=interface&s=viewplugin&plugin='.$folderName.'">'.$title.'</a></td>';
					echo '<td>'.substr($desc,0,40).'</td>';
					echo '<td>'.$author.'</td>';
					echo '<td>'.$created.'</td>';
					$server->selectDB('webdb');
					$chk = mysql_query("SELECT COUNT(*) FROM disabled_plugins WHERE foldername='".mysql_real_escape_string($folderName)."'");
					if(mysql_result($chk,0)>0)
						echo '<td>禁用</td>';
					else
						echo '<td>启用</td>';
				echo '</tr>';
			}
		}
	}
	
	if($count==0)
	{
		$_SESSION['loaded_plugins'] = NULL;
	}
	else
	{
		$_SESSION['loaded_plugins'] = $loaded_plugins;
	}
?>
</table>