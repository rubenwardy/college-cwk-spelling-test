<?php
// The root url
$root_url = "http://spell.rubenwardy.kd.io/";

function burl($page){
	global $root_url;
	return $root_url.$page;
}

// Shows the web page to the user, with a title
function showHeader($title){
	include("template/header.php");
}

// Shows a message screen to the user, and stops the scripts
function msgscrn($msg,$text,$more,$buttons){
	showHeader($msg);
	echo "<h1>$msg</h1>\n";
	echo $text;
	
	// Add buttons
	if ($buttons){
		if ($buttons == "rc"){
			echo "<p><a class=\"button\" href=\"//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\">Retry</a>";
			echo "<a class=\"button\" href=\"".burl("")."\">Cancel</a>";
		}else if ($buttons->type == "yn"){
			echo "<p><a class=\"button\" href=\"".$buttons->yes."\">Yes</a>";
			echo "<a class=\"button\" href=\"".$buttons->no."\">No</a>";
		}else if ($buttons->type == "oc"){
			echo "<p><a class=\"button\" href=\"".$buttons->ok."\">Ok</a>";
			echo "<a class=\"button\" href=\"".$buttons->cancel."\">Cancel</a>";
		}else if ($buttons->type == "c"){
			echo "<p><a class=\"button\" href=\"".$buttons->c."\">Continue</a>";
		}
	}
	
	// Add more stuff text box
	if ($more){
		echo "<div class=\"code\">$more</div>";
	}
	
	// Exit
	die ("");
}

// Check that the user is logged in
define("AUTH_PUPIL",1);
define("AUTH_STAFF",2);
define("AUTH_ADMIN",3);
function auth($level){
	global $current_user;
	if (!$current_user || $current_user->rank < $level){
		if ($current_user->rank >= AUTH_PUPIL){
			msgscrn("Access Denied","You do not have the authority to do this.<br>Try logging in with ".getAuthLabel($level)." privileges.<p><a class=\"button\" href=\"".burl("logout.php")."\">Log Out</a></p>","","");
		}
		header("location: /login.php?id=$level");
		die("");
	}
}

// Return a string describing the given rank level
function getAuthLabel($level){
	if ($level == AUTH_PUPIL)
		return "pupil";
	else if ($level == AUTH_STAFF)
		return "staff";
	else if ($level == AUTH_STAFF)
		return "admin";
	else
		return "lvl($level)";
}

// Connect to the database
$handle = mysqli_connect("localhost","root","pass","spelling") or msgscrn("Database connection error","We can not connect to the MySQL database at this time.",0,0);

// Include modules
require_once "database/user.php";
require_once "database/test.php";
require_once "database/testassign.php";
require_once "database/word.php";
require_once "database/nearword.php";
require_once "database/score.php";
require_once "database/wrongword.php";

// User login
session_start();
$current_user = null;
if ($_SESSION['user']!="" && $_SESSION['user']!=null){
	$current_user = user::getUsername($_SESSION['user']);
}
?>