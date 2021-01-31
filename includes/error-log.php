<?php 
define('INIT_SITE', TRUE);
require('configuration.php'); 
?>

<h2>错误日志</h2>
<?php
if($GLOBALS['useDebug']==false)
{
	exit();
}
?>

<a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=clear" title="Clear the entire log">清除日志</a><hr/>
<?php
if (isset($_GET['action']) && $_GET['action']=="clear") {
$myFile = "../error.log";
$fh = fopen($myFile, 'w') or die("无法打开文件");
$stringData = "";
fwrite($fh, $stringData);
fclose($fh);
  	?>
	<meta http-equiv="Refresh" content="0; url=<?php echo $_SERVER['PHP_SELF']; ?>">
  	<?php
}
if(!$file = file_get_contents('../error.log')) 
{
  echo "该脚本无法从error.log文件中获取任何内容。";
}

echo str_replace("*","<br/>",$file);

?>