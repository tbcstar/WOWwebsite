<?php 

	global $Server, $Page;

	$Server->selectDB('webdb');	 
	$Page->validatePageAccess('News');

    if($Page->validateSubPage() == TRUE) 
    {
		$Page->outputSubPage();
	} 
	else 
	{
?>
	<div class="box_right_title">新闻 &raquo; 发布新闻</div>
	<div id="news_status"></div>
	<input type="text" value="标题..." id="news_title"/> <br/>
	<input type="text" value="作者..." id="news_author"/> <br/>
	<input type="text" value="Image URL..." id="news_image"/> <br/>
	<textarea cols="72" rows="7" id="news_content">内容...

	</textarea>
	<input type="submit" value="Post" onclick="postNews()"/>  <input type="submit" value="Preview" onclick="previewNews()" disabled="disabled"/>                                
<?php } ?> 