<?php

    global $GameServer, $GamePage;
    $conn = $GameServer->connect();

    $GameServer->selectDB('webdb', $conn);

	$GamePage->validatePageAccess('Pages');

    if($GamePage->validateSubPage() == TRUE) 
    {
		$GamePage->outputSubPage();
	} 
	else 
	{
    echo "<div class='box_right_title'>页面</div>";

    if(!isset($_GET['action']))
    {
    ?>
    <table class='center'>
    <tr>
        <th>名称</th>
        <th>文件名</th>
        <th>动作</th>
    </tr>
    <?php
        $result = mysqli_query($conn, "SELECT * FROM custom_pages ORDER BY id ASC;");
        while ($row = mysqli_fetch_assoc($result))
            {
                $disabled = true;
        $check = mysqli_query($conn, "SELECT COUNT(filename) AS filename FROM disabled_pages WHERE filename='". $row['filename'] ."';");
            if (mysqli_fetch_assoc($check)['filename'] == 1)
            {
                $disabled = true;
            }
            ?>
            <tr <?php if ($disabled) echo "style='color: #999;'"; ?>>
                <td width="50"><?php echo $row['name']; ?></td>
                <td width="100"><?php echo $row['filename']; ?>(Database)</td>
                <td><select id="action-<?php echo $row['filename']; ?>"><?php
                        if ($disabled == true)
                        {
                            ?>
                            <option value="1">启用</option>
                            <?php
                        }
                        elseif ($disabled == false)
                        {
                            ?>
                            <option value="2">关闭</option>
                    <?php } ?>
                        <option value="3">编辑</option>
                        <option value="4">移除</option>
                    </select> &nbsp;<input type="submit" value="Save" onclick="savePage('<?php echo $row['filename']; ?>')"></td>
            </tr>
        <?php
    }

        if (is_array($GLOBALS['core_pages']) || is_object($GLOBALS['core_pages']))
    {
        foreach ($GLOBALS['core_pages'] as $pageName => $fileName)
	{ 
        $filename = substr($fileName, 0, -4);
        unset($check);
        $check = mysqli_query($conn, "SELECT COUNT(filename) AS filename FROM disabled_pages WHERE filename='". $filename ."';");
        if (mysqli_fetch_assoc($check)['filename'] == 1)
	 	{
			$disabled = false;
	 	} 
	 	else 
	 	{
			$disabled = true;
	 	}
        ?>
	    <tr <?php if ($disabled) echo "style='color: #999;'"; ?>>
            <td><?php echo $pageName; ?></td>
            <td><?php echo $fileName; ?></td>
        <td><select id="action-<?php echo $row['filename']; ?>">
            <?php 
            if($disabled == true)
            {
            ?>
            <option value="1">启用</option>
		    <?php
		    }
		    elseif (!$disabled)
		    { ?>
			<option value="2">禁用</option>
		<?php } ?>
        <option value="3">编辑</option>
        <option value="4">移除</option>
        </select> &nbsp;<input type="submit" value="保存" onclick="savePage('<?php echo $row['filename']; ?>')"></td>
    </tr>
<?php }

if (is_array($GLOBALS['core_pages']) || is_object($GLOBALS['core_pages']))
{
	foreach ($GLOBALS['core_pages'] as $k => $v) 
	{ 
		$filename = substr($v, 0, -4);
		unset($check);
		$check = mysqli_query($conn, "SELECT COUNT(filename) FROM disabled_pages WHERE filename='".$filename."';");
		if(mysqli_data_seek($check,0) == 0) 
		{
			$disabled = false;
		} 
		else 
		{
			$disabled = true;
		}
	?>

	<tr <?php if($disabled == true) { echo "style='color: #999;'";
	}?>>
	    <td><?php echo $k; ?></td>
	    <td><?php echo $v; ?></td>
        <td><select id="action-<?php echo $filename; ?>">
    <?php
	    if($disabled == true)
	    {
	    ?>
	    <option value="1">启用</option>
		<?php
		}
		else
		{
		?>
		<option value="2">禁用</option>
    <?php } ?>
        </select> &nbsp;<input type="submit" value="Save" onclick="savePage('<?php echo $filename; ?>')"></td>
	    </tr>
	<?php } ?>
}


</table>

<?php } elseif($_GET['action']=='new') {
	 
 ?>


<?php } elseif($_GET['action']=='edit') {
	
	if(isset($_POST['editpage']))
	{
		
		$name 		= mysqli_real_escape_string($conn, $_POST['editpage_name']);
		$filename 	= mysqli_real_escape_string($conn, trim(strtolower($_POST['editpage_filename'])));
		$content 	= mysqli_real_escape_string($conn, htmlentities($_POST['editpage_content']));

		if(empty($name) || empty($filename) || empty($content)) 
		{
			echo "<h3>请输入 <u>所有</u> 字段。</h3>";
		} 
		else 
		{
            mysqli_query($conn, "UPDATE custom_pages 
                SET name='". $name ."', filename='". $filename ."', content='". $content ."' 
                WHERE filename='". mysqli_real_escape_string($conn, $_GET['filename']) ."';");

            echo "<h3>页面已成功更新。</h3> <a href='" . $GLOBALS['website_domain'] . "?p=" . $filename . "' target='_blank'>查看页面</a>";
		}
	}

	$result = mysqli_query($conn, "SELECT * FROM custom_pages WHERE filename='".mysqli_real_escape_string($conn, $_GET['filename'])."';"); 
	$row = mysqli_fetch_assoc($result);
?>
	   
     <h4>编辑 <?php echo $_GET['filename']; ?>.php</h4>
    <form action="?p=pages&action=edit&filename=<?php echo $_GET['filename']; ?>" method="post">
	名字<br/>
    <input type="text" name="editpage_name" value="<?php echo $row['name']; ?>"><br/>
    文件名<br/>
    <input type="text" name="editpage_filename" value="<?php echo $row['filename']; ?>"><br/>
    内容<br/>
    <textarea cols="77" rows="14" id="wysiwyg" name="editpage_content"><?php echo $row['content']; ?></textarea>    
    <br/>
    <input type="submit" value="保存" name="editpage">
    
<?php
            }
        }
    }
}