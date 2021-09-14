<?php

require_once "random_compat-2.0.20/lib/random.php";
##############
##  Account functions goes here
##############

class Account 
{
	
	###############################
	####### 登录方法
	###############################
	public function logIn($username, $password, $last_page, $remember)
	{
		if (!isset($username) || !isset($password) || empty($username) || empty($password))
		{
			echo "<span class=\"red_text\">
			    请输入账号密码。
			    </span>";
		}
		else 
		{
            global $Connect;
            $conn       = $Connect->connectToDB();;
            $username   = $conn->escape_string(trim(strtoupper($username)));
            $password   = $conn->escape_string(trim(strtoupper($password)));

			$Connect->selectDB("logondb", $conn);

            $checkForAccount = $conn->query("SELECT COUNT(id) AS username FROM account WHERE username='". $username ."';");

            if ($checkForAccount->fetch_assoc()['username'] == 0)
			{
				echo "<span class=\"red_text\">
			       无效的用户名。
			       </span>";
			}
			else 
			{
				if ($remember != 835727313)
				{
					$data = $conn->query("SELECT salt, verifier FROM account WHERE username = '".$username."'");
					$data = $data->fetch_assoc();
					$salt = $data['salt'];
					$verifier = $data['verifier'];
				}

				if (!account::verifySRP6($username, $password, $salt, $verifier))
					echo '<span class="red_text">密码错误。</span>';
                exit;
                }

                if ($remember == "on")
				{
                    # Set "remember me" cookie. Expires in 1 week
                    setcookie("cw_rememberMe", $username .' * '. $password, time() + ( (60*60)*24)*7);
                }

				$id = $result->fetch_assoc()['id'];
					
				$this->GMLogin($username);
				$_SESSION['cw_user'] = ucfirst(strtolower($username));
				$_SESSION['cw_user_id'] = $id;
					
				$Connect->selectDB("webdb", $conn);

                $count = $conn->query("SELECT COUNT(*) FROM account_data WHERE id=". $id .";");
                if ($count->data_seek(0) == 0)
                {
                    $conn->query("INSERT INTO account_data (id) VALUES(". $id .");");
                }
					
                if (!empty($last_page))
                {
                    header("Location: ". $last_page);
                }
                else
                {
                    header("Location: index.php");
				}
			}
			
		}
		
	}
	
	public function loadUserData()
	{
		//未使用的函数
		$user_info = array();
		global $Connect, $conn;
		$Connect->selectDB("logondb", $conn);

        $account_info = $conn->query("SELECT id, username, email, joindate, locked, last_ip, expansion FROM account WHERE username='". $_SESSION['cw_user'] ."';");
            while ($row = $account_info->fetch_array())
		{
			$user_info[] = $row;
		}
		
	    return $user_info;
	}
	
	###############################
	####### 注销方法
	###############################
	public function logOut($last_page)
	{
		session_destroy();
		setcookie('cw_rememberMe', '', time()-30758400);

		if (empty($last_page)) 
		{
			header('Location: ?page=home"');
			exit();
		}
		header('Location: '.$last_page);
	}


	###############################
	####### SRP6 methods
	###############################
	public function calculateSRP6Verifier($username, $password, $salt)
    {
        // algorithm constants
        $g = gmp_init(7);
        $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);

        // calculate first hash
        $h1 = sha1(strtoupper($username . ':' . $password), TRUE);

        // calculate second hash
        $h2 = sha1($salt.$h1, TRUE);

        // convert to integer (little-endian)
        $h2 = gmp_import($h2, 1, GMP_LSW_FIRST);

        // g^h2 mod N
        $verifier = gmp_powm($g, $h2, $N);

        // convert back to a byte array (little-endian)
        $verifier = gmp_export($verifier, 1, GMP_LSW_FIRST);

        // pad to 32 bytes, remember that zeros go on the end in little-endian!
        $verifier = str_pad($verifier, 32, chr(0), STR_PAD_RIGHT);

        // done!
        return $verifier;
    }

    // Returns SRP6 parameters to register this username/password combination with
    public function getRegistrationData($username, $password)
    {
        // generate a random salt
        $salt = random_bytes(32);

        // calculate verifier using this salt
        $verifier = account::calculateSRP6Verifier($username, $password, $salt);

        // done - this is what you put in the account table!
        return array($salt, $verifier);
    }

    public function verifySRP6($user, $pass, $salt, $verifier)
    {
        $g = gmp_init(7);
        $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
        $x = gmp_import(
            sha1($salt . sha1(strtoupper($user . ':' . $pass), TRUE), TRUE),
            1,
            GMP_LSW_FIRST
        );
        $v = gmp_powm($g, $x, $N);
        return ($verifier === str_pad(gmp_export($v, 1, GMP_LSW_FIRST), 32, chr(0), STR_PAD_RIGHT));
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
				
			if ($GLOBALS['registration']['validateEmail'] == TRUE)
			{
			    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
			    {
				       $errors[] = '输入一个有效的电子邮件地址。';
			    }
			}
			
		}
		
        global $Connect;
        $conn            = $Connect->connectToDB();
        $username_clean  = $conn->escape_string(trim($username));
        $password_clean  = $conn->escape_string(trim($password));
        $username        = $conn->escape_string(trim(strtoupper(strip_tags($username))));
        $email           = $conn->escape_string(trim(strip_tags($email)));
        $password        = $conn->escape_string(trim(strtoupper(strip_tags($password))));
        $repeat_password = $conn->escape_string(trim(strtoupper($repeat_password)));
        $raf             = $conn->escape_string($raf);
		
		
		$Connect->selectDB("logondb", $conn);
		
		//检查现有用户
		$result = $conn->query("SELECT COUNT(id) FROM account WHERE username='". $username ."';");

		if ($result->data_seek(0) > 1)
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
						echo "<strong>*", $error, "</strong><br/>";
					}
				}

			echo "</div>";
			exit();
		} 
		else 
		{
            $data = account::getRegistrationData($username, $password);
			$salt = $data[0];
			$verifier = $data[1];

			$conn->query("INSERT INTO account (username, salt, verifier, email, joindate, expansion, recruiter)
			VALUES('".$username."', '".$salt."', '".$verifier."', '".$email."', '".date("Y-m-d H:i:s")."', '".$GLOBALS['core_expansion']."', '".$raf."') ");

            $getID = $conn->query("SELECT id FROM account WHERE username='". $username ."';");
            $row   = $getID->fetch_assoc();

			$Connect->selectDB("webdb", $conn);
            $conn->query("INSERT INTO account_data (id) VALUES(". $row['id'] .");");

            $Connect->selectDB("logondb", $conn);
            $result = $conn->query( "SELECT id FROM account WHERE username='". $username_clean ."';");
            $id     = $result->fetch_assoc();
			$id 	= $id['id'];

			$this->GMLogin($username_clean);

			$_SESSION['cw_user'] 	= ucfirst(strtolower($username_clean));
			$_SESSION['cw_user_id'] = $id;
			
			$this->forumRegister($username_clean,$password_clean,$email);
		}

	}
	
	
    // Unused
    public function forumRegister($username, $password, $email)
	{
	date_default_timezone_set($GLOBALS['timezone']);

    global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
	if($GLOBALS['forum']['type'] == 'phpbb' && $GLOBALS['forum']['autoAccountCreate'] == TRUE) 
	{
	////////PHPBB集成//////////////
			ini_set('display_errors',1);
			define('IN_PHPBB', TRUE);
			define('ROOT_PATH', '../..'. $GLOBALS['forum']['forum_path']);

			$phpEx = "php";
			$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH;

            if (file_exists($phpbb_root_path . 'common.' . $phpEx) && file_exists($phpbb_root_path . 'includes/functions_user.' . $phpEx))
            {
				include $phpbb_root_path ."common.". $phpEx;
				
				include $phpbb_root_path ."includes/functions_user.". $phpEx;
				
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
	public function isLoggedIn() 
	{
		if (isset($_SESSION['cw_user']))
		{
			header("Location: ?page=account");
		}
	}
	
	
	
	###############################
	####### 检查用户是否未登录方法。
	###############################
	public function isNotLoggedIn() 
	{
		if (!isset($_SESSION['cw_user']))
		{
			header("Location: ?page=login&r=" . $_SERVER['REQUEST_URI']);
		}
	}
	
	public function isNotGmLoggedIn() 
	{
		if (!isset($_SESSION['cw_gmlevel']))
		{
			header("Location: ?page=home");
		}
	}
	
	
	###############################
	####### 返回禁止状态方法。
	###############################
	public function checkBanStatus($user) 
	{
        global $Connect;
        $conn = $Connect->connectToDB();
        $Connect->selectDB("logondb", $conn);

		$acct_id = $this->getAccountID($user);
		
        $result = $conn->query("SELECT bandate, unbandate, banreason FROM account_banned WHERE id=". $acct_id ." AND active=1;");
            if ($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
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
	public function getAccountID($user) 
	{
		global $Connect;
        $conn   = $Connect->connectToDB();

        $user   = $conn->escape_string($user);

		$Connect->selectDB("logondb", $conn);

        $result = $conn->query("SELECT id FROM account WHERE username='". $user ."';");
        $row    = $result->fetch_assoc();

		return $row['id'];
	}
	
	public function getAccountName($id) 
	{
        global $Connect;
        $conn = $Connect->connectToDB();

        $id = $conn->escape_string($id);

		$Connect->selectDB("logondb", $conn);

        $result = $conn->query("SELECT username FROM account WHERE id=". $id .";");
        $row    = $result->fetch_assoc();

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

			$this->logIn($account_data[0],$account_data[1],$_SERVER['REQUEST_URI'],835727313);
		}	
	}
	
	
	###############################
	####### Return account Vote Points method.
	###############################
	public function loadVP($account_name) 
	{
		global $Connect;
        $conn = $Connect->connectToDB();

        $accountName    = $conn->escape_string($account_name);
        $acct_id        = $this->getAccountID($accountName);

		$Connect->selectDB("webdb", $conn);

        $result = $conn->query("SELECT vp FROM account_data WHERE id=". $acct_id .";");
        if ($result->num_rows == 0)
		{
			return 0;
		}
		else 
		{
            $row = $result->fetch_assoc();

            return $row['vp'];
		}
	}
	
	
	public function loadDP($account_name) 
	{
		global $Connect;
        $conn = $Connect->connectToDB();

        $accountName    = $conn->escape_string($account_name);
        $acct_id        = $this->getAccountID($accountName);

		$Connect->selectDB("webdb", $conn);

        $result  = $conn->query("SELECT dp FROM account_data WHERE id=". $acct_id .";");
        if ($result->num_rows == 0)
		{
			return 0;
		}
		else 
		{
			$row = $result->fetch_assoc();

			return $row['dp'];
		}
	}
	
	
	
	###############################
	####### 返回电子邮件的方法。
	###############################
	public function getEmail($account_name) 
	{
		global $Connect;
        $conn = $Connect->connectToDB();

        $accountName = $conn->escape_string($account_name);

		$Connect->selectDB("logondb", $conn);

        $result       = $conn->query("SELECT email FROM account WHERE username='". $accountName ."';");
        $row          = $result->fetch_assoc();
		return $row['email'];
	}
	
	
	###############################
	####### 返回在线状态方法。
	###############################
	public function getOnlineStatus($account_name) 
	{
		global $Connect;
        $conn = $Connect->connectToDB();

        $accountName = $conn->escape_string($account_name);

		$Connect->selectDB("logondb", $conn);

        $result       = $conn->query("SELECT COUNT(online) FROM account WHERE username='" . $accountName . "' AND online=1;");
        if ($result->data_seek(0) == 0)
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
	public function getJoindate($account_name) 
	{
		global $Connect;
        $conn = $Connect->connectToDB();

        $accountName = $conn->escape_string($account_name);

		$Connect->selectDB("logondb", $conn);

        $result       = $conn->query("SELECT joindate FROM account WHERE username='". $account_name ."';");
        $row          = $result->fetch_assoc();

		return $row['joindate'];
	}
	
	###############################
	####### 如果用户是级别为2及以上的GM，则返回一个GM会话。
	###############################
	public function GMLogin($account_name) 
	{
		global $Connect;
        $conn = $Connect->connectToDB();
		$Connect->selectDB("logondb", $conn);

		$accountName = $conn->escape_string($account_name);

        $acct_id = $this->getAccountID($accountName);
		
		$result = $conn->query("SELECT gmlevel FROM account_access WHERE gmlevel > 2 AND id=". $acct_id .";");
        if ($result->num_rows > 0)
		{
			$row                    = $result->fetch_assoc();
			$_SESSION['cw_gmlevel'] = $row['gmlevel'];
		}
		
	}
	
	public function getCharactersForShop($account_name) 
	{
		global $Connect;
        $conn = $Connect->connectToDB();

        $accountName = $conn->escape_string($account_name);

		$acct_id = $this->getAccountID($accountName);

		$Connect->selectDB("webdb", $conn);

        $getRealms = $conn->query("SELECT id, name FROM realms;");
        while ($row = $getRealms->fetch_assoc())
		{
			$Connect->connectToRealmDB($row['id']);

            $result = $conn->query("SELECT name, guid FROM characters WHERE account=". $acct_id .";");
            if ($result->num_rows == 0 && !isset($x))
			{
				$x = TRUE;
			    echo '<option value="">没有发现角色！</option>';
			}

			while ($char = $result->fetch_assoc())
			{
				echo '<option value="'.$char['guid'].'*'.$row['id'].'">'.$char['name'].' - '.$row['name'].'</option>';
			}
		}
	}
	
	
	public function changeEmail($email, $current_pass) 
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

			global $Connect;
            $conn = $Connect->connectToDB();

			$Connect->selectDB("logondb", $conn);

			$username = $conn->escape_string(trim(strtoupper($_SESSION['cw_user'])));
            $password = $conn->escape_string(trim(strtoupper($current_pass)));
			
			$data = $conn->query("SELECT salt, verifier FROM account WHERE username = '".$username."'");
			$data = $data->fetch_assoc();
			$salt = $data['salt'];
			$verifier = $data['verifier'];

			if (!account::verifySRP6($username, $password, $salt, $verifier))
			{
				$errors[] = '当前密码不正确。';
			}
			
			if ($GLOBALS['registration']['validateEmail'] == TRUE) 
			{
			    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
			    {
			    	$errors[] = '请输入有效的电子邮件地址。';
			    }
				else
				{
					$conn->query("UPDATE account SET email = '". $email ."' WHERE username = '".$username."'");
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
	public function changePass($old, $new, $new_repeat) 
	{
		global $Connect;
        $conn = $Connect->connectToDB();

        $old        = $conn->escape_string($old);
        $new        = $conn->escape_string($new);
        $new_repeat = $conn->escape_string($new_repeat);
		
		//检查是否所有字段值都已输入
        if (empty($_POST['current_password']) || empty($_POST['new_password']) || empty($_POST['new_password_repeat']))
		{
			echo '<b class="red_text">请输入所有字段!</b>';
		}
	    else 
		{
			//检查新密码是否匹配?
            if ($new != $new_repeat)
			{
				echo '<b class="red_text">新密码不匹配!</b>';
			}
			else 
			{
                if (strlen($new) < $GLOBALS['registration']['passMinLength'] ||
			        strlen($new) > $GLOBALS['registration']['passMaxLength'])
				{
                    echo '<b class="red_text">
                        您的密码必须介于 '.$GLOBALS['registration']['passMinLength'].' 
                        和 '.$GLOBALS['registration']['passMaxLength'].' 字母和/或数字。
                    </b>';
				}
				else 
				{
					//让我们检查一下旧密码是否正确!
					$username = $conn->escape_string(strtoupper(trim($_SESSION['cw_user'])));

					$Connect->selectDB("logondb", $conn);

				    $data = $conn->query("SELECT salt, verifier FROM account WHERE username = '".$username."'");
				    $data = $data->fetch_assoc();
				    $salt = $data['salt'];
				    $verifier = $data['verifier'];

					if (!account::verifySRP6($username, $old, $salt, $verifier))
					{
						echo "<b class='red_text'>
						    旧密码不正确!
					    </b>";
					}
					else 
					{
						//成功,更改密码
						$data2 = account::getRegistrationData($username, $new);
					    $salt2 = $data2[0];
					    $verifier2 = $data2[1];
					    $conn->query("UPDATE account SET salt = '".$salt2."', verifier = '".$verifier2."' WHERE username = '".$username."'");
						echo "<b class='green_text'>
						    您的密码已更改!
					    </b>";
						if (isset($_COOKIE['cw_rememberMe']))
						setcookie("cw_rememberMe", $username.' * '.$new, time()+30758400);
					}
				}
			}
		}
	}
	
	public function changePassword($account_name,$password) 
	{
	    global $Connect;
        $conn = $Connect->connectToDB();

        $username  = $conn->escape_string(strtoupper($account_name));
        $pass      = $conn->escape_string(strtoupper($password));

		$pass_hash = SHA1($username.':'.$pass);
			
		$Connect->selectDB("logondb", $conn);

        $conn->query("UPDATE `account` SET `sha_pass_hash`='". $pass_hash ."' WHERE `username`='". $username ."';");
        $conn->query("UPDATE `account` SET `v`=0 AND `s`=0 WHERE username='". $username ."';");
	
		$this->logThis("Changed password","passwordchange",NULL);
	}
	
	public function forgotPW($account_name, $account_email) 
	{
		global $Website, $Account, $Connect;
        $conn = $Connect->connectToDB();

        $accountName  = $conn->escape_string($account_name);
        $accountEmail = $conn->escape_string($account_email);
		
		if (empty($accountName) || empty($accountEmail))
		{
			echo '<b class="red_text">请输入用户名和Email。</b>';
		}
		else 
		{
			$Connect->selectDB("logondb", $conn);

			$result = $conn->query("SELECT COUNT('id') FROM account WHERE username='". $accountName ."' AND email='". $accountEmail ."';");

            if ($result->data_seek(0) == 0)
			{
				echo '<b class="red_text">
				    用户名或电子邮件不正确。</b>';
			}
			else 
			{
				//Success, lets send an email & add the forgotpw thingy.
				$code = RandomString();

				$Website->sendEmail($accountEmail, $GLOBALS['default_email'], '忘记密码', "
				你好。<br/><br/>
				要求为帐户重设密码 ". $accountName ." <br/>
				如果要重置密码，请单击下面的链接：<br/>
				<a href='". $GLOBALS['website_domain'] ."?page=forgotpw&code=". $code ."&account=". $this->getAccountID($accountName) ."'>
				". $GLOBALS['website_domain'] ."?page=forgotpw&code=". $code ."&account=". $this->getAccountID($accountName) ."</a>
				
				<br/><br/>
				
				如果您没有请求此消息，请忽略此消息。<br/><br/>
				来自TBCstar的问候。");

				$account_id = $this->getAccountID($accountName);

				$Connect->selectDB("webdb", $conn);
				
				$conn->query("DELETE FROM password_reset WHERE account_id=". $account_id .";");
                $conn->query("INSERT INTO password_reset (code, account_id) VALUES ('". $code ."', ". $account_id .");");

				echo "
				包含重置密码链接的电子邮件已发送到您指定的电子邮件地址。
				如果您在此之前已经提交了其他密码重置请求，则这些请求将不起作用。<br/>";
			}
		}

		function hasVP($account_name,$points) 
		{
			global $Connect;
            $conn = $Connect->connectToDB();

            $points         = $conn->escape_string($points);
            $accountName    = $conn->escape_string($account_name);

            $account_id = $this->getAccountID($accountName);

			$Connect->selectDB("webdb", $conn);
            $result = $conn->query("SELECT COUNT(id) FROM account_data WHERE vp >= ". $points ." AND id=". $account_id .";");

            if ($result->data_seek(0) == 0)
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
			global $Connect;
            $conn = $Connect->connectToDB();

            $points         = $conn->escape_string($points);
            $accountName    = $conn->escape_string($account_name);

            $account_id = $this->getAccountID($accountName);
			
			$Connect->selectDB("webdb", $conn);

            $result = $conn->query("SELECT COUNT('id') FROM account_data WHERE dp >=". $points ." AND id=". $account_id .";");
			
			if ($result->data_seek(0) == 0)
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
			global $Connect;
            $conn = $Connect->connectToDB();

			$points     = $conn->escape_string($points);
            $accountId  = $conn->escape_string($account_id);
			
			$Connect->selectDB("webdb", $conn);
            
			$conn->query("UPDATE account_data SET vp=vp - ". $points ." WHERE id=". $accountId .";");
		}
		
		function deductDP($account_id, $points) 
		{
			global $Connect;
            $conn = $Connect->connectToDB();

            $points     = $conn->escape_string($points);
            $accountId  = $conn->escape_string($account_id);

			$Connect->selectDB("webdb", $conn);
            
			$conn->query("UPDATE account_data SET dp=dp - ". $points ." WHERE id=". $accountId .";");
		}
		
		function addDP($account_id, $points)
		{
			global $Connect;
            $conn = $Connect->connectToDB();

			$accountId  = $conn->escape_string($account_id);
            $points     = $conn->escape_string($points);

            $Connect->selectDB("webdb", $conn);
			
			$conn->query("UPDATE account_data SET dp=dp + ". $points ." WHERE id=". $accountId .";");
		}
		
		function addVP($account_id, $points)
		{
			global $Connect;
            $conn = $Connect->connectToDB();

            $accountId  = $conn->escape_string($account_id);
            $points     = $conn->escape_string($points);
			$Connect->selectDB("webdb", $conn);
			
			$conn->query("UPDATE account_data SET dp=dp + ". $points ." WHERE id=". $accountId .";");
		}
		
		function getAccountIDFromCharId($char_id, $realm_id) 
		{
            global $Connect;
            $conn = $Connect->connectToDB();

            $charId  = $conn->escape_string($char_id);
            $realmId = $conn->escape_string($realm_id);

            $Connect->selectDB("webdb", $conn);
			$Connect->connectToRealmDB($realmId);
			
			$result = $conn->query("SELECT account FROM characters WHERE guid=". $charId .";");
            $row    = $result->fetch_assoc();
			return $row['account'];
		}
		
		function isGM($account_name) 
		{
			global $Connect;
            $conn = $Connect->connectToDB();

            $accountName = $conn->escape_string($account_name);

            $account_id  = $this->getAccountID($accountName);

            $result      = $conn->query("SELECT COUNT(id) FROM account_access WHERE id=". $account_id ." AND gmlevel >= 1;");
            if ($result->data_seek(0) > 0)
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
            global $Connect;
            $conn = $Connect->connectToDB();

            $desc    = $conn->escape_string($desc);
            $realmId = $conn->escape_string($realmid);
            $service = $conn->escape_string($service);
            $account = $conn->escape_string($_SESSION['cw_user_id']);
			
			$Connect->selectDB("webdb", $conn);
            $conn->query("INSERT INTO user_log (`account`, `service`, `timestamp`, `ip`, `realmid`, `desc`) 
                VALUES('". $account ."','". $service ."','". time() ."','". $_SERVER['REMOTE_ADDR'] ."','". $realmId ."','". $desc ."');");
		}
	}
}

$Account = new Account(); 