<?php
// Include shared subprograms
$level = "../";
require "../common/common.php";

auth(AUTH_PUPIL);

// Load Test details from database
$test = Test::get($_GET['id']);

// Check that test exists
if (!$test)
	msgscrn("Test not found","Test could not be found","","");
	
// Get author of test
$author = User::get($test->userID);

// Show page
showHeader($test->title." - Results");
echo "<h2>".$test->title."</h2>";
echo "<p>Created on ".$test->datecreated;
if ($author)
	echo " by ".$author->firstname ." ". $author->surname;
echo "</p>";

// Show user test submissions
if ($current_user->rank == 1 || $_GET['user']){
	// Get the id of the pupil whose results we are looking at
	$search_id = ($current_user->rank == 1) ? $current_user->id : $_GET['user'];
	
	// Get results
	$myres = Score::_search("WHERE userID = $search_id AND testID = {$test->id} ORDER BY scoreID desc");
	
	if (count($myres)<1){
		// They have not taken this test yet, display message
		if ($search_id == $current_user->id)
			echo "You have not taken this test yet.";
		else
			echo "The pupil has not taken this test yet.";
			
		// Show appropriate buttons
		if($current_user->rank > 1)
			echo "<p><a href=\"view.php?id={$test->id}\" class=\"button\">Back</a></p>";
		else
			echo "<p><a href=\"take.php?id={$test->id}\" class=\"button\">Take test</a></p>";
	}else{
		//	Display messages, and table start
		echo "<p>Each row in this table is an attempt at the test. The latest attempt is at the top</p>";
		if ($_GET['latest']==1){
			?>
			<style>
				.resultTable tr:nth-child(2){
					background: yellow;
				}
			</style>
			<p>The row marked in yellow was the latest test.</p>
			<?php
		}
		echo "<table class=\"resultTable\">";
		echo "<tr><th>Score</th><th>Incorrect words</th></tr>";
		
		// Loop through scores
		foreach($myres as $s){
			echo "<tr><td>{$s->score}</td><td>";
			
			// Get the words they got wrong, and print them.
			$ww = $s->wrongWords();
			if ($ww){
				echo "<span style=\"color:red\">";
				$comma = false;
				foreach($ww as $w){
					if ($comma)
						echo ", ";
						
					echo "'".$w->word."'";
					$comma = true;
				}
				echo "</span>";
			}else{
				echo ":D";
			}
			echo "</td></tr>";
		}
		echo "</table>";
		
		// Display appropriate buttons
		if($current_user->rank > 1)
			echo "<p><a href=\"view.php?id={$test->id}\" class=\"button\">Back</a>";
		else
			echo "<p><a href=\"take.php?id={$test->id}\" class=\"button\">Retake test</a>";
		echo "<a href=\"../report.php?id=$search_id\" class=\"button\">View Report</a></p>";
	}
}else if($current_user->rank > 1){
	// Get users that this test applies to
	$users = $test->users();
	if (!$users || count($users)<1){
		// Display message
		echo "No pupils are to take this test<br>";
	}else{
		// Display table head
		echo "<table class=\"resultTable\">";
		echo "<tr><th>User</th><th>Score</th><th>Attempts</td><th></th></tr>";
		
		// Loop through users
		foreach($users as $u){
			echo "<tr><td>{$u->surname} {$u->firstname}</td>";
			
			// Declare score here, so it is in the correct scope
			$score = -1;
			
			// Load score submissions
			$scr = Score::_search("WHERE userID = {$u->id} AND testID = {$test->id}");
			if ($scr){
				foreach ($scr as $s){
					if ($s->score > $score || $score == -1){
						$score = $s->score;
					}
				}
			}
			
			// Display score and attempts
			if (!$scr || count($scr)<1){
				echo "<td style=\"background:red;color:white;\" colspan=2>Test not taken yet!</td>";
			}else{
				echo "<td>$score</td><td>".count($scr)." attempts</td>";
			}
			echo "<td style=\"width: 200px;\"><a class=\"button\" href=\"view.php?id={$test->id}&user={$u->id}\">View</a> <a class=\"button\" href=\"../profile.php?id={$u->id}\">Profile</a></td></tr>";
		}
		echo "</table>";
	}
	
}

?>