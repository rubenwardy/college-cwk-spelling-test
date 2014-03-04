<?php
// Include shared subprograms
require "common/common.php";

auth(AUTH_STAFF);

// Get search critera
$surname = $_GET['surname'];
$group = null;
$year = null;
$teacher = $_GET['teacher'];
$rank = $_GET['rank'];

// Do validation
if (($year!=null && !is_numeric($year)) || ($rank!=null && !is_numeric($rank)))
	msgscrn("Query blocked","Your search terms are invalid.","","");

$q = "";

if ($surname)
	$q .= "surname LIKE '$surname'";	

if ($rank)
	$q .= (($q!="")?" AND ":"") . "rank = $rank"; // The ? here adds ' AND ' if there was a previous condition

// Get tests
$users = User::_search( ($q!="")? "WHERE $q" : "" ); // The ? here adds 'WHERE' if there is a query

// Show test player page
showHeader("User Search");

echo "<h1>Users</h1>\n";

// Show filter form
echo "<form action=\"users.php\" method=\"get\"><fieldset><legend>Filters</legend>\n";
echo "Surname: <input type=\"text\" name=\"surname\" value=\"$surname\"><br>\n";
echo "Rank: <select name=\"rank\">\n";
echo "<option value=\"\"".(($rank==null)?" selected":"").">Any</option>";
echo "<option value=\"1\"".(($rank==1)?" selected":"").">Pupil</option>";
echo "<option value=\"2\"".(($rank==2)?" selected":"").">Staff</option>";
echo "<option value=\"3\"".(($rank==3)?" selected":"").">Admin</option>";
echo "</select><br>\nYear group: <i>content coming</i><br>\n";
echo "<input type=\"submit\" value=\"Filter\">";
echo "</fieldset></form><br>\n";

echo "<table class=\"resultTable\"><tr><th width=\"50%\">Name</th><th style=\"width: 10%;\">Year</th><th style=\"width:10%;\">Group</th><th style=\"width:30%;\">Controls</th></tr>\n";

foreach ($users as $u){
	// Apply additional filters
	if ($year!=null || $group!=null){
		//TODO
	}
	
	echo "<tr><td>{$u->surname}, {$u->firstname}</td><td>{$u->year}</td><td>{$u->group}</td><td><a href=\"profile.php?id={$u->id}\" class=\"button\">Profile</a></td></tr>\n";
}

echo "</table>\n";