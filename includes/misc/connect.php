<?php

class Database
{
	
	public $connectedTo = "global";
    public $conn = null;

    public function __construct()
    {
        $config_file = file_get_contents("includes/configuration.json");
        $config_file1 = file_get_contents("includes/classes/error.php");
        define("DATA", json_decode($config_file, true));
        $this->connect();

        /*************************#/
        # Realms & service prices #
        # automatic settings      #
        #*************************/
        $realms  = array();
        $service = array();

        try
        {
            $this->selectDB("webdb");
            //Realms
            $statement = $this->select("realms", null, null, null, "ORDER BY id ASC");
            $getRealms = $statement->get_result();
            while ($row = $getRealms->fetch_assoc())
            {
                $realms['realms'][$row['id']]['id']           = $row['id'];
                $realms['realms'][$row['id']]['name']         = $row['name'];
                $realms['realms'][$row['id']]['chardb']       = $row['char_db'];
                $realms['realms'][$row['id']]['description']  = $row['description'];
                $realms['realms'][$row['id']]['port']         = $row['port'];

                $realms['realms'][$row['id']]['rank_user']    = $row['rank_user'];
                $realms['realms'][$row['id']]['rank_pass']    = $row['rank_pass'];
                $realms['realms'][$row['id']]['ra_port']      = $row['ra_port'];
                $realms['realms'][$row['id']]['soap_port']    = $row['soap_port'];

                $realms['realms'][$row['id']]['host']         = $row['host'];

                $realms['realms'][$row['id']]['sendType']     = $row['sendType'];

                $realms['realms'][$row['id']]['mysqli_host']  = $row['mysqli_host'];
                $realms['realms'][$row['id']]['mysqli_user']  = $row['mysqli_user'];
                $realms['realms'][$row['id']]['mysqli_pass']  = $row['mysqli_pass'];
            }
            $statement->close();

            # Service prices
            $statement = $this->select("service_prices", "enabled, price, currency, service");
            $getServices = $statement->get_result();
            while ($row = $getServices->fetch_assoc())
            {
                $service['service'][$row['service']]['status']   = $row['enabled'];
                $service['service'][$row['service']]['price']    = $row['price'];
                $service['service'][$row['service']]['currency'] = $row['currency'];
            }
            $statement->close();

            if ( defined("DATA") )
            {
                $config_file = fopen("includes/configuration.json", "w");
                $data = @array_merge(DATA, $realms, $service);
                $json_config = json_encode($data);
                fwrite($config_file, $json_config);
                fclose($config_file);
            }
        }
        catch( Exception $e )
        {
            $this->buildError($e, null);
        }
    }
    
 
    public function connect()
    {   
        
        @$this->conn = mysqli_connect(
            @DATA['website']['connection']['host'], 
            @DATA['website']['connection']['username'], 
            @DATA['website']['connection']['password']);

        if ( $this->conn)
	    {
            $this->conn->set_charset("UTF8");
	    }
        else
	    {   /*echo '数据库连接错误';*/
            /*$this->buildError("<b>数据库连接错误：</b> 连接不能建立。 错误：", NULL);*/
            $this->connectedTo = null;
        }
    }

    public function selectDB($db, $realmid = 1)
    {
        switch ($db)
        {
            default:
                $this->conn->select_db($db);
                break;

            case "logondb": 
                $this->conn->select_db(@DATA['logon']['database']);
                break;

            case "webdb":
                $this->conn->select_db(@DATA['website']['connection']['name']);
                break;

            case "worlddb":
                $this->conn->select_db(@DATA['world']['database']);
                break;

            case "chardb":
                $this->conn->select_db(@DATA['characters']['database']);
                break;
        }
        return TRUE;

		$Database->conn->query( "SET NAMES 'utf8'");
		$Database->conn->query( 'SET character_set_connection=utf8');
		$Database->conn->query( 'SET character_set_client=utf8');
		$Database->conn->query( 'SET character_set_results=utf8');

	}
    public function select($table, $column = null, $variables = null, $where = null, $extra = null)
    {
        if ( is_null($this->conn) )
        {
            throw new Exception("数据库变量为空", 1);
        }

        $sql = "SELECT ";

        # Checks wheter there's a specific value to return
        # If not set to all e.g *
        if ( $column == null )
        {
            $sql .= "* ";
        }
        # Checks if there's more than 1 specific value
        # And sets them
        else if ( is_array($column) )
        {
            $column = implode(",", $column);
            $sql .= $column." ";
        }
        # Sets the specific value
        else
        {
            $sql .= $column." ";
        }

        # Checks wether the table name is empty
        ## If it is throws an error with the given message
        if ( empty($table) )
        {
            throw new Exception("第一个参数不能为空", 1);
            exit;
        }
        $sql .= "FROM $table ";

        # Create the bind_values variable to save the value types that are going to
        ## be `changed` in the sql statement
        $bind_values = "";

        # Checks wether the $variables is an array
        ## If so run through the values within it and check wether
        ## they are numeric or string and adds it into the $bind_values
        if ( is_array($variables) )
        {
            foreach ($variables as $key)
            {
                if ( is_numeric($key) )
                {
                    $bind_values .= "i";
                }
                elseif ( is_string($key) )
                {
                    $bind_values .= "s";
                }
                elseif ( is_double($key) )
                {
                    $bind_values .= "d";
                }
                else
                {
                    $bind_values .= "b";
                }
            }
        }
        # Or checks if it's numeric
        elseif ( is_numeric($variables) )
        {
            $bind_values .= "i";
        }
        # If it's not an array checks wether its a string
        elseif ( is_string($variables) )
        {
            $bind_values .= "s";
        }
        elseif ( is_double($variables) )
        {
            $bind_values .= "d";
        }
        elseif ( !is_null($variables) )
        {
            $bind_values .= "b";
        }

        # Adds into the $sql string the value given by the parameter
        if ( $where !== null )
        {
            $sql .= "WHERE $where";
        }

        if ( $extra !== null )
        {
            $sql .= $extra;
        }

        # Prepares the statement
        if ( ( $statement = $this->conn->prepare($sql) ) === false )
        {
            throw new Exception($this->conn->error, 1);
        }

        # Checks wether the $variables is null, if not proceeds
        if ( !is_null($variables) )
        {
            # If it's an array, bind_param with ... to go through all of the array's values
            if ( is_array($variables) )
            {
                $statement->bind_param($bind_values, ...$variables);
            }
            else
            {
                $statement->bind_param($bind_values, $variables);
            }
        }

        # Executes the statement and prints an error if there is one into the logs
        if ( !$statement->execute() )
        {
            throw new Exception($statement->error, 1);          
        }

        return $statement;
    }

    public function insert($table, $variables)
	{
        $sql = "INSERT INTO ";

        # Checks wether the table name is empty
        ## If it is throws an error with the given message
        if ( empty($table) )
        {
            throw new Exception("First Parameter Cannot Be Empty", 1);
            exit;
        }

        if ( empty($variables) )
        {
            throw new Exception("Second Parameter Cannot Be Empty", 1);
            exit;
        }
        $sql .= "$table ";

        # Checks if there's more than 1 specific value
        # And sets them
        $sql .= "(";


        # Create the bind_values variable to save the value types that are going to
        ## be `changed` in the sql statement
        $bind_values = null;

        $vals = [];

        # Checks wether the $variables is an array
        ## If so run through the values within it and check wether
        ## they are numeric or string and adds it into the $bind_values
        if ( is_array($variables) )
		{
            foreach (array_keys($variables) as $key => $value)
            {
                $sql .= "$value";
                if ( !empty(array_keys($variables)[$key + 1]) )
                {
                    $sql .= ",";
                }
            }
            $sql .= ") VALUES (";

            foreach ($variables as $key)
			{
                $vals[] = $key;
                if ( substr($sql, -1) !== "," && substr($sql, -1) == "?")
				{
                    $sql .= ",";
                }

                if ( is_numeric($key) )
                {
                    $bind_values .= "i";
                    $sql .= "?";
                }
                elseif ( is_string($key) )
                {
                    $bind_values .= "s";
                    $sql .= "?";
                }
                elseif ( is_double($key) )
                {
                    $bind_values .= "d";
                    $sql .= "?";
                }
                else
                {
                    $bind_values .= "b";
                    $sql .= "?";
				}
			}
		}
        else
        {
            throw new Exception("Second Paramenter Should Be An Array", 1);
        }

        $sql .= ")";


        # Prepares the statement
        if ( ($statement = $this->conn->prepare($sql)) === false )
        {
            throw new Exception($this->conn->error, 1);
        }

        # If it's an array, bind_param with ... to go through all of the array's values
        if ( is_array($variables) )
        {
            $statement->bind_param($bind_values, ...$vals);
        }
        else
        {
            $statement->bind_param($bind_values, $variables);
        }

        # Executes the statement and prints an error if there is one into the logs
        $statement->execute();

        return $statement;
    }

    public function update($table, $update, $where)
    {
        # Starts the UPDATE SQL string with UPDATE
        ## And also initializes a string $bind_values as a string
        ## to be the bind_param's first parameter
        $sql = "UPDATE ";
        $bind_values = "";

        # Checks whether the first parameter of the function is empty
        ## If so throws an Exception which then should be caught on the usage
        if ( empty($table) )
        {
            throw new Exception("First parameter cannot be empty", 1);
            exit;
        }

        # Adds into the $sql string the table name and a space
        $sql .= "$table ";

        # Checks whether $update is null
        if ( !is_null($update) )
        {
            # Adds the SET into the $sql
            $sql .= "SET ";

            if ( is_array($update) )
            {

                foreach (array_keys($update) as $key => $value)
                {
                    $sql .= "$value=?";
                    if ( !empty($update[$key + 1]) )
                    {
                        $sql .= ",";
                    }
                }
                # If $update is an array, it goes through it and
                ## adds into the $sql string each value and a comma if needed
                ## (which it checks if there's another value after the current one to add the comma)
                foreach ($update as $key => $value)
                {
                    if ( is_numeric($value) )
                    {
                        $bind_values .= "i";
                    }
                    elseif ( is_string($value) )
                    {
                        $bind_values .= "s";
                    }
                    elseif ( is_double($value) )
                    {
                        $bind_values .= "d";
                    }
                    else
                    {
                        $bind_values .= "b";
                    }
                }
            }
            elseif ( is_numeric($update) )
            {
                $bind_values .= "i";
                $sql .= "$update=? ";
            }
            elseif ( is_string($update) )
            {
                $bind_values .= "s";
                $sql .= "$update=? ";
            }
            elseif ( is_double($update) )
            {
                $bind_values .= "d";
                $sql .= "$update=? ";
            }
            else
            {
                # If it's not an array it adds into the $sql string
                ## the variable
                $bind_values .= "b";
                $sql .= "$update=? ";
            }

        }

        if ( !is_null($where) )
        {
            $sql .= " WHERE ";
            if ( is_array($where) )
            {
                foreach (array_keys($where) as $key => $value)
                {
                    $sql .= "$value=? ";
                    if ( !empty($where[$key + 1]) )
                    {
                        $sql .= "AND";
                    }
                }

                # If $where is an array it goes through it and
                ## adds into the $sql string each $value within the array
                ### By default it adds an AND if there's more than 1 value
                foreach ($where as $key => $value)
                {
                    if ( is_numeric($value) )
                    {
                        $bind_values .= "i";
                    }
                    elseif ( is_string($value) )
                    {
                        $bind_values .= "s";
                    }
                    elseif ( is_double($value) )
                    {
                        $bind_values .= "d";
                    }
                    else
                    {
                        $bind_values .= "b";
                    }
                }
            }
            elseif ( is_numeric($where) )
            {
                $bind_values .= "i";
                $sql .= "$where=? ";
            }
            elseif ( is_string($where) )
            {
                $bind_values .= "s";
                $sql .= "$where=? ";
            }
            elseif ( is_double($where) )
            {
                $bind_values .= "d";
                $sql .= "$where=? ";
            }
            else
            {
                # If it's not an array it adds into the $sql string
                ## the variable
                $bind_values .= "b";
                $sql .= "$where=? ";
            }
        }

        $variables = array();

        if ( is_array($update) )
        {
            # If $update is an array it merges it into $variables
            $variables = array_merge($variables, $update);
        }
        elseif ( is_string($update) || !is_string($update) )
        {
            if ( !is_string($update) )
            {
                # If $update is not a string, it turns it into one
                $update .= "";
            }
            # Converts the string into an array and merges it into $variables
            $update = explode(" ", $update);
            $variables = array_merge($variables, $update);
        }

        if ( is_array($where) )
        {
            # If $where is an array it merges it into $variables
            $variables = array_merge($variables, $where);
        }
        elseif ( is_string($where) || !is_string($where) )
        {
            if ( !is_string($where) )
            {
                # If $where is not a string, it turns it into one
                $where .= "";
            }
            # Converts the string into an array and merges it into $variables
            $where = explode(" ", $where);
            $variables = array_merge($variables, $where);
        }


        # Checks whether the Update $sql worked, if not prints a message with a default error to the user
        ## And prints the actual error to the logs of the CMS
        if ( ($statement = $this->conn->prepare($sql)) === false )
        {
            throw new Exception($this->conn->error, 1);
        }
        else
        {
            # Then it checks whether $variables is not empty so that it can be used to
            ## go through the second parameter of bind_param, having the "..." makes it go
            ## through all of the values within the array
            # If $variables is empty it checks wether if $update is an array
            ## and does the same as $variables if not just adds it directly into bind_param as a string
            if ( !empty($variables) )
            {
                $statement->bind_param($bind_values, ...$variables);
            }
            elseif ( is_array($update))
            {
                $statement->bind_param($bind_values, ...$update);
            }
            elseif ( !is_array($update) )
            {
                $statement->bind_param($bind_values, $update);
            }

            # Executes the statement and prints an error if theres any
            if ( !$statement->execute() )
            {
                throw new Exception($statement->error, 1);
            }

            # And prints the $statement which can be used to check wether
            ## there was an error or not
            return $statement;
        }
    }
    
 public    function buildError($error, $num , $hidden_error = "")
	{
		if ( @DATA['use']['debug'] == false )
		{
			$this->log_error($error ." ". $hidden_error, $num);
		}
		else
		{
			$this->errors($error, $num);
		}
	}

public	function errors($error, $num)
	{
		$this->log_error(strip_tags($error), $num);
		die("<center><b>网站错误</b><br/>
			网站脚本遇到错误并关闭。 <br/><br/>
			<b>错误消息: </b>". $error ."  <br/>
			<b>错误序号: </b>". $num ."
			<br/><br/><br/><i>TBCstar 团队
			<br/><font size='-2'>www.tbcstar.com</font></i></center>
			");
	}

public	function log_error($error, $num)
	{
		$this->error_log("*[" . date("d M Y H:i") . "] " . $error ."\n", 3, "error.log");
	}

public	function loadCustomErrors()
	{
		$this->set_error_handler("customError");
	}

public	function customError($errno, $errstr)
	{
		if ($errno != 8 && $errno != 2048 && @DATA['use']['debug'] == true)
		{
			$this->error_log("*[" . date("d M Y H:i") . "]<i>" . $errstr . "</i>\n", 3, "error.log");
		}
	} 
} 