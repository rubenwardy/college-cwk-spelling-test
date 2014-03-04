<?php
// Include shared subprograms
$level = "../";
require "../common/common.php";

auth(AUTH_PUPIL);

// Get search critera
$author = $_GET['user'];
$group = null;
$year = null;

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

if ($author != $current_user->id){
	echo "<a class=\"button\" href=\"?user={$current_user->id}\">My Tests</a><br><br>\n";
}

echo "<table class=\"resultTable\"><tr><th>Name</th><th>Controls</th></tr>\n";

foreach ($tests as $t){
	// Apply additional filters
	if ($year!=null || $group!=null){
		//TODO
	}
	
	echo "<tr><td style=\"width: 60%;\">{$t->title}</td><td><a href=\"edit.php?id={$t->id}\" class=\"button\">Edit</a> <a href=\"view.php?id={$t->id}\" class=\"button\">View Submissions</a></td></tr>\n";
}

echo "</table>\n";