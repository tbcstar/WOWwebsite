<?php

define('INIT_SITE', TRUE);
include('../../includes/misc/headers.php');
include('../../includes/configuration.php');
include('../functions.php');
global $GameServer, $GameAccount;
$conn = $GameServer->connect();

$GameServer->selectDB("webdb", $conn);

  # Organized Alphabeticaly

  switch ($_POST['function']) 
  {
    case "delete":
    {
      if (empty($_POST['id']))
      {
        die('未指定 ID。 中止...');
      }

      mysqli_query($conn, "DELETE FROM news WHERE id=". mysqli_real_escape_string($conn, $_POST['id']) .";");
      mysqli_query($conn, "DELETE FROM news_comments WHERE id=". mysqli_real_escape_string($conn, $_POST['id']) .";");
      $GameServer->logThis("删除了一个新闻帖子");

      break;
    }

    case "edit":
    {
      $id      = mysqli_real_escape_string($conn, $_POST['id']);
      $title   = mysqli_real_escape_string($conn, ucfirst($_POST['title']));
      $author  = mysqli_real_escape_string($conn, ucfirst($_POST['author']));
      $content = mysqli_real_escape_string($conn, $_POST['content']);

      if (empty($id) || empty($title) || empty($content))
      {
          die("请输入两个字段。");
      }
      else
      {
          mysqli_query($conn, "UPDATE news SET title='". $title ."', author='". $author ."', body='". $content ."' WHERE id=". $id .";");
          $GameServer->logThis("已更新新闻帖子，ID: <b>". $id ."</b>");
          return TRUE;
      }

      break;
    }

    case "getNewsContent":
    {
      $result  = mysqli_query($conn, "SELECT * FROM news WHERE id=". mysqli_real_escape_string($conn, $_POST['id']) .";");
      $row     = mysqli_fetch_assoc($result);
      $content = str_replace('<br />', "\n", $row['body']);

      echo "<h3>编辑新闻</h3><br/>标题: <br/><input type='text' id='editnews_title' value='". $row['title'] ."'><br/><br/>
            内容:<br/><textarea cols='55' rows='8' id='editnews_content'>". $content ."</textarea><br/>
            <br/><input type='submit' value='保存' onclick='editNewsNow(". $row['id'] .")'>";

      break;
    }

    case "post":
    {
      if (empty($_POST['title']) || empty($_SESSION['cw_user']) || empty($_POST['content']))
      {
        die('<span class="red_text">请输入所有字段。</span>');
      }
      
      $title    = mysqli_real_escape_string($conn, $_POST['title']);
      $content  = mysqli_real_escape_string($conn, $_POST['content']);
      $author   = mysqli_real_escape_string($conn, $_SESSION['cw_user']);
      $img      = mysqli_real_escape_string($conn, $_POST['image']);
      $date     = date("Y-m-d H:i:s");

      $result = mysqli_query($conn, "INSERT INTO news (`title`, `body`, `author`, `image`, `date`) VALUES 
        ('". $title ."','". $content ."', '". $author ."','". $img ."', '". $date ."');");
      if ($result) 
      {
        $GameServer->logThis("发布新闻");
        echo "已成功发布新闻。";
      }
      else
      {
        die("错误 - ". mysqli_error($conn));
      }

      break;
    }

  }