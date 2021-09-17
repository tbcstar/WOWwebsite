<?php
    /**********************
    CraftedWeb第一代
    主要加载程序文件
    *********************/
    
    require "includes/misc/headers.php"; //Load sessions, error reporting & ob.
    
    if ( file_exists("install/index.php") )
    {
    	header("Location: install/index.php");
    }
    
    define('INIT_SITE', TRUE);
    
    require "includes/configuration.php"; //Load configuration file
    $config_file = file_get_contents("includes/configuration.json");
    define("DATA", json_decode($config_file, true));
    
    
    ###LOAD MAXIMUM ITEM LEVEL DEPENDING ON EXPANSION###
    switch(DATA['website']['expansion']) 
    {
        case 0:
            $maxItemLevel = 100;
            break;

        case 1:
            $maxItemLevel = 175;
            break;

        default:
        case 2:
            $maxItemLevel = 284;
            break;

        case 3:
            $maxItemLevel = 416;
            break;

        case 4:
        case 5:
        case 6:
        case 7:
            break;
    }

    if( DATA['website']['expansion'] > 2 )
    {
        $tooltip_href = "www.wowhead.com/";
    }
    else
    {
        $tooltip_href = "www.openwow.com/?";
    }

    //Set the error handling.
    if(file_exists("includes/classes/error.php"))
    {
        require "includes/classes/error.php";
    }       
    elseif(file_exists("../classes/error.php"))
    {
        require "../classes/error.php";
    }       
    elseif(file_exists("../includes/classes/error.php"))
    {
        require "../includes/classes/error.php";
    }   
    elseif(file_exists("../../includes/classes/error.php"))
    {
        require "../../includes/classes/error.php";
    }   
    elseif(file_exists("../../../includes/classes/error.php"))
    {
        require "../../../includes/classes/error.php";
    }

    loadCustomErrors(); //Load custom errors


    if ( DATA['maintainance']['state'] == TRUE && !in_array($_SERVER['REMOTE_ADDR'], DATA['maintainance']['allowed_ips']) )
    {
        die("<center><h3>网站维护</h3>". DATA['website']['title'] ." 目前正在进行一些重大维护，将尽快恢复。<br/><br/>TBCstar 项目组</center>");
    }
    
    require "includes/misc/connect.php"; //Load connection class
    
    $Database = new Database();
    
    require "includes/misc/func_lib.php";
    require "includes/misc/compress.php";
    
    require "includes/classes/account.php";
    require "includes/classes/server.php";
    require "includes/classes/website.php";
    require "includes/classes/shop.php";
    require "includes/classes/character.php";
    require "includes/classes/cache.php";
    require "includes/classes/plugins.php";
    
    global $Plugins, $Account, $Website;
    
    /******* 加载插件 ***********/
    $Plugins->globalInit();
    
    $Plugins->init("classes");
    $Plugins->init("javascript");
    $Plugins->init("modules");
    $Plugins->init("styles");
    $Plugins->init("pages");
    
    //加载配置。
    if ( DATA['website']['enable_plugins'] == true )
    {
    	if ( $_SESSION['loaded_plugins'] != NULL )
    	{
    		if ( is_array($_SESSION['loaded_plugins']) || is_object($_SESSION['loaded_plugins']) )
    		{
    			foreach($_SESSION['loaded_plugins'] as $folderName)
    			{
    				if ( file_exists("plugins/". $folderName ."/config.php") )
    				{
    					include_once "plugins/". $folderName ."/config.php";
    				}
    			}
    		}
    	}
    }
    
    $Account->getRemember(); //Remember thingy.
    
    //这是为了防止错误 "Undefined index: p"
    if ( !isset($_GET['page']) )
    {
    	$_GET['page'] = 'login';
    }
    
    ###投票系统####
    if ( isset($_SESSION['votingUrlID']) && $_SESSION['votingUrlID'] != 0 && DATA['website']['vote']['type'] == "confirm" )
    {
        if ( $Website->checkIfVoted($Database->conn->escape_string($_SESSION['votingUrlID'])) == TRUE )
            {
                die("?page=vote");
            }
    	
    	$accound_id = $Account->getAccountID($_SESSION['cw_user']);
    	
    	$next_vote = time() + DATA['website']['vote']['timer'];
    	
        $Database->selectDB("webdb");
    
        $insert_values = array
        (
            "siteid" => $Database->conn->escape_string($_SESSION['votingUrlID']),
            "userid" => $accound_id,
            "timestamp" => time(),
            "next_vote" => $next_vote,
            "ip" => $_SERVER['REMOTE_ADDR']
        );
    
    	$Database->insert("votelog", $insert_values);
    
    	$statement = $Database->select("votingsites", "points, url", null, "id=". $Database->conn->escape_string($_SESSION['votingUrlID']));
        $siteData = $statement->get_result();
        $row = $siteData->fetch_assoc();
    	
    	if ( $siteData->num_rows == 0 )
    	{
    		header("Location: index.php");
    		unset($_SESSION['votingUrlID']);
    	}
    	
    	//Update the points table.
    	$add = $row['points'] * DATA['website']['vote']['multiplier'];
    	$Database->update("account_data", array("vp" => "vp+$add"), array("id"=>$accound_id));
    	
    	unset($_SESSION['votingUrlID']);
    	
    	header("Location: ?page=vote");
    }
    
    ###会话安全###
    if ( !isset($_SESSION['last_ip']) && isset($_SESSION['cw_user']) )
    {
    	$_SESSION['last_ip'] = $_SERVER['REMOTE_ADDR'];
    }
    elseif ( isset($_SESSION['last_ip']) && isset($_SESSION['cw_user']) )
    {
    	if ( $_SESSION['last_ip'] != $_SERVER['REMOTE_ADDR'] )
    	{
    		header("Location: ?page=logout");
    	}
    	else
    	{
    		$_SESSION['last_ip']=$_SERVER['REMOTE_ADDR'];
    	}
    }