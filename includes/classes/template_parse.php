<?php

class Page
{

	public static $page   = null;
	public static $values = array();

	function __construct($template)
	{
		if (file_exists($template))
		{
			self::$page = join("", file($template));
		}
	}

	function parse($file)
	{
		ob_start();
		include $file;
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}

	function replace_tags($tags = array())
	{
		if (sizeof($tags) > 0)
		{
			if (is_array($tags) || is_object($tags))
			{
				foreach ($tags as $tag => $data)
				{
					$data       = (file_exists($data)) ? $this->parse($data) : $data;
					self::$page = preg_replace("({" . $tag . "})", $data, self::$page);
				}
			}
		}
	}

	function setVar($key, $array)
	{
		self::$values[$key] = $array;
	}

	function output()
	{
		echo self::$page;
	}

	function loadCustoms()
	{
		if ( DATA['website']['enable_plugins'] == TRUE )
		{
			if (isset($_SESSION['loaded_plugins_modules']))
			{
				if (is_array($_SESSION['loaded_plugins_modules']) || is_object($_SESSION['loaded_plugins_modules']))
				{
					foreach ($_SESSION['loaded_plugins_modules'] as $filename)
					{
						$name = basename(substr($filename, 0, -4));

						$this->replace_tags(array($name => $filename));
					}
				}
			}
		}
	}

}