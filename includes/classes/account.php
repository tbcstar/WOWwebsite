<?php
##############
##  Account functions goes here
##############

class Account 
{
	
	###############################
	####### 登录方法
	###############################
	public static function logIn($username, $password, $last_page, $remember) 
	{
		if (!isset($username) || !isset($password) || $username == "Username..." || $password == "Password...")
		{
			echo '<span class="red_text">请输入账号密码。</span>'; 
		}
		else 
		{
			global $Connect, $conn;
			$username = mysqli_real_escape_string($conn, trim(strtoupper($username)));
			$password = mysqli_real_escape_string($conn, trim(strtoupper($password)));
			
			$Connect->selectDB('logondb');
			$checkForAccount = mysqli_query($conn, "SELECT COUNT(id) FROM account WHERE username='". $username ."'");
			if (mysqli_data_seek($checkForAccount,0) == 0)
			{
				echo '
			<div id="ajax_notification" class="notification_red" style="z-index: 999999; display: block;">无效的用户名。</div>

			';	
			}
			else 
			{
				if($remember != 835727313)
					$password = sha1("".$username.":".$password.""); 
					
				$result = mysqli_query($conn, "SELECT id FROM account WHERE username='". $username ."' AND sha_pass_hash='". $password ."'");
				if (mysqli_num_rows($result)==0) 
					echo '
				<div id="ajax_notification" class="notification_red" style="z-index: 999999; display: block;">错误的密码。</div>

			';
				else 
				{
					if($remember=='on')
					{ 
						setcookie("cw_rememberMe", $username .' * '. $password, time()+30758400);
						//Set "remember me" cookie. Expires in 1 year.
					}

					$id = mysqli_fetch_assoc($result); 
					$id = $id['id'];
					
					self::GMLogin($username);
					$_SESSION['cw_user'] = ucfirst(strtolower($username));
					$_SESSION['cw_user_id'] = $id;
					
					$Connect->selectDB('webdb');
					$count = mysqli_query($conn, "SELECT COUNT(*) FROM account_data WHERE id='". $id ."'");
					if(mysqli_data_seek($count,0)==0)
						mysqli_query($conn, "INSERT INTO account_data VALUES('".$id."','0','0')");
					
					if(!empty($last_page))
					{
					   header("Location: ".$last_page);
					}
					else
					{
					   header("Location: index.php"); 
					}
				}
			}
			
		}
		
	}
	
	public static function loadUserData() 
	{
		//未使用的函数
		$user_info = array();
		global $Connect, $conn;
		$Connect->selectDB('logondb');

		$account_info = mysqli_query($conn, "SELECT id, username, email, joindate, locked, last_ip, expansion FROM account 
		WHERE username='". $_SESSION['cw_user'] ."'");
		while($row = mysqli_fetch_array($account_info)) 
		{
			$user_info[] = $row;
		}
		
	    return $user_info;
	}
	
	###############################
	####### 注销方法
	###############################
	public static function logOut($last_page) 
	{
		session_destroy();
		setcookie('cw_rememberMe', '', time()-30758400);
		if (empty($last_page)) 
		{
			header('Location: ?p=home"');
			exit();
		}
		header('Location: '.$last_page);
	}
	
	
	###############################
	####### 注册方法
	###############################
	public function register($username, $email, $password, $repeat_password, $captcha, $raf) 
	{
		$errors = array();
		
		if (empty($username))
		{
			$errors[] = '输入用户名。';
		}
			
		if (empty($email))
		{
			$errors[] = '输入电子邮件地址。';
		}
			
		if (empty($password))
		{
			$errors[] = '输入密码。';
		}
			
		if (empty($repeat_password))
		{
			$errors[] = '再次输入密码。';
		}
			
		if($username == $password)
		{
			$errors[] = '您的密码不能是您的用户名!';
		}
			
		else 
		{
			session_start();
			/*if($GLOBALS['registration']['captcha'] == TRUE) 
			{ 
				if($captcha!=$_SESSION['captcha_numero'])
				{ 
					$errors[] = '验证码不正确!';
				}
			}*/
			
			if (strlen($username) > $GLOBALS['registration']['userMaxLength'] || strlen($username) < $GLOBALS['registration']['userMinLength'])
			{
				$errors[] = '用户名必须介于'.$GLOBALS['registration']['userMinLength'].' 和 '.$GLOBALS['registration']['userMaxLength'].' letters.';
			} 

			if (strlen($password) > $GLOBALS['registration']['passMaxLength'] || strlen($password) < $GLOBALS['registration']['passMinLength'])
			{
				$errors[] = '密码必须介于'.$GLOBALS['registration']['passMinLength'].' 和 '.$GLOBALS['registration']['passMaxLength'].' letters.';
			}
				
			if ($GLOBALS['registration']['validateEmail'] == true)
			{
			    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
			    {
				       $errors[] = '输入一个有效的电子邮件地址。';
			    }
			}
			
		}
		
		global $Connect, $conn;
		$username_clean 	= mysqli_real_escape_string($conn, trim($username));
		$password_clean 	= mysqli_real_escape_string($conn, trim($password));
		$username 			= mysqli_real_escape_string($conn, trim(strtoupper(strip_tags($username))));
		$email 				= mysqli_real_escape_string($conn, trim(strip_tags($email)));
		$password 			= mysqli_real_escape_string($conn, trim(strtoupper(strip_tags($password))));
		$repeat_password 	= trim(strtoupper($repeat_password));
		$raf 				= (int)$raf;
		
		
		$Connect->selectDB('logondb');
		
		//检查现有用户
		$result = mysqli_query($conn, "SELECT COUNT(id) FROM account WHERE username='". $username ."';");

		if (mysqli_data_seek($result, 0) > 1)
		{
			$errors[] = '用户名已经存在!';
		}
		
		if ($password != $repeat_password)
		{
			$errors[] = '密码不匹配!';
		}
		
		if (!empty($errors)) 
		{
			//发现错误。
			echo "<div class='news' style='padding: 5px;'><h4>出现以下错误:</h4>";
				if (is_array($errors) || is_object($errors))
				{
					foreach($errors as $error) 
					{
						echo  "<strong>*", $error ,"</strong><br/>";
					}
				}

			echo "</div>";
			exit();
		} 
		else 
		{
			$password = sha1("".$username.":".$password."");
		
			mysqli_query($conn, "INSERT INTO account (username,email,sha_pass_hash,joindate,expansion,recruiter) 
			VALUES('". $username ."','". $email ."','". $password ."','". date("Y-m-d H:i:s") ."','". $GLOBALS['core_expansion'] ."','". $raf ."') "); 
			
			$getID 	= mysqli_query($conn, "SELECT id FROM account WHERE username='". $username ."'");
			$row 	= mysqli_fetch_assoc($getID);

			$Connect->selectDB('webdb');
			mysqli_query($conn, "INSERT INTO account_data VALUES('".$row['id']."','','','')"); 

			$result = mysqli_query($conn, "SELECT id FROM account WHERE username='". $username_clean ."'");	 
			$id 	= mysqli_fetch_assoc($result); 
			$id 	= $id['id'];

			self::GMLogin($username_clean);

			$_SESSION['cw_user'] 	= ucfirst(strtolower($username_clean));
			$_SESSION['cw_user_id'] = $id;
			
			self::forumRegister($username_clean,$password_clean,$email);
		}

	}
	
	
	public static function forumRegister($username, $password, $email) 
	{
	date_default_timezone_set($GLOBALS['timezone']);

    global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
	if($GLOBALS['forum']['type'] == 'phpbb' && $GLOBALS['forum']['autoAccountCreate'] == TRUE) 
	{
	////////PHPBB集成//////////////
			ini_set('display_errors',1);
			define('IN_PHPBB', true);
			define('ROOT_PATH', '../..'. $GLOBALS['forum']['forum_path']);

			$phpEx = "php";
			$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH;

			if(file_exists($phpbb_root_path . 'common.' . $phpEx) && file_exists($phpbb_root_path . 'includes/functions_user.' . $phpEx)) 
			{
				include($phpbb_root_path .'common.'. $phpEx);
				
				include($phpbb_root_path .'includes/functions_user.'. $phpEx);
				
				$arrTime = getdate();
				$unixTime = strtotime($arrTime['year']."-".$arrTime['mon'].'-'.$arrTime['mday']." ".$arrTime['hours'].":".
													$arrTime['minutes'].":".$arrTime['seconds']);

				$user_row = array(
					'username'              => $username,
					'user_password'         => phpbb_hash($password),
					'user_email'            => $email,
					'group_id'              => (int) 2,
					'user_timezone'         => (float) 0,
					'user_dst'              => "0",
					'user_lang'             => "en",
					'user_type'             => 0,
					'user_actkey'           => "",
					'user_ip'               => $_SERVER['REMOTE_HOST'],
					'user_regdate'          => $unixTime,
					'user_inactive_reason'  => 0,
					'user_inactive_time'    => 0
				);

				// All the information has been compiled, add the user
				// tables affected: users table, profile_fields_data table, groups table, and config table.
				$user_id = user_add($user_row);
			}
  		}
	}
	
	###############################
	####### 检查用户是否登录方法。
	###############################
	public static function isLoggedIn() 
	{
		if (isset($_SESSION['cw_user']))
		{
			header("Location: ?p=account");
		}
	}
	
	
	
	###############################
	####### 检查用户是否未登录方法。
	###############################
	public static function isNotLoggedIn() 
	{
		if (!isset($_SESSION['cw_user']))
		{
			header("Location: ?p=login&r=". $_SERVER['REQUEST_URI']);
		}
	}
	
	public static function isNotGmLoggedIn() 
	{
		if (!isset($_SESSION['cw_gmlevel']))
		{
			header("Location: ?p=home");
		}
	}
	
	
	###############################
	####### 返回禁止状态方法。
	###############################
	public static function checkBanStatus($user) 
	{
		global $Connect, $conn;
		$Connect->selectDB('logondb');
		$acct_id = $this->getAccountID($user);
		
		$result = mysqli_query($conn, "SELECT bandate,unbandate,banreason FROM account_banned WHERE id='". $acct_id ."' AND active=1");
		if (mysqli_num_rows($result) > 0) 
		{
			$row = mysqli_fetch_assoc($result);
			if($row['bandate'] > $row['unbandate'])
			{
				$duration = 'Infinite';
			}
			else 
			{
				$duration = $row['unbandate'] - $row['bandate'];
				$duration = ($duration / 60) / 60;
				$duration = $duration.' hours';  
			}
				echo '<span class="yellow_text">禁用<br/>
				Reason: '. $row['banreason'] .'<br/>
				Time left: '. $duration. '</span>';
		} 
		else
		{
			echo '<b class="green_text">启用</b>';
		}
	}
	
	
	###############################
	####### 返回帐户ID方法。
	###############################
	public static function getAccountID($user) 
	{
		global $Connect, $conn;
		$user = mysqli_real_escape_string($conn, $user);
		$Connect->selectDB('logondb');
		$result = mysqli_query($conn, "SELECT id FROM account WHERE username='".$user."'");
		$row 	= mysqli_fetch_assoc($result);
		return $row['id'];
	}
	
	public static function getAccountName($id) 
	{
		global $Connect, $conn;
		$id = (int)$id;
		$Connect->selectDB('logondb');
		$result = mysqli_query($conn, "SELECT username FROM account WHERE id='".$id."'");
		$row 	= mysqli_fetch_assoc($result);
		return $row['username'];
	}
	
	
	###############################
	####### "Remember me" method. Loads on page startup.
	###############################
	public function getRemember() 
	{
		if (isset($_COOKIE['cw_rememberMe']) && !isset($_SESSION['cw_user'])) 
		{
			$account_data = explode("*", $_COOKIE['cw_rememberMe']);
			self::logIn($account_data[0],$account_data[1],$_SERVER['REQUEST_URI'],835727313);
		}	
	}
	
	
	###############################
	####### Return account Vote Points method.
	###############################
	public static function loadVP($account_name) 
	{
		global $Connect, $conn;
		$acct_id = $this->getAccountID($account_name);
		$Connect->selectDB('webdb');
		$result = mysqli_query($conn, "SELECT vp FROM account_data WHERE id=".$acct_id);
		if (mysqli_num_rows($result) == 0)
		{
			return 0;
		}
		else 
		{
			$row = mysqli_fetch_assoc($result);
			return $row['vp'];
		}
	}
	
	
	public static function loadDP($account_name) 
	{
		global $Connect, $conn;
	    $acct_id = $this->getAccountID($account_name);
		$Connect->selectDB('webdb');
		$result = mysqli_query($conn, "SELECT dp FROM account_data WHERE id=". $acct_id);
		if (mysqli_num_rows($result) == 0)
		{
			return 0;
		}
		else 
		{
			$row = mysqli_fetch_assoc($result);
			return $row['dp'];
		}
	}
	
	
	
	###############################
	####### 返回电子邮件的方法。
	###############################
	public static function getEmail($account_name) 
	{
		global $Connect, $conn;
		$account_name = mysqli_real_escape_string($conn, $account_name);
		$Connect->selectDB('logondb');
		$result = mysqli_query($conn, "SELECT email FROM account WHERE username='". $account_name ."'");
		$row 	= mysqli_fetch_assoc($result);
		return $row['email'];
	}
	
	
	###############################
	####### 返回在线状态方法。
	###############################
	public static function getOnlineStatus($account_name) 
	{
		global $Connect, $conn;
		$account_name = mysqli_real_escape_string($conn, $account_name);
		$Connect->selectDB('logondb');
		$result 	= mysqli_query($conn, "SELECT COUNT(online) FROM account WHERE username='". $account_name ."' AND online=1");
		if (mysqli_data_seek($result,0) == 0)
		{
			return '<b class="red_text">离线</b>';
		}
		else
		{
			return '<b class="green_text">在线</b>';
		}
	}
	
	###############################
	####### Return Join date method.
	###############################
	public static function getJoindate($account_name) 
	{
		global $Connect, $conn;
		$account_name = mysqli_real_escape_string($conn, $account_name);
		$Connect->selectDB('logondb');
		$result = mysqli_query($conn, "SELECT joindate FROM account WHERE username='". $account_name ."'");
		$row 	= mysqli_fetch_assoc($result);
		return $row['joindate'];
	}
	
	###############################
	####### 如果用户是级别为2及以上的GM，则返回一个GM会话。
	###############################
	public static function GMLogin($account_name) 
	{
		global $Connect, $conn;
		$Connect->selectDB('logondb');
		$acct_id = $this->getAccountID($account_name);
		
		$result = mysqli_query($conn, "SELECT gmlevel FROM account_access WHERE gmlevel > 2 AND id=".$acct_id);
		if(mysqli_num_rows($result) > 0) 
		{
			$row = mysqli_fetch_assoc($result);
			$_SESSION['cw_gmlevel'] = $row['gmlevel'];
		}
		
	}
	
	public static function getCharactersForShop($account_name) 
	{
		global $Connect, $conn;

		$acct_id = $this->getAccountID($account_name);

		$Connect->selectDB('webdb');

		$getRealms = mysqli_query($conn, "SELECT id,name FROM realms");
		while($row = mysqli_fetch_assoc($getRealms)) 
		{
			$Connect->connectToRealmDB($row['id']);
			$result = mysqli_query($conn, "SELECT name,guid FROM characters WHERE account='".$acct_id."'");
			if(mysqli_num_rows($result) == 0 && !isset($x))
			{
				$x = true;
			    echo '<option value="">没有发现角色！</option>';
			}

			while($char = mysqli_fetch_assoc($result)) 
			{
				echo '<option value="'.$char['guid'].'*'.$row['id'].'">'.$char['name'].' - '.$row['name'].'</option>';
			}
		}
	}
	
	
	public static function changeEmail($email, $current_pass) 
	{
		$errors = array();

		if (empty($current_pass))
		{
			$errors[] = '请输入您的当前密码'; 
		}
		else 
		{
			if (empty($email))
			{
				$errors[] = '请输入电子邮件地址。';
			}

			global $Connect, $conn;

			$Connect->selectDB('logondb');

			$username = mysqli_real_escape_string($conn, trim(strtoupper($_SESSION['cw_user'])));
			$password = mysqli_real_escape_string($conn, trim(strtoupper($current_pass)));
			
			$password = sha1("". $username .":". $password ."");

			$result = mysqli_query($conn, "SELECT COUNT(id) FROM account WHERE username='". $username ."' AND sha_pass_hash='". $password ."'");
			if (mysqli_data_seek($result, 0) == 0)
			{
				$errors[] = '当前密码不正确。';
			}
			
			if ($GLOBALS['registration']['validateEmail'] == true) 
			{
			    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
			    {
			    	$errors[] = '请输入有效的电子邮件地址。';
			    }
				else
				{
					mysqli_query($conn, "UPDATE account SET email='".$email."' WHERE username='".$_SESSION['cw_user']."'");	
				}
			}
			
		}

		if(empty($errors))
		{
			echo '您的帐号更新成功。';
		}
		else 
		{
			echo '<div class="news" style="padding: 5px;">
			<h4 class="red_text">出现以下错误:</h4>';
			if (is_array($errors) || is_object($errors))
			{
				foreach($errors as $error) 
				{
					echo  '<strong class="yellow_text">*', $error ,'</strong><br/>';
				}
			}
			echo '</div>';
		}
	}

	//用于更改密码页面。
	public static function changePass($old, $new, $new_repeat) 
	{
		global $Connect, $conn;
            $_POST['current_password']    = mysqli_real_escape_string($conn, trim($old));
            $_POST['new_password']        = mysqli_real_escape_string($conn, trim($new));
            $_POST['new_password_repeat'] = mysqli_real_escape_string($conn, trim($new_repeat));
		
		//检查是否所有字段值都已输入
        if (empty($_POST['current_password']) || empty($_POST['new_password']) || empty($_POST['new_password_repeat']))
		{
			echo '<b class="red_text">请输入所有字段!</b>';
		}
	    else 
		{
			//检查新密码是否匹配?
            if ($_POST['new_password'] != $_POST['new_password_repeat'])
			{
				echo '<b class="red_text">新密码不匹配!</b>';
			}
			else 
			{
                if (strlen($_POST['new_password']) < $GLOBALS['registration']['passMinLength'] || strlen($_POST['new_password']) > $GLOBALS['registration']['passMaxLength'])
				{
                    echo "<b class='red_text'>您的密码必须是 ". $GLOBALS['registration']['passMinLength'] ." 和 ". $GLOBALS['registration']['passMaxLength'] ." 之间</b>";
				}
				else 
				{
					//让我们检查一下旧密码是否正确!
					$username 		= mysqli_real_escape_string($conn, strtoupper($_SESSION['cw_user']));

					$Connect->selectDB('logondb');

                    $getPass = mysqli_query($conn, "SELECT `sha_pass_hash` FROM `account` WHERE `username`='" . $username . "';");
					$row 			= mysqli_fetch_assoc($getPass);
					$thePass 		= strtoupper($row['sha_pass_hash']);

					$pass      = mysqli_real_escape_string($conn, strtoupper($_POST['current_password']));
					$pass_hash 		= strtoupper(sha1($username.':'.$pass));

                    $new_password      = mysqli_real_escape_string($conn, strtoupper($_POST['new_password']));
                    $new_password_hash = sha1($username . ':' . $new_password);

					if ($thePass != $pass_hash)
					{
						echo '<b class="red_text">旧密码不正确!</b>';
					}
					else 
					{
						//成功,更改密码
						echo "<b class='green_text'>您的密码已更改!</b>";
						mysqli_query($conn, "UPDATE account SET sha_pass_hash='" . $new_password_hash . "' WHERE username='" . $username . "';");
						mysqli_query($conn, "UPDATE account SET v='0' AND s='0' WHERE username='" . $username . "';");
					}
				}
			}
		}
	}
	
	public static function changePassword($account_name,$password) 
	{
			$username = mysqli_real_escape_string($conn, strtoupper($account_name));
			$pass = mysqli_real_escape_string($conn, strtoupper($password));
			$pass_hash = SHA1($username.':'.$pass);
			
			$Connect->selectDB('logondb');
			mysqli_query($conn, "UPDATE `account` SET `sha_pass_hash`='$pass_hash' WHERE `username`='".$username."'");
			mysqli_query($conn, "UPDATE `account` SET `v`='0' AND `s`='0' WHERE username='".$username."'");
			
			self::logThis("Changed password","passwordchange",NULL);
	}
	
	public static function forgotPW($account_name, $account_email) 
	{
		global $Connect, $conn; global $Website; global $Account;
		$account_name 	= mysqli_real_escape_string($conn, $account_name);
		$account_email 	= mysqli_real_escape_string($conn, $account_email);
		
		if (empty($account_name) || empty($account_email))
		{
			echo '<b class="red_text">请输入用户名和Email。</b>';
		}
		else 
		{
			$Connect->selectDB('logondb');
			$result = mysqli_query($conn, "SELECT COUNT('id') FROM account WHERE username='". $account_name ."' AND email='". $account_email ."'");
			
			if (mysqli_data_seek($result, 0) == 0)
			{
				echo '<b class="red_text">用户名或电子邮件不正确。</b>';
			}
			else 
			{
				//Success, lets send an email & add the forgotpw thingy.
				$code = RandomString();
				$Website->sendEmail($account_email, $GLOBALS['default_email'],'忘记密码',"
				你好。<br/><br/>
				要求为帐户重设密码 ". $account_name ." <br/>
				如果要重置密码，请单击下面的链接：<br/>
				<a href='". $GLOBALS['website_domain'] ."?p=forgotpw&code=". $code ."&account=". $this->getAccountID($account_name) ."'>
				". $GLOBALS['website_domain'] ."?p=forgotpw&code=". $code ."&account=". $this->getAccountID($account_name) ."</a>
				
				<br/><br/>
				
				如果您没有请求此消息，请忽略此消息。<br/><br/>
				来自TBCstar的问候。");

				$account_id = $this->getAccountID($account_name);
				$Connect->selectDB('webdb');
				
				mysqli_query($conn, "DELETE FROM password_reset WHERE account_id='".$account_id."'");
				mysqli_query($conn, "INSERT INTO password_reset VALUES ('','". $code ."','". $account_id ."')");
				echo "
				包含重置密码链接的电子邮件已发送到您指定的电子邮件地址。
				如果您在此之前已经提交了其他密码重置请求，则这些请求将不起作用。<br/>";
			}
		}

		function hasVP($account_name,$points) 
		{
			global $Connect, $conn;
			$points 	= (int)$points;
			$account_id = $this->getAccountID($account_name);
			$Connect->selectDB('webdb');
			$result 	= mysqli_query($conn, "SELECT COUNT('id') FROM account_data WHERE vp >= '". $points ."' AND id='". $account_id ."'");
			
			if (mysqli_data_seek($result, 0) == 0)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		
		function hasDP($account_name, $points) 
		{
			global $Connect, $conn;
			$points 	= (int)$points;
			$account_id = $this->getAccountID($account_name);
			$Connect->selectDB('webdb');
			$result 	= mysqli_query($conn, "SELECT COUNT('id') FROM account_data WHERE dp >= '". $points ."' AND id='". $account_id ."'");
			
			if (mysqli_data_seek($result, 0) == 0)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		
		function deductVP($account_id, $points) 
		{
			global $Connect, $conn;

			$points 	= (int)$points;
			$account_id = (int)$account_id;
			$Connect->selectDB('webdb');
            
			mysqli_query($conn, "UPDATE account_data SET vp=vp - ".$points." WHERE id='".$account_id."'");
		}
		
		function deductDP($account_id, $points) 
		{
			global $Connect, $conn;
			$points 	= (int)$points;
			$account_id = (int)$account_id;
			$Connect->selectDB('webdb');
            
			mysqli_query($conn, "UPDATE account_data SET dp=dp - ".$points." WHERE id='".$account_id."'");
		}
		
		function addDP($account_id, $points)
		{
			global $Connect, $conn;

			$account_id = (int)$account_id;
			$points 	= (int)$points;
			$Connect->selectDB('webdb');
			
			mysqli_query($conn, "UPDATE account_data SET dp=dp + ".$points." WHERE id='".$account_id."'");
		}
		
		function addVP($account_id, $points)
		{
			global $Connect, $conn;
			$account_id = (int)$account_id;
			$points 	= (int)$points;
			$Connect->selectDB('webdb');
			
			mysqli_query($conn, "UPDATE account_data SET dp=dp + ".$points." WHERE id='". $account_id ."'");
		}
		
		function getAccountIDFromCharId($char_id, $realm_id) 
		{
			global $Connect, $conn;
			$char_id = (int)$char_id;
			$realm_id = (int)$realm_id;
			$Connect->selectDB('webdb');
			$Connect->connectToRealmDB($realm_id);
			
			$result = mysqli_query($conn, "SELECT account FROM characters WHERE guid='". $char_id ."'");
			$row 	= mysqli_fetch_assoc($result);
			return $row['account'];
		}
		
		function isGM($account_name) 
		{
			global $conn;
	        $account_id = $this->getAccountID($account_name);
			$result = mysqli_query($conn, "SELECT COUNT(id) FROM account_access WHERE id='". $account_id ."' AND gmlevel >= 1");
			if (mysqli_data_seek($result,0) > 0)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		
		function logThis($desc, $service, $realmid)
		{
			global $Connect, $conn;
			$desc 		= mysqli_real_escape_string($conn, $desc);
			$realmid 	= (int)$realmid;
			$service 	= mysqli_real_escape_string($conn, $service);
			$account 	= (int)$_SESSION['cw_user_id'];
			
			$Connect->selectDB('webdb');
			mysqli_query($conn, "INSERT INTO user_log VALUES('','". $account ."','". $service ."','". time() ."','". $_SERVER['REMOTE_ADDR'] ."','". $realmid ."','". $desc ."')");
		}
	}
}

$Account = new Account(); 