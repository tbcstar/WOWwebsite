<?php

function send_ra($command, $ra_user, $ra_pass, $server, $realm_port)
{
	$telnet = @fsockopen($server, $realm_port, $error, $error_string, 3);
	if($telnet)
	{
		fgets($telnet,1024);
		fputs($telnet, $ra_user."\n");

	    fputs($telnet, $ra_pass."\n");
	
		fputs($telnet, $command."\n");
		fclose($telnet);
	}
	else
	{
		die('连接问题...正在断开 | 错误: ' . $error_string);
	}
} 