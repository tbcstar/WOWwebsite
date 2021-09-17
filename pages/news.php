<div id="overlay"></div>
<div id="wrapper">

<?php include "header.php" ?>

<div id="leftcontent">
<div class="box_two">
<?php
if ( isset($_GET['newsid']) )
{
    global $Database, $Website;
    $id = $Database->conn->escape_string($_GET['newsid']);
    $Database->selectDB("webdb");

	$result = $Database->select("news", null, null, "id=$id")->get_result();
	$row = $result->fetch_assoc(); ?>
    <div class='box_two_title'><?php echo $row['title']; ?></div>
    <?php 
	$text = preg_replace("
	  #((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
	  "'<a href=\"$1\" target=\"_blank\">http://$3</a>$4'",
	  $row['body']
	);
	echo nl2br($text); ?> 
   
    <br/><br/>
    <span class='yellow_text'>作者 <b><?php echo $row['author'];?></b> | <?php echo $row['date']; ?></span>
    <?php
    if ( DATA['website']['news']['enable_comments'] == true )
	{
		$result = $Database->select("news_comments", "poster", null, "newsid=$id ORDER BY id DESC LIMIT 1")->get_result();
		$rows = $result->fetch_assoc();
	if($rows['poster'] == $_SESSION['cw_user_id'] && isset($_SESSION['cw_user'])) 
	{
		echo "<span class='attention'>您不能连续发表2条评论！</span>";
	}
	else 
	{ ?>
    <hr>
    <h4 class="yellow_text">评论</h4>
    <?php if ( $_SESSION['cw_user'] )
    { ?>
    <form action="?page=news&id=<?php echo $id; ?>" method="post">
    <table width="100%"> 
    <tr> 
        <td>
            <textarea id="newscomment_textarea" name="text" placeholder="评论这个帖子..."></textarea> 
        </td>
        <td>
            <input type="submit" value="Post" name="comment"> 
        </td>
    </tr>
    </table>
    </form>
    <br/>
    <?php
	} 
	else
	{
		echo "<span class='note'>请登录才能发表评论！</span>";
	{
	}
	if ( isset($_POST['comment']) )
	{
		if ( isset($_POST['text']) && isset($_SESSION['cw_user']) && strlen($_POST['text']) <= 1000 )
		{
			$text = $Database->conn->escape_string(trim($_POST['text']));

			$ip = $_SERVER['REMOTE_ADDR'];
			$Database->selectDB("logondb");
            $getAcct = $Database->select("account", "id", null, "username='" . $_SESSION['cw_user'] . "'")->get_result();
			$row     = $getAcct->fetch_assoc();
			$account = $row['id'];

			$Database->selectDB("webdb");
			$Database->conn->query("INSERT INTO news_comments (`newsid`, `text`, `poster`, `ip`) VALUES 
                (". $id .", '". $text ."', '". $account ."', '". $_SERVER['REMOTE_ADDR'] ."');");

			header("Location: ?page=news&newsid=". $id);
		}
	}

    $result = $Database->select("news_comments", null, null, "newsid=". $row['id'] ." ORDER BY id ASC")->get_result();
	if ($result->num_rows ==0)
	{
		echo "<span class='alert'>还没有发表任何评论!</span>";
	}
	else 
	{
		$c = 0;
		while($row = $result->fetch_assoc())
		{
			$c++;
			$text = preg_replace("
              #((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
             "'<a href=\"$1\" target=\"_blank\">http://$3</a>$4'",
             $row['text']
            );
			$Database->selectDB("logondb");
			$query  = $Database->select("account", "username, id", null, "id=". $row['poster'])->get_result();
			$pi		= $query->fetch_assoc();
			$user	= ucfirst(strtolower($pi['username']));

			$getGM  = $Database->select("account_access", "COUNT(gmlevel)", null, "id=". $pi['id'] ." AND gmlevel>0")->get_result();
			?>
			<div class="news_comment" id="comment-<?php echo $row['id']; ?>"> 
                <div class="news_comment_user"><?php
                    echo $user; 
				if ( $getGM->data_seek(0) > 0 )
                {
					echo "<br/><span class='blue_text' style='font-size: 11px;'>职员</span>";
				}
				?>
                </div> 
                <div class="news_comment_body"><?php
                if ( $getGM->data_seek(0) > 0 )
                {
                    echo "<span class='blue_text'>";
                }
				echo nl2br(htmlentities($text));
                if ( $getGM->data_seek(0) > 0 )
                {
                    echo "</span>";
                }
				
                if (isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel'] >= DATA['admin']['minlvl'] ||
                    isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel'] >= DATA['staff']['minlvl'] && 
                    DATA['staff']['permissions']['editNewsComments'] == true)
                {
                    echo '<br/><br/> ( <a href="#">Edit</a> | <a href="#remove" onclick="removeNewsComment('. $row['id'] .')">移除</a> )';
                }?>
                <div class='news_count'><?php echo '#' . $c; ?></div>
              </div>
            </div>
            <?php
		}
	}
}  
}
}
else
{
	$result = $Database->select("news", null, null, null, "ORDER BY id DESC")->get_result();
	while($row = $result->fetch_assoc()) 
	{
        if ( file_exists($row['image']) )
        {?>
			<table class="news" width="100%"> 
            <tr>
                <td><h3 class="yellow_text"><?php echo $row['title']; ?></h3></td>
            </tr>
			</table>
			<table class="news_content" cellpadding="4"> 
				<tr>
					<td><img src="'.$row['image'].'" alt=""/></td> 
                <td><?php
			}
			else
            { ?>
                <table class="news" width="100%"> 
                <tr>
                    <td><h3 class="yellow_text"><?php echo $row['title']; ?></h3></td>
                </tr>
                </table>
                <table class="news_content" cellpadding="4"> 
                    <tr>
                        <td><?php
            }

            $text = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie", "'<a href=\"$1\" target=\"_blank\">http://$3</a>$4'", $row['body']);

            if ( DATA['website']['news']['limit_home_characters'] == true )
            {
                echo $Website->limit_characters($text, 200);
                $output .= $Website->limit_characters($row['body'], 200);
            }
            else
            {
                echo nl2br($text);
                $output .= nl2br($row['body']);
            }

            $commentsNum = $Database->select("news_comments", "COUNT(id) AS comments", null, "newsid=". $row['id'])->get_result();

            if ( DATA['website']['news']['enableComments'] == true )
			{
                $comments = '| <a href="?page=news&amp;newsid=' . $row['id'] . '">Comments ('. $commentsNum->fetch_assoc()['comments'] .')</a>';
            }
            else
            {
                $comments = NULL;
            }

            echo '
    			<br/><br/><br/>
    			<i class="gray_text"> 作者 ' . $row['author'] . ' | ' . $row['date'] . ' ' . $comments . '</i>
    			</td> 
    			</tr>
    			</table>';
            }
        }
    }
?>

</div>
<div id="footer">
{footer}
</div>
</div>

<div id="rightcontent">     
{login}          
{serverstatus}  			
</div>
</div>