<?php

class Cache 
{

	public static function buildCache($filename, $content)
	{
		if ( DATA['website']['use']['cache'] == TRUE ) 
		{
			if(!$fh = fopen('cache/'. $filename .'.cache.php', 'w+'))
			{
				buildError('<b>缓存错误。</b> 无法加载文件 (cache/'. $filename .'.cache.php)');
			}

			fwrite($fh,$content);
			fclose($fh); 
			unset($content,$filename);
		} 
		else
		{
			$this->deleteCache($filename);
		}
	}

	public static function loadCache($filename) 
	{
		if ( DATA['website']['use']['cache'] == TRUE )
		 {
			if (file_exists('cache/'.$filename.'.cache.php'))
			{
				include "cache/". $filename .".cache.php";
			}
			else
			{
				buildError('<b>缓存错误。</b> 无法加载该文件 (cache/'. $filename .'.cache.php)');
			}
		} 
		else
		{
			$this->deleteCache($filename);
		}
	}

	public static function deleteCache($filename) 
	{
		if (file_exists('cache/'. $filename .'.cache.php')) 
		{
			$del = unlink('cache/'. $filename .'.cache.php');
			if(!$del)
			{
				buildError('<b>缓存错误。</b> 试图删除不存在的缓存文件 (cache/'. $filename .'.cache.php)');
			}
		} 
	}

	public static function exists($filename) 
	{
		if (file_exists('cache/'.$filename.'.cache.php'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

}

$Cache = new Cache();  