<?php
include ("functions/functions.php");

head("User Management");
if ($GLOBALS ['user']->getDetail ("userType") == 1)
{
	//logged in as admin
	navigation ();
	echo "<div id=\"below_nav\">";
		echo "<div class=\"main\">";
		echo "<h1>Manage Users</h1>";
		echo "<div id=\"display_users\" style=\"margin-bottom:20px;\">";
			display_users ();
		echo "</div>";
		echo "<h2>Add New User</h2>";
		user_registration (0, "users.php");
		echo "</div>";
	echo "</div>";
}
body_end ();
?>