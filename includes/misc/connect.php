<?php

class Connect 
{
	
	public static $connectedTo = "global";

    public static function connectToDB()
    {
        if ($conn = new mysqli(
            $GLOBALS['connection']['web']['host'], 
            $GLOBALS['connection']['web']['user'], 
            $GLOBALS['connection']['web']['password']))
	    {
            $conn->set_charset("UTF8");
            return $conn;
	    }
	 
        else
	    { 
            buildError("<b>数据库连接错误：</b> 连接不能建立。 错误： " . $conn->error, NULL);
            self::$connectedTo = null;
        }
    }

    public static function connectToRealmDB($realmid)
    {
        $conn = self::connectToDB();
        self::selectDB("webdb", $conn);

        if ($GLOBALS['realms'][$realmid]['mysqli_host'] != $GLOBALS['connection']['host'] || 
            $GLOBALS['realms'][$realmid]['mysqli_user'] != $GLOBALS['connection']['user'] || 
            $GLOBALS['realms'][$realmid]['mysqli_pass'] != $GLOBALS['connection']['password'])
	    {
            $conn->set_charset("UTF8");
            return new mysqli($GLOBALS['realms'][$realmid]['mysqli_host'], $GLOBALS['realms'][$realmid]['mysqli_user'], $GLOBALS['realms'][$realmid]['mysqli_pass'])
            or buildError("<b>数据库连接错误：</b> 无法与 Realm 建立连接。 错误： " . $conn->error, NULL);
        }
        else
        {
            self::connectToDB();
        }
        $conn->select_db($GLOBALS['realms'][$realmid]['chardb']) or buildError("<b>数据库选择错误：</b> 无法选择Realm数据库。 错误： " . $conn->error, NULL);
        self::$connectedTo = 'chardb';
    }
    public static function selectDB($db, $conn, $realmid = 1)
    {
        switch ($db)
        {
            default:
                $conn->select_db($db);
                break;

            case "logondb": 
                $conn->select_db($GLOBALS['connection']['logon']['database']);
                break;

            case "webdb":
                $conn->select_db($GLOBALS['connection']['web']['database']);
                break;

            case "worlddb":
                $conn->select_db($GLOBALS['connection']['world']['database']);
                break;

            case "chardb":
                $conn->select_db($GLOBALS['realms'][$realmid]['chardb']);
                break;
        }
        return TRUE;

		$conn->query( "SET NAMES 'utf8'");
		$conn->query( 'SET character_set_connection=utf8');
		$conn->query( 'SET character_set_client=utf8');
		$conn->query( 'SET character_set_results=utf8');

	}
}

$Connect = new Connect();
$conn    = $Connect->connectToDB();


/*     * ********************** */
/* Realms & service prices automatic settings
  /* (Indented on purpose)
  /************************ */
$realms  = array();
$service = array();

$Connect->selectDB("webdb", $conn);

//Realms
$getRealms = $conn->query("SELECT * FROM realms ORDER BY id ASC;");
while ($row = $getRealms->fetch_assoc())
{
    $realms[$row['id']]['id']           = $row['id'];
    $realms[$row['id']]['name']         = $row['name'];
    $realms[$row['id']]['chardb']       = $row['char_db'];
    $realms[$row['id']]['description']  = $row['description'];
    $realms[$row['id']]['port']         = $row['port'];

    $realms[$row['id']]['rank_user']    = $row['rank_user'];
    $realms[$row['id']]['rank_pass']    = $row['rank_pass'];
    $realms[$row['id']]['ra_port']      = $row['ra_port'];
    $realms[$row['id']]['soap_port']    = $row['soap_port'];

    $realms[$row['id']]['host']         = $row['host'];

    $realms[$row['id']]['sendType']     = $row['sendType'];

    $realms[$row['id']]['mysqli_host']  = $row['mysqli_host'];
    $realms[$row['id']]['mysqli_user']  = $row['mysqli_user'];
    $realms[$row['id']]['mysqli_pass']  = $row['mysqli_pass'];
}

//Service prices
$getServices = $conn->query("SELECT enabled, price, currency, service FROM service_prices;");
while ($row = $getServices->fetch_assoc())
{
    $service[$row['service']]['status']   = $row['enabled'];
    $service[$row['service']]['price']    = $row['price'];
    $service[$row['service']]['currency'] = $row['currency'];
}


## Unset Magic Quotes
if (get_magic_quotes_gpc())
{
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process))
	{
        if (is_array($val) || is_object($val))
		{
			foreach ($val as $k => $v)
			{
                unset($process[$key][$k]);
                if (is_array($v))
				{
                    $process[$key][stripslashes($k)] = $v;
                    $process[]                       = &$process[$key][stripslashes($k)];
                }
                else
                {
                    $process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
    }
    unset($process);
} 