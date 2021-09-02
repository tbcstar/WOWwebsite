<?php

class Cache 
{

	public function buildCache($filename, $content) 
	{
		if ($GLOBALS['useCache'] == TRUE)
        {
            if (!$fh = fopen('cache/' . $filename . '.cache.php', 'w+'))
		    {
                buildError('<b>缓存错误。</b> 无法加载文件 (cache/' . $filename . '.cache.php)');
		    }
	    }

        fwrite($fh, $content);
        fclose($fh);
        unset($content, $filename);
    }
    else
    {
        $this->deleteCache($filename);
    }

	public function loadCache($filename) 
	{
        if ($GLOBALS['useCache'] == TRUE)
        {
            if (file_exists('cache/' . $filename . '.cache.php'))
		{
			include('cache/' . $filename . '.cache.php');
		else
		{
			buildError('<b>缓存错误。</b> 无法加载文件 (cache/' . $filename . '.cache.php)');
		}
        }
        else
        {
            $this->deleteCache($filename);
        }
	}
	
	public function deleteCache($filename) 
	{
        if (file_exists('cache/' . $filename . '.cache.php'))
        {
            $del = unlink('cache/' . $filename . '.cache.php');
            if (!$del)
		{
			buildError('<b>缓存错误。</b> 试图删除不存在的缓存文件 (cache/' . $filename . '.cache.php)');
		}
    }
}
	
	public function exists($filename) 
	{
        if (file_exists('cache/' . $filename . '.cache.php'))
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