<?php
// Include shared subprograms
require "common/common.php";

// Get the user whose profile we are going to show
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

// Change the user's password, if requested.
if ( ($_GET['edit']==1) && ($_POST['submitted']==1) && ($user->id == $current_user->id || $current_user->isAdmin()) ){
	// Check that the old password given is correct.
	// Admins editing other user's passwords do not need this
	if ($current_user->id == $user->id){
		$hashed = md5($_POST['old']);
		if ($user->password != $hashed){
			// Show error page
			header("location: profile.php?id={$user->id}&edit=1&failed=1");
			die("");
		}
	}
	// Check that the new passwords match
	if ($_POST['new'] == $_POST['confirm'] && $_POST['new']!=""){
		// Change password
		$user->password = md5($_POST["new"]);
		$user->save();
		
		// Go back to the profile page
		header("location: profile.php?id={$user->id}");
		die("");
	}
	// Show error page
	header("location: profile.php?id={$user->id}&edit=1&failed=1");
	die("");
}

// Show test player page
showHeader($user->firstname." ".$user->surname." - Profile");
?>
<h1><?php echo $user->firstname;?> <?php echo $user->surname;?></h1>
<?php

// This function is an easy way of outputing the user's details
function profile_row($name,$value){
	if ($value)
		echo "$name: $value<br />\n";
}

profile_row("Username", $user->username);
profile_row("Year", $user->year);
profile_row("Group", $user->group);

echo "<p>";
if ($user->id == $current_user->id || $current_user->isAdmin()){
if ($_GET['edit']==1){
	?>
	<form action="<?php echo "profile.php?id={$user->id}&edit=1";?>" method="post">
		<input type="hidden" value="1" name="submitted" />
		<?php if ($_GET['failed']==1) echo "<p style=\"color:red;\">Failed to change password.</p>";
		if ($current_user->id == $user->id){ ?>
		Old password: <input type="password" name="old" /><br>
		<?php } ?>
		New password: <input type="password" name="new" /><br>
		Confirm new password: <input type="password" name="confirm" /><br>
		<input type="submit" value="Save">
	</form>
	</p><p>
	<?php
}else{
		echo "<a class=\"button\" href=\"profile.php?id={$user->id}&edit=1\">Edit</a>";
}
}
	
if ($user->isPupil())
	echo " <a class=\"button\" href=\"report.php?id={$user->id}\">View Report</a>";
echo "</p>";

?>