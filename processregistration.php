<?php
require_once ("functions/functions.php");
//check to see if other users exist
$result = $GLOBALS ['conn']->query ("SELECT COUNT(*) AS total FROM ".db_prefix()."_login");
$countRow = $result->fetch_assoc ();
$count = $countRow ['total'];
if ($GLOBALS ['user']->getDetail ('userType') == 1 || $count == 0)
{
	$userData = array ();
	$userData ['email'] = $_POST ['email'];
	$userData ['name'] = $_POST ['name'];
	$userData ['userType'] = $_POST ['userType'];
	$userData ['password'] = $_POST ['password'];
	$redirect = $_POST ['redirect'];
	
	$user = new User ($userData);
	
	header ("location: $redirect");
}
?>