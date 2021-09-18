<?php

global $Database;
$Database->selectDB("webdb");

if ( !isset($_SESSION['cw_user']) )
{
    $sql = "shownWhen LIKE('always') OR shownWhen LIKE('notlogged')";
}
else
{
    $sql = "shownWhen LIKE('always') OR shownWhen LIKE('logged')";
}
$getMenuLinks = $Database->select("site_links", null, null, $sql." ORDER BY position ASC")->get_result();
if ( $getMenuLinks->num_rows == 0 )
{
    buildError("<b>模板错误：</b> 在 网站 数据库中找不到菜单链接！", NULL);
    echo "<br/>没有找到菜单链接！";
}

while ($row = $getMenuLinks->fetch_assoc())
{
    $curr = substr($row['url'], 3);
    echo "<li>";
    if ( $_GET['page'] == $curr )
    {
        echo "<a href=\"" . $row['url'] . "\" class=\"current\">" . $row['title'] . "</a>";
    }
    else
    {
        echo "<a href=\"" . $row['url'] ."\">". $row['title'] ."</a>";
	}
    echo "</li>";
} 