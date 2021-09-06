<?php
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
			echo '<span class="red_text">
			    请输入账号密码。
			    </span>'; 
		}
		else 
		{
            global $Connect;
            $conn       = $Connect->connectToDB();;
            $username   = $conn->escape_string(trim(strtoupper($username)));
            $password   = $conn->escape_string(trim(strtoupper($password)));

			$Connect->selectDB('logondb', $conn);

            $checkForAccount = $conn->query("SELECT COUNT(id) AS username FROM account WHERE username='". $username ."';");

            if ($checkForAccount->fetch_assoc()['username'] == 0)
			{
				echo '<span class="red_text">
			       无效的用户名。
			       </span>';
			}
			else 
			{
				if ($remember != 835727313) $password = sha1("". $username .":". $password ."");

				$result = $conn->query("SELECT id FROM account WHERE username='". $username ."' AND sha_pass_hash='". $password ."';");
                if ($result->num_rows == 0)
                {
                    echo '<span class="red_text">
                        错误的密码。
                    </span>';
                }
				else 
				{
					if($remember=='on')
					{ 
						setcookie("cw_rememberMe", $username .' * '. $password, time()+30758400);
						//Set "remember me" cookie. Expires in 1 year.
					}

					$id = $result->fetch_assoc();
					$id = $id['id'];
					
					$this->GMLogin($username);
					$_SESSION['cw_user'] = ucfirst(strtolower($username));
					$_SESSION['cw_user_id'] = $id;
					
					$Connect->selectDB('webdb', $conn);

                    $count = $conn->query("SELECT COUNT(*) FROM account_data WHERE id=". $id .";");
                    if ($count->data_seek(0) == 0)
                    {
                        $conn->query("INSERT INTO account_data (id) VALUES(". $id .");");
                    }
					
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
	
	public function loadUserData()
	{
		//未使用的函数
		$user_info = array();
		global $Connect, $conn;
		$Connect->selectDB('logondb', $conn);

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
		
        global $Connect;
        $conn            = $Connect->connectToDB();
        $username_clean  = $conn->escape_string(trim($username));
        $password_clean  = $conn->escape_string(trim($password));
        $username        = $conn->escape_string(trim(strtoupper(strip_tags($username))));
        $email           = $conn->escape_string(trim(strip_tags($email)));
        $password        = $conn->escape_string(trim(strtoupper(strip_tags($password))));
        $repeat_password = $conn->escape_string(trim(strtoupper($repeat_password)));
        $raf             = $conn->escape_string($raf);
		
		
		$Connect->selectDB('logondb', $conn);
		
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
			$password = sha1("". $username .":". $password ."");
			
            $conn->query("INSERT INTO account (username, email, sha_pass_hash, joindate, expansion, recruiter) VALUES
                ('". $username ."', '". $email ."', '". $password ."', '". date("Y-m-d H:i:s") ."', '". $GLOBALS['core_expansion'] ."', '". $raf ."');");

            $getID = $conn->query("SELECT id FROM account WHERE username='". $username ."';");
            $row   = $getID->fetch_assoc();

			$Connect->selectDB('webdb', $conn);
            $conn->query("INSERT INTO account_data (id) VALUES(". $row['id'] .");");

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
	public function isLoggedIn() 
	{
		if (isset($_SESSION['cw_user']))
		{
			header("Location: ?p=account");
		}
	}
	
	
	
	###############################
	####### 检查用户是否未登录方法。
	###############################
	public function isNotLoggedIn() 
	{
		if (!isset($_SESSION['cw_user']))
		{
			header("Location: ?p=login&r=". $_SERVER['REQUEST_URI']);
		}
	}
	
	public function isNotGmLoggedIn() 
	{
		if (!isset($_SESSION['cw_gmlevel']))
		{
			header("Location: ?p=home");
		}
	}
	
	
	###############################
	####### 返回禁止状态方法。
	###############################
	public function checkBanStatus($user) 
	{
        global $Connect;
        $conn = $Connect->connectToDB();
        $Connect->selectDB('logondb', $conn);

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

		$Connect->selectDB('logondb', $conn);

        $result = $conn->query("SELECT id FROM account WHERE username='". $user ."';");
        $row    = $result->fetch_assoc();

		return $row['id'];
	}
	
	public function getAccountName($id) 
	{
        global $Connect;
        $conn = $Connect->connectToDB();

        $id = $conn->escape_string($id);

		$Connect->selectDB('logondb', $conn);

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

		$Connect->selectDB('webdb', $conn);

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

		$Connect->selectDB('webdb', $conn);

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

		$Connect->selectDB('logondb', $conn);

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

		$Connect->selectDB('logondb', $conn);

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

		$Connect->selectDB('logondb', $conn);

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
		$Connect->selectDB('logondb', $conn);

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

		$Connect->selectDB('webdb', $conn);

        $getRealms = $conn->query("SELECT id, name FROM realms;");
        while ($row = $getRealms->fetch_assoc())
		{
			$Connect->connectToRealmDB($row['id']);

            $result = $conn->query("SELECT name, guid FROM characters WHERE account=". $acct_id .";");
            if ($result->num_rows == 0 && !isset($x))
			{
				$x = true;
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

			$Connect->selectDB('logondb', $conn);

			$username = $conn->escape_string(trim(strtoupper($_SESSION['cw_user'])));
            $password = $conn->escape_string(trim(strtoupper($current_pass)));
			
			$password = sha1("". $username .":". $password ."");

			$result = $conn->query("SELECT COUNT(id) FROM account WHERE username='". $username ."' AND sha_pass_hash='". $password ."';");
            if ($result->data_seek(0) == 0)
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
					$conn->query("UPDATE account SET email='". $email ."' WHERE username='". $_SESSION['cw_user'] ."';");
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

        $_POST['current_password']    = $conn->escape_string($old);
        $_POST['new_password']        = $conn->escape_string($new);
        $_POST['new_password_repeat'] = $conn->escape_string($new_repeat);
		
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
                if (strlen($_POST['new_password']) < $GLOBALS['registration']['passMinLength'] ||
                    strlen($_POST['new_password']) > $GLOBALS['registration']['passMaxLength'])
				{
                    echo "<b class='red_text'>
                        您的密码必须是 ". $GLOBALS['registration']['passMinLength'] ."
                        和 ". $GLOBALS['registration']['passMaxLength'] ." 之间。
                    </b>";
				}
				else 
				{
					//让我们检查一下旧密码是否正确!
					$username = $conn->escape_string(strtoupper($_SESSION['cw_user']));

					$Connect->selectDB('logondb', $conn);

                    $getPass = $conn->query("SELECT `sha_pass_hash` FROM `account` WHERE `username`='". $username ."';");

                    $row     = $getPass->fetch_assoc();
					$thePass = strtoupper($row['sha_pass_hash']);

					$pass      = $conn->escape_string(strtoupper($_POST['current_password']));

					$pass_hash = sha1("". $username .":". $pass ."");

                    $new_password      = $conn->escape_string(strtoupper($_POST['new_password']));
                    $new_password_hash = sha1("". $username .":". $new_password ."");

					if ($thePass != $pass_hash)
					{
						echo "<b class='red_text'>
						    旧密码不正确!
					    </b>";
					}
					else 
					{
						//成功,更改密码
						echo "<b class='green_text'>
						    您的密码已更改!
					    </b>";
						$conn->query("UPDATE account SET sha_pass_hash='". $new_password_hash ."' WHERE username='". $username ."';");
                        $conn->query("UPDATE account SET v=0 AND s=0 WHERE username='". $username ."';");
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
			
		$Connect->selectDB('logondb', $conn);

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
			$Connect->selectDB('logondb', $conn);

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
				<a href='". $GLOBALS['website_domain'] ."?p=forgotpw&code=". $code ."&account=". $this->getAccountID($accountName) ."'>
				". $GLOBALS['website_domain'] ."?p=forgotpw&code=". $code ."&account=". $this->getAccountID($accountName) ."</a>
				
				<br/><br/>
				
				如果您没有请求此消息，请忽略此消息。<br/><br/>
				来自TBCstar的问候。");

				$account_id = $this->getAccountID($accountName);

				$Connect->selectDB('webdb', $conn);
				
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

			$Connect->selectDB('webdb', $conn);
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
			
			$Connect->selectDB('webdb', $conn);

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
			
			$Connect->selectDB('webdb', $conn);
            
			$conn->query("UPDATE account_data SET vp=vp - ". $points ." WHERE id=". $accountId .";");
		}
		
		function deductDP($account_id, $points) 
		{
			global $Connect;
            $conn = $Connect->connectToDB();

            $points     = $conn->escape_string($points);
            $accountId  = $conn->escape_string($account_id);

			$Connect->selectDB('webdb', $conn);
            
			$conn->query("UPDATE account_data SET dp=dp - ". $points ." WHERE id=". $accountId .";");
		}
		
		function addDP($account_id, $points)
		{
			global $Connect;
            $conn = $Connect->connectToDB();

			$accountId  = $conn->escape_string($account_id);
            $points     = $conn->escape_string($points);

            $Connect->selectDB('webdb', $conn);
			
			$conn->query("UPDATE account_data SET dp=dp + ". $points ." WHERE id=". $accountId .";");
		}
		
		function addVP($account_id, $points)
		{
			global $Connect;
            $conn = $Connect->connectToDB();

            $accountId  = $conn->escape_string($account_id);
            $points     = $conn->escape_string($points);
			$Connect->selectDB('webdb', $conn);
			
			$conn->query("UPDATE account_data SET dp=dp + ". $points ." WHERE id=". $accountId .";");
		}
		
		function getAccountIDFromCharId($char_id, $realm_id) 
		{
            global $Connect;
            $conn = $Connect->connectToDB();

            $charId  = $conn->escape_string($char_id);
            $realmId = $conn->escape_string($realm_id);

            $Connect->selectDB('webdb', $conn);
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
			
			$Connect->selectDB('webdb', $conn);
            $conn->query("INSERT INTO user_log (`account`, `service`, `timestamp`, `ip`, `realmid`, `desc`) 
                VALUES('". $account ."','". $service ."','". time() ."','". $_SERVER['REMOTE_ADDR'] ."','". $realmId ."','". $desc ."');");
		}
	}
}

$Account = new Account(); 