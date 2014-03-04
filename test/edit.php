<?php
// Include shared subprograms
$level = "../";
require "../common/common.php";

auth(AUTH_STAFF);

$test = Test::get($_GET['id']);

if (!$test){
	msgscrn("Test not found.","We were unable to find that test.","","");
}

// Show test player page
showHeader("Edit - {$test->title}");

echo "<h1>{$test->title}</h1>\n";


?>

<fieldset>
<legend>Test Properties</legend>

Title: <input type="text" value="<?php echo $test->title;?>" />

<br /><br />

<input type="submit" value="Save">
</fieldset>

<table style="margin-top: 30px;">
<tr><th>Answer</th><th>Definition</th><th>Near words</th><th></th></tr>
<!--<tr><td>Potato</td><td>Not a vegetable</td><td>Potatoe</td><td><a href="edit.php?id=<?php echo $test->id;?>&w=1" class="button">Edit</a> <a class="button">Delete</a></td></tr>-->
<?php
	// Output row
	function orow($word,$def,$near,$wid){
		global $test;
		echo "<tr><td style=\"width: 10%;\">$word</td><td style=\"width: 40%;\">$def</td><td style=\"width: 30%;\">$near</td><td><a style=\"width: 20%;\" href=\"edit.php?id={$test->id}&w=$wid\" class=\"button\">Edit</a> <a href=\"edit.php?id={$test->id}&delete_word=$wid\" class=\"button\">Delete</a></td></tr>\n";
	}
	$words = $test->words();
	foreach ($words as $w){
		orow($w->word,$w->def,"",$w->id);
	}
?>
</table>