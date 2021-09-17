<?php

global $GameServer;
$conn = $GameServer->connect();
$GameServer->selectDB("webdb", $conn);
$result = $Database->select("news", null, null, null, "ORDER BY id DESC")->get_result();
if ($result->num_rows == 0)
{ 
	echo "<span class='blue_text'>目前还没有任何消息!</span>"; 
}
else { 
?>
<div class="box_right_title">新闻 &raquo; 管理</div>
<table width="100%">
<tr>
    <th>ID</th>
    <th>标题</th>
    <th>内容</th>
    <th>评论</th>
    <th>动作</th>
</tr>
<?php
while ($row = $result->fetch_assoc())
{
    $comments = $Database->select("news_comments", "COUNT(id) AS comments", null, "newsid=". $row['id'])->get_result();
	echo "<tr class='center'>
                  		<td>". $row['id'] ."</td>
                  		<td>". $row['title'] ."</td>
                  		<td>". substr($row['body'], 0, 25) ."...</td>
                  		<td>". $comments->fetch_assoc()['comments'] ."</td>
                  		<td> <a onclick='editNews(". $row['id'] .")' href='#'>编辑</a> &nbsp;  
                  		<a onclick='deleteNews(". $row['id'] .")' href='#'>删除</a></td>
    </tr>";
}
?>
    </table><?php
}
