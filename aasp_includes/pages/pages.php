<?php

    global $GameServer, $GamePage;
    $conn = $GameServer->connect();

    $$GameServer->selectDB("webdb");

	$GamePage->validatePageAccess("Pages");

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
        $result = $Database->select("custom_pages", null, null, null, "ORDER BY id ASC");
        while ($row = $result->fetch_assoc())
            {
                $disabled = TRUE;
                $check = $Database->select("disabled_pages", "COUNT(filename) AS filename", null, "filename='". $row['filename'] ."'");
                if ($check->fetch_assoc()['filename'] == 1)
                {
                    $disabled = TRUE;
                }
            ?>
            <tr <?php if ($disabled) echo "style='color: #999;'"; ?>>
                <td width="50"><?php echo $row['name']; ?></td>
                <td width="100"><?php echo $row['filename']; ?>(Database)</td>
                <td><select id="action-<?php echo htmlentities($row['filename']); ?>"><?php
                        if ($disabled == TRUE)
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

    if (is_array(DATA['website']['core_pages']) || is_object(DATA['website']['core_pages']))
    {
        foreach (DATA['website']['core_pages'] as $pageName => $fileName)
	{ 
        $filename = substr($fileName, 0, -4);
        unset($check);
        $check = $Database->select("disabled_pages", "COUNT(filename) AS filename", null, "filename='". $filename ."';");
        if ($check->fetch_assoc()['filename'] == 1)
	 	{
			$disabled = false;
	 	} 
	 	else 
	 	{
			$disabled = TRUE;
	 	}
        ?>
	    <tr <?php if ($disabled) echo "style='color: #999;'"; ?>>
            <td><?php echo $pageName; ?></td>
            <td><?php echo $fileName; ?></td>
        <td><select id="action-<?php echo $row['filename']; ?>">
            <?php 
            if($disabled == TRUE)
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

if (is_array(DATA['website']['core_pages']) || is_object(DATA['website']['core_pages']))
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
			$disabled = TRUE;
		}
	?>

	<tr <?php if($disabled == TRUE) { echo "style='color: #999;'";
	}?>>
	    <td><?php echo $k; ?></td>
	    <td><?php echo $v; ?></td>
        <td><select id="action-<?php echo $filename; ?>">
    <?php
	    if($disabled == TRUE)
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
		
		$name     = $Database->conn->escape_string($_POST['editpage_name']);
        $filename = $Database->conn->escape_string(trim(strtolower($_POST['editpage_filename'])));
        $content  = $Database->conn->escape_string(htmlentities($_POST['editpage_content']));

		if(empty($name) || empty($filename) || empty($content)) 
		{
			echo "<h3>请输入 <u>所有</u> 字段。</h3>";
		} 
		else 
		{
            $Database->conn->query("UPDATE custom_pages 
                SET name='". $name ."', filename='". $filename ."', content='". $content ."' 
                WHERE filename='". $Database->conn->escape_string($_GET['filename']) ."';");

            echo "<h3>页面已成功更新。</h3> <a href='" . DATA['website']['domain'] . "?page=" . $filename . "' target='_blank'>查看页面</a>";
		}
	}

	$result = $Database->select("custom_pages", null, null, null, "filename='". $Database->conn->escape_string($_GET['filename']) ."'");
    $row    = $result->fetch_assoc();
?>
	   
     <h4>编辑 <?php echo $_GET['filename']; ?>.php</h4>
    <form action="?page=pages&action=edit&filename=<?php echo $_GET['filename']; ?>" method="post">
	名字<br/>
    <input type="text" name="editpage_name" value="<?php echo $row['name']; ?>"><br/>
    文件名<br/>
    <input type="text" name="editpage_filename" value="<?php echo $row['filename']; ?>"><br/>
    内容<br/>
    <textarea cols="77" rows="14" id="wysiwyg" name="editpage_content"><?php echo $row['content']; ?></textarea>    
    <br/>
    <input type="submit" value="保存" name="editpage">
    
    </form><?php
            }
        }
    }
}