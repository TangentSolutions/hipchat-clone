// JavaScript Document
var usercount = 1;
function add_user_type ()
{
	usercount ++;
	$('.users').append ("<div class=\"textfielddiv\">User Type "+usercount+": <input type=\"text\" class=\"textfield\" value=\"User\" name=\"user[]\" id=\"user"+usercount+"\" onfocus=\"checktext ('User', '', 'user"+usercount+"')\" onblur=\"checktext ('', 'User', 'user"+usercount+"')\" /></div>");
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
	
});

$(window).load (function ()
{
	set_layout ();
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
	$('#chatarea').css ("height", (window_height-140));
	$('#chattext').css ("width", (chatsize-100));
	$("#chatarea").niceScroll();
}

function add_chat ()
{
	var text = $('#chattext').val ();
	var name = $('#chatname').val ();
	var timestamp = new Date().getTime();
	$('#chattext').val ("");
	fbase.push ({name: name, message: text, time: timestamp});
}