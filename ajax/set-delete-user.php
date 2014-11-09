<?php
include ("../functions/functions.php");
ajaxhead ();

if ($GLOBALS ['user']->getDetail("userType") == 1)
{
	$delete = new User (intval ($_POST ['loginId']));
	$delete->deleteUser ();
	
	display_users ();
}
?>