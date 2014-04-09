<?php
require "common/common.php";

// Check for submissions
if ($_POST['submitted']=="true"){
	// Check username and password
	$user = User::getUsername($_POST['username']);
	if ($user){
		// hash password to be checked against database
		$hash = md5($_POST['password']);
		if ($hash == $user->password){
			// Log user in
			$_SESSION['user'] = $user->username;
			header("location: index.php");
		}
	}
}

// Show page
showHeader("Log in");
?>
<form action="login.php" method="post">
	<input type="hidden" name="submitted" value="true" />
	<?php
		if ($_POST['submitted']=="true"){
			// Show wrong username / password message
			echo "<span style=\"color:red;\">Wrong username / password</span><br>";
		}
	?>
	Username: <input type="test" name="username"><br>
	Password: <input type="password" name="password"><br>
	<input type="submit" value="Log in">
</form>
