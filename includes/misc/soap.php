<?php	

function send_soap($command, $username, $password, $host, $soapport)
{

$client = new SoapClient(NULL,
	array(
		"location" => "http://$host:$soapport/",
		"uri" => "urn:TC",
		"style" => SOAP_RPC,
		'login' => $username,
		'password' => $password
	));
try 
    {
    $result = $client->executeCommand(new SoapParam($command, "command"));
    return;

    echo "命令成功!输出:<br />\n";
    echo $result;
    }
catch (Exception $e)
    {
    echo "命令失败!原因:<br />\n";
    echo $e->getMessage();
    }
}