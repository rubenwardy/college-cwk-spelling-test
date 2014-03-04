<?php
// Include shared subprograms
require "common/common.php";

$user = 0;
if (!$_GET['id'] || $current_user->id == $_GET['id']){
	$user = $current_user;
	auth(AUTH_PUPIL);
}else{
	auth(AUTH_STAFF);
	if (!is_numeric($_GET['id']))
		msgscrn("User not found","The user id is invalid.","","");
	$user = User::get($_GET['id']);
	if (!$user)
		msgscrn("User not found","The user does not exist.","","rc");
}

// Show test player page
showHeader($user->firstname." ".$user->surname." - Profile");


?>
<h1><?php echo $user->firstname;?> <?php echo $user->surname;?></h1>
<?php

function prof($one,$two){
	if ($two)
		echo "$one: $two<br />\n";
}

prof("Username", $user->username);
prof("Year", $user->year);
prof("Group", $user->group);

if ($user->id == $current_user->id || $current_user->isAdmin())
	echo "<p><a class=\"button\" href=\"profile.php?id={$user->id}&edit=1\">Edit</a></p>";

?>