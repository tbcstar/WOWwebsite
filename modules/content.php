<?php

    global $Database, $Plugins;
    $Database->selectDB("webdb");
    
    $pages = scandir('pages');
    unset($pages[0], $pages[1]);
    $page  = $Database->conn->escape_string($_GET['page']);

    if ( !isset($page) )
    {
        include "pages/home.php";
    }
    elseif ( isset($_SESSION['loaded_plugins_pages']) && DATA['website']['enable_plugins'] == true && !in_array($page . '.php', $pages) )
    {
        $Plugins->load("pages");
    }
    elseif ( in_array($page . ".php", $pages) )
    {
        $result = $Database->select("disabled_pages", "COUNT(filename) AS filename", null, "filename='$page'")->get_result();
        if ( $result->data_seek(0) == 1 )
        {
            include "pages/". $page .".php";
        }
        else
        {
            include "pages/404.php";
        }
    }
    else
    {
        $result = $Database->select("custom_pages", null, null, "filename='$page'")->get_result();
        if ( $result->num_rows > 0 )
        {
            $check = $Database->select("disabled_pages", "COUNT(filename) AS filename", null, "filename='$page'")->get_result();
            if ( $check->fetch_assoc()['filename'] == 0 )
            {
                $row = $result->fetch_assoc();
                echo html_entity_decode($row['content']);
            }
        }
        else
        {
            include "pages/404.php";
        }
    }
?>