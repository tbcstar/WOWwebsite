<?php
function exit_page() 
{
	die("<h1>服务器离线</h1>
		目前服务器正在维护中，请稍候。
		<br/>
		<br/>
		<br/>
		<br/>
		<i>TBCstar时光回溯 项目组</i>");
}

function RandomString() 
{
    $length = 10;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';    
    for ($p = 0; $p < $length; $p++) 
	{
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}

function convTime($time)
{
	if($time < 60)
	{
		$string = '秒';
	}
	elseif ($time > 60)
	{
	    $time = $time / 60;
		$string = '分';
	}
	elseif ($time > 60) 
	{									 
		$string = '小时';
		$time = $time / 60;
	}
    elseif ($time > 24) 
	{
		$string = '天';
		$time = $time / 24;
	}

    return ceil($time) . " " . $string;
} 