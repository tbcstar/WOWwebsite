<?php

class Messages
{
	public function success($message, $strong = true)
	{
		if ( empty($message) )
		{
			return null;
		}
		else
		{
			$message = ucwords($message);
			echo "
			<div class=\"alert alert-success\">";
				if ( $strong == true )
				{
					echo "<strong>成功！ </strong>";
				}
				echo "$message</div>";
		}
	}

	public function info($message, $strong = true)
	{
		if ( empty($message) )
		{
			return null;
		}
		else
		{
			$message = ucwords($message);
			echo "<div class=\"alert alert-info\">";
				if ( $strong == true )
				{
					echo "<strong>信息！ </strong>";
				}
				echo "$message </div>";
		}
	}

	public function warning($message, $strong = true)
	{
		if ( empty($message) )
		{
			return null;
		}
		else
		{
			$message = ucwords($message);
			echo "
			<div class=\"alert alert-warning\">";
				if ( $strong == true )
				{
					echo "<strong>警告！ </strong>";
				}
				echo "$message</div>";
		}
	}

	public function error($message, $strong = true)
	{
		if ( empty($message) )
		{
			return null;
		}
		else
		{
			$message = ucwords($message);
			echo "
			<div class=\"alert alert-danger\">";
				if ( $strong == true )
				{
					echo "<strong>错误！ </strong>";
				}
				echo "$message</div>";
		}
	}

}

$Messages = new Messages();