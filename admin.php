<?php
// Include shared subprograms
require "common/common.php";

// Only allow admins to view this page
auth(AUTH_ADMIN);

if ($_GET['id']=="user"){
	if (strlen($_POST['username']) > 25)
		msgscrn("Username too long","Unable to create that user because the username was too long.");
	if (strlen($_POST['first']) > 30)
		msgscrn("Firstname too long","Unable to create that user because the first name was too long.");
	if (strlen($_POST['sur']) > 30)
		msgscrn("Surname too long","Unable to create that user because the surname was too long.");
		
		
	$user = new User(null);
	$user->username = $_POST['username'];
	$user->firstname = $_POST['first'];
	$user->surname = $_POST['sur'];
	$user->password = md5($_POST['pass']);
	$user->rank = $_POST['rank'];
	$user->year = $_POST['year'];
	$user->group = $_POST['group'];
	$user->save();
	header("location: admin.php");
	die("");
}

if ($_GET['id']=="increase"){
	if ($_GET['con']==1){
		$all_users = User::all();
		foreach ($all_users as $user){
			if (is_numeric($user->year)){
				$user->year = $user->year + 1;
				$user->save();
			}
		}
		header("location: admin.php");
		die("");
	}else{
		showHeader("Are you sure?");?>
		<h2>Are you sure?</h2>
		
		<p>
			This will increase all pupils years by 1.
		</p>
		
		<a class="button" href="admin.php?id=increase&con=1">Increase all years</a> <a class="button" href="admin.php">Cancel</a>
		<?php die("");
	}
}

// Show admin settings page
showHeader("Admin Settings");
?>
<h2>Admin Settings</h2>

<p>Please be careful in this section!</p>

<fieldset>
<legend>Danger Zone</legend>
<a href="admin.php?id=increase">Increase years</a>
</fieldset>

<form action="admin.php?id=user" method="post">
<fieldset>
<legend>Add User</legend>
<i>* - required</i><br>
* Firstname: <input type="text" name="first" required><br>
* Surname: <input type="text" name="sur" required><br>
* Username: <input type="text" name="username" required><br>
* Password: <input type="text" name="pass" value="password" required><br>
* Rank: <select name="rank">
<option value="1" selected>Pupil</option>";
<option value="2">Staff</option>";
<option value="3">Admin</option>";
</select><br>
Year: <input type="number" name="year" min=3 max=6 /><br>
Group: <input type="text" name="group" size=1 /><br><br>
<input type="submit" value="Create" />
</fieldset>
</form>