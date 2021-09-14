<?php
    define('INIT_SITE', TRUE);
    include "../../includes/misc/headers.php";
    include "../../includes/configuration.php";
    include "../functions.php";
    
    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();

    $GameServer->selectDB("webdb", $conn);

    # Organized Alphabeticaly

    switch ($_POST['action'])
    {
        case "addVoteLink":
	    {
            $title  = $conn->escape_string($_POST['title']);
            $points = $conn->escape_string($_POST['points']);
            $image  = $conn->escape_string($_POST['image']);
            $url    = $conn->escape_string($_POST['url']);

            if (!empty($title) && !empty($points) && !empty($image) && !empty($url))
            {
                $conn->query("INSERT INTO votingsites (title, points, image, url) VALUES('". $title ."', '". $points ."', '". $image ."', '". $url ."');");
            }

            break;
        }

        case "delete":
	    {
            $conn->query("DELETE FROM custom_pages WHERE filename='". $conn->escape_string($_POST['filename']) ."';");
            return;

            break;
        }
        case "removeVoteLink":
        {
            $id = $conn->escape_string($_POST['id']);

            $conn->query("DELETE FROM votingsites WHERE id=". $id .";");

            break;
        }
        case "saveServicePrice":
        {
            $service  = $conn->escape_string($_POST['service']);
            $price    = $conn->escape_string($_POST['price']);
            $currency = $conn->escape_string($_POST['currency']);
            $enabled  = $conn->escape_string($_POST['enabled']);

            $conn->query("UPDATE service_prices SET price=". $price .", currency='". $currency ."', enabled='". $enabled ."' WHERE service='". $service ."';");

            break;
        }

        case "saveVoteLink":
        {
            $id     = $conn->escape_string($_POST['id']);
            $title  = $conn->escape_string($_POST['title']);
            $points = $conn->escape_string($_POST['points']);
            $image  = $conn->escape_string($_POST['image']);
            $url    = $conn->escape_string($_POST['url']);

            if (!empty($id))
            {
                $conn->query("UPDATE votingsites SET title='". $title ."', points='". $points ."', image='". $image ."', url='". $url ."' 
                    WHERE id=". $id .";");
            }

            break;
        }

        case "toggle":
        {
            if ($_POST['value'] == 1)
            {
                //Enable
                $conn->query("DELETE FROM disabled_pages WHERE filename='". $conn->escape_string($_POST['filename']) ."';");
            }
            elseif ($_POST['value'] == 2)
            {
                //Disable
                $conn->query("INSERT INTO disabled_pages values('". $conn->escape_string($_POST['filename']) ."');");
            }

            break;
        }
    } 