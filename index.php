<?php
include ("functions/functions.php");

head("Home Page");
if ($GLOBALS ['user']->isLoggedIn() == true)
{
	echo "<div id=\"main_container\">";
	//logged in
	navigation ();
	if (isset ($_POST ['newtopic']))
	{
		$topic = create_category ($_POST ['newtopic']);
	}
	else if (isset ($_GET ['topic']))
	{
		$topic = $_GET ['topic'];
		$result = $GLOBALS['conn']->query ("SELECT active FROM bz_category WHERE categoryId = $topic");
		if ($result->num_rows > 0)
		{
			$row = $result->fetch_assoc ();
			if ($row ['active'] == 1)
			{
				//topic checks out
			}
			else
			{
				//topic has been deleted
				$topic = default_category ();
			}
		}
		else
		{
			//topic does not exist
			$topic = default_category ();
		}
	}
	else
	{
		//no topic in query string
		$topic = default_category ();
	}
	//update lastTopicId and lastPing in login table
	$GLOBALS['conn']->query ("UPDATE bz_login SET lastCategoryId = $topic, lastActive = '".date ("Y-m-d G:i:s")."', lastPing = '".date ("Y-m-d G:i:s")."' WHERE loginId = ".$GLOBALS['user']->getDetail ("loginId"));
	include ("php-jwt-master/Authentication/JWT.php");
    include_once "FirebaseToken.php";
    $tokenGen = new Services_FirebaseTokenGenerator(SECRET_KEY);
    $token = $tokenGen->createToken(array("uid" => $GLOBALS ['user']->getDetail ('loginId'), "name" => $GLOBALS ['user']->getDetail ('name')));
	?>
    <script src='https://cdn.firebase.com/js/client/1.1.1/firebase.js'></script>
    <script type="text/javascript">
    var fbase = new Firebase("https://incandescent-inferno-4342.firebaseio.com/<?php echo $topic; ?>");

    fbase.authWithCustomToken(<?php echo "'".$token."'" ?>, function(error, authData) {
      if (error) {
        console.log("Login Failed!", error);
      } else {
        console.log("Login Succeeded!", authData);
      }
    });
   
	fbase.once ("value", function (snapshot) {
		var historytest = snapshot.hasChildren();
		if (historytest === false)
		{
			$('#chatarea').html ("No Messages Sent Yet");
		}
	});
    fbase.on("child_added", function(snapshot) {
		 
      var data = snapshot.val();
	  // create a new javascript Date object based on the timestamp
	var date = new Date(data.time);
	// hours part from the timestamp
	var hours = "0" + date.getHours();
	// minutes part from the timestamp
	var minutes = "0" + date.getMinutes();
	// seconds part from the timestamp
	var seconds = "0" + date.getSeconds();
	
	// will display time in 10:30:23 format
	var formattedTime = hours.substr(hours.length-2) + ':' + minutes.substr(minutes.length-2) + ':' + seconds.substr(seconds.length-2);
	
	if (new Date ().getTime () - date.getTime () >= 86400000)
	{
		var curr_date = date.getDate();
		var curr_month = date.getMonth() + 1; //Months are zero based
		var curr_year = date.getFullYear();
		if (curr_date < 10)
			curr_date = "0" + curr_date;
		if (curr_month < 10)
			curr_month = "0" + curr_month;
		formattedTime = curr_date + "-" + curr_month + "-" + curr_year + " " + formattedTime;
	}
	var temptext = "<div class=\"chatmsg";
	if (data.userid == <?php echo $GLOBALS ['user']->getDetail ('loginId'); ?>)
	{
		temptext += " mymsg";
	}
	temptext += "\">";
			temptext += "<div class=\"namearea\">"+data.name+"</div>";
			temptext += "<div class=\"msgarea\">"+nl2br(data.message)+"</div>";
			temptext += "<div class=\"timearea\">"+formattedTime+"</div>";
		temptext += "</div>";
	if ($('#chatarea').html () == "Loading..." || $('#chatarea').html () == "No Messages Sent Yet")
	{
	  	$('#chatarea').html (temptext);
	}
	else
	    $('#chatarea').append (temptext);
	set_layout ();
    });
    </script>
        <div id="chatleft" class="fulllength">
        	<h2 id="topicheading">Select Topic</h2>
            <div style="position:relative">
        	<ul id="topics">
        	<?php
			$top = 5;
			$delbtns = "";
			$result = $GLOBALS ['conn']->query ("SELECT categoryId, name FROM bz_category WHERE active = 1");
			while ($row = $result->fetch_assoc())
			{
				echo "<a href=\"index.php?topic=".$row ['categoryId']."\"><li";
				
				if ($row ['categoryId'] == $topic)
					echo " class=\"selected\" ";
				
				echo ">".$row ['name'];
				echo "</li></a>";
				if ($GLOBALS ['user']->getDetail ('userType') == 1)
				{
					$delbtns .= "<div class=\"deletetopic\" style=\"top:".$top."px !important;\"><img src=\"images/cross.png\" onclick=\"delete_topic (".$row ['categoryId'].")\"></div>";
					$top += 27;
				}
			}
			?>
            </ul>
            <?php echo $delbtns; ?>
            </div>
            <div id="create_topic_div">
            	<form method="post">
            		<input type="text" name="newtopic" id="topic_create" placeholder="New Topic" /> 
                	<input type="submit" value="Create" id="topic_create_btn" />
                </form>
            </div>
        </div>
        <div id="chatarea" class="fulllength">Loading...</div>
        <textarea id="chattext" placeholder="Type Message Here"></textarea>
        <input type="hidden" id="chatname" value="<?php echo $GLOBALS ['user']->getDetail ('name'); ?>" />
        <input type="hidden" id="userid" value="<?php echo $GLOBALS ['user']->getDetail ('loginId'); ?>" />
        <input type="hidden" id="topic" value="<?php echo $topic; ?>" />
        <input type="button" id="chatsubmit" onclick="add_chat ()" value="Send" />
        <div id="chatright" class="fulllength"></div>
    </div>
    <?php
}
else
{
	//not logged in
	login ();
}
body_end ();
?>