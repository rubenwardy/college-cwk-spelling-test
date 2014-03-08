<?php
// Include shared subprograms
$level = "../";
require "../common/common.php";

auth(AUTH_STAFF);

$test = Test::get($_GET['id']);

if (!$test){
	msgscrn("Test not found.","We were unable to find that test.","","");
}

// Perform event callbacks
if ($_GET['mode']=="prop" && isset($_POST['title']) && $_POST['title']!=""){
	// Change the test's properties
	$test->title = $_POST['title'];
	$test->save();
	header("location: edit.php?id={$test->id}");
	die("");
}else if (
			$_GET['mode']=="nw" &&
			isset($_POST['word']) &&
			isset($_POST['def'])&&
			$_POST['word']!="" &&
			$_POST['def']!=""
		){
	// Add a new word
	$w = new Word(null);
	$w->testID = $test->id;
	$w->word = $_POST['word'];
	$w->def = $_POST['def'];
	$w->save();
	
	// Split near word field into elements
	$nws = explode(',', $_POST['nearword']);
	foreach ($nws as $nw){
		$nearword_word = trim($nw);
		if ($nearword_word){
			$nearword = new Nearword(null);
			$nearword->wordID = $w->id;
			$nearword->word = $nearword_word;
			$nearword->save();
		}
	}
	
	header("location: edit.php?id={$test->id}");
	die("");
}

// Show test player page
showHeader("Edit - {$test->title}");

echo "<h1>{$test->title}</h1>\n";

// Gets the mode, and validates the type of the word id given (should be integer)
if ($_GET['mode']=="ew" && $_GET['word'] && is_numeric($_GET['word'])){
	$word = Word::get($_GET['word']);
	if ($word){
		if ($_GET['submit']==1 &&
				isset($_POST['word']) && $_POST['word']!="" &&
				isset($_POST['def']) && $_POST['def']!="" &&
				isset($_POST['nearword'])){
			// Update word
			$word->word = $_POST['word'];
			$word->def = $_POST['def'];
			$word->save();
			$handle->query("DELETE FROM nearword WHERE wordID = ".$word->id) or die("Error deleting near words from mysql database :/<br>".mysqli_error($handle));
			
			// Split near word field into elements
			$nws = explode(',', $_POST['nearword']);
			foreach ($nws as $nw){
				$nearword_word = trim($nw);
				if ($nearword_word){
					$nearword = new Nearword(null);
					$nearword->wordID = $word->id;
					$nearword->word = $nearword_word;
					$nearword->save();
				}
			}
			
			header("location: edit.php?id={$test->id}");
			die("");
		}
		$nw = "";
		$list = $word->nearwords();
		foreach ($list as $n){
			// Add near word to list
			$nw .= (($nw=="")?"":", ")."{$n->word}";
		}
?>
<a href="edit.php?id=<?php echo $test->id;?>">Back</a><br>
<fieldset>
<legend>Edit Word</legend>

<form action="edit.php?id=<?php echo $test->id;?>&mode=ew&submit=1&word=<?php echo $word->id;?>" method="POST">
Word: <input type="text" value="<?php echo $word->word;?>" name="word" /><br>
Definition: <input type="text" value="<?php echo $word->def;?>" name="def" /><br>
Nearwords: <input type="text" size="70" value="<?php echo $nw;?>" name="nearword" />

<br /><br />

<input type="submit" value="Save">
</form>
</fieldset>
<?php
	}
}else if ($_GET['mode']=="dw" && $_GET['word'] && is_numeric($_GET['word'])){
	$word = Word::get($_GET['word']);
	if ($word){
		if ($_GET['submit']==1){
			$handle->query("DELETE FROM word WHERE wordID = ".$word->id) or die("Error deleting words from mysql database :/<br>".mysqli_error($handle));
			$handle->query("DELETE FROM nearword WHERE wordID = ".$word->id) or die("Error deleting near words from mysql database :/<br>".mysqli_error($handle));
			$handle->query("DELETE FROM wrongword WHERE wordID = ".$word->id) or die("Error deleting wrong word records from mysql database :/<br>".mysqli_error($handle));
			header("location: edit.php?id={$test->id}");
			die("");
		}
		$nw = "";
		$list = $word->nearwords();
		foreach ($list as $n){
			// Add near word to list
			$nw .= (($nw=="")?"":", ")."{$n->word}";
		}
	?>
<a href="edit.php?id=<?php echo $test->id;?>">Back</a><br>
<fieldset>
<legend>Delete Word</legend>

<form action="edit.php?id=<?php echo $test->id;?>&mode=dw&submit=1&word=<?php echo $word->id;?>" method="POST">
Are you sure?<br><br>
Word: <?php echo $word->word;?><br>
Definition: <?php echo $word->def;?><br>
Nearwords: <?php echo $nw;?>

<br /><br />

<input type="submit" value="Delete">
</form>
</fieldset>
<?php
}
}else{
?>

<fieldset>
<legend>Test Properties</legend>

<form action="edit.php?id=<?php echo $test->id;?>&mode=prop" method="POST">
Title: <input type="text" value="<?php echo $test->title;?>" name="title" />

<br /><br />

<input type="submit" value="Save">
</form>
</fieldset>

<fieldset>
<legend>Add Word</legend>

<form action="edit.php?id=<?php echo $test->id;?>&mode=nw" method="POST">
Word: <input type="text" name="word" /><br>
Definition: <input type="text" name="def" /><br>
Nearwords: <input type="text" size="60" name="nearword" /><br>
<i style="font-size:75%;">(^ Words that get 1/2 marks. Separate using commas)</i>

<br /><br />

<input type="submit" value="Save">
</form>
</fieldset>

<?php
}
?>

<table style="margin-top: 30px;">
<tr><th>Answer</th><th>Definition</th><th>Near words</th><th></th></tr>
<?php
	// Output row
	function orow($word,$def,$near,$wid){
		global $test;
		echo "<tr><td style=\"width: 10%;\">$word</td><td style=\"width: 40%;\">$def</td><td style=\"width: 30%;\">$near</td><td><a style=\"width: 20%;\" href=\"edit.php?id={$test->id}&mode=ew&word=$wid\" class=\"button\">Edit</a> <a href=\"edit.php?id={$test->id}&mode=dw&word=$wid\" class=\"button\">Delete</a></td></tr>\n";
	}
	$words = $test->words();
	foreach ($words as $w){
		$nw = "";
		$list = $w->nearwords();
		foreach ($list as $n){
			// Add near word to list
			$nw .= (($nw=="")?"":", ")."{$n->word}";
		}
		orow($w->word,$w->def,$nw,$w->id);
	}
?>
</table>