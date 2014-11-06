<?php
error_reporting (-1);
include ("functions/functions.php");
$email = $_POST ['email'];
$pass = md5 ($_POST ['password']);

$result = $GLOBALS ['conn']->query("SELECT * FROM ".db_prefix()."_login WHERE email LIKE '$email' AND password = '$pass' LIMIT 1");
if ($row = $result->fetch_assoc())
{
	$loginId = $row ["loginId"];
	login_user ($loginId);
	header ("location:index.php");
}
else
{
	//echo "Email = $email<br>Password (hashed) = $pass";
	header ("location:index.php?incp=1");
}
?>