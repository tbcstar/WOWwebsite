<?php

    global $Connect, $Plugins;
    $conn = $Connect->connectToDB();
    $Connect->selectDB('webdb', $conn);
    
    $pages = scandir('pages');
    unset($pages[0], $pages[1]);
    $page  = $conn->escape_string($_GET['page']);

    if (!isset($page))
    {
        include("pages/home.php");
    }
    elseif (isset($_SESSION['loaded_plugins_pages']) && $GLOBALS['enablePlugins'] == TRUE && !in_array($page . '.php', $pages))
    {
        $Plugins->load("pages");
    }
    elseif (in_array($page . ".php", $pages))
    {
        $result = $conn->query("SELECT COUNT(filename) AS filename FROM disabled_pages WHERE filename='" . $page . "';");
        if ($result->data_seek(0) == 1)
        {
            include("pages/". $page .".php");
        }
        else
        {
            include("pages/404.php");
        }
    }
    else
    {
        $result = $conn->query("SELECT * FROM custom_pages WHERE filename='". $page ."';");
        if ($result->num_rows > 0)
        {
            $check = $conn->query("SELECT COUNT(filename) AS filename FROM disabled_pages WHERE filename='" . $page . "';");
            if ($check->fetch_assoc()['filename'] == 0)
            {
                $row = $result->fetch_assoc();
                echo html_entity_decode($row['content']);
            }
        }
        else
        {
            include('pages/404.php');
        }
    }
?>