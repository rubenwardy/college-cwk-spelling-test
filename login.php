<?php
require "common/common.php";

if ($_POST['submitted']=="true"){
	$user = User::getUsername($_POST['username']);
	if ($user){
		$hash = md5($_POST['password']);
		if ($hash == $user->password){
			$_SESSION['user'] = $user->username;
			header("location: index.php");
		}
	}
}

//msgscrn("Log in","<a href=\"login.php?l=1\">Log in</a>","","");
showHeader("Log in");
?>
<form action="login.php" method="post">
	<input type="hidden" name="submitted" value="true" />
	<?php
		if ($_POST['submitted']=="true"){
			echo "<span style=\"color:red;\">Wrong username / password</span><br>";
		}
	
	?>
	Username: <input type="test" name="username"><br>
	Password: <input type="password" name="password"><br>
	<input type="submit" value="Log in">
	
	<p>
		<b>Admin account:</b> aw / pass<br>
		<b>Pupil account:</b> to / pass
	</p>
</form>
