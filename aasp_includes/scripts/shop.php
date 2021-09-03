<?php
    define('INIT_SITE', TRUE);
    include('../../includes/misc/headers.php');
    include('../../includes/configuration.php');
    include('../functions.php');

    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();;

    $GameServer->selectDB("webdb", $conn);

    # Organized Alphabeticaly

    switch ($_POST['action'])
    {

        case "addmulti":
        {
            $il_from = mysqli_real_escape_string($conn, $_POST['il_from']);
            $il_to   = mysqli_real_escape_string($conn, $_POST['il_to']);
            $price   = mysqli_real_escape_string($conn, $_POST['price']);
            $quality = mysqli_real_escape_string($conn, $_POST['quality']);
            $shop    = mysqli_real_escape_string($conn, $_POST['shop']);
            $type    = mysqli_real_escape_string($conn, $_POST['type']);

            if (empty($il_from) || empty($il_to) || empty($price) || empty($shop))
            {
                die("请输入所有字段。");
            }

            $advanced = "";
            if ($type != "all")
            {
                if ($type == "15-5" || $type == "15-5")
                {
                    //Mount or pet
                    $type = explode('-', $type);

                    $advanced .= "AND class='" . $type[0] . "' AND subclass='" . $type[1] . "'";
                }
                else
                {
                    $advanced .= "AND class='" . $type . "'";
                }
            }

            if ($quality != "all")
            {
                $advanced .= " AND quality='" . $quality . "'";
            }

            $GameServer->selectDB('worlddb');
            $get = mysqli_query($conn, "SELECT entry,name,displayid,ItemLevel,quality,class,AllowableRace,AllowableClass,subclass,Flags 
                FROM item_template WHERE itemlevel>=". $il_from ."  AND itemlevel<=". $il_to ." ". $advanced .";") 
            or die('从数据库获取物品数据时出错。错误消息: ' . mysqli_error());

            $GameServer->selectDB('webdb', $conn);

            $c   = 0;
            while ($row = mysqli_fetch_assoc($get))
            {
                $faction = 0;

                if ($row['AllowableRace'] == 690)
                {
                    $faction = 1;
                }
                elseif ($row['AllowableRace'] == 1101)
                {
                    $faction = 2;
                }
                else
                {
                    $faction = $row['AllowableRace'];
                }

                mysqli_query($conn, "INSERT INTO shopitems (entry,name,in_shop,displayid,type,itemlevel,quality,price,class,faction,subtype,flags) VALUES 
                    ('". $row['entry'] ."',
                    '". mysqli_real_escape_string($conn, $row['name']) ."',
                    '". $shop ."',
                    '". $row['displayid'] ."',
                    '". $row['class'] ."',
                    '". $row['ItemLevel'] ."',
                    '". $row['quality'] ."',
                    '". $price ."',
                    '". $row['AllowableClass'] ."',
                    '". $faction ."',
                    '". $row['subclass'] ."',
                    '". $row['Flags'] ."')")
                or die("向数据库添加物品时出错。错误消息: " . mysqli_error());

                $c++;
            }

            $GameServer->logThis("将多个物品添加到 " . $shop . " 商城");
            echo '已成功添加 ' . $c . ' 物品';
            break;
        }

        case "addsingle":
        {
            $entry = mysqli_real_escape_string($conn, $_POST['entry']);
            $price = mysqli_real_escape_string($conn, $_POST['price']);
            $shop  = mysqli_real_escape_string($conn, $_POST['shop']);

            if (empty($entry) || empty($price) || empty($shop))
            {
                die("请输入所有字段。");
            }

            $GameServer->selectDB('worlddb');
            $get = mysqli_query($conn, "SELECT name,displayid,ItemLevel,quality,AllowableRace,AllowableClass,class,subclass,Flags FROM item_template WHERE entry=". $entry ."")
                or die('从数据库获取物品数据时出错。错误消息: ' . mysqli_error($conn));
            $row = mysqli_fetch_assoc($get);

            $GameServer->selectDB('webdb', $conn);

            if ($row['AllowableRace'] == "-1")
            {
                $faction = 0;
            }
            elseif ($row['AllowableRace'] == 690)
            {
                $faction = 1;
            }
            elseif ($row['AllowableRace'] == 1101)
            {
                $faction = 2;
            }
            else
            {
                $faction = $row['AllowableRace'];
            }

            mysqli_query($conn, "INSERT INTO shopitems (entry,name,in_shop,displayid,type,itemlevel,quality,price,class,faction,subtype,flags) VALUES 
                (". $entry .",
                '". mysqli_real_escape_string($conn, $row['name']) ."',
                '". $shop ."',
                '". $row['displayid'] ."',
                '". $row['class'] ."',
                '". $row['ItemLevel'] ."',
                '". $row['quality'] ."',
                '". $price ."',
                '". $row['AllowableClass'] ."',
                '". $faction ."',
                '". $row['subclass'] ."',
                '". $row['Flags'] ."');")
                or die("向数据库添加物品时出错。错误消息: ". mysqli_error($conn));

            $GameServer->logThis("添加 " . $row['name'] . " 到 " . $shop . " 商城");

            echo '已成功添加物品';
            break;
        }

        case "clear":
        {
            $shop = mysqli_real_escape_string($conn, $_POST['shop']);

            if ($shop == 1)
            {
                $shop = "vote";
            }
            elseif ($shop == 2)
            {
                $shop = "donate";
            }

            mysqli_query($conn, "DELETE FROM shopitems WHERE in_shop='". $shop ."';");
            mysqli_query($conn, "TRUNCATE shopitems;");
            return;

            break;
        }

        case "delmulti":
        {
            $il_from = mysqli_real_escape_string($conn, $_POST['il_from']);
            $il_to   = mysqli_real_escape_string($conn, $_POST['il_to']);
            $quality = mysqli_real_escape_string($conn, $_POST['quality']);
            $shop    = mysqli_real_escape_string($conn, $_POST['shop']);
            $type    = mysqli_real_escape_string($conn, $_POST['type']);

            if (empty($il_from) || empty($il_to) || empty($shop))
            {
                die("请输入所有字段。");
            }

            $advanced = "";
            if ($type != "all")
            {
                if ($type == "15-5" || $type == "15-5")
                {
                    //Mount or pet
                    $type = explode('-', $type);

                    $advanced .= "AND type='" . $type[0] . "' AND subtype='" . $type[1] . "'";
                }
                else
                    $advanced .= "AND type='" . $type . "'";
            }

            if ($quality != "all")
                $advanced .= "AND quality='" . $quality . "'";

            $count = mysqli_query($conn, "SELECT COUNT(*) FROM shopitems WHERE itemlevel >=". $il_from ." AND itemlevel <=". $il_to ." ". $advanced .";");

            mysqli_query($conn, "DELETE FROM shopitems WHERE itemlevel >=". $il_from ." AND itemlevel <=". $il_to ." ". $advanced .";");
            echo "已成功移除 ". $count ." items!";

            break;
        }

        case "delsingle":
        {
            $entry = mysqli_real_escape_string($conn, $_POST['entry']);
            $shop  = mysqli_real_escape_string($conn, $_POST['shop']);

            if (empty($entry) || empty($shop))
                die("请输入所有字段。");

            mysqli_query($conn, "DELETE FROM shopitems WHERE entry=". $entry ." AND in_shop='". $shop ."';");
            echo '已成功移除物品';

            break;
        }

        case "modmulti":
        {
            $il_from = mysqli_real_escape_string($conn, $_POST['il_from']);
            $il_to   = mysqli_real_escape_string($conn, $_POST['il_to']);
            $price   = mysqli_real_escape_string($conn, $_POST['price']);
            $quality = mysqli_real_escape_string($conn, $_POST['quality']);
            $shop    = mysqli_real_escape_string($conn, $_POST['shop']);
            $type    = mysqli_real_escape_string($conn, $_POST['type']);

            if (empty($il_from) || empty($il_to) || empty($price) || empty($shop))
                die("请输入所有字段。");

            $advanced = "";
            if ($type != "all")
            {
                if ($type == "15-5" || $type == "15-5")
                {
                    //Mount or pet
                    $type = explode('-', $type);

                    $advanced .= "AND type='" . $type[0] . "' AND subtype='" . $type[1] . "'";
                }
                else
                    $advanced .= "AND type='" . $type . "'";
            }

            if ($quality != "all")
                $advanced .= "AND quality='" . $quality . "'";

            $count = mysqli_query($conn, "COUNT(*) FROM shopitems WHERE itemlevel >='" . $il_from . "' AND itemlevel <='" . $il_to . "' " . $advanced);

            mysqli_query($conn, "UPDATE shopitems SET price='". $price ."' WHERE itemlevel >=". $il_from ." AND itemlevel <=". $il_to ." ". $advanced .";");
            echo "成功修改 ". $count ." 物品！";

            break;
        }

        case "modsingle":
        {
            $entry = mysqli_real_escape_string($conn, $_POST['entry']);
            $price = mysqli_real_escape_string($conn, $_POST['price']);
            $shop  = mysqli_real_escape_string($conn, $_POST['shop']);

            if (empty($entry) || empty($price) || empty($shop))
            {
                die("请输入所有字段。");
            }

            mysqli_query($conn, "UPDATE shopitems SET price='". $price ."' WHERE entry=". $entry ." AND in_shop='". $shop ."';");
            echo '已成功修改物品';
            break;
        }

    }