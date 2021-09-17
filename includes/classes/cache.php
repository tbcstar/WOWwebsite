<?php


class Cache
{
	private $prefered_values = null;
	private $classes = null;

	public function __construct()
	{
		$this->prefered_values = array
		(
			"opcache.memory_consumption" 		=> 128,
			"opcache.interned_strings_buffer" 	=> 8,
			"opcache.max_accelerated_files" 	=> 4000,
			"opcache.revalidate_freq" 			=> 15,
			"opcache.enable_cli" 				=> 1
		);

		foreach ($this->prefered_values as $name => $value)
		{
			if ( ini_get($name) != $value )
			{
				ini_set($name, $value);
			}
		}

		$files = scandir("includes/misc/");
		array_splice($files, array_search(".", $files), 1);
		array_splice($files, array_search("..", $files), 1);
		array_splice($files, array_search("arial.ttf", $files), 1);
		array_splice($files, array_search("index.html", $files), 1);
		array_splice($files, array_search("connect.php", $files), 1);

		foreach ($files as $name)
		{
			if ( !opcache_compile_file("includes/misc/$name") )
			{
				die("error");
			}
		}

		$files = null;
		$files = scandir("pages/");
		array_splice($files, array_search(".", $files), 1);
		array_splice($files, array_search("..", $files), 1);
		array_splice($files, array_search(".htaccess", $files), 1);
		array_splice($files, array_search("index.html", $files), 1);

		foreach ($files as $name)
		{
			if ( !opcache_compile_file("pages/$name") )
			{
				die("error");
			}
		}

		$files = null;
		$files = scandir("modules/");
		array_splice($files, array_search(".", $files), 1);
		array_splice($files, array_search("..", $files), 1);
		array_splice($files, array_search("index.html", $files), 1);

		foreach ($files as $name)
		{
			if ( !opcache_compile_file("modules/$name") )
			{
				die("error");
			}
		}

		//opcache_compile_file("../../../../index.php");
	}

	public function isCached($class)
	{
		$class .= ".php";
		if ( array_search($class, $files) != false )
		{
			return opcache_is_script_cached($class);
		}
		else
		{
			return false;
		}
	}
}

$Cache = new Cache();