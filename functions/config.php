<?php
//configuration
function dbconn ($create = 0)
{
	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "bizchat";

	if ($create == 0)
		$conn = new mysqli($server, $username, $password, $database);
	else
	{
		$conn = new mysqli($server, $username, $password);
		$sql = "CREATE DATABASE $database";
		$conn->query($sql);
		$conn->close();
		$conn = new mysqli($server, $username, $password, $database);
	}
	
	return $conn;
}

function app_name ()
{
	return "BizChat"; //app name
}
function db_prefix ()
{
	return "bz"; //prefix
}
?>