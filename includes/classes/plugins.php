<?php

class Plugins 
{
	public static function globalInit()
	{
		if($GLOBALS['enablePlugins'] == true)
		{
			if(!isset($_SESSION['loaded_plugins']))
			{
				global $Connect, $conn;
				$loaded_plugins = array();
				
				$bad 	= array('.','..','index.html');
				$count 	= 0;
				
				$folder = scandir('plugins/');
				if (is_array($folder) || is_object($folder))
				{
					foreach($folder as $folderName)
					{
						if(!in_array($folderName,$bad))
						{
							$Connect->selectDB('webdb');
							if(file_exists('plugins/'. $folderName .'/config.php'))
							{
								include('plugins/'. $folderName .'/config.php');
							}

							$loaded_plugins[] = $folderName;
							$count++;
						}
					}
				}

				if($count == 0)
				{
					$_SESSION['loaded_plugins'] = NULL;
				}
				else
				{
					$_SESSION['loaded_plugins'] = $loaded_plugins;
				}
			}
		}
	}
	
	public static function init($type)
	{
		if($GLOBALS['enablePlugins'] == true)
		{
			if($_SESSION['loaded_plugins'] != NULL)
			{
				global $Connect; global $conn;
				$bad = array('.','..','index.html');
				$loaded = array();
				if (is_array($_SESSION['loaded_plugins']) || is_object($_SESSION['loaded_plugins']))
				{
					foreach($_SESSION['loaded_plugins'] as $folderName)
					{	
						$Connect->selectDB('webdb');
						$chk = mysqli_query($conn, "SELECT COUNT(*) FROM disabled_plugins WHERE foldername='". mysqli_real_escape_string($conn, $folderName) ."'");
						if(mysqli_field_seek($chk, 0) == 0 && file_exists('plugins/' . $folderName . '/'. $type . '/'))
						{	
							$folder = scandir('plugins/' . $folderName . '/'. $type . '/');

							foreach($folder as $fileName)
							{
								if(!in_array($fileName,$bad))
								{
									$loaded[] = 'plugins/' . $folderName . '/'. $type . '/'.$fileName;
								}
							}

							$_SESSION['loaded_plugins_' . $type] = $loaded;
						}
					}
				}

			}
		}
	}
	
	public static function load($type)
	{
		if($GLOBALS['enablePlugins'] == true)
		{
			##########################
			if($type == 'pages')
			{	
				$count = 0;
				if (is_array($_SESSION['loaded_plugins_' . $type]) || is_object($_SESSION['loaded_plugins_' . $type]))
				{
					foreach($_SESSION['loaded_plugins_' . $type] as $filename)
					{
						$name = basename(substr($filename,0,-4));
						if($name == $_GET['p'])
						{
							include($filename);
							$count = 1;
						}
					}
				}

				if($count == 0)
				{
					include('pages/404.php');
				}		  
			}
			###########################
			elseif($type == 'javascript')
			{
				if (is_array($_SESSION['loaded_plugins_' . $type]) || is_object($_SESSION['loaded_plugins_' . $type]))
				{
					foreach($_SESSION['loaded_plugins_' . $type] as $filename)
					{
						echo '<script type="text/javascript" src="'.$filename.'"></script>';
					}
				}

			}
			###########################
			elseif($type == 'styles')
			{
				if (is_array($_SESSION['loaded_plugins_' . $type]) || is_object($_SESSION['loaded_plugins_' . $type]))
				{
					foreach($_SESSION['loaded_plugins_' . $type] as $filename)
					{
						echo '<link rel="stylesheet" href="'.$filename.'" />';
					}
				}
			}
			###########################
			elseif($type == 'classes')
			{
				if (is_array($_SESSION['loaded_plugins_' . $type]) || is_object($_SESSION['loaded_plugins_' . $type]))
				{
					foreach($_SESSION['loaded_plugins_' . $type] as $filename)
					{
						include($filename);
					}
				}
			}
		}
	}
}

$Plugins = new Plugins(); 