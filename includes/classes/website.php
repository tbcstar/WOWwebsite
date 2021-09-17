<?php
########################
## 包含网站功能的脚本将在这里添加。例如新闻。
#######################

    class Website
    {
        public function __construct()
        {
            $_GET = filter_input_array(
                INPUT_GET,
                array
                (
                    'account' => FILTER_SANITIZE_ENCODED,
                    'action' => FILTER_SANITIZE_ENCODED,
                    'c' => FILTER_SANITIZE_ENCODED,
                    'char' => FILTER_SANITIZE_ENCODED,
                    'code' => FILTER_SANITIZE_ENCODED,
                    'db' => FILTER_SANITIZE_ENCODED,
                    'error' => FILTER_SANITIZE_ENCODED,
                    'editaccount' => FILTER_SANITIZE_ENCODED,
                    'f' => FILTER_SANITIZE_ENCODED,
                    'filename' => FILTER_SANITIZE_ENCODED,
                    'getlogs' => FILTER_SANITIZE_ENCODED,
                    'guid' => FILTER_SANITIZE_ENCODED,
                    'id' => FILTER_SANITIZE_ENCODED,
                    'ilfrom' => FILTER_SANITIZE_ENCODED,
                    'ilto' => FILTER_SANITIZE_ENCODED,
                    'last_page' => FILTER_SANITIZE_ENCODED,
                    'newsid' => FILTER_SANITIZE_ENCODED,
                    'page' => FILTER_SANITIZE_ENCODED,
                    'plugin' => FILTER_SANITIZE_ENCODED,
                    'q' => FILTER_SANITIZE_ENCODED,
                    'r' => FILTER_SANITIZE_ENCODED,
                    'return' => FILTER_SANITIZE_ENCODED,
                    'rid' => FILTER_SANITIZE_ENCODED,
                    's' => FILTER_SANITIZE_ENCODED,
                    'search' => FILTER_SANITIZE_ENCODED,
                    'search_value' => FILTER_SANITIZE_ENCODED,
                    'service' => FILTER_SANITIZE_ENCODED,
                    'step' => FILTER_SANITIZE_ENCODED,
                    't' => FILTER_SANITIZE_ENCODED,
                    'user' => FILTER_SANITIZE_ENCODED
                )
            );

        $_POST = filter_input_array(
            INPUT_POST,
            array(
                'account' => FILTER_SANITIZE_STRING,
                'action' => FILTER_SANITIZE_STRING,
                'addSlideImage' => FILTER_SANITIZE_STRING,
                'add_realm' => FILTER_SANITIZE_STRING,
                'address_city' => FILTER_SANITIZE_STRING,
                'address_country' => FILTER_SANITIZE_STRING,
                'alert_enalbe' => FILTER_SANITIZE_STRING,
                'alert_message' => FILTER_SANITIZE_STRING,
                'ap_additionalcharges' => FILTER_SANITIZE_STRING,
                'ap_amount' => FILTER_SANITIZE_STRING,
                'ap_currency' => FILTER_SANITIZE_STRING,
                'ap_custaddress' => FILTER_SANITIZE_STRING,
                'ap_custcity' => FILTER_SANITIZE_STRING,
                'ap_custcountry' => FILTER_SANITIZE_STRING,
                'ap_custemailaddress' => FILTER_SANITIZE_STRING,
                'ap_custfirstname' => FILTER_SANITIZE_STRING,
                'ap_custlastname' => FILTER_SANITIZE_STRING,
                'ap_custstate' => FILTER_SANITIZE_STRING,
                'ap_custzip' => FILTER_SANITIZE_STRING,
                'ap_description' => FILTER_SANITIZE_STRING,
                'ap_discounntamount' => FILTER_SANITIZE_STRING,
                'ap_feeamount' => FILTER_SANITIZE_STRING,
                'ap_itemcode' => FILTER_SANITIZE_STRING,
                'ap_itemname' => FILTER_SANITIZE_STRING,
                'ap_merchant' => FILTER_SANITIZE_STRING,
                'ap_netamount' => FILTER_SANITIZE_STRING,
                'ap_purchasetype' => FILTER_SANITIZE_STRING,
                'ap_quantity' => FILTER_SANITIZE_STRING,
                'ap_referencenumber' => FILTER_SANITIZE_STRING,
                'ap_securitycode' => FILTER_SANITIZE_STRING,
                'ap_shippingcharges' => FILTER_SANITIZE_STRING,
                'ap_status' => FILTER_SANITIZE_STRING,
                'ap_taxamount' => FILTER_SANITIZE_STRING,
                'ap_test' => FILTER_SANITIZE_STRING,
                'ap_totalamount' => FILTER_SANITIZE_STRING,
                'ap_transactiondate' => FILTER_SANITIZE_STRING,
                'ap_transactiontype' => FILTER_SANITIZE_STRING,
                'apc_1' => FILTER_SANITIZE_STRING,
                'apc_2' => FILTER_SANITIZE_STRING,
                'apc_3' => FILTER_SANITIZE_STRING,
                'apc_4' => FILTER_SANITIZE_STRING,
                'apc_5' => FILTER_SANITIZE_STRING,
                'apc_6' => FILTER_SANITIZE_STRING,
                'author' => FILTER_SANITIZE_STRING,
                'captcha' => FILTER_SANITIZE_STRING,
                'cart' => FILTER_SANITIZE_STRING,
                'change_password' => FILTER_SANITIZE_STRING,
                'char_db' => FILTER_SANITIZE_STRING,
                'character' => FILTER_SANITIZE_STRING,
                'character_checked' => FILTER_SANITIZE_STRING,
                'character_database' => FILTER_SANITIZE_STRING,
                'character_host' => FILTER_SANITIZE_STRING,
                'character_password' => FILTER_SANITIZE_STRING,
                'character_port' => FILTER_SANITIZE_STRING,
                'character_realm' => FILTER_SANITIZE_STRING,
                'character_user' => FILTER_SANITIZE_STRING,
                'chardb' => FILTER_SANITIZE_STRING,
                'check' => FILTER_SANITIZE_STRING,
                'class' => FILTER_SANITIZE_STRING,
                'comment' => FILTER_SANITIZE_STRING,
                'content' => FILTER_SANITIZE_STRING,
                'conv_vp' => FILTER_SANITIZE_STRING,
                'convert' => FILTER_SANITIZE_STRING,
                'convertDonnationList' => FILTER_SANITIZE_STRING,
                'currency' => FILTER_SANITIZE_STRING,
                'current_pass' => FILTER_SANITIZE_STRING,
                'current_password' => FILTER_SANITIZE_STRING,
                'custom' => FILTER_SANITIZE_STRING,
                'db' => FILTER_SANITIZE_STRING,
                'domain' => FILTER_SANITIZE_STRING,
                'dp' => FILTER_SANITIZE_STRING,
                'editpage_content' => FILTER_SANITIZE_STRING,
                'editpage_filename' => FILTER_SANITIZE_STRING,
                'editpage_name' => FILTER_SANITIZE_STRING,
                'element' => FILTER_SANITIZE_STRING,
                'email' => FILTER_SANITIZE_STRING,
                'enabled' => FILTER_SANITIZE_STRING,
                'entry' => FILTER_SANITIZE_STRING,
                'expansion' => FILTER_SANITIZE_STRING,
                'first_name' => FILTER_SANITIZE_STRING,
                'foldername' => FILTER_SANITIZE_STRING,
                'forgot_email' => FILTER_SANITIZE_STRING,
                'forgot_username' => FILTER_SANITIZE_STRING,
                'forgotpw' => FILTER_SANITIZE_STRING,
                'function' => FILTER_SANITIZE_STRING,
                'gender' => FILTER_SANITIZE_STRING,
                'getRefundPolicy' => FILTER_SANITIZE_STRING,
                'getTos' => FILTER_SANITIZE_STRING,
                'guid' => FILTER_SANITIZE_STRING,
                'host' => FILTER_SANITIZE_STRING,
                'id' => FILTER_SANITIZE_STRING,
                'il_from' => FILTER_SANITIZE_STRING,
                'il_to' => FILTER_SANITIZE_STRING,
                'image' => FILTER_SANITIZE_STRING,
                'input' => FILTER_SANITIZE_STRING,
                'ir_char' => FILTER_SANITIZE_STRING,
                'ir_instance' => FILTER_SANITIZE_STRING,
                'ir_realm' => FILTER_SANITIZE_STRING,
                'ir_step1' => FILTER_SANITIZE_STRING,
                'ir_step2' => FILTER_SANITIZE_STRING,
                'ir_step3' => FILTER_SANITIZE_STRING,
                'item_entry' => FILTER_SANITIZE_STRING,
                'item_number' => FILTER_SANITIZE_STRING,
                'last_name' => FILTER_SANITIZE_STRING,
                'location' => FILTER_SANITIZE_STRING,
                'login' => FILTER_SANITIZE_STRING,
                'login_password' => FILTER_SANITIZE_STRING,
                'login_remember' => FILTER_SANITIZE_STRING,
                'login_username' => FILTER_SANITIZE_STRING,
                'logon_checked' => FILTER_SANITIZE_STRING,
                'logon_database' => FILTER_SANITIZE_STRING,
                'logon_host' => FILTER_SANITIZE_STRING,
                'logon_password' => FILTER_SANITIZE_STRING,
                'logon_port' => FILTER_SANITIZE_STRING,
                'logon_user' => FILTER_SANITIZE_STRING,
                'mc_fee' => FILTER_SANITIZE_STRING,
                'mc_gross' => FILTER_SANITIZE_STRING,
                'money' => FILTER_SANITIZE_STRING,
                'name' => FILTER_SANITIZE_STRING,
                'new_id' => FILTER_SANITIZE_STRING,
                'new_password' => FILTER_SANITIZE_STRING,
                'new_password_repeat' => FILTER_SANITIZE_STRING,
                'newpage' => FILTER_SANITIZE_STRING,
                'newpage_content' => FILTER_SANITIZE_STRING,
                'newpage_filename' => FILTER_SANITIZE_STRING,
                'newpage_name' => FILTER_SANITIZE_STRING,
                'offline' => FILTER_SANITIZE_STRING,
                'panel' => FILTER_SANITIZE_STRING,
                'pass' => FILTER_SANITIZE_STRING,
                'password' => FILTER_SANITIZE_STRING,
                'password_repeat' => FILTER_SANITIZE_STRING,
                'path' => FILTER_SANITIZE_STRING,
                'payer_email' => FILTER_SANITIZE_STRING,
                'payment_date' => FILTER_SANITIZE_STRING,
                'payment_status' => FILTER_SANITIZE_STRING,
                'payment_type' => FILTER_SANITIZE_STRING,
                'paypal' => FILTER_SANITIZE_STRING,
                'points' => FILTER_SANITIZE_STRING,
                'port' => FILTER_SANITIZE_STRING,
                'price' => FILTER_SANITIZE_STRING,
                'quality' => FILTER_SANITIZE_STRING,
                'quantity' => FILTER_SANITIZE_STRING,
                'race' => FILTER_SANITIZE_STRING,
                'rank' => FILTER_SANITIZE_STRING,
                'realm' => FILTER_SANITIZE_STRING,
                'realm_a_host' => FILTER_SANITIZE_STRING,
                'realm_a_pass' => FILTER_SANITIZE_STRING,
                'realm_a_user' => FILTER_SANITIZE_STRING,
                'realm_access_password' => FILTER_SANITIZE_STRING,
                'realm_access_username' => FILTER_SANITIZE_STRING,
                'realm_chardb' => FILTER_SANITIZE_STRING,
                'realm_desc' => FILTER_SANITIZE_STRING,
                'realm_description' => FILTER_SANITIZE_STRING,
                'realm_host' => FILTER_SANITIZE_STRING,
                'realm_name' => FILTER_SANITIZE_STRING,
                'realm_port' => FILTER_SANITIZE_STRING,
                'realm_ra_port' => FILTER_SANITIZE_STRING,
                'realm_rank_password' => FILTER_SANITIZE_STRING,
                'realm_rank_username' => FILTER_SANITIZE_STRING,
                'realm_sendtype' => FILTER_SANITIZE_STRING,
                'realm_soap_port' => FILTER_SANITIZE_STRING,
                'realmlist' => FILTER_SANITIZE_STRING,
                'receiver_email' => FILTER_SANITIZE_STRING,
                'referer' => FILTER_SANITIZE_STRING,
                'register' => FILTER_SANITIZE_STRING,
                'rid' => FILTER_SANITIZE_STRING,
                'save' => FILTER_SANITIZE_STRING,
                'send_mode' => FILTER_SANITIZE_STRING,
                'serverStatus' => FILTER_SANITIZE_STRING,
                'service' => FILTER_SANITIZE_STRING,
                'shop' => FILTER_SANITIZE_STRING,
                'siteid' => FILTER_SANITIZE_STRING,
                'slideImage_path' => FILTER_SANITIZE_STRING,
                'slideImage_url' => FILTER_SANITIZE_STRING,
                'step' => FILTER_SANITIZE_STRING,
                'submit' => FILTER_SANITIZE_STRING,
                'test' => FILTER_SANITIZE_STRING,
                'text' => FILTER_SANITIZE_STRING,
                'title' => FILTER_SANITIZE_STRING,
                'txn_id' => FILTER_SANITIZE_STRING,
                'type' => FILTER_SANITIZE_STRING,
                'update_alert' => FILTER_SANITIZE_STRING,
                'url' => FILTER_SANITIZE_STRING,
                'user' => FILTER_SANITIZE_STRING,
                'username' => FILTER_SANITIZE_STRING,
                'value' => FILTER_SANITIZE_STRING,
                'values' => FILTER_SANITIZE_STRING,
                'vp' => FILTER_SANITIZE_STRING,
                'web_database' => FILTER_SANITIZE_STRING,
                'web_host' => FILTER_SANITIZE_STRING,
                'web_password' => FILTER_SANITIZE_STRING,
                'web_port' => FILTER_SANITIZE_STRING,
                'web_user' => FILTER_SANITIZE_STRING,
                'world_checked' => FILTER_SANITIZE_STRING,
                'world_database' => FILTER_SANITIZE_STRING,
                'world_host' => FILTER_SANITIZE_STRING,
                'world_password' => FILTER_SANITIZE_STRING,
                'world_port' => FILTER_SANITIZE_STRING,
                'world_user' => FILTER_SANITIZE_STRING,
                'x_login' => FILTER_SANITIZE_STRING,
                'x_password' => FILTER_SANITIZE_STRING,
                'x_redirect' => FILTER_SANITIZE_STRING,
                'x_remember' => FILTER_SANITIZE_STRING,
                'x_username' => FILTER_SANITIZE_STRING,
                'editpage' => FILTER_SANITIZE_STRING
                )
            );
        }

        public function getNews()
        {
            global $Cache, $Database, $Website;

            if ( DATA['website']['news']['enable'] == TRUE )
            {
                echo "<div class='box_two_title'>最近新闻</div>";

                $Database->selectDB("webdb");

                $result = $Database->select("news", null, null, null, "ORDER BY id DESC LIMIT ". DATA['website']['news']['max_shown'])->get_result();

                if ($result->num_rows == 0)
                {
                    echo "没有找到任何消息。";
                }
                else
                {
                $output = null;
                while ($row = $result->fetch_assoc())
                    {
                        if (file_exists($row['image']))
                        {
                            echo $newsPT1 = "
					       <table class='news' width='100%'>
						        <tr>
								    <td>
                                        <h3 class='yellow_text'>". 
                                            $row['title'] 
                                        ."</h3>
                                    </td>
							    </tr>
						   </table>
                           <table class='news_content' cellpadding='4'> 
						       <tr>
						          <td><img src='". $row['image'] ."' alt=''/></td> 
						          <td>";
                        }
                        else
                        {
                            echo $newsPT1 = "
					       <table class='news' width='100%'> 
						        <tr>
								    <td>
                                        <h3 class='yellow_text'>". 
                                            $row['title'] 
                                        ."</h3>
                                    </td>
							    </tr>
						   </table>";
                        }
                        $output .= $newsPT1;
                        unset($newsPT1);

                        if (file_exists("includes/classes/validator.php"))
                        {
                            include "includes/classes/validator.php";

                            $Validator = new Validator(array(), array($row['body']), array($row['body']));
                            $sanatized_text = $Validator->sanatize($row['body'], "string");

                            if ( DATA['website']['news']['limit_home_characters'] == TRUE )
                            {
                                echo $Website->limit_characters($sanatized_text, 200);
                                $output .= $Website->limit_characters($row['body'], 200);
                            }
                            else
                            {
                                echo nl2br("<br>".$sanatized_text);
                                $output .= nl2br($row['body']);
                            }

                            $result = $Database->select("news_comments", "COUNT(id)", null, "newsid=". $row['id'])->get_result();
                            $commentsNum = $result->fetch_row();

                            if ( DATA['website']['news']['enable_comments'] == true )
                            {
                                $comments = '| <a href="?page=news&amp;newsid=' . $row['id'] . '">Comments ('. $commentsNum[0] .')</a>';
                            }
                            else
                            {
                                $comments = "";
                            }

                            echo $newsPT2 = "<br/><br/><br/>
                                <i class='gray_text'>Written by ". $row['author'] ." | ". $row['date'] ." ". $comments ."</i>
                                </td> 
                                </tr>
                                </table";
                            $output  .= $newsPT2;
                            unset($newsPT2);
                        }
                    }
                    echo "<br><hr/><a href='?page=news'>查看旧新闻...</a>";
                }
            }
        }

        public function getSlideShowImages()
        {
            global $Cache, $Database;
            
            if ($Cache->exists("slideshow") == TRUE)
            {
                $Cache->loadCache("slideshow");
            }
            else
            {
                $Database->selectDB("webdb");
                $result = $Database->select("slider_images", "path,link", null, null, "ORDER BY position ASC")->get_result();
                while ($row = $result->fetch_assoc())
                {
                    echo $outPutPT = '<a href="'. htmlspecialchars($row['link']) .'"><img border="none" src="'. htmlspecialchars($row['path']) .'" alt="" class="slideshow_image"></a>';
                    $output   .= $outPutPT;
                }
                $Cache->buildCache('slideshow', $output);
            }
        }

        public function getSlideShowImageNumbers()
        {
            global $Database;
            $Database->selectDB("webdb");

            $result = $Database->select("slider_images", "position", null, null, "ORDER BY position ASC")->get_result();
            $x      = 1;

            while ($row = $result->fetch_assoc())
            {
                echo '<a href="#" rel="'. htmlspecialchars($x) .'">'. htmlspecialchars($x) .'</a>';
                $x++;
            }

            unset($x);
        }

        public function limit_characters($str, $n)
        {
            $str = preg_replace("/<img[^>]+\>/i", "(image)", $str);

            if (strlen($str) <= $n)
            {
                return $str;
            }
            else
            {
                return substr($str, 0, $n) . '...';
            }
        }

        public function loadVotingLinks()
        {
            global $Database, $Account, $Website;
            $Database->selectDB("webdb");

            $result = $Database->select("votingsites", null, null, null, "ORDER BY id DESC")->get_result();

            if ($result->num_rows == 0)
            {
                buildError("无法从数据库中获取任何投票链接. ". $Database->conn->error);
            }
            else
            {
                while ($row = $result->fetch_assoc())
                { ?>

				<div class="col">
				<div class="item">
				<img src="/themes/cp_nefelin/images/vote-bg.jpg" alt="" /> <div class="item-content">
				<h3><?php echo $row['title']; ?></strong></h3>
				<div>

				</div>

				<div class="bonus"><?php if($Website->checkIfVoted($row['id'])==FALSE) {?> 

				<?php
				}
				else 
				{
				$getNext = $Database->select(DATA['website']['connection']['name'].".votelog", "next_vote", null, " WHERE userid=". $Account->getAccountID($_SESSION['cw_user']) ." AND siteid=". $row['id'] ." ORDER BY id DESC LIMIT 1;")->get_result();

                $row  = $getNext->fetch_assoc();
				$time = $row['next_vote'] - time();

				echo '<font color="red">'.convTime($time);
				}
				?></font><br><br><span class="coin-silver"></span> <span class="numbers"></span> 2点积分</div>
				<input type='submit' target='_blank' class='btn btn-low-green' value='Vote'  onclick="vote('<?php echo $row['id']; ?> ',this)">
				</div>
				</div>
				</div>

				<?php
				}
			}
		}

        public function checkIfVoted($siteid)
        {
            global $Account, $Database;

            $siteId  = $Database->conn->escape_string($siteid);

            $acct_id = $Account->getAccountID($_SESSION['cw_user']);
            $Database->selectDB("webdb");

            $result = $Database->select("votelog", "COUNT(id) AS voted", null, "userid=". $acct_id ." AND siteid=". $siteId ." AND next_vote > ". time())->get_result();

            if ($result->fetch_assoc()['voted'] == 0)
            {
                return false;
            }
            else
            {
                return TRUE;
            }
        }

        public function sendEmail($to, $from, $subject, $body)
        {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= 'From: ' . $from . "\r\n";

            mail($to, $subject, $body, $headers);
        }

        public function convertCurrency($currency)
        {
            if ($currency == "dp") return DATA['website']['donation']['coins_name'];
            elseif ($currency == "vp") return "Vote Points";
        }

        public function getTitle()
        {
            echo DATA['website']['title'] ." - ";

            foreach (DATA['website']['core_pages'] as $key => $value)
            {
                if ( $value == $_GET['page'].".php" )
                {
                    $value = str_replace(".php", "", $value);
                    echo ucfirst($value);
                    $foundPT = true;
                }
            }

            if ( !isset( $foundPT ) )
            {
                echo htmlentities( ucfirst( $_GET['page'] ) );
            }
        }

    }
    $Website = new Website();
