<?php
// Include shared subprograms
$level = "../";
require "../common/common.php";

auth(AUTH_PUPIL);

// Load Test details from database
$test = Test::get($_GET['id']);

if (!$test)
	msgscrn("Test not found","Test could not be found","","");
	
$author = User::get($test->userID);



// Show test player page
showHeader($test->title." - Results");

echo "<h2>".$test->title."</h2>";

echo "<p>Created on ".$test->datecreated;

if ($author)
	echo " by ".$author->firstname ." ". $author->surname;
	
echo "</p>";

// Show user test submissions
if ($current_user->rank == 1 || $_GET['user']){
	$search_id = ($current_user->rank == 1) ? $current_user->id : $_GET['user'];
	$me = Score::_search("WHERE userID = $search_id AND testID = {$test->id}");
	if (count($me)<1){
		echo ($search_id == $current_user->id) ? "You have not taken this test yet.":"The pupil has not taken this test yet.";
		if($current_user->rank > 1)
			echo "<p><a href=\"view.php?id={$test->id}\" class=\"button\">Back</a></p>";
		else
			echo "<p><a href=\"take.php?id={$test->id}\" class=\"button\">Take test</a></p>";
	}else{
		echo "<table class=\"resultTable\">";
		echo "<tr><th>Score</th><th>Incorrect words</th></tr>";
		foreach($me as $s){
			echo "<tr><td>{$s->score}</td><td>";
			$ww = $s->wrongWords();
			if ($ww){
				echo "<span style=\"color:red\">";
				$comma = false;
				foreach($ww as $w){
					if ($comma)
						echo ", ";
						
					echo $w->word;
					$comma = true;
				}
				echo "</span>";
			}else{
				echo ":D";
			}
			echo "</td></tr>";
		}
		echo "</table>";
		
		if($current_user->rank > 1)
			echo "<p><a href=\"view.php?id={$test->id}\" class=\"button\">Back</a></p>";
		else
			echo "<p><a href=\"take.php?id={$test->id}\" class=\"button\">Retake test</a></p>";
	}
}else if($current_user->rank > 1){
	$users = $test->users();
	if (!$users || count($users)<1){
		echo "No pupils are to take this test<br>";
	}else{
		echo "<table class=\"resultTable\">";
		echo "<tr><th>User</th><th>Score</th><th>Attempts</td><th></th></tr>";
		foreach($users as $u){
			echo "<tr><td>{$u->surname} {$u->firstname}</td>";
			
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