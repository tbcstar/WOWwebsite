<div id="overlay"></div>
<div id="wrapper">

<?php include "header.php" ?>

<div id="leftcontent">
<div class="box_two">
<?php
if (isset($_GET['newsid'])) 
{
	global $Connect, $Website, $conn;
	$id = (int)$_GET['newsid'];
	$Connect->selectDB('webdb');
	
	$result = mysqli_query($conn, "SELECT * FROM news WHERE id='".$id."'");
	$row = mysqli_fetch_assoc($result); ?>
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
    <?php if ($GLOBALS['news']['enableComments']==true) 
	{ 
		$result = mysqli_query($conn, "SELECT poster FROM news_comments WHERE newsid='".$id."' ORDER BY id DESC LIMIT 1");
		$rows = mysqli_fetch_assoc($result);
	if($rows['poster'] == $_SESSION['cw_user_id'] && isset($_SESSION['cw_user'])) 
		echo "<p>您不能连续发表2条评论！</p>"; 
	else 
	{
	?>
    <hr/>
    <h4 class="yellow_text">评论</h4>
    <?php if ($_SESSION['cw_user']) {?>
    <table width="100%"> <tr> <td>
    <form action="?p=news&id=<?php echo $id; ?>" method="post">
    <textarea id="newscomment_textarea" name="text">评论这篇文章……</textarea> </td>
    <td><input type="submit" value="Post" name="comment"></td>
    </form> 
    </tr></table>
    <br/>
    <?php
	} 
	else
		echo "<span class='note'>请登录才能发表评论！</span>";
	}
	if (isset($_POST['comment'])) 
	{
		if (isset($_POST['text']) && isset($_SESSION['cw_user']) && strlen($_POST['text'])<1000) 
		{
			$text = mysqli_real_escape_string($conn, trim($_POST['text']));
			$ip = $_SERVER['REMOTE_ADDR'];
			$Connect->selectDB('logondb');
			
			$getAcct = mysqli_query($conn, "SELECT `id` FROM `account` WHERE `username` = '{$_SESSION['cw_user']}'"); $row = mysqli_fetch_assoc($getAcct);
			$acct = $row['id'];
			$Connect->selectDB('webdb'); 
			mysqli_query($conn, "INSERT INTO news_comments VALUE ('','".$id."','".$text."','".$acct."','".$ip."')");
			
			header("Location: ?p=news&id=".$_GET['id']."");
		}
	}
	
    $result = mysqli_query($conn, "SELECT * FROM news_comments WHERE newsid='".$row['id']."' ORDER BY id ASC");
	if (mysqli_num_rows($result)==0)
		echo "<span class='alert'>还没有发表任何评论!</span>";
	else 
	{
		$c = 0;
		while($row = mysqli_fetch_assoc($result))
		{
			$c++;
			$text = preg_replace("
              #((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
             "'<a href=\"$1\" target=\"_blank\">http://$3</a>$4'",
             $row['text']
            );
			$Connect->selectDB('logondb');
			$query	= mysqli_query($conn, "SELECT username,id FROM account WHERE id='".$row['poster']."'"); 
			$pi		= mysqli_fetch_assoc($query); 
			$user	= ucfirst(strtolower($pi['username']));
			
			$getGM = mysqli_query($conn, "SELECT COUNT(gmlevel) FROM account_access WHERE id='".$pi['id']."' AND gmlevel>'0'");
			?>
			<div class="news_comment" id="comment-<?php echo $row['id']; ?>"> 
                <div class="news_comment_user"><?php echo $user; 
				if(mysqli_data_seek($getGM,0) > 0)
					echo "<br/><span class='blue_text' style='font-size: 11px;'>职员</span>";?>
                </div> 
                <div class="news_comment_body"><?php if(mysqli_data_seek($getGM,0)>0) { echo "<span class='blue_text'>"; }?>
				<?php echo nl2br(htmlentities($text));
                if(mysqli_data_seek($getGM,0)>0) { echo "</span>"; }
				
				if(isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=$GLOBALS['adminPanel_minlvl'] || 
				isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=$GLOBALS['staffPanel_minlvl'] && $GLOBALS['editNewsComments']==true)
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
else
{
	 $result = mysqli_query($conn, "SELECT * FROM news ORDER BY id DESC");
	 while($row = mysqli_fetch_assoc($result)) 
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
			
			if ($GLOBALS['news']['limitHomeCharacters']==true) 
			{ 		
					echo $Website->limit_characters(htmlentities($text,200));
					$output.= $Website->limit_characters($row['body'],200);
				 } 
				 else 
				 {
					 echo nl2br(htmlentities($text)); 
					 $output .= nl2br($row['body']); 
				}
			 $commentsNum = mysqli_query($conn, "SELECT COUNT(id) FROM news_comments WHERE newsid='".$row['id']."'");
							 
			 if($GLOBALS['news']['enableComments']==TRUE) 
				 $comments = '| <a href="?p=news&amp;newsid='.$row['id'].'">评论 ('.mysqli_data_seek($commentsNum,0).')</a>';
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