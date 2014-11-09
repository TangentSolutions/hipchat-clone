<?php
include ("../functions/functions.php");
ajaxhead ();

if ($GLOBALS ['user']->getDetail("userType") == 1)
{
	//update activity
	$GLOBALS['conn']->query ("UPDATE bz_category SET active = 0 WHERE categoryId = ".$_POST ['categoryId']);
}
?>