<?php
// Include shared subprograms
$level = "../";
require "../common/common.php";

auth(AUTH_STAFF);

// Even callback
if (isset($_POST['title']) && $_POST['title']!=""){
	$title = $_POST['title'];
	$test = new Test(null);
	$test->title = $title;
	$test->datecreated = date('Y-m-d H:i:s');
	$test->userID = $current_user->id;
	$test->save();
	header("location: edit.php?id={$test->id}");
	die("");
}

// Show test player page
showHeader("Create test");

echo "<h1>{$test->title}</h1>\n";
?>
<fieldset>
<legend>Add Test</legend>

<form action="add.php" method="POST">
Title: <input type="text" name="title" />
<br /><br />
<input type="submit" value="Create">
</form>
</fieldset>
