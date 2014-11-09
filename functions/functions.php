<?php
//functions file
include ("config.php");
include ("secret.php");
//include classes
foreach (glob("classes/*.php") as $filename)
{
    include $filename;
}
foreach (glob("../classes/*.php") as $filename)
{
    include $filename;
}
$url = explode("/", $_SERVER ['PHP_SELF']);
$pieces = sizeof ($url);
if (strcmp ($url [$pieces - 1], "setup.php") != 0)
	$GLOBALS ['conn'] = dbconn ();
$GLOBALS ['user'] = new User (0);
function head ($title)
{
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $title . " | " . app_name (); ?></title>
    <script type="text/javascript" src="scripts/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="scripts/jquery.nicescroll.min.js"></script>
    <script type="text/javascript" src="scripts/base.js"></script>
    <script type="text/javascript" src="scripts/sha1.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale:1.0, user-scalable=no" />
    <link rel="stylesheet" href="style.css" />
    </head>
    <body>
    <?php
}
function body_end ()
{
	?>
    </body>
    </html>
    <?php
}
function step1_form ()
{
	?>
	<h2>Step 1 - Database Connection</h2>
    <form method="post" action="setup.php">
    	<input type="hidden" name="step" value="2" />
        <div class="textfielddiv">
        	<input type="text" class="textfield" name="username" id="username" placeholder="Username" />
        </div>
        <div class="textfielddiv">
        	<input type="password" class="textfield" placeholder="Password" name="password" id="password" />
        </div>
        <div class="textfielddiv">
        	<input type="text" class="textfield" placeholder="Database" name="database" id="database" />
        </div>
        <div class="textfielddiv">
        	<input type="text" class="textfield" placeholder="Host" name="host" id="host" />
        </div>
        <div class="textfielddiv">
        	<input type="text" class="textfield" placeholder="Prefix" name="prefix" id="prefix" />
        </div>
        <div class="textfielddiv">
        	<input type="text" class="textfield" placeholder="Project Name" name="project" id="project" />
        </div>
        <div class="textfielddiv"><input type="submit" class="submitfield" value="Next" /></div>
    </form>
    <?php
}

function user_logout ()
{
	$cookie = $_COOKIE [db_prefix()."-login"];
	$GLOBALS ['conn']->query ("DELETE FROM ".db_prefix."_cookie_tracker WHERE cookie = '$cookie'");
	setcookie (db_prefix()."-login", "", time() - 3600,'/');
}

function ajaxhead ()
{
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache"); 
}

function randomString($length = 50) 
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $charLength = strlen($chars)-1;
	$randomString = "";
    for($i = 0 ; $i < $length ; $i++)
    {
        $randomString .= $chars[mt_rand(0,$charLength)];
    }
    return $randomString;
}

function user_registration ($userType = 0, $redirect)
{
	?>
    <div style="position:relative;">
    <form method="post" action="processregistration.php" onsubmit="return register();">
    	<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
        <div class="textfielddiv">
        	<input type="text" class="textfield" placeholder="Name" name="name" id="name"  />
        </div>
        <div class="textfielddiv">
        	<input type="text" class="textfield" placeholder="Email Address" name="email" id="email" />
        </div>
        <div class="textfielddiv">
        	<input type="password" class="textfield" name="password" id="password" placeholder="Chosen Password" />
        </div>
        <div class="textfielddiv">
        	<input type="password" class="textfield" name="confirm" id="confirm"  placeholder="Confirm Password" />
        </div>
        <?php
		if ($userType == 0)
		{
        	echo "<div class=\"textfielddiv\">";
        	echo "<select name=\"userType\" class=\"textfield\">";
			$result = $GLOBALS ['conn']->query("SELECT userType, name FROM ".db_prefix()."_user_type");
			while ($row = $result->fetch_assoc())
			{
				echo "<option value=\"".$row ['userType']."\">".$row ['name']."</option>";
			}
        	echo "</select></div>";
		}
		else
		{
			echo "<input type=\"hidden\" name=\"userType\" value=\"$userType\" />";
		}
		?>
        <div class="textfielddiv"><input type="submit" class="submitfield" value="Next" /></div>
    </form>
    </div>
    <?php
}

function login_user ($loginId)
{
	$value = $loginId. randomString (20);
	setcookie (db_prefix()."-login",$value,time()+3600*24*30,'/');
	$query = "SELECT COUNT(*) AS total FROM ".db_prefix()."_cookie_tracker WHERE loginId = $loginId";
	$loginresult = $GLOBALS ['conn']->query($query);
	if (!$loginresult)
		$num = 0;
	else
	{
		$row = $loginresult->fetch_assoc();
		$num = $row ['total'];
	}
	if($num >= 2)
	{
	 	$GLOBALS ['conn']->query ("UPDATE ".db_prefix()."_cookie_tracker SET cookie = '".$value."', lastIp = '".$_SERVER['REMOTE_ADDR']."' WHERE loginId = $loginId LIMIT 1"); 
	}
	else
	{
		$GLOBALS ['conn']->query("INSERT INTO ".db_prefix()."_cookie_tracker (cookie,loginId,lastIP) VALUES ('$value',$loginId,'".$_SERVER['REMOTE_ADDR']."')");		
	}
}

function login ()
{
	?>
    <div class="loginbox">
    <div style="width:207px; position:relative; margin-left:auto; margin-right:auto">
        <h2>Login</h2>
        <form action="processlogin.php" method="post" onsubmit="login_enc();">
        <div style="margin-bottom:15px;"><?php if (isset ($_GET ['incp'])) echo "<span style=\"font-size:12px; color:red;\">Incorrect email or password. Please try again</span>"; ?>&nbsp;</div>
        <div class="textfielddiv"><input type="text" class="textfield" placeholder="Email Address" name="email" id="email"  /></div>
        <div class="textfielddiv">
            <input type="password" class="textfield" name="password" id="password" placeholder="Password" />
            <!--<div class="passwordtip" onclick="focustextbox ('password','passtip1')" id="passtip1">Chosen Password</div>-->
        </div>
        <div class="textfielddiv"><input type="submit" class="submitfield" value="Login" /></div>
        </form>
    </div>
    </div>
<?php
}

function navigation ()
{
	$next = 2;
	?>
    
    <a href="javascript: toggle_menu ()" id="mobilenav"><img src="images/mobile_menu_button.png" id="mobile_menu" width="40" /></a>
    <div id="navigation">
    	<div id="nav_left">
        	Welcome <?php echo $GLOBALS ['user']->getDetail ('Name'); ?>
        </div>
        <div id="nav_right">
        	<a id="nav1" class="navs" href="index.php">Chat</a>
            <?php
			if ($GLOBALS ['user']->getDetail ('userType') == 1)
			{
				echo "<a id=\"nav2\" class=\"navs\" href=\"users.php\">Manage Users</a>";
				$next = 3;
			}
			?>
            <a id="nav<?php echo $next; ?>" class="navs" href="logout.php">Logout</a>
        </div>
    </div>
    <?php
}

function create_category ($name)
{
	$GLOBALS ['conn']->query ("INSERT INTO bz_category (name) VALUES ('$name')");
	$topic = $GLOBALS ['conn']->insert_id;
	return $topic;
}

function default_category ()
{
	$result = $GLOBALS['conn']->query ("SELECT categoryId FROM bz_category WHERE active = 1 ORDER BY categoryId ASC LIMIT 1");
	if ($result->num_rows > 0)
	{
		$row = $result->fetch_assoc ();
		$topic = $row ['categoryId'];
	}
	else
	{
		//no active topics exist
		$topic = create_category ("BizChat");
	}
	return $topic;
}

function display_users ()
{
	echo "<h2>View Users</h2>";
			echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\">";
				echo "<tr>";
					echo "<th>Name</th><th>Email</th><th>Last Activity</th><th>User Type</th><th>Delete</th>";
				echo "</tr>";
	$result = $GLOBALS['conn']->query ("SELECT * FROM bz_login, bz_user_details WHERE bz_login.loginId = bz_user_details.loginId AND bz_user_details.name LIKE 'name'");
	while ($row = $result->fetch_assoc ())
	{
				echo "<tr>";
					echo "<td>".$row ['value']."</td>";
					echo "<td>".$row ['email']."</td>";
					echo "<td>".$row ['lastActive']."</td>";
					if (intval ($row ['userType']) == 1)
						echo "<td>Admin</td>";
					else
						echo "<td>User</td>";
					echo "<td align=\"center\"><a href=\"javascript: delete_user (".$row ['loginId'].")\"><img src=\"images/cross.png\"></a></td>";
				echo "</tr>";
	}
			echo "</table>";
}
?>