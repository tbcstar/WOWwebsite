<?php

class Database
{
	
	public $connectedTo = "global";
    public $conn = null;

    public function __construct()
    {
        $this->connect();
    }

    public function connect()
    {
        $this->conn = mysqli_connect(
            DATA['website']['connection']['host'], 
            DATA['website']['connection']['username'], 
            DATA['website']['connection']['password']);

        if ( $this->conn != null && $this->conn != false )
	    {
            $this->conn->set_charset("UTF8");
	    }
        else
	    {
            buildError("<b>数据库连接错误：</b> 连接不能建立。 错误：" . $this->conn->error, NULL);
            $this->connectedTo = null;
        }
    }

    public function realm($realmid)
    {
        self::selectDB("webdb");

        if (DATA['characters']['host'] != DATA['website']['connection']['host'] || 
            DATA['characters']['user'] != DATA['website']['connection']['user'] || 
            DATA['characters']['pass'] != DATA['website']['connection']['password'])
	    {
            $this->conn->set_charset("UTF8");
            return mysqli_connect(
                DATA['characters']['host'], 
                DATA['characters']['user'], 
                DATA['characters']['pass'])
            or buildError("<b>数据库连接错误：</b> 无法与 Realm 建立连接。 错误： " . $this->conn->error, NULL);
        }
        else
        {
            self::connect();
        }

        $this->conn->select_db(DATA['characters']['database']) 
            or buildError("<b>数据库选择错误：</b> 无法选择Realm数据库。 错误：" . $this->conn->error, NULL);

        $this->connectedTo = "chardb";
    }

    public function selectDB($db, $realmid = 1)
    {
        switch ($db)
        {
            default:
                $this->conn->select_db($db);
                break;

            case "logondb": 
                $this->conn->select_db(DATA['logon']['database']);
                break;

            case "webdb":
                $this->conn->select_db(DATA['website']['connection']['name']);
                break;

            case "worlddb":
                $this->conn->select_db(DATA['world']['database']);
                break;

            case "chardb":
                $this->conn->select_db(DATA['characters']['database']);
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
            throw new Exception("First Parameter Cannot Be Empty", 1);
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
        else
        {
            $bind_values .= "b";
        }

        # Adds into the $sql string the value given by the parameter
        if ( $where !== null )
        {
            $sql .= "WHERE $where ";
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

        # Checks wether the $variables is an array
        ## If so run through the values within it and check wether
        ## they are numeric or string and adds it into the $bind_values
        if ( is_array($variables) )
		{
            foreach (array_keys($variables) as $key => $value)
            {
                $sql .= "$value";
                if ( !empty($variables[$key + 1]) )
                {
                    $sql .= ",";
                }
            }
            $sql .= ") VALUES (";

            foreach ($variables as $key)
			{
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
            $statement->bind_param($bind_values, ...$variables);
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
}

$Database = new Database();

/*************************#/
# Realms & service prices #
# automatic settings      #
#*************************/
$realms  = array();
$service = array();

$Database->selectDB("webdb");

try
{
    //Realms
    $statement = $Database->select("realms", null, null, "ORDER BY id ASC");
    $getRealms = $statement->get_result();
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
    $statement->close();

    # Service prices
    $statement = $Database->select("service_prices", "enabled, price, currency, service");
    $getServices = $statement->get_result();
    while ($row = $getServices->fetch_assoc())
    {
        $service[$row['service']]['status']   = $row['enabled'];
        $service[$row['service']]['price']    = $row['price'];
        $service[$row['service']]['currency'] = $row['currency'];
    }
    $statement->close();
}
catch( Exception $e )
{
    buildError($e, null);
} 