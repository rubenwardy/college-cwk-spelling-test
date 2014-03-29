<?php
// Include shared subprograms
require "common/common.php";

// Get the pupil that is the subject of this report
$user = 0;
if (!$_GET['id'] || $current_user->id == $_GET['id']){
	$user = $current_user;
	auth(AUTH_PUPIL);
}else{
	auth(AUTH_STAFF);
	if (!is_numeric($_GET['id']))
		msgscrn("User not found","The user id is invalid.","","");
	$user = User::get($_GET['id']);
	if (!$user)
		msgscrn("User not found","The user does not exist.","","rc");
}

// Get assigned tests
$ass = $user->tests();

// Averaging variables
$sigma = 0.0; // Sum of scores
$count = 0; // Number of scores

// Hold some the processed scores, so we don't need to call the database again
$the_tests = Array();

foreach ($ass as $a){
	// Check the test has not already been added
	$is_in = false;
	foreach ($the_tests as $t){
		if ($a->test()->id == $t['testID']){
			$is_in = true;
			break;
		}
	}
	if ($is_in)
		continue;
	
	// Get score from database
	$res = Score::getfromusertest($user->id,$a->testID);
	$score = 0;
	$score_got = false;
	foreach ($res as $r){
		if (!$score_got==false || $r->score >= $score){
			$score_got = true;
			$score = $r->score;
		}
	}
	
	// Get maximum score
	$max = count($a->test()->words()) * 2;
	
	// Update average
	$perc = (($score+0.0) / ($max+0.0)) * 100;
	$sigma += $perc;
	$count += 1;
	
	// Add test overview to array
	array_push($the_tests,Array(
		"testID" => $a->test()->id,
		"date" => $a->test()->datecreated,
		"score" => $perc,
		"actual" => $score,
		"max" => $max,
		"title" => $a->test()->title,
		"taken" => $score_got
	));
}

// Calculate average
$aver = ($sigma / $count);

// Show page
showHeader("Pupil Report");

// Show header
echo "<h1>Report</h1>\n";
?>

<div id="chartContainer" style="height: 300px; width: 100%;">
	<h3>There should be a graph here!</h3>
	Your browser may not support this.
</div>
<table id="testlist">
<tr><th>Date</th><th>Test name</th><th>Score</th><th></th></tr>

<?php
// Create the table
foreach ($the_tests as $t){
	// Create table row - display message if pupil has not taken the test
	if ($t['taken']){
		// Get comparison to average
		$stat = "";
		if ($t['score'] > $aver)
			$stat = "Above average";
		elseif ($t['score'] < $aver)
			$stat = "Below average";
		else
			$stat = "Average";
			
		// Output table row
		echo "<tr><td style=\"width: 15%;\">".$t['date']."</td><td><a href=\"test/view.php?id=".
		$t['testID']."&user=".$user->id."\">".$t['title'] .
		"</a></td><td>".$t['actual']."/ ".$t['max']." - ".$t['score']."%</td><td>$stat</td></tr>\n";
	}else
		// Output table row
		echo "<tr><td style=\"width: 15%;\">".$t['date']."</td><td><a href=\"test/view.php?id=".
		$t['testID']."&user=".$user->id."\">".$t['title'] .
		"</a></td><td>Test not taken!</td><td></td></tr>\n";
}

echo "</table>\n<p>Average score: ".$aver."%</p>\n";

// Create the graph
?>
<script src="graph.min.js"></script>
<script>
	window.onload = function () {
		var chart = new CanvasJS.Chart("chartContainer", {
			title:{
				text: "<?php echo $user->firstname." ".$user->surname;?>"              
			},
			data: [              
				{
				/*** Change type "column" to "bar", "area", "line" or "pie"***/
				type: "column",
					dataPoints: [
<?php
	foreach ($the_tests as $t){
		echo "{label:\"".$t['date']."\", y:".$t['score']."},\n";
	}
?>
					]
				}
			],
			axisY:{
		      suffix: "%",
		      minimum: 0,
		      maximum: 100
		    },
		    axisX:{
		    	
		    }
		});
		
		chart.render();
	}
</script>