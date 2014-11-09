<?php
include ("../functions/functions.php");
ajaxhead ();

if ($GLOBALS ['user']->isLoggedIn() == true)
{
	//update activity
	$GLOBALS['conn']->query ("UPDATE bz_login SET lastActive = '".date ("Y-m-d G:i:s")."' WHERE loginId = ".$GLOBALS['user']->getDetail ("loginId"));
}
?>