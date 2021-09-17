<?php

	function buildError($error, $num , $hidden_error = "")
	{
		if (DATA['use_debug'] == false)
		{
			log_error($error ." ". $hidden_error, $num);
		}
		else
		{
			errors($error, $num);
		}
	}

	function errors($error, $num)
	{
		log_error(strip_tags($error), $num);
		die("<center><b>网站错误</b><br/>
			网站脚本遇到错误并关闭。 <br/><br/>
			<b>错误消息: </b>". $error ."  <br/>
			<b>错误序号: </b>". $num ."
			<br/><br/><br/><i>TBCstar 团队
			<br/><font size='-2'>www.tbcstar.com</font></i></center>
			");
	}

	function log_error($error, $num)
	{
		error_log("*[" . date("d M Y H:i") . "] " . $error ."\n", 3, "error.log");
	}

	function loadCustomErrors()
	{
		set_error_handler("customError");
	}

	function customError($errno, $errstr)
	{
		if ($errno != 8 && $errno != 2048 && DATA['website']['use_debug'] == TRUE)
		{
			error_log("*[" . date("d M Y H:i") . "]<i>" . $errstr . "</i>\n", 3, "error.log");
		}
	} 