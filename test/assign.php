<?php
// Include shared subprograms
$level = "../";
require "../common/common.php";

auth(AUTH_STAFF);

$test = Test::get($_GET['id']);

if (!$test){
	msgscrn("Test not found.","We were unable to find that test.","","");
}

// Even callback
if (isset($_GET['mode']) && $_GET['mode']=="add"){
	$year = $_POST['year'];
	$group = $_POST['group'];
	if ($year=="" || is_numeric($year)){
		$ass = new TestAssign(null);
		$ass->testID = $test->id;
		$ass->year = $year;
		$ass->group = $group;
		$ass->save();
	}
	header("location: assign.php?id={$test->id}");
	die("");
}

// Show test player page
showHeader("Assignment - {$test->title}");

echo "<h1>{$test->title}</h1>\n";
?>
<a href="edit.php?id=<?php echo $test->id;?>">Back</a><br>
<fieldset>
<legend>Assign</legend>

<form action="assign.php?id=<?php echo $test->id;?>&mode=add" method="POST">
Year: <input type="number" name="year" /><br />
Group: <input type="text" name="group" />
<br /><br />
<input type="submit" value="Save">
</form>
</fieldset>

<table style="margin-top: 30px;">
<tr><th>Year</th><th>Group</th><th></th></tr>
<?php
	// Output row
	function orow($year,$group,$id){
		global $test;
		echo "<tr><td style=\"width: 20%;text-align:center;\">$year</td><td style=\"width: 20%;text-align:center;\">$group</td><td><a href=\"assign.php?id={$test->id}&mode=delete&delete=$id\" class=\"button\">Unassign</a></td></tr>\n";
	}
	$ass = TestAssign::_search("WHERE testID = ".$test->id);
	if ($ass){
		foreach ($ass as $a){
			orow($a->year,$a->group,$a->id);
		}
	}
?>
</table>