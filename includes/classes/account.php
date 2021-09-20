<?php

require_once "random_compat-2.0.20/lib/random.php";

##############
##  Account functions goes here
##############

class account {
	
	###############################
	####### 登录方法
	###############################
	public static function logIn($username,$password,$last_page,$remember) 
	{
		if (!isset($username) || !isset($password) || $username=="Username..." || $password=="Password...") 
			echo '<span class="red_text">请输入账号密码。</span>'; 
		else 
		{
			$username = mysql_real_escape_string(trim(strtoupper($username)));
			$password = mysql_real_escape_string(trim(strtoupper($password)));
			
			connect::selectDB('logondb');
			$checkForAccount = mysql_query("SELECT COUNT(id) FROM account WHERE username='".$username."'");
			if (mysql_result($checkForAccount,0)==0) 
				echo '
			
			<div id="ajax_notification" class="notification_red" style="z-index: 999999; display: block;">无效的用户名。</div>
			

			';	
			else 
			{
				if($remember!=835727313) 
				{
					$data = mysql_query("SELECT salt, verifier FROM account WHERE username = '".$username."'");
					$data = mysql_fetch_assoc($data);
					$salt = $data['salt'];
					$verifier = $data['verifier'];
				}

				if (!account::verifySRP6($username, $password, $salt, $verifier))
					echo '<div id="ajax_notification" class="notification_red" style="z-index: 999999; display: block;">错误的密码。</div>';
				else 
				{
					if($remember=='on') 
						setcookie("cw_rememberMe", $username.' * '.$password, time()+30758400);
						//Set "remember me" cookie. Expires in 1 year.
						 
					$id = mysql_fetch_assoc($result); 
					$id = $id['id'];
					
					self::GMLogin($username);
					$_SESSION['cw_user']=ucfirst(strtolower($username));
					$_SESSION['cw_user_id']=$id;
					
					connect::selectDB('webdb');
					$count = mysql_query("SELECT COUNT FROM account_data WHERE id='".$id."'");
					if(mysql_result($count,0)==0)
						mysql_query("INSERT INTO account_data VALUES('".$id."','0','0','')");
					
					if(!empty($last_page))
					   header("Location: ".$last_page);
					else
					   header("Location: index.php"); 
				}
			}
			
		}
		
	}
	
	public static function loadUserData() 
	{
		//未使用的函数
		$user_info = array();
		
		connect::selectDB('logondb');
		$account_info = mysql_query("SELECT id, username, email, joindate, locked, last_ip, expansion FROM account 
		WHERE username='".$_SESSION['cw_user']."'");
		while($row = mysql_fetch_array($account_info)) 
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
		//setcookie('cw_rememberMe', '', time()-30758400);
		setcookie("cw_rememberMe", $username.' * '.$new, time()+30758400);
		if (empty($last_page)) 
		{
			header('Location: ?p=home"');
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
	####### Registration method
	###############################
	public function register($username,$email,$password,$repeat_password,$captcha,$raf) 
	{
		$errors = array();
		
		if (empty($username))  
			$errors[] = '输入用户名。';
			
		if (empty($email)) 
			$errors[] = '输入电子邮件地址。';
			
		if (empty($password)) 
			$errors[] = '输入密码。';
			
		if (empty($repeat_password)) 
			$errors[] = '再次输入密码。';
			
		if($username==$password) 
			$errors[] = '您的密码不能是您的用户名!';
			
		else 
		{
			session_start();
			/*if($GLOBALS['registration']['captcha']==TRUE) 
			{ 
				if($captcha!=$_SESSION['captcha_numero']) 
					$errors[] = '验证码不正确!';
			}*/
			
			if (strlen($username)>$GLOBALS['registration']['userMaxLength'] || strlen($username)<$GLOBALS['registration']['userMinLength']) 
				$errors[] = '用户名必须介于'.$GLOBALS['registration']['userMinLength'].' 和 '.$GLOBALS['registration']['userMaxLength'].' letters.';
				
			if (strlen($password)>$GLOBALS['registration']['passMaxLength'] || strlen($password)<$GLOBALS['registration']['passMinLength']) 
				$errors[] = '密码必须介于'.$GLOBALS['registration']['passMinLength'].' 和 '.$GLOBALS['registration']['passMaxLength'].' letters.';
				
			if ($GLOBALS['registration']['validateEmail']==true) 
			{
			    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) 
				       $errors[] = '输入一个有效的电子邮件地址。';
			}
			
		}
		
		$username_clean = mysql_real_escape_string(trim($username));
		$password_clean = mysql_real_escape_string(trim($password));
		$username = mysql_real_escape_string(trim(strtoupper(strip_tags($username))));
		$email = mysql_real_escape_string(trim(strip_tags($email)));
		$password = mysql_real_escape_string(trim(strtoupper(strip_tags($password))));
		$repeat_password = trim(strtoupper($repeat_password));
		$raf = (int)$raf;
		
		
		connect::selectDB('logondb');
		
		//检查现有用户
		$result = mysql_query("SELECT COUNT(id) FROM account WHERE username='".$username."'");
		
		if (mysql_result($result,0)>0) 
			$errors[] = '用户名已经存在!';
		
		if ($password != $repeat_password) 
			$errors[] = '密码不匹配!';
		
		if (!empty($errors)) 
		{
			//发现错误。
			echo "<div class='news' style='padding: 5px;'><h4>出现以下错误:</h4>";
			foreach($errors as $error) 
			{
				echo  "<strong>*", $error ,"</strong><br/>";
			}
			echo "</div>";
			exit();
		} 
		else 
		{
			$data = account::getRegistrationData($username, $password);
			$salt = $data[0];
			$verifier = $data[1];

			mysql_query("INSERT INTO account (username, salt, verifier, email, joindate, expansion, recruiter)
			VALUES('".$username."', '".$salt."', '".$verifier."', '".$email."', '".date("Y-m-d H:i:s")."', '".$GLOBALS['core_expansion']."', '".$raf."') ");
			
			$getID = mysql_query("SELECT id FROM account WHERE username='".$username."'");
			$row = mysql_fetch_assoc($getID);
			connect::selectDB('webdb');
			mysql_query("INSERT INTO account_data VALUES('".$row['id']."','','','')"); 
			
			$result = mysql_query("SELECT id FROM account WHERE username='".$username_clean."'");
						 
			$id = mysql_fetch_assoc($result); 
			$id = $id['id'];
					
			account::GMLogin($username_clean);
			$_SESSION['cw_user']=ucfirst(strtolower($username_clean));
			$_SESSION['cw_user_id']=$id;
			
			account::forumRegister($username_clean,$password_clean,$email);
		}

	}
	
	
	public static function forumRegister($username,$password,$email) 
	{
	 date_default_timezone_set($GLOBALS['timezone']);
	 
     global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
	 if($GLOBALS['forum']['type']=='phpbb' && $GLOBALS['forum']['autoAccountCreate']==TRUE) 
	 {
		     ////////PHPBB集成//////////////
			ini_set('display_errors',1);
			define('IN_PHPBB', true);
			define('ROOT_PATH', '../..'.$GLOBALS['forum']['forum_path']);

			$phpEx = "php";
			$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH;
			
			if(file_exists($phpbb_root_path . 'common.' . $phpEx) && file_exists($phpbb_root_path . 'includes/functions_user.' . $phpEx)) 
			{
			include($phpbb_root_path . 'common.' . $phpEx);
			
			include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
			
			$arrTime = getdate();
			$unixTime = strtotime($arrTime['year'] . "-" . $arrTime['mon'] . '-' . $arrTime['mday'] . " " . $arrTime['hours'] . ":" . $arrTime['minutes'] . ":" . $arrTime['seconds']);

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
	####### Check if a user is logged in method.
	###############################
	public static function isLoggedIn() 
	{
		if (isset($_SESSION['cw_user'])) 
			header("Location: ?p=account");
	}
	
	
	
	###############################
	####### Check if a user is NOT logged in method.
	###############################
	public static function isNotLoggedIn() 
	{
		if (!isset($_SESSION['cw_user'])) 
			header("Location: ?p=login&r=".$_SERVER['REQUEST_URI']);
	}
	
	public static function isNotGmLoggedIn() 
	{
		if (!isset($_SESSION['cw_gmlevel']))
			header("Location: ?p=home");
	}
	
	
	###############################
	####### Return ban status method.
	###############################
	public static function checkBanStatus($user) 
	{
		connect::selectDB('logondb');
		$acct_id = self::getAccountID($user);
		
		$result = mysql_query("SELECT bandate,unbandate,banreason FROM account_banned WHERE id='".$acct_id."' AND active=1");
		if (mysql_num_rows($result)>0) 
		{
			$row = mysql_fetch_assoc($result);
			if($row['bandate'] > $row['unbandate']) 
				$duration = 'Infinite';
			else 
			{
				$duration = $row['unbandate'] - $row['bandate'];
				$duration = ($duration / 60)/60;
				$duration = $duration.' hours';  
			}
				echo '<span class="yellow_text">禁用<br/>
				Reason: '.$row['banreason'].'<br/>
				Time left: '.$duration.'</span>';
		} 
		else 
			echo '<b class="green_text">启用</b>';
	}
	
	
	###############################
	####### Return account ID method.
	###############################
	public static function getAccountID($user) 
	{
		$user = mysql_real_escape_string($user);
		connect::selectDB('logondb');
		$result = mysql_query("SELECT id FROM account WHERE username='".$user."'");
		$row = mysql_fetch_assoc($result);
		return $row['id'];
	}
	
	public static function getAccountName($id) 
	{
		$id = (int)$id;
		connect::selectDB('logondb');
		$result = mysql_query("SELECT username FROM account WHERE id='".$id."'");
		$row = mysql_fetch_assoc($result);
		return $row['username'];
	}
	
	
	###############################
	####### "Remember me" method. Loads on page startup.
	###############################
	public function getRemember() 
	{
		if (isset($_COOKIE['cw_rememberMe']) && !isset($_SESSION['cw_user'])) {
			$account_data = explode("*", $_COOKIE['cw_rememberMe']);
			$this->logIn($account_data[0],$account_data[1],$_SERVER['REQUEST_URI'],835727313);
		}	
	}
	
	
	###############################
	####### Return account Vote Points method.
	###############################
	public static function loadVP($account_name) 
	{
		$acct_id = self::getAccountID($account_name);
		connect::selectDB('webdb');
		$result = mysql_query("SELECT vp FROM account_data WHERE id=".$acct_id);
		if (mysql_num_rows($result)==0) 
			return 0;
		else 
		{
			$row = mysql_fetch_assoc($result);
			return $row['vp'];
		}
	}
	
	
	public static function loadDP($account_name) 
	{
	    $acct_id = self::getAccountID($account_name);
		connect::selectDB('webdb');
		$result = mysql_query("SELECT dp FROM account_data WHERE id=".$acct_id);
		if (mysql_num_rows($result)==0) 
			return 0;
		else 
		{
			$row = mysql_fetch_assoc($result);
			return $row['dp'];
		}
	}
	
	
	
	###############################
	####### Return email method.
	###############################
	public static function getEmail($account_name) 
	{
		$account_name = mysql_real_escape_string($account_name);
		connect::selectDB('logondb');
		$result = mysql_query("SELECT email FROM account WHERE username='".$account_name."'");
		$row = mysql_fetch_assoc($result);
		return $row['email'];
	}
	
	
	###############################
	####### Return online status method.
	###############################
	public static function getOnlineStatus($account_name) 
	{
		$account_name = mysql_real_escape_string($account_name);
		connect::selectDB('logondb');
		$result = mysql_query("SELECT COUNT(online) FROM account WHERE username='".$account_name."' AND online=1");
		if (mysql_result($result,0)==0) 
			return '<b class="red_text">离线</b>';
		else
			return '<b class="green_text">在线</b>';
	}
	
	
	###############################
	####### Return Join date method.
	###############################
	public static function getJoindate($account_name) 
	{
		$account_name = mysql_real_escape_string($account_name);
		connect::selectDB('logondb');
		$result = mysql_query("SELECT joindate FROM account WHERE username='".$account_name."'");
		$row = mysql_fetch_assoc($result);
		return $row['joindate'];
	}
	
	
	###############################
	####### 如果用户是级别为2及以上的GM，则返回一个GM会话。
	###############################
	public static function GMLogin($account_name) 
	{
		connect::selectDB('logondb');
		$acct_id = self::getAccountID($account_name);
		
		$result = mysql_query("SELECT gmlevel FROM account_access WHERE gmlevel > 2 AND id=".$acct_id);
		if(mysql_num_rows($result)>0) 
		{
			$row = mysql_fetch_assoc($result);
			$_SESSION['cw_gmlevel']=$row['gmlevel'];
		}
		
	}
	
	public static function getCharactersForShop($account_name) 
	{
		$acct_id = self::getAccountID($account_name);
		connect::selectDB('webdb');
		$getRealms = mysql_query("SELECT id,name FROM realms");
		while($row = mysql_fetch_assoc($getRealms)) 
		{
			connect::connectToRealmDB($row['id']);
			$result = mysql_query("SELECT name,guid FROM characters WHERE account='".$acct_id."'");
			if(mysql_num_rows($result)==0 && !isset($x))
			{
				$x = true;
			     echo '<option value="">没有发现角色！</option>';
			}
				  
			while($char = mysql_fetch_assoc($result)) 
			{
				echo '<option value="'.$char['guid'].'*'.$row['id'].'">'.$char['name'].' - '.$row['name'].'</option>';
			}
		}
	}


    public static function changeEmail($email, $current_pass)
	{
		$errors = array();
		if (empty($current_pass)) 
			$errors[] = '请输入您的当前密码'; 
		else 
		{
			if (empty($email)) 
				$errors[] = '请输入电子邮件地址。';
			
			connect::selectDB('logondb');
			$username = mysql_real_escape_string(trim(strtoupper($_SESSION['cw_user'])));
			$password = mysql_real_escape_string(trim(strtoupper($current_pass)));
			
			$data = mysql_query("SELECT salt, verifier FROM account WHERE username = '".$username."'");
			$data = mysql_fetch_assoc($data);
			$salt = $data['salt'];
			$verifier = $data['verifier'];

			if (!account::verifySRP6($username, $password, $salt, $verifier))
				$errors[] = '当前密码不正确。';
			
			
			if ($GLOBALS['registration']['validateEmail']==true) 
			{
			    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) 
				    $errors[] = '输入一个有效的电子邮件地址。';
				 else 
					 //mysql_query("UPDATE account SET email = '".$email."' WHERE username = '".$_SESSION['cw_user']."'");
					 mysql_query("UPDATE account SET email = '".$email."' WHERE username = '".$username."'");
			}
			
		}
		if(empty($errors)) 
			echo '您的帐号更新成功。';
		else 
		{
			echo '<div class="news" style="padding: 5px;">
			<h4 class="red_text">出现以下错误:</h4>';
				   foreach($errors as $error) 
				   {
					 echo  '<strong class="yellow_text">*', $error ,'</strong><br/>';
				   }
			echo '</div>';
		}
	}
	
	
	
	//用于更改密码页面。
	public static function changePass($old,$new,$new_repeat) 
	{
		$old = mysql_real_escape_string(trim($old));
        $new = mysql_real_escape_string(trim($new));
        $new_repeat = mysql_real_escape_string(trim($new_repeat));
		
		//检查是否所有字段值都已输入
		if (!isset($_POST['cur_pass']) || !isset($_POST['new_pass']) || !isset($_POST['new_pass_repeat'])) 
			echo '<b class="red_text">请输入所有字段!</b>';
	    else 
		{
			//检查新密码是否匹配?
			if ($new != $new_repeat)
				echo '<b class="red_text">新密码不匹配!</b>';
			else 
			{
			  if (strlen($new) < $GLOBALS['registration']['passMinLength'] ||
			      strlen($new) > $GLOBALS['registration']['passMaxLength'])
				  echo '<b class="red_text">您的密码必须介于 '.$GLOBALS['registration']['passMinLength'].' 和 '.$GLOBALS['registration']['passMaxLength'].' 字母和/或数字。</b>';
			  else 
			  {
				//让我们检查一下旧密码是否正确!
				//$username = strtoupper(mysql_real_escape_string($_SESSION['cw_user']));
				$username = strtoupper(mysql_real_escape_string(trim($_SESSION['cw_user'])));
				connect::selectDB('logondb');
				$data = mysql_query("SELECT salt, verifier FROM account WHERE username = '".$username."'");
				$data = mysql_fetch_assoc($data);
				$salt = $data['salt'];
				$verifier = $data['verifier'];
				
				if (!account::verifySRP6($username, $old, $salt, $verifier))
					echo '<b class="red_text">旧密码不正确!</b>';
				else 
				{
					//成功,更改密码
					$data2 = account::getRegistrationData($username, $new);
					$salt2 = $data2[0];
					$verifier2 = $data2[1];
					mysql_query("UPDATE account SET salt = '".$salt2."', verifier = '".$verifier2."' WHERE username = '".$username."'");
					echo '您的密码已修改!';
					mysql_query("UPDATE `account` SET `sha_pass_hash`='$new_pass_hash' WHERE `username`='".$username."'");
					mysql_query("UPDATE `account` SET `v`='0' AND `s`='0' WHERE username='".$username."'");
				}
			}
		  }
		}
	}
	
	public static function changePassword($account_name,$password) 
	{
			$username = mysql_real_escape_string(strtoupper($account_name));
			$pass = mysql_real_escape_string(strtoupper($password));
			$pass_hash = SHA1($username.':'.$pass);
			
			connect::selectDB('logondb');
			mysql_query("UPDATE `account` SET `sha_pass_hash`='$pass_hash' WHERE `username`='".$username."'");
			mysql_query("UPDATE `account` SET `v`='0' AND `s`='0' WHERE username='".$username."'");
			
			account::logThis("Changed password","passwordchange",NULL);
	}
	
	public static function forgotPW($account_name, $account_email) 
	{
		$account_name = mysql_real_escape_string($account_name);
		$account_email = mysql_real_escape_string($account_email);
		
		if (empty($account_name) || empty($account_email)) 
			echo '<b class="red_text">请输入用户名和Email。</b>';
		else 
		{
			connect::selectDB('logondb');
			$result = mysql_query("SELECT COUNT('id') FROM account 
								 WHERE username='".$account_name."' AND email='".$account_email."'");
			
			if (mysql_result($result,0)==0) 
				echo '<b class="red_text">用户名或电子邮件不正确。</b>';
			else 
			{
				//Success, lets send an email & add the forgotpw thingy.
				$code = RandomString();
				website::sendEmail($account_email,$GLOBALS['default_email'],'找回密码',"
				你好。<br/><br/>
				要求为帐户重设密码 ".$account_name." <br/>
				如果要重置密码，请单击下面的链接：<br/>
				<a href='".$GLOBALS['website_domain']."?p=forgotpw&code=".$code."&account=".account::getAccountID($account_name)."'>
				".$GLOBALS['website_domain']."?p=forgotpw&code=".$code."&account=".account::getAccountID($account_name)."</a>
				
				<br/><br/>
				
				如果您没有请求此消息，请忽略此消息。<br/><br/>
				来自TBCstar的问候。
				");
				$account_id = self::getAccountID($account_name);
				connect::selectDB('webdb');
				
				mysql_query("DELETE FROM password_reset WHERE account_id='".$account_id."'");
				mysql_query("INSERT INTO password_reset VALUES ('','".$code."','".$account_id."')");
				echo "
				包含重置密码链接的电子邮件已发送到您指定的电子邮件地址。
				如果您在此之前已经提交了其他密码重置请求，则这些请求将不起作用。<br/>";
				}	
			}	
		}
	
		public static function hasVP($account_name,$points) 
		{
			$points = (int)$points;
			$account_id = self::getAccountID($account_name);
			connect::selectDB('webdb');
			$result = mysql_query("SELECT COUNT('id') FROM account_data WHERE vp >= '".$points."' 
			AND id='".$account_id."'");
			
			if (mysql_result($result,0)==0) 
				return FALSE;
			else
				return TRUE;
		}
		
		public static function hasDP($account_name,$points) 
		{
			$points = (int)$points;
			$account_id = self::getAccountID($account_name);
			connect::selectDB('webdb');
			$result = mysql_query("SELECT COUNT('id') FROM account_data WHERE dp >= '".$points."' 
			AND id='".$account_id."'");
			
			if (mysql_result($result,0)==0)
				return FALSE;
			else
				return TRUE;
		}
		
		
		public static function deductVP($account_id,$points) 
		{
			$points = (int)$points;
			$account_id = (int)$account_id;
			connect::selectDB('webdb');
            
			mysql_query("UPDATE account_data SET vp=vp - ".$points." WHERE id='".$account_id."'");
		}
		
		public static function deductDP($account_id,$points) 
		{
			$points = (int)$points;
			$account_id = (int)$account_id;
			connect::selectDB('webdb');
            
			mysql_query("UPDATE account_data SET dp=dp - ".$points." WHERE id='".$account_id."'");
		}
		
		public static function addDP($account_id,$points)
		{
			$account_id = (int)$account_id;
			$points = (int)$points;
			connect::selectDB('webdb');
			
			mysql_query("UPDATE account_data SET dp=dp + ".$points." WHERE id='".$account_id."'");
		}
		
		public static function addVP($account_id,$points)
		{
			$account_id = (int)$account_id;
			$points = (int)$points;
			connect::selectDB('webdb');
			
			mysql_query("UPDATE account_data SET dp=dp + ".$points." WHERE id='".$account_id."'");
		}
		
		public static function getAccountIDFromCharId($char_id,$realm_id) 
		{
			$char_id = (int)$char_id;
			$realm_id = (int)$realm_id;
			connect::selectDB('webdb');
			connect::connectToRealmDB($realm_id);
			
			$result = mysql_query("SELECT account FROM characters WHERE guid='".$char_id."'");
			$row = mysql_fetch_assoc($result);
			return $row['account'];
		}
		
		
		public static function isGM($account_name) 
		{
	         $account_id = self::getAccountID($account_name);
			 $result = mysql_query("SELECT COUNT(id) FROM account_access WHERE id='".$account_id."' AND gmlevel >= 1");
			 if (mysql_result($result,0)>0)
				 return TRUE;
			 else
				 return FALSE;
		}
		
		public static function logThis($desc,$service,$realmid)
		{
			$desc = mysql_real_escape_string($desc);
			$realmid = (int)$realmid;
			$service = mysql_real_escape_string($service);
			$account = (int)$_SESSION['cw_user_id'];
			
			connect::selectDB('webdb');
			mysql_query("INSERT INTO user_log VALUES('','".$account."','".$service."','".time()."','".$_SERVER['REMOTE_ADDR']."','".$realmid."','".$desc."')");
	}
}
?>