<?php 

if ( !isset($_POST['action']) )
{
    exit;
}

require "../ext_scripts_class_loader.php";

global $Database, $Server, $Character, $Account;

if ( $_POST['action'] == "unstuck" )
{
    $guid     = $Database->conn->escape_string($_POST['guid']);
    $realm_id = $Server->getRealmId($_POST['char_db']);
    $Database->realm($realm_id);

    $Character->unstuck($guid, $_POST['char_db']);
}

if ( $_POST['action'] == "revive" )
{
    $guid     = $Database->conn->escape_string($_POST['guid']);
    $realm_id = $Server->getRealmId($_POST['char_db']);
    $Database->realm($realm_id);

    $Character->revive($guid, $_POST['char_db']);
}

if ( $_POST['action'] == "getLocations" )
{
    $values = explode('*', $_POST['values']);

    $char     = $Database->conn->escape_string($values[0]);
    $realm_id = $Server->getRealmId($values[1]);
    $Database->realm($realm_id);

    $statement   = $Database->select("characters", "race", null, "guid=$char");
    $result = $statement->get_result();
    $row      = $result->fetch_assoc();
    $statement->close();
    $alliance = array(1, 3, 4, 7, 11);
    if ( in_array($row['race'], $alliance) )
    {
        //Alliance
        $locations_name = array(
            1 => "Stormwind",
            2 => "Ironforge",
            3 => "Darnassus",
            4 => "The Exodar",
            5 => "Dalaran",
            6 => "Shattrath"
        );

        $locations_image = array(
            "Stormwind"  => "spell_arcane_teleportstormwind",
            "Ironforge"  => "spell_arcane_teleportironforge",
            "Darnassus"  => "spell_arcane_teleportdarnassus",
            "The Exodar" => "spell_arcane_teleportexodar",
            "Dalaran"    => "spell_arcane_teleportdalaran",
            "Shattrath"  => "spell_arcane_teleportshattrath"
        );
    }
    else
    {
        //Horde
        $locations_name  = array(
            1 => "Orgrimmar",
            2 => "Undercity",
            3 => "Thunder Bluff",
            4 => "Silvermoon",
            5 => "Dalaran",
            6 => "Shattrath"
        );
        $locations_image = array(
            "Orgrimmar"     => "spell_arcane_teleportorgrimmar",
            "Undercity"     => "spell_arcane_teleportundercity",
            "Thunder Bluff" => "spell_arcane_teleportthunderbluff",
            "Silvermoon"    => "spell_arcane_teleportsilvermoon",
            "Dalaran"       => "spell_arcane_teleportdalaran",
            "Shattrath"     => "spell_arcane_teleportshattrath"
        );
    }	
    echo '<h3>Choose Location</h3>';
    if ( is_array($locations_name) || is_object($locations_name) )
    {
        foreach ($locations_name as $v)
	{
        ?>
            <div class="charBox" style="cursor:pointer;" onclick="portTo('<?php echo $v; ?>', '<?php echo $values[1]; ?>', '<?php echo $values[0]; ?>')">
                <table width="100%">
                    <tr> <td width="15%"><img src="styles/global/images/icons/<?php echo $locations_image[$v] ?>.jpg" /></td>
                        <td align="left" width="90%"><b><?php echo $v; ?></b><br/>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
	    }
    }
}

if ( $_POST['action'] == "teleport" )
{
    $character = $Database->conn->escape_string($_POST['character']);
    $char_db   = $Database->conn->escape_string($_POST['char_db']);
    $location  = $Database->conn->escape_string($_POST['location']);

    $realm_id = $Server->getRealmId($_POST['char_db']);
    $Database->realm($realm_id);
    $statement   = $Database->select("characters", "race, account, level, online", null, "guid='$character'");
    $result = $statement->get_result();
    if ( $result->num_rows == 0 )
    {
        die("<span class=\"alert\">该角色不存在于该帐户！</span>");
    }
    else
    {
        $row = $result->fetch_assoc();
        $statement->close();

        if ( $row['online'] == 1 )
	    {
            die("请在传送前退出游戏。");
	    }
	
        $acct  = $row['account'];
        $race  = $row['race'];
        $level = $row['level'];

        if ( DATA['service']['teleport']['currency'] == "vp" && DATA['service']['teleport']['price'] > 0 )
	    {
            if ($Account->hasVP($_SESSION['cw_user'], DATA['service']['teleport']['price']) == false)
            {
                die("投票积分不足！");
            }
	    }
        elseif ( DATA['service']['teleport']['currency'] == "dp" && DATA['service']['teleport']['price'] > 0 )
	    {
            if ( $Account->hasDP($_SESSION['cw_user'], DATA['service']['teleport']['price']) == false )
		    {
                die("Insufficent " . DATA['website']['donation']['coins_name'] . "!");
		    }
	    }

        $map = $x = $y = $z = null;

        switch ($location)
    	{
            //stormwind
            case "Stormwind":
                $map = "0";
                $x   = "-8913.23";
                $y   = "554.633";
                $z   = "93.7944";
			    break;
            //ironforge
            case "Ironforge":
                $map = "0";
                $x   = "-4981.25";
                $y   = "-881.542";
                $z   = "501.66";
                break;
            //darnassus
            case "Darnassus":
                $map = "1";
                $x   = "9951.52";
                $y   = "2280.32";
                $z   = "1341.39";
                break;
            //exodar
            case "The Exodar":
                $map = "530";
                $x   = "-3987.29";
                $y   = "-11846.6";
                $z   = "-2.01903";
                break;
            //orgrimmar
            case "Orgrimmar":
                $map = "1";
                $x   = "1676.21";
                $y   = "-4315.29";
                $z   = "61.5293";
                break;
            //thunderbluff
            case "Thunder Bluff":
                $map = "1";
                $x   = "-1196.22";
                $y   = "29.0941";
                $z   = "176.949";
                break;
            //undercity
            case "Undercity":
                $map = "0";
                $x   = "1586.48";
                $y   = "239.562";
                $z   = "-52.149";
                break;
            //silvermoon
            case "Silvermoon":
                $map = "530";
                $x   = "9473.03";
                $y   = "-7279.67";
                $z   = "14.2285";
			    break;
            //shattrath
            case "Shattrath":
                $map = "530";
                $x   = "-1863.03";
                $y   = "4998.05";
                $z   = "-21.1847";
                break;
            //dalaran
            case "Dalaran":
                $map = "571";
                $x   = "5812.79";
                $y   = "647.158";
                $z   = "647.413";
                break;
        }

        //disallows factions to use enemy portals
        switch ($race)
        {
            //alliance
            case 1:
            case 3:
            case 4:
            case 7:
            case 11:
                if ((($location >= 5) && ($location <= 8)) && ($location != 9))
                    die("<span class=\"alert\">联盟玩家 <b>不可以</b> 传送到部落地区！</span>");
			    break;

            //horde
            case 2:
            case 5:
            case 6:
            case 8:
            case 10:
                if ((($location >= 1) && ($location <= 4)) && ($location != 9))
                    die("<span class=\"alert\">部落玩家 <b>不可以</b> 传送到联盟区域！</span>");
			    break;

            default:
                die("<span class=\"alert\">那不是有效的种族！</span>");
			    break;

	    }

        if ( $location == "Dalaran" && $level < 68 )
        {
            die("中止...<br/><span class=\"alert\">您的角色必须达到 68 级或更高才能传送到诺森德！</span>");
        }

        if ( DATA['service']['teleport']['currency'] == "vp" )
        {
            $Account->deductVP($Account->getAccountID($_SESSION['cw_user']), DATA['service']['teleport']['price']);
        }
        elseif ( DATA['service']['teleport']['currency'] == "dp" )
        {
            $Account->deductDP($Account->getAccountID($_SESSION['cw_user']), DATA['service']['teleport']['price']);
        }

        $Database->realm($realm_id);

        //get pos x, y etc for the logs.
        $statement = $Database->select("characters", "position_x, position_y, position_z, map", null, "guid='$character'");
        $result = $statement->get_result();
        $pos    = $result->fetch_assoc();
        $statement->close();

        $char_x   = $pos['position_x'];
        $char_y   = $pos['position_y'];
        $char_z   = $pos['position_z'];
        $char_map = $pos['map'];
        $from     = "X: ". $char_x ." - Y: ". $char_y ." - Z: ". $char_z ." - MAP ID: ". $char_map;

        $set = [
            "position_x" => $x, 
            "position_y" => $y, 
            "position_z" => $z, 
            "map" => $map 
        ];
        $Database->update("characters", $set, array("account" => $acct, "guid" => $character));

        if ( DATA['service']['teleport']['currency'] == "vp" )
	    {
            echo DATA['service']['teleport']['price'] . " 投票积分已从您的帐户中扣除。";
        }
        elseif ( DATA['service']['teleport']['currency'] == "dp" )
	    {
            echo DATA['service']['teleport']['price'] ." ". DATA['website']['donation']['coins_name'] ." 已从您的帐户中扣除。";
        }
        $Account->logThis("Teleported ". $Character->getCharName($character, $realm_id) ." to ". $location, "Teleport", $realm_id);
        echo TRUE;
    }
}

if ( $_POST['action'] == "service" )
{
    $guid     = $Database->conn->escape_string($_POST['guid']);
    $realm_id = $Database->conn->escape_string($_POST['realm_id']);
    $serviceX = $Database->conn->escape_string($_POST['service']);


    if ( $Character->isOnline($guid) == TRUE )
    {
        die("<b class=\"red_text\">请在继续之前退出游戏。");
    }

    if ( DATA['service'][$serviceX]['currency'] == 'vp' )
    {
        if ( $Account->hasVP($_SESSION['cw_user'], DATA['service'][$serviceX]['price']) == false )
	    {
            die("<b class=\"red_text\">投票积分不足！</b>");
    	}
    }

    if ( DATA['service'][$serviceX]['currency'] == 'dp' )
    {
        if ( $Account->hasDP($_SESSION['cw_user'], DATA['service'][$serviceX]['price']) == false )
	    {
            die("<b class=\"red_text\">钱不够 ". DATA['website']['donation']['coins_name'] ."</b>");
        }
    }

    switch ($serviceX)
    {
        default:
            die("未知错误");
            break;

        case('appearance'):
            $command = "customize";
            $info    = "Character customization";
            break;

        case('name'):
            $command = "rename";
            $info    = "Character rename";
            break;

        case('faction'):
            $command = "changefaction";
            $info    = "Faction change";
            break;

        case('race'):
            $command = "changerace";
            $info    = "Race change";
            break;
    }

    $Database->selectDB("webdb", $Database->conn);
    $statement = $Database->select("realms", null, null, "id='$realm_id'");
    $result = $statement->get_result();
    $row   = $result->fetch_assoc();

    if ( $row['sendType'] == "ra" )
    {
        $Server->sendRA("character ". $command ." ". $Character->getCharname($guid, $realm_id), $row['rank_user'], $row['rank_pass'], $row['host'], $row['ra_port']);
    }
    elseif ( $row['sendType'] == "soap" )
    {
        $Server->sendSoap("character ". $command ." ". $Character->getCharname($guid, $realm_id), $row['rank_user'], $row['rank_pass'], $row['host'], $row['soap_port']);
    }

    if ( DATA['service'][$serviceX]['currency'] == "vp" )
    {
        $Account->deductVP($Account->getAccountID($_SESSION['cw_user']), DATA['service'][$serviceX]['price']);
    }

    if ( DATA['service'][$serviceX]['currency'] == "dp" )
    {
        $Account->deductDP($Account->getAccountID($_SESSION['cw_user']), DATA['service'][$serviceX]['price']);
    }

    $Account->logThis("Performed a $info on ". $Character->getCharName($guid, $realm_id), $serviceX, $realm_id);

    echo TRUE;
} 