<?php 

    global $GameServer, $GamePage;
    $conn = $GameServer->connect();

    $GameServer->selectDB("webdb");
    $GamePage->validatePageAccess('News');

    if ($GamePage->validateSubPage() == TRUE)
    {
        $GamePage->outputSubPage();
	} 
	else 
	{
?>
	<div class="box_right_title">新闻 &raquo; 发布新闻</div>
	<div id="news_status"></div>
	<input type="text" value="标题..." id="news_title"/> <br/>
	<input type="text" value="图片链接..." id="news_image"/> <br/>
    <textarea cols="72" rows="7" id="news_content" placeholder="内容..."></textarea>
	<input type="submit" value="Post" onclick="postNews()"/>  <input type="submit" value="Preview" onclick="previewNews()" disabled="disabled"/>                                
<?php } ?> 