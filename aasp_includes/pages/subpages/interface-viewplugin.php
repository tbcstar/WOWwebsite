<?php 
    global $GameServer;
    $conn = $GameServer->connect();
    $GameServer->selectDB('webdb', $conn);

	$filename = $_GET['plugin']; 
	include('../plugins/'.$filename.'/info.php');			
?>
<div class="box_right_title"><a href="?p=interface&s=plugins">插件</a> &raquo; <?php echo $title; ?></div>
<b><?php echo $title; ?></b><br/>
<?php echo $desc; ?>
<hr/>
作者： <?php echo $author; ?> - <?php echo $created; ?>
<p/>
<b>文件：</b><br/>
<?php
$bad = array('.','..');
//Classes
$folder = scandir('../plugins/'.$filename.'/classes/');
if (is_array($folder) || is_object($folder))
{
	foreach($folder as $file)
	{
		if(!in_array($file,$bad))
		{
			echo $file.' (Class)<br/>';
		}
	}
}
//Modules
$folder = scandir('../plugins/'.$filename.'/modules/');
if (is_array($folder) || is_object($folder))
{
	foreach($folder as $file)
	{
		if(!in_array($file,$bad))
		{
			echo $file.' (Module)<br/>';
		}
	}
}

//Pages
$folder = scandir('../plugins/'.$filename.'/pages/');
if (is_array($folder) || is_object($folder))
{
	foreach($folder as $file)
	{
		if(!in_array($file,$bad))
		{
			echo $file.' (Page)<br/>';
		}
	}
}

//Styles
$folder = scandir('../plugins/'.$filename.'/styles/');
if (is_array($folder) || is_object($folder))
{
	foreach($folder as $file)
	{
		if(!in_array($file,$bad))
		{
			echo $file.' (Stylesheet)<br/>';
		}
	}
}

//Javascript
$folder = scandir('../plugins/'.$filename.'/javascript/');
if (is_array($folder) || is_object($folder))
{
	foreach($folder as $file)
	{
		if(!in_array($file,$bad))
		{
			echo $file.' (Javascript)<br/>';
		}
	}
}

$chk = mysqli_query($conn, "SELECT COUNT(*) FROM disabled_plugins WHERE foldername='".mysqli_real_escape_string($conn, $filename)."'");
if(mysqli_data_seek($chk,0) > 0)
{
	echo '<input type="submit" value="启用插件" onclick="enablePlugin(\''.$filename.'\')">';
}
else
{
	echo '<input type="submit" value="禁用插件" onclick="disablePlugin(\''.$filename.'\')">';
} 