<?php

/**
 * Account Class 
 */
 
class Account
{
    public function logIn($username, $password, $last_page, $remember)
    {
        if ( empty($username) || empty($password) )
        {
            echo "<span class=\"red_text\">Please enter both fields.</span>";
            exit;
        }
        global $Database;

        $username   = $Database->conn->escape_string( trim( strtoupper($username) ) );
        $password   = $Database->conn->escape_string( trim( strtoupper($password) ) );

        $Database->selectDB("logondb", $Database->conn);

        $checkForAccount = $Database->select("account", null, null, "username='$username'");

        if ( $checkForAccount->num_rows == 0 )
        {
            echo "<span class=\"red_text\">Invalid username.</span>";
        }
        else
    	{
            if ( $remember != 835727313 )
		{
            $password = sha1( $username .":". $password );
		}

            $statement = $Database->select("account", "id", null, "username='$username' AND sha_pass_hash='$password'");
            $result = $statement->get_result();
            if ( $result->num_rows == 0 )
            {
                echo "<span class=\"red_text\">Wrong password.</span>";
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

    public function register($username, $email, $password, $repeat_password, $captcha, $raf)
    {
        $errors = array();

        if ( empty($username) )
            $errors[] = "Enter a username.";

        if ( empty($email) )
            $errors[] = "Enter an email address.";

        if ( empty($password) )
            $errors[] = "Enter a password.";

        if ( empty($repeat_password) )
            $errors[] = "Enter the password repeat.";

        if ( $username == $password )
        {
            $errors[] = "Your password cannot be your username!";
        }
        else
        {
            session_start();
            if ( DATA['website']['registration']['captcha'] == TRUE && defined("CAPTCHA_VALUE") )
		    {
                if ( $captcha != CAPTCHA_VALUE )
			    { 
                    $errors[] = "The captcha is incorrect!";
		    	}
            }

            if ( strlen($username) > DATA['website']['registration']['user_max_length'] || 
                strlen($username) < DATA['website']['registration']['user_min_length'] )
                $errors[] = "The username must be between ". DATA['website']['registration']['user_minlength'] ." and ". DATA['website']['registration']['user_max_length'] ." letters.";

            if ( strlen($password) > DATA['website']['registration']['pass_max_length'] || 
                strlen($password) < DATA['website']['registration']['pass_min_length'] )
                $errors[] = "The password must be between ". DATA['website']['registration']['pass_min_length'] ." and ". DATA['website']['registration']['pass_max_length'] ." letters.";
				
            if ( DATA['website']['registration']['validate_email'] == TRUE )
            {
                if ( filter_var($email, FILTER_VALIDATE_EMAIL) === false )
			    {
                    $errors[] = "Enter a valid email address.";
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
            $errors[] = "The username already exists!";
        }

        if ( $password != $repeat_password )
        {
            $errors[] = "The passwords does not match!";
        }

        if ( !empty($errors) )
        {
            //errors found.
            echo "<p><h4>The following errors occured:</h4>";
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
            $password = sha1( $username .":". $password );

            if ( empty($raf) )
            {
                $raf = 0;
            }

            $Database->selectDB("logondb");

            $result = $Database->insert("account", array("username", "email", "sha_pass_hash", "joindate", "expansion", "recruiter"), array($username, $email, $password, date("Y-m-d H:i:s"), DATA['website']['core_expansion']), $raf)->get_result();

            if ( !$result )
            {
                buildError("Could not create user!", null, $Database->conn->error);
            }

            $statement = $Database->select("account", "id", null, "username='$username'");
            $row = $statement->get_result()->fetch_assoc();

            $Database->selectDB("webdb");

            $Database->insert("account_data", "id", $row['id']);

            $Database->selectDB("logondb");
            $result = $Database->select("account", "id", null, "username='$username_clean'");
            $id     = $result->fetch_assoc();
            $id     = $id['id'];

            $this->GMLogin($username_clean);

            $_SESSION['cw_user']    = ucfirst(strtolower($username_clean));
            $_SESSION['cw_user_id'] = $id;

            #$this->forumRegister($username_clean, $password_clean, $email);
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
            echo '<span class="yellow_text">Banned<br/>
				  Reason: ' . $row['banreason'] . '<br/>
				  Time left: ' . $duration . '</span>';
	    }
        else
	    {
            echo '<b class="green_text">Active</b>';
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
            return '<b class="red_text">Offline</b>';
        }
        else
        {
            return '<b class="green_text">Online</b>';
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
                echo "<option value=\"\">No characters found!</option>";
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
            $errors[] = 'Please enter your current password';
        }
        else
        {
            if ( empty($email) )
		    {
			    $errors[] = 'Please enter an email address.';
		    }

            global $Database;

            $Database->selectDB("logondb");

            $username = $Database->conn->escape_string( trim( strtoupper( $_SESSION['cw_user'] ) ) );
            $password = $Database->conn->escape_string( trim( strtoupper( $current_pass ) ) );

            $password = sha1($username .":". $password);

            $statement = $Database->select("account", "COUNT(id) AS id", null, "username='$username' AND sha_pass_hash='$password'");
            $result = $statement->get_result();
            if ( $result->num_rows == 0 )
            {
                $errors[] = "The current password is incorrect.";
            }

            if ( DATA['website']['registration']['validate_email'] == TRUE )
            {
                if ( filter_var($email, FILTER_VALIDATE_EMAIL) === false )
			    {
                    $errors[] = 'Enter a valid email address.';
			    }
                else
			    {
                    $Database->update("account", array("email"=> $email), array("username" => $_SESSION['cw_user']));
			    }
		    }
        }

        if ( empty($errors) )
        {
            echo "Successfully updated your account.";
        }
        else
        {
            echo "<div class=\"news\" style=\"padding: 5px;\">
            <h4 class=\"red_text\">The following errors occured:</h4>";
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

        $_POST['current_password']    = $Database->conn->escape_string($old);
        $_POST['new_password']        = $Database->conn->escape_string($new);
        $_POST['new_password_repeat'] = $Database->conn->escape_string($new_repeat);
		
        //Check if all field values has been typed into
        if ( empty($_POST['current_password']) || 
            empty($_POST['new_password']) || 
            empty($_POST['new_password_repeat']) )
        {
            echo "<b class=\"red_text\">Please type in all fields!</b>";
        }
        else
        {
            //Check if new passwords match?
            if ( $_POST['new_password'] !== $_POST['new_password_repeat'] )
            {
                echo "<b class=\"red_text\">The new passwords doesnt match!</b>";
            }
            elseif ( strlen($_POST['new_password']) < DATA['website']['registration']['pass_min_length'] || 
                    strlen($_POST['new_password']) > DATA['website']['registration']['pass_max_length'] )
		    {
                echo "<b class=\"red_text\">
                        Your password must be between ". DATA['website']['registration']['pass_min_length'] ." 
                        and ". DATA['website']['registration']['pass_max_length'] ." letters.
                    </b>";
		    }
	        else 
		    {
                //Lets check if the old password is correct!
                $username = $Database->conn->escape_string(strtoupper($_SESSION['cw_user']));

                $Database->selectDB("logondb");

                $statement = $Database->select("account", "sha_pass_hash", null, "username='$username'");
                $getPass = $statement->get_result();

                $row     = $getPass->fetch_assoc();
                $thePass = $row['sha_pass_hash'];

                $pass      = $Database->conn->escape_string(strtoupper($_POST['current_password']));

                $pass_hash = sha1($username .":". $pass);

                $new_password      = $Database->conn->escape_string(strtoupper($_POST['new_password']));
                $new_password_hash = sha1($username .":". $new_password);

                if ( $thePass != $pass_hash )
                {
                    echo "<b class=\"red_text\">
                            The old password is not correct!
                        </b>'";
			    }
			    else 
			    {
                    //success, change password
                    echo "<b class=\"green_text\">
                            Your Password was changed!
                        </b>";
                    $Database->update("account", array("sha_pass_hash" => $new_password_hash), array("username" => $username));
                    $Database->update("account", array("v" => 0,"s" => 0), array("username" => $username));
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
            echo "<b class=\"red_text\">Please enter both fields.</b>";
        }
        else
	    {
            $Database->selectDB("logondb");

            $statement = $Database->select("account", null, null, "username='$accountName' AND email='$accountEmail'");
            $result = $statement->get_result();

            if ( $result->num_rows == 0 )
		    {
                echo "<b class=\"red_text\">
                        The username or email is incorrect.
                    </b>";
		    }
		    else 
		    {
                //Success, lets send an email & add the forgotpw thingy.
                $code = RandomString();

                $Website->sendEmail($accountEmail, DATA['website']['email'], "Forgot Password", "
    				Hello there. <br/><br/>
    				A password reset has been requested for the account $accountName <br/>
    				If you wish to reset your password, click the following link: <br/>
    				<a href='". DATA['website']['domain'] ."?page=forgotpw&code=". $code ."&account=". $this->getAccountID($accountName) ."'>
    				". DATA['website']['domain'] ."?page=forgotpw&code=". $code ."&account=". $this->getAccountID($accountName) ."</a>
			
			<br/><br/>
			
			If you did not request this, just ignore this message.<br/><br/>
			Sincerely, The Management.");

                $account_id = $this->getAccountID($accountName);

                $Database->selectDB("webdb");

                $Database->conn->query("DELETE FROM password_reset WHERE account_id=". $account_id .";");
                $Database->insert("password_reset", array("code","account_id"), array($code, $account_id));

                echo "An email containing a link to reset your password has been sent to the Email address you specified. 
				  If you've tried to send other forgot password requests before this, they won't work. <br/>";
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