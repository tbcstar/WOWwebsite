<?php
    define('INIT_SITE', TRUE);
    include "../../includes/misc/headers.php";
    include "../../includes/configuration.php";
    include "../functions.php";

    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();;

    $GameServer->selectDB("webdb");

    # Organized Alphabeticaly

    switch ($_POST['action'])
    {

        case "addmulti":
        {
            $il_from = $Database->conn->escape_string($_POST['il_from']);
            $il_to   = $Database->conn->escape_string($_POST['il_to']);
            $price   = $Database->conn->escape_string($_POST['price']);
            $quality = $Database->conn->escape_string($_POST['quality']);
            $shop    = $Database->conn->escape_string($_POST['shop']);
            $type    = $Database->conn->escape_string($_POST['type']);

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

            $GameServer->selectDB("worlddb");
            $get = $Database->select("item_template", null, null, "itemlevel>=$il_from AND itemlevel<=$il_to $advanced")->get_result() 
            or die('从数据库获取物品数据时出错。错误消息: ' . $Database->conn->error);

            $GameServer->selectDB("webdb");

            $c   = 0;
            while ($row = $get->fetch_assoc())
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

                $Database->conn->query("INSERT INTO shopitems (entry,name,in_shop,displayid,type,itemlevel,quality,price,class,faction,subtype,flags) VALUES 
                    ('". $row['entry'] ."',
                    '". $Database->conn->escape_string($row['name']) ."',
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
                or die("向数据库添加物品时出错。错误消息: " . $Database->conn->error);

                $c++;
            }

            $GameServer->logThis("将多个物品添加到 " . $shop . " 商城");
            echo '已成功添加 ' . $c . ' 物品';
            break;
        }

        case "addsingle":
        {
            $entry = $Database->conn->escape_string($_POST['entry']);
            $price = $Database->conn->escape_string($_POST['price']);
            $shop  = $Database->conn->escape_string($_POST['shop']);

            if (empty($entry) || empty($price) || empty($shop))
            {
                die("请输入所有字段。");
            }

            $GameServer->selectDB("worlddb");
            $get = $Database->select("item_template", null, null, "entry=$entry")->get_result()
                or die('从数据库获取物品数据时出错。错误消息:' . $Database->conn->error);
            $row = $get->fetch_assoc();

            $GameServer->selectDB("webdb");

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

            $Database->conn->query("INSERT INTO shopitems (entry,name,in_shop,displayid,type,itemlevel,quality,price,class,faction,subtype,flags) VALUES 
                (". $entry .",
                '". $Database->conn->escape_string($row['name']) ."',
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
                or die("向数据库添加物品时出错。错误消息: ". $Database->conn->error);

            $GameServer->logThis("添加 " . $row['name'] . " 到 " . $shop . " 商城");

            echo '已成功添加物品';
            break;
        }

        case "clear":
        {
            $shop = $Database->conn->escape_string($_POST['shop']);

            if ($shop == 1)
            {
                $shop = "vote";
            }
            elseif ($shop == 2)
            {
                $shop = "donate";
            }

            $Database->conn->query("DELETE FROM shopitems WHERE in_shop='". $shop ."';");
            $Database->conn->query("TRUNCATE shopitems;");
            return;

            break;
        }

        case "delmulti":
        {
            $il_from = $Database->conn->escape_string($_POST['il_from']);
            $il_to   = $Database->conn->escape_string($_POST['il_to']);
            $quality = $Database->conn->escape_string($_POST['quality']);
            $shop    = $Database->conn->escape_string($_POST['shop']);
            $type    = $Database->conn->escape_string($_POST['type']);

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

            $count = $Database->select("shopitems", "COUNT(*)", null, "itemlevel >=$il_from AND itemlevel <=$il_to $advanced")->get_result();

            $Database->conn->query("DELETE FROM shopitems WHERE itemlevel >=". $il_from ." AND itemlevel <=". $il_to ." ". $advanced .";");
            echo "已成功移除 ". $count ." items!";

            break;
        }

        case "delsingle":
        {
            $entry = $Database->conn->escape_string($_POST['entry']);
            $shop  = $Database->conn->escape_string($_POST['shop']);

            if (empty($entry) || empty($shop))
                die("请输入所有字段。");

            $Database->conn->query("DELETE FROM shopitems WHERE entry=". $entry ." AND in_shop='". $shop ."';");
            echo '已成功移除物品';

            break;
        }

        case "modmulti":
        {
            $il_from = $Database->conn->escape_string($_POST['il_from']);
            $il_to   = $Database->conn->escape_string($_POST['il_to']);
            $price   = $Database->conn->escape_string($_POST['price']);
            $quality = $Database->conn->escape_string($_POST['quality']);
            $shop    = $Database->conn->escape_string($_POST['shop']);
            $type    = $Database->conn->escape_string($_POST['type']);

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

            $count = $Database->conn->query("COUNT(*) FROM shopitems WHERE itemlevel >='" . $il_from . "' AND itemlevel <='" . $il_to . "' " . $advanced);

            $Database->conn->query("UPDATE shopitems SET price='". $price ."' WHERE itemlevel >=". $il_from ." AND itemlevel <=". $il_to ." ". $advanced .";");
            echo "成功修改 ". $count ." 物品！";

            break;
        }

        case "modsingle":
        {
            $entry = $Database->conn->escape_string($_POST['entry']);
            $price = $Database->conn->escape_string($_POST['price']);
            $shop  = $Database->conn->escape_string($_POST['shop']);

            if (empty($entry) || empty($price) || empty($shop))
            {
                die("请输入所有字段。");
            }

            $Database->conn->query("UPDATE shopitems SET price='". $price ."' WHERE entry=". $entry ." AND in_shop='". $shop ."';");
            echo '已成功修改物品';
            break;
        }

    }