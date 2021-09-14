<div id="overlay"></div>
<div id="wrapper">

<?php include "header.php" ?>

<div id="leftcontent">
<div class="box_two">
<?php
if (isset($_GET['newsid'])) 
{
    global $Connect, $Website;
    $conn = $Connect->connectToDB();
	$id = $conn->escape_string($_GET['newsid']);
	$Connect->selectDB("webdb", $conn);

	$result = $conn->query("SELECT * FROM news WHERE id=". $id .";");
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
    <?php if ($GLOBALS['news']['enableComments']==TRUE) 
	{
		$result = $conn->query("SELECT poster FROM news_comments WHERE newsid=" . $id . " ORDER BY id DESC LIMIT 1;");
		$rows = $result->fetch_assoc();
	if($rows['poster'] == $_SESSION['cw_user_id'] && isset($_SESSION['cw_user'])) 
	{
		echo "<span class='attention'>您不能连续发表2条评论！</span>";
	}
	else 
	{
	?>
    <hr/>
    <h4 class="yellow_text">评论</h4>
    <?php if ($_SESSION['cw_user']) {?>
    <table width="100%"> <tr> <td>
    <form action="?page=news&id=<?php echo $id; ?>" method="post">
    <textarea id="newscomment_textarea" name="text" placeholder="评论这篇文章..."></textarea>
    <td><input type="submit" value="Post" name="comment"></td>
    </form> 
    </tr></table>
    <br/>
    <?php
	} 
	else
	{
		echo "<span class='note'>请登录才能发表评论！</span>";
	{
	}
	if (isset($_POST['comment'])) 
	{
		if (isset($_POST['text']) && isset($_SESSION['cw_user']) && strlen($_POST['text'])<1000) 
		{
			$text = $conn->escape_string(trim($_POST['text']));
			$ip = $_SERVER['REMOTE_ADDR'];
			$Connect->selectDB("logondb", $conn);

			$getAcct = $conn->query("SELECT id FROM account WHERE username='" . $_SESSION['cw_user'] . "';");
			$row     = $getAcct->fetch_assoc();
			$account = $row['id'];
			$Connect->selectDB("webdb", $conn); 
			$conn->query("INSERT INTO news_comments (`newsid`, `text`, `poster`, `ip`) VALUES 
                (". $id .", '". $text ."', '". $account ."', '". $_SERVER['REMOTE_ADDR'] ."');");

			header("Location: ?page=news&newsid=". $id);
		}
	}

    $result = $conn->query("SELECT * FROM news_comments WHERE newsid=". $row['id'] ." ORDER BY id ASC;");
	if ($result->num_rows ==0)
		echo "<span class='alert'>还没有发表任何评论!</span>";
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
			$Connect->selectDB("logondb", $conn);
			$query  = $conn->query("SELECT username, id FROM account WHERE id=". $row['poster'] .";");
			$pi		= $query->fetch_assoc();
			$user	= ucfirst(strtolower($pi['username']));

			$getGM = $conn->query("SELECT COUNT(gmlevel) FROM account_access WHERE id=". $pi['id'] ." AND gmlevel>0;");
			?>
			<div class="news_comment" id="comment-<?php echo $row['id']; ?>"> 
                <div class="news_comment_user"><?php
                    echo $user; 
				if($getGM->data_seek(0) > 0)
					echo "<br/><span class='blue_text' style='font-size: 11px;'>职员</span>";?>
                </div> 
                <div class="news_comment_body"><?php
                if($getGM->data_seek(0)>0)
                {
                    echo "<span class='blue_text'>";
                }?>
				<?php echo nl2br(htmlentities($text));
                if($getGM->data_seek(0)>0) { echo "</span>"; }
				
				if(isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=$GLOBALS['adminPanel_minlvl'] || 
				isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=$GLOBALS['staffPanel_minlvl'] && $GLOBALS['editNewsComments']==TRUE)
				 	echo '<br/><br/> ( <a href="#">编辑</a> | <a href="#remove" onclick="removeNewsComment('.$row['id'].')">移除</a> )';  
			   ?>
               <div class='news_count'>
               		<?php echo '#'.$c; ?>
               </div>
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
	 $result = $conn->query("SELECT * FROM news ORDER BY id DESC;");
	 while($row = $result->fetch_assoc()) 
	 {
			if(file_exists($row['image']))
			{
			echo '
				   <table class="news" width="100%"> 
						<tr>
							<td><h3 class="yellow_text">'.$row['title'].'</h3></td>
						</tr>
				   </table>
				   <table class="news_content" cellpadding="4"> 
					   <tr>
						  <td><img src="'.$row['image'].'" alt=""/></td> 
						  <td>';
			}
			else
			{
				echo '
				   <table class="news" width="100%"> 
						<tr>
							<td><h3 class="yellow_text">'.$row['title'].'</h3></td>
						</tr>
				   </table>
				   <table class="news_content" cellpadding="4"> 
					   <tr>
						   <td>';
			}
			
			$text = preg_replace("
			#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
			"'<a href=\"$1\" target=\"_blank\">http://$3</a>$4'",
			$row['body']
			);
			
			if ($GLOBALS['news']['limitHomeCharacters']==TRUE) 
			{ 		
				    echo $Website->limit_characters(htmlentities($text,200));
				    $output.= $Website->limit_characters($row['body'],200);
				} 
				else 
				{
					echo nl2br(htmlentities($text)); 
					$output .= nl2br($row['body']); 
				}
			$commentsNum = $conn->query("SELECT COUNT(id) AS comments FROM news_comments WHERE newsid=". $row['id'] .";");
							 
			if($GLOBALS['news']['enableComments']==TRUE) 
			   $comments = '| <a href="?page=news&amp;newsid=' . $row['id'] . '">Comments (' . $commentsNum->fetch_assoc()['comments'] . ')</a>';
			else
			    $comments = '';
			 
			echo '
			<br/><br/><br/>
			<i class="gray_text"> 作者 '.$row['author'].' | '.$row['date'].' '.$comments.'</i>
			</td> 
			</tr>
			</table>';
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