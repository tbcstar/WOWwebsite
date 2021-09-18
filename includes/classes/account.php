<?php

global $Database, $Account, $Messages, $Server;

require_once "random_compat-2.0.20/lib/random.php";
 
/**
 * Account Class 
 */
 
class Account
{
    public function logIn($username, $password, $last_page, $remember)
    {
        if ( empty($username) || empty($password) )
        {
            $Messages->error("请输入用户名和密码");
            return null;
        }

        $username = $Database->conn->escape_string( strtoupper($username) );
        $password = $Database->conn->escape_string( strtoupper($password) );

        $Database->selectDB("logondb", $Database->conn);

        $checkForAccount = $Database->select("account", null, null, "username='$username'");

        if ( $checkForAccount->num_rows == 0 )
        {
            $Messages->error("用户名和密码不匹配");
            return;
        }

        if ( $remember != 835727313 )
    	{
			$data = mysqli_query("SELECT salt, verifier FROM account WHERE username = '".$username."'");
			$data = mysqli_fetch_assoc($data);
			$salt = $data['salt'];
			$verifier = $data['verifier'];
        }

        if (!$Account->verifySRP6($username, $password, $salt, $verifier))
        {
            $Messages->error("密码错误");
            return;
        }

        # Set "remember me" cookie. Expires in 1 week
        if ( $remember == "on" )
        {
            setcookie("cw_rememberMe", $username ." * ". $password, time() + ( (60*60)*24)*7);
        }

        $id = $result->fetch_assoc()['id'];

        $this->GMLogin($username);
        $_SESSION['cw_user']    = ucfirst(strtolower($username));
        $_SESSION['cw_user_id'] = $id;

        $statement->close();

        $Database->selectDB("webdb");

        $statement = $Database->select("account_data", "COUNT(*)", null, "id='$id'");
        $count = $statement->get_result();
        if ( $count->data_seek(0) == 0 )
        {
            $Database->insert("account_data", "id", $id);
        }
        $statement->close();

        if ( !empty($last_page) )
        {
            header("Location: $last_page");
            exit;
        }
        else
        {
            header("Location: index.php");
            exit;
        }
    }

    public function loadUserData()
    {
        # Unused function
        $user_info = array();
        global $Database;
        $Database->selectDB("logondb");

        $statement = $Database->select("account", "id,username,email,joindate,locked,last_ip,expansion", null, "username='".$_SESSION['cw_user']."'");
        $account_info = $statement->get_result();
        while ($row = $account_info->fetch_array())
        {
            $user_info[] = $row;
	    }
	
        return $user_info;
    }

    public function logOut($last_page)
    {
        $_SESSION = array();
        session_destroy();
        setcookie("cw_rememberMe", "", time() - 30758400);

        if ( empty($last_page) )
        {
            header("Location: ?page=home");
            exit();
        }
        else
        {
            header("Location: $last_page");
            exit();
        }
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
        $verifier = $Account->calculateSRP6Verifier($username, $password, $salt);

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


    public function register($username, $email, $password, $repeat_password, $captcha, $raf)
    {
        $errors = array();

        if ( empty($username) )
            $errors[] = "输入用户名。";

        if ( empty($email) )
            $errors[] = "输入电子邮件地址。";

        if ( empty($password) )
            $errors[] = "输入密码。";

        if ( empty($repeat_password) )
            $errors[] = "再次输入密码。";

        if ( $username == $password )
        {
            $errors[] = "您的密码不能是您的用户名！";
        }
        else
        {
            @session_start();
            if ( DATA['website']['registration']['captcha'] == TRUE && defined("CAPTCHA_VALUE") )
		    {
                if ( $captcha != CAPTCHA_VALUE )
			    { 
                    $errors[] = "验证码不正确！";
		    	}
            }

            if ( strlen($username) > DATA['website']['registration']['user_max_length'] || 
                strlen($username) < DATA['website']['registration']['user_min_length'] )
                $errors[] = "用户名必须介于 ". DATA['website']['registration']['user_minlength'] ." 和 ". DATA['website']['registration']['user_max_length'] ." 之间。";

            if ( strlen($password) > DATA['website']['registration']['pass_max_length'] || 
                strlen($password) < DATA['website']['registration']['pass_min_length'] )
                $errors[] = "密码必须介于 ". DATA['website']['registration']['pass_min_length'] ." 和 ". DATA['website']['registration']['pass_max_length'] ." 之间。";
				
            if ( DATA['website']['registration']['validate_email'] == TRUE )
            {
                if ( filter_var($email, FILTER_VALIDATE_EMAIL) === false )
			    {
                    $errors[] = "输入一个有效的电子邮件地址。";
			    }
	    	}

        }
        global $Database;

        $username_clean  = $Database->conn->escape_string( trim( $username ) );
        $password_clean  = $Database->conn->escape_string( trim( $password ) );
        $username        = $Database->conn->escape_string( trim( strtoupper( strip_tags( $username ) ) ) );
        $email           = $Database->conn->escape_string( trim( strip_tags( $email ) ) );
        $password        = $Database->conn->escape_string( trim( strtoupper( strip_tags($password ) ) ) );
        $repeat_password = $Database->conn->escape_string( trim( strtoupper( $repeat_password ) ) );
        $raf             = $Database->conn->escape_string($raf);


        $Database->selectDB("logondb");

        # Check for existing user
        $statement = $Database->select("account", "COUNT(id) AS user", null, "username='$username'");
        $result = $statement->get_result();

        if ( $result->fetch_assoc()['user'] > 1 )
        {
            $errors[] = "这个用户名已经存在！";
        }

        if ( $password != $repeat_password )
        {
            $errors[] = "密码不匹配！";
        }

        if ( !empty($errors) )
        {
            //errors found.
            echo "<p><h4>出现以下错误：</h4>";
            if ( is_array($errors) || is_object($errors) )
		    {
                foreach ($errors as $error)
				{
                    echo "<strong>*$error</strong><br/>";
				}
		    }

            echo "</p>";
            exit();
        }
        else
        {
			$data = $Account->getRegistrationData($username, $password);
			$salt = $data[0];
			$verifier = $data[1];

            if ( empty($raf) )
            {
                $raf = 0;
            }

            $Database->selectDB("logondb");

            $statement = $Database->insert("account", 
                [
                    "username" => $username, 
                    "salt" => $salt, 
                    "verifier" => $verifier, 
                    "email" => $email, 
                    "joindate" => date("Y-m-d H:i:s"), 
                    "expansion" => DATA['website']['expansion'], 
                    "recruiter" => $raf
                ]);
            if ( !empty($statement->error) )
            {
                global $Messages;
                $Messages->error("错误联系管理员！");
                return;
            }

            $statement = $Database->select("account", "id", null, "username='$username'");
            $row = $statement->get_result()->fetch_assoc();

            $Database->selectDB("webdb");

            $Database->insert("account_data", ["id" => $row['id']]);

            $Database->selectDB("logondb");
            $result = $Database->select("account", "id", null, "username='$username_clean'");
            $id     = $result->fetch_assoc();
            $id     = $id['id'];

            $this->GMLogin($username_clean);

            $_SESSION['cw_user']    = ucfirst(strtolower($username_clean));
            $_SESSION['cw_user_id'] = $id;

            $Account->forumRegister($username_clean,$password_clean,$email);
	    }
    }

    // Unused
    public function forumRegister($username, $password, $email)
    {
        date_default_timezone_set(DATA['website']['timezone']);

        global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
        if ( DATA['website']['forum']['type'] == 'phpbb' && DATA['website']['forum']['auto_account_create'] == TRUE )
	    {
            ////////PHPBB INTEGRATION//////////////
            define('IN_PHPBB', TRUE);
            define('ROOT_PATH', '../..' . DATA['website']['forum']['path']);

            $phpEx           = "php";
            $phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH;

            if ( file_exists($phpbb_root_path . 'common.' . $phpEx) && file_exists($phpbb_root_path . 'includes/functions_user.' . $phpEx) )
            {
                include $phpbb_root_path ."common.". $phpEx;

                include $phpbb_root_path ."includes/functions_user.". $phpEx;

                $arrTime  = getdate();
                $unixTime = strtotime($arrTime['year'] . "-" . $arrTime['mon'] . '-' . $arrTime['mday'] . " " . $arrTime['hours'] . ":" .$arrTime['minutes'] . ":" . $arrTime['seconds']);

                $user_row = array
                (
                    'username'             => $username,
                    'user_password'        => phpbb_hash($password),
                    'user_email'           => $email,
                    'group_id'             => (int) 2,
                    'user_timezone'        => (float) 0,
                    'user_dst'             => "0",
                    'user_lang'            => "en",
                    'user_type'            => 0,
                    'user_actkey'          => "",
                    'user_ip'              => $_SERVER['REMOTE_HOST'],
                    'user_regdate'         => $unixTime,
                    'user_inactive_reason' => 0,
                    'user_inactive_time'   => 0
                );

                // All the information has been compiled, add the user
                // tables affected: users table, profile_fields_data table, groups table, and config table.
                $user_id = user_add($user_row);
		    }
	    }
    }


    public function isLoggedIn()
    {
        if ( isset($_SESSION['cw_user']) )
	    {
            header("Location: ?page=account");
	    }
    }

    public function isNotLoggedIn()
    {
        if ( !isset($_SESSION['cw_user']) )
	    {
            header("Location: ?page=login&r=" . $_SERVER['REQUEST_URI']);
	    }
    }
	
    public function isNotGmLoggedIn()
    {
        if ( !isset($_SESSION['cw_gmlevel']) )
	    {
            header("Location: ?page=home");
        }
    }

    public function checkBanStatus($user)
    {
        global $Database;

        $Database->selectDB("logondb");

        $acct_id = $this->getAccountID($user);

        $statement = $Database->select("account_banned", "bandate, unbandate, banreason", null, "id='$acct_id' AND active=1");
        $result = $statement->get_result();
        if ( $result->num_rows > 0 )
        {
            $row = $result->fetch_assoc();
            if ($row['bandate'] > $row['unbandate'])
		    {
                $duration = 'Infinite';
		    } 
		    else
		    {
                $duration = $row['unbandate'] - $row['bandate'];
                $duration = ($duration / 60) / 60;
                $duration = $duration . ' hours';
		    }
            echo '<span class="yellow_text">禁用<br/>
				  原因： ' . $row['banreason'] . '<br/>
				  剩余时间： ' . $duration . '</span>';
	    }
        else
	    {
            echo '<b class="green_text">活跃</b>';
        }
    }

    ###############################
    ####### Return account ID method.
    ###############################

    public function getAccountID($user)
    {
        global $Database;
        $user   = $Database->conn->escape_string($user);

        $Database->selectDB("logondb");

        $statement = $Database->select("account", "id", null, "username='$user'");
        $result = $statement->get_result();
        $row    = $result->fetch_assoc();

        return $row['id'];
    }

    public function getAccountName($id)
    {
        global $Database;
        $id = $Database->conn->escape_string($id);

        $Database->selectDB("logondb");

        $statement = $Database->select("account", "username", "id='$id'");
        $result = $statement->get_result();
        $row    = $result->fetch_assoc();

        return $row['username'];
    }

    ###############################
    ####### "Remember me" method. Loads on page startup.
    ###############################

    public function getRemember()
    {
        if ( isset($_COOKIE['cw_rememberMe']) && !isset($_SESSION['cw_user']) )
	    {
            $account_data = explode("*", $_COOKIE['cw_rememberMe']);

            $this->logIn($account_data[0], $account_data[1], $_SERVER['REQUEST_URI'], 835727313);
	    }
    }

    ###############################
    ####### Return account Vote Points method.
    ###############################

    public function loadVP($account_name)
    {
        global $Database;
        $accountName    = $Database->conn->escape_string($account_name);
        $acct_id        = $this->getAccountID($accountName);

        $Database->selectDB("webdb");

        $statement = $Database->select("account_data", "vp", null, "id='$acct_id'");
        $result = $statement->get_result();
        if ( $result->num_rows == 0 )
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
        global $Database;
        $accountName    = $Database->conn->escape_string($account_name);
        $acct_id        = $this->getAccountID($accountName);

        $Database->selectDB("webdb");

        $statement  = $Database->select("account_data", "dp", null, "id=$acct_id");
        $result = $statement->get_result();
        if ( $result->num_rows == 0 )
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
    ####### Return email method.
    ###############################

    public function getEmail($account_name)
    {
        global $Database;
        $accountName = $Database->conn->escape_string($account_name);

        $Database->selectDB("logondb");

        $statement = $Database->select("account", "email", null, "username=$accountName");
        $result = $statement->get_result();
        $row = $result->fetch_assoc();

        return $row['email'];
    }

    ###############################
    ####### Return online status method.
    ###############################

    public function getOnlineStatus($account_name)
    {
        global $Database;

        $account_name = $Database->conn->escape_string($account_name);

        $Database->selectDB("logondb");

        $statement = $Database->select("account", "COUNT(online) AS online", null, "username=$account_name AND online=1");
        $result = $statement->get_result();
        if ( $result->fetch_assoc()['online'] == 0 )
	    {
            return "<b style=\"color:red;\">离线</b>";
        }
        else
        {
            return "<b style=\"color:green;\">在线</b>";
        }
    }

    ###############################
    ####### Return Join date method.
    ###############################

    public function getJoindate($account_name)
    {
        global $Database;
        $accountName = $Database->conn->escape_string($account_name);

        $Database->selectDB("logondb");

        $result       = $Database->select("account", "joindate", null, "username=$account_name");
        $row          = $result->fetch_assoc();
	
        return $row['joindate'];
    }

    ###############################
    ####### Returns a GM session if the user is a GM with rank 2 and above.
    ###############################

    public function GMLogin($account_name)
    {
        global $Database;
        $Database->selectDB("logondb");

        $accountName = $Database->conn->escape_string($account_name);

        $acct_id = $this->getAccountID($accountName);

        $statement = $Database->select("account_access", "gmlevel", null, "gmlevel > 2 AND id=$acct_id");
        $result = $statement->get_result();
        if ( $result->num_rows > 0 )
	    {
            $row                    = $result->fetch_assoc();
            $_SESSION['cw_gmlevel'] = $row['gmlevel'];
        }
    }

    public function getCharactersForShop($account_name)
    {
        global $Database;
        $accountName = $Database->conn->escape_string($account_name);

        $acct_id = $this->getAccountID($accountName);

        $Database->selectDB("webdb");

        $statement = $Database->select("realms", "id, name");
        $getRealms = $statement->get_result();
        while ($row = $getRealms->fetch_assoc())
        {
            $Database->realm($row['id']);

            $statement = $Database->select("characters", "name, guid", null, "account=$acct_id");
            $result = $statement->get_result();
            if ( $result->num_rows == 0 && !isset($x) )
            {
                $x = TRUE;
                echo "<option value=\"\">没有找到角色！</option>";
            }

            while ($char = $result->fetch_assoc())
            {
                echo "<option value=\"". $char['guid'] ."*". $row['id'] ."\">". $char['name'] ." - ". $row['name'] ."</option>";
		    }
	    }
    }

    public function changeEmail($email, $current_pass)
    {
        $errors = array();

        if ( empty($current_pass) )
        {
            $errors[] = '请输入您当前的密码';
        }
        else
        {
            if ( empty($email) )
		    {
			    $errors[] = '请输入电子邮件地址。';
		    }

            global $Database;

            $Database->selectDB("logondb");

            $username = $Database->conn->escape_string( trim( strtoupper( $_SESSION['cw_user'] ) ) );
            $password = $Database->conn->escape_string( trim( strtoupper( $current_pass ) ) );

			$data = mysqli_query("SELECT salt, verifier FROM account WHERE username = '".$username."'");
			$data = mysqli_fetch_assoc($data);
			$salt = $data['salt'];
			$verifier = $data['verifier'];

			if (!$Account->verifySRP6($username, $password, $salt, $verifier))
            {
                $errors[] = "当前密码不正确。";
            }

            if ( DATA['website']['registration']['validate_email'] == TRUE )
            {
                if ( filter_var($email, FILTER_VALIDATE_EMAIL) === false )
			    {
                    $errors[] = '输入一个有效的电子邮件地址。';
			    }
                else
			    {
                    $Database->update("account", array("email"=> $email), array("username" => $_SESSION['cw_user']));
			    }
		    }
        }

        if ( empty($errors) )
        {
            echo "已成功更新您的帐户。";
        }
        else
        {
            echo "<div class=\"news\" style=\"padding: 5px;\">
            <h4 class=\"red_text\">出现以下错误：</h4>";
            if ( is_array($errors) || is_object($errors) )
		    {
                foreach ($errors as $error)
			    {
                    echo "<strong class=\"yellow_text\">*$error</strong><br/>";
			    }
		    }
            echo "</div>";
	    }
    }

    //Used for the change password page.
    public function changePass($old, $new, $new_repeat)
    {
        global $Database;

        $old['current_password']           = $Database->conn->escape_string($old);
        $new['new_password']               = $Database->conn->escape_string($new);
        $new_repeat['new_password_repeat'] = $Database->conn->escape_string($new_repeat);
		
        //Check if all field values has been typed into
        if ( empty($_POST['current_password']) || 
            empty($_POST['new_password']) || 
            empty($_POST['new_password_repeat']) )
        {
            echo "<b class=\"red_text\">请在所有字段中输入！</b>";
        }
        else
        {
            //Check if new passwords match?
            if ($new != $new_repeat)
            {
                echo "<b class=\"red_text\">新密码不匹配！</b>";
            }
            elseif ( strlen($new) < DATA['website']['registration']['pass_min_length'] || 
                    strlen($new) > DATA['website']['registration']['pass_max_length'] )
		    {
                echo "<b class=\"red_text\">
                        您的密码必须介于 ". DATA['website']['registration']['pass_min_length'] ." 
                        和 ". DATA['website']['registration']['pass_max_length'] ." 之间。
                    </b>";
		    }
	        else 
		    {
                //Lets check if the old password is correct!
                $username = $Database->conn->escape_string(strtoupper($_SESSION['cw_user']));

                $Database->selectDB("logondb");

				$data = mysqli_query("SELECT salt, verifier FROM account WHERE username = '".$username."'");
				$data = mysqli_fetch_assoc($data);
				$salt = $data['salt'];
				$verifier = $data['verifier'];

                if (!$Account->verifySRP6($username, $old, $salt, $verifier))
                {
                    echo "<b class=\"red_text\">
                            旧密码不正确！
                        </b>'";
			    }
			    else 
			    {
                    //success, change password
                    $data2 = $Account->getRegistrationData($username, $new);
					$salt2 = $data2[0];
					$verifier2 = $data2[1];
					mysqli_query("UPDATE account SET salt = '".$salt2."', verifier = '".$verifier2."' WHERE username = '".$username."'");
                    echo "<b class=\"green_text\">
                            您的密码已更改！
                        </b>";
                    setcookie("cw_rememberMe", $username.' * '.$new, time()+30758400);
				}
                $statement->close();
			}
		}
	}

    public function changePassword($account_name, $password)
    {
        global $Database;

        $username  = $Database->conn->escape_string(strtoupper($account_name));
        $pass      = $Database->conn->escape_string(strtoupper($password));

        $pass_hash = sha1($username .":". $pass);
	
        $Database->selectDB("logondb");

        $Database->update("account", array("sha_pass_hash" => $pass_hash), array("username" => $username));
        $Database->update("account", array("v" => 0,"s" => 0), array("username" => $username));

        $this->logThis("Changed password", "passwordchange", NULL);
    }

    public function forgotPW($account_name, $account_email)
    {
        global $Website, $Account, $Database;

        $accountName  = $Database->conn->escape_string($account_name);
        $accountEmail = $Database->conn->escape_string($account_email);

        if ( empty($accountName) || empty($accountEmail) )
        {
            echo "<b class=\"red_text\">请输入用户名和密码。</b>";
        }
        else
	    {
            $Database->selectDB("logondb");

            $statement = $Database->select("account", null, null, "username='$accountName' AND email='$accountEmail'");
            $result = $statement->get_result();

            if ( $result->num_rows == 0 )
		    {
                echo "<b class=\"red_text\">
                        用户名或电子邮件不正确。
                    </b>";
		    }
		    else 
		    {
                //Success, lets send an email & add the forgotpw thingy.
                $code = RandomString();

                $Website->sendEmail($accountEmail, DATA['website']['email'], "Forgot Password", "
    				您好。 <br/><br/>
    				已请求为该帐户重置密码 $accountName <br/>
    				如果您想重置密码，请点击以下链接：<br/>
    				<a href='". DATA['website']['domain'] ."?page=forgotpw&code=". $code ."&account=". $this->getAccountID($accountName) ."'>
    				". DATA['website']['domain'] ."?page=forgotpw&code=". $code ."&account=". $this->getAccountID($accountName) ."</a>
			
			<br/><br/>
			
			如果您没有请求此信息，请忽略此消息。<br/><br/>
			TBCstar 时光回溯团队");

                $account_id = $this->getAccountID($accountName);

                $Database->selectDB("webdb");

                $Database->conn->query("DELETE FROM password_reset WHERE account_id=". $account_id .";");
                $Database->insert("password_reset", array("code","account_id"), array($code, $account_id));

                echo "一封包含重置密码链接的电子邮件已发送至您指定的电子邮件地址。
				  如果您在此之前尝试发送其他忘记密码的请求，它们将不起作用。<br/>";
		    }
        }

        function hasVP($account_name, $points)
        {
            global $Database;

            $points         = $Database->conn->escape_string($points);
            $accountName    = $Database->conn->escape_string($account_name);

            $account_id = $this->getAccountID($accountName);

            $Database->selectDB("webdb");

            $statement = $Database->select("account_data", null, null, "vp >='$points' AND id='$account_id'");
            $result = $statement->get_result();

            if ( $result->num_rows == 0 )
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
            global $Database;

            $points         = $Database->conn->escape_string($points);
            $accountName    = $Database->conn->escape_string($account_name);

            $account_id = $this->getAccountID($accountName);

            $Database->selectDB("webdb");

            $statement = $Database->select("account_data", null, null, "dp >='$points' AND id='$account_id'");
            $result = $statement->get_result();

            if ( $result->num_rows == 0 )
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
            global $Database;

            $points = $Database->conn->escape_string($points);
            $account_id = $Database->conn->escape_string($account_id);

            $Database->selectDB("webdb");

            $Database->update("account_data", array("vp" => "vp-$points"), array("id" => $account_id));
        }

        function deductDP($account_id, $points)
        {
            global $Database;

            $points = $Database->conn->escape_string($points);
            $accountId = $Database->conn->escape_string($account_id);

            $Database->selectDB("webdb");

            $Database->update("account_data", array("dp" => "dp-$points"), array("id"=> $account_id));
        }

        function addDP($account_id, $points)
        {
            global $Database;

            $account_id = $Database->conn->escape_string($account_id);
            $points = $Database->conn->escape_string($points);

            $Database->selectDB("webdb");

            $Database->update("account_data", array("dp" => "dp+$points"), array("id" =>$account_id));
        }

        function addVP($account_id, $points)
        {
            global $Database;

            $account_id = $Database->conn->escape_string($account_id);
            $points = $Database->conn->escape_string($points);

            $Database->selectDB("webdb");

            $Database->update("account_data", array("dp" => "dp+$points"), array("id" => $account_id));
        }

        function getAccountIDFromCharId($char_id, $realm_id)
        {
            global $Database;

            $charId = $Database->conn->escape_string($char_id);
            $realmId = $Database->conn->escape_string($realm_id);

            $Database->selectDB("webdb");
            $Database->realm($realmId);

            $statement = $Database->select("characters", "account", null, "guid='$charId'");
            $result = $statement->get_result();
            $row = $result->fetch_assoc();

            return $row['account'];
        }

        function isGM($account_name)
        {
            global $Database;

            $accountName = $Database->conn->escape_string($account_name);

            $account_id  = $this->getAccountID($accountName);

            $statement = $Database->select("account_access", "COUNT(id) AS gm", null, "id='$account_id' AND gmlevel >= 1");
            $result = $statement->get_result();
            if ( $result->fetch_assoc()['gm'] > 0 )
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
            global $Database;

            $desc    = $Database->conn->escape_string($desc);
            $realmid = $Database->conn->escape_string($realmid);
            $service = $Database->conn->escape_string($service);
            $account = $Database->conn->escape_string($_SESSION['cw_user_id']);

            $Database->selectDB("webdb");

            $Database->insert("user_log", array("account", "service", "timestamp", "ip", "realmid", "desc"), array($account, $service, time(), $_SERVER['REMOTE_ADDR'], $realmid, $desc));
        }
    }
}

$Account = new Account();