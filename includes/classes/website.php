<?php
########################
## 包含网站功能的脚本将在这里添加。例如新闻。
#######################

    class Website
    {

        public function getNews()
        {

            global $Cache, $Connect, $Website;
            $conn = $Connect->connectToDB();

            if ($GLOBALS['news']['enable'] == true)
            {
                echo "<div class='box_two_title'>最近新闻</div>";

                if ($Cache->exists("news") == true)
                {
                    $Cache->loadCache("news");
                }
                else
                {
                    $Connect->selectDB("webdb", $conn);

                    $result = $conn->query("SELECT * FROM news ORDER BY id DESC LIMIT ". $GLOBALS['news']['maxShown'] .";");

                    if ($result->num_rows == 0)
                    {
                        echo "没有发现任何新闻。";
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

                                if ($GLOBALS['news']['limitHomeCharacters'] == true)
                                {
                                    echo $Website->limit_characters($sanatized_text, 200);
                                    $output .= $Website->limit_characters($row['body'], 200);
                                }
                                else
                                {
                                    echo nl2br("<br>".$sanatized_text);
                                    $output .= nl2br($row['body']);
                                }

                                $result      = $conn->query("SELECT COUNT(id) FROM news_comments WHERE newsid=". $row['id'] .";");
                                $commentsNum = $result->fetch_row();

                                if ($GLOBALS['news']['enableComments'] == true)
                                {
                                    $comments = '| <a href="?p=news&amp;newsid=' . $row['id'] . '">Comments ('. $commentsNum[0] .')</a>';
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
                        echo "<br><hr/><a href='?p=news'>查看旧的新闻...</a>";
                        $Cache->buildCache("news", $output);
                    }
                }
            }

        }

        public function getSlideShowImages()
        {
            global $Cache, $Connect;
            $conn = $Connect->connectToDB();
            
            if ($Cache->exists("slideshow") == true)
            {
                $Cache->loadCache("slideshow");
            }
            else
            {
                $Connect->selectDB("webdb", $conn);
                $result = $conn->query("SELECT `path`, `link` FROM slider_images ORDER BY position ASC;");
                while ($row = $result->fetch_assoc())
                {
                    echo $outPutPT = '<a href="'. $row['link'] .'"><img border="none" src="'. $row['path'] .'" alt="" class="slideshow_image"></a>';
                    $output   .= $outPutPT;
                }
                $Cache->buildCache('slideshow', $output);
            }
        }

        public function getSlideShowImageNumbers()
        {
            global $Connect;
            $conn = $Connect->connectToDB();
            $Connect->selectDB("webdb", $conn);

            $result = $conn->query("SELECT `position` FROM slider_images ORDER BY position ASC;");
            $x      = 1;

            while ($row = $result->fetch_assoc())
            {
                echo '<a href="#" rel="' . $x . '">' . $x . '</a>';
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
            global $Connect, $Account, $Website;
            $conn = $Connect->connectToDB();
            $Connect->selectDB("webdb", $conn);

            $result = $conn->query("SELECT * FROM votingsites ORDER BY id DESC;");

            if ($result->num_rows == 0)
            {
                buildError("无法从数据库中获取任何投票链接. ". $conn->error);
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
				$getNext = $conn->query("SELECT next_vote FROM ". $GLOBALS['connection']['webdb'] .".votelog 
					WHERE userid=". $Account->getAccountID($_SESSION['cw_user']) ." 
					AND siteid=". $row['id'] ." ORDER BY id DESC LIMIT 1;");

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
            global $Account, $Connect;
            $conn = $Connect->connectToDB();

			$db = $GLOBALS['connection']['webdb'];
            $siteId  = $conn->escape_string($siteid);

            $acct_id = $Account->getAccountID($_SESSION['cw_user']);
            $Connect->selectDB("webdb", $conn);

            $result = $conn->query("SELECT COUNT(id) AS voted FROM votelog WHERE userid=". $acct_id ." AND siteid=". $siteId ." AND next_vote > ". time() .";");

            if ($result->fetch_assoc()['voted'] == 0)
            {
                return false;
            }
            else
            {
                return true;
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
            if ($currency == "dp") return $GLOBALS['donation']['coins_name'];
            elseif ($currency == "vp") return "Vote Points";
        }

    }
    $Website = new Website();
