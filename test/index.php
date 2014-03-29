<?php
// Include shared subprograms
$level = "../";
require "../common/common.php";

auth(AUTH_PUPIL);

// Get search critera
$author = $_GET['user'];

// Do validation
if (($author!=null && !is_numeric($author)) || ($year!=null &&!is_numeric($year)))
	msgscrn("Query blocked","Your search terms are invalid.","","");

// Collect search criteria
$query = "";
if ($author)
	$query .= "userID = $author";


// Get tests
$tests = Test::_search( ($author!="")? "WHERE userID = $author" : "" ); // The ? here adds WHERE if there is a query

// Show test player page
showHeader("Test Search");

echo "<h1>Tests</h1>\n";

// Show buttons for staff members
if ($current_user->isStaff()){
	// Create a test button
	echo "<a class=\"button\" href=\"add.php\">Create</a>\n";
	
	// Filter by my tests button
	if ($author != $current_user->id)
		echo "<a class=\"button\" href=\"?user={$current_user->id}\">My Tests</a>\n";
		
	// Spacing
	echo "<br /><br />";
}

// Start table
echo "<table class=\"resultTable\"><tr><th>Name</th><th>Controls</th></tr>\n";

// Loop through tests with applied filters
foreach ($tests as $t){
	// Test details
	echo "<tr><td style=\"width: 60%;\">{$t->title}</td><td>";
	
	// Buttons
	if ($current_user->isStaff())
		echo "<a href=\"edit.php?id={$t->id}\" class=\"button\">Edit</a> ";
	else{
		$res = Score::getfromusertest($current_user->id,$t->id);
		if (!$res || count($res)==0)
			echo "<a href=\"take.php?id={$t->id}\" class=\"button\">Take</a> ";
		else
			echo "<a href=\"take.php?id={$t->id}\" class=\"button\">Retake</a> ";
	}
	echo "<a href=\"view.php?id={$t->id}\" class=\"button\">View Submissions</a></td></tr>\n";
}

// End table
echo "</table>\n";
