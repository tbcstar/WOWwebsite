<?php

    global $Connect, $Plugins;
    $conn = $Connect->connectToDB();
    $Connect->selectDB('webdb', $conn);
    $pages = scandir('pages');
    unset($pages[0], $pages[1]);
    $page  = mysqli_real_escape_string($conn, $_GET['p']);

    if (!isset($page))
    {
        include("pages/home.php");
    }
    elseif (isset($_SESSION['loaded_plugins_pages']) && $GLOBALS['enablePlugins'] == true && !in_array($page . '.php', $pages))
    {
        $Plugins->load("pages");
    }
    elseif (in_array($page . ".php", $pages))
    {
        $result = mysqli_query($conn, "SELECT COUNT(filename) AS filename FROM disabled_pages WHERE filename='" . $page . "';");
        if (mysqli_data_seek($result, 0) == 1)
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
        $result = mysqli_query($conn, "SELECT * FROM custom_pages WHERE filename='". $page ."';");
        if (mysqli_num_rows($result) > 0)
        {
            $check = mysqli_query($conn, "SELECT COUNT(filename) AS filename FROM disabled_pages WHERE filename='" . $page . "';");
            if (mysqli_fetch_assoc($check)['filename'] == 0)
            {
                $row = mysqli_fetch_assoc($result);
                echo html_entity_decode($row['content']);
            }
        }
        else
        {
            include('pages/404.php');
        }
    }
?>