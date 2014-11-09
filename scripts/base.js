// JavaScript Document
var usercount = 1;
function add_user_type ()
{
	usercount ++;
	$('.users').append ("<div class=\"textfielddiv\">User Type "+usercount+": <input type=\"text\" class=\"textfield\" value=\"User\" name=\"user[]\" id=\"user"+usercount+"\" onfocus=\"checktext ('User', '', 'user"+usercount+"')\" onblur=\"checktext ('', 'User', 'user"+usercount+"')\" /></div>");
}
function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
function register ()
{
	var password = $('#password').val();
	var hash = CryptoJS.SHA1(password);
	var conf = $('#confirm').val();
			
	if (password == conf) //register user
	{
		$('#password').val (hash);
		$('#confirm').val (hash);
		return true;
	}
	else //registration criteria not met
	{
		alert ("Passwords do not match. Please try again");
		return false;
	}
}
function login_enc ()
{
	var password = $('#password').val();
	var hash = CryptoJS.SHA1(password);
	$('#password').val (hash);
}

$(document).ready (function ()
{
	$('#chattext').keydown (function (e)
	{
		if(e.which == 13 && e.shiftKey == false)
		{
			add_chat ();
		}
	});
});

$(window).load (function ()
{
	$("#chatarea").niceScroll();
	set_layout ();
	ping_update ()
	$(window).resize (function ()
	{
		set_layout ();
	});
});

function set_layout ()
{
	var window_height = $(window).height();
	var window_width = $(window).width();
	var chatsize = window_width - 435;
	$('#main_container').css ("height", window_height);
	$('.fulllength').css ("height", window_height-50);
	$('#chatarea').css ("width", chatsize);
	$('#chatarea').css ("height", (window_height-110));
	$('#chattext').css ("width", (chatsize-80));
	//var chatheight = $("#chatarea").height ();
	//$("#chatarea").niceScroll();
	$("#chatarea").getNiceScroll().resize();
	$("#chatarea").scrollTop($("#chatarea").prop("scrollHeight"));
	$('.msgarea').css ("width", (chatsize-340));
}

function add_chat ()
{
	var text = $('#chattext').val ();
	var name = $('#chatname').val ();
	var id = $('#userid').val ();
	var timestamp = new Date().getTime();
	$('#chattext').val ("");
	fbase.push ({name: name, message: text, time: timestamp, userid: id});
	ping_activity_update ()
}

function ping_update ()
{
	var topic = $('#topic').val ();
	var senddata = "categoryId="+topic;
	$.ajax (
	{
		url:"ajax/set-ping.php",
		dataType: "json",
		type:"POST",
		data:senddata,
		success: function(returndata)
		{
			var num = returndata.online.length;
			$('#chatright').html ("<ul id=\"online\">");
			for (var i = 0; i < num; i++)
			{
				var row = "<li style=\"list-style:none; padding:5px; font-size:14px; line-height:10px\">";
				if (returndata.online[i].status == 1)
					row += "<img src=\"images/green-led.png\" width=\"10\"> ";	
				else
					row += "<img src=\"images/orange-led.png\" width=\"10\"> ";
				row += returndata.online[i].name+"</li>";
				$('#chatright').append (row);
			}
			$('#chatright').append ("</ul>");
			setTimeout (ping_update, 30000);
		}
	});
}

function ping_activity_update ()
{
	$.ajax (
	{
		url:"ajax/set-ping-activity.php",
		type:"GET",
	});
}

function delete_topic (categoryId)
{
	var senddata = "categoryId="+categoryId;
	$.ajax (
	{
		url:"ajax/set-delete-category.php",
		data:senddata,
		type:"POST",
		success:function (returndata)
		{
			location.reload ();
		}
	});
}