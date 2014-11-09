<?php
include ("../functions/functions.php");
ajaxhead ();

if ($GLOBALS ['user']->isLoggedIn() == true)
{
	//update my ping
	$GLOBALS['conn']->query ("UPDATE bz_login SET lastPing = '".date ("Y-m-d G:i:s")."' WHERE loginId = ".$GLOBALS['user']->getDetail ("loginId"));
	
	//get data on other users
	$query = "SELECT bz_login.lastActive, bz_user_details.value AS name FROM bz_login, bz_user_details"
			." WHERE bz_login.lastPing >= '".date ("Y-m-d G:i:s", strtotime ("-90 seconds"))."'"
			." AND bz_login.lastCategoryId = ".$_POST ['categoryId']
			." AND bz_login.loginId = bz_user_details.loginId"
			." AND bz_user_details.name LIKE 'name'";
	$result = $GLOBALS['conn']->query ($query);
	//return JSON object
	echo "{";
		echo "\"online\": [";
	$num = 0;
	while ($row = $result->fetch_assoc ())
	{
		if ($num > 0)
			echo ",";
		echo "{";
		echo "\"name\":\"".$row ['name']."\",";
		$lastActive = strtotime ($row ['lastActive']);
		$diff = time () - $lastActive;
		if ($diff <= 600)
			$status = 1;
		else
			$status = 2;
		echo "\"status\":\"$status\"";
		echo "}";
		$num ++;
	}
		echo "]";
	echo "}";
}
?>