<?php
// Include shared subprograms
$level = "../";
require "../common/common.php";

auth(AUTH_PUPIL);

// Load Test details from database
$test = Test::get($_GET['id']);

if (!$test)
	msgscrn("Test not found","Test could not be found","","rc");
	
$author = User::get($test->userID);
$words = $test -> words();

if (!$words || count($words)==0)
	msgscrn("Test is empty","Test could not be taken",$test->title." by ".$author->firstname ." ". $author->surname,"rc");


// Check for form submission
if ($_POST['submitted']=="true"){
	// Score submission
	$scores = new Score(null);
	$scores->userID = $current_user->id;
	$scores->testID = $test->id;
	$scores->score = -1;
	$scores->save();
	
	// Marking submitted test
	$count = 0;
	foreach ($words as $w){
		$guess = $_POST['word_'.$w->id];
		
		if ($guess == $w->word){
			// Pupil got the word correct
			$count += 2;
		}else{
			// Enter as wrong
			$wrongword = new WrongWord(null);
			$wrongword->wordID = $w->id;
			$wrongword->scoreID = $scores->id;
			$wrongword->word = $guess;
			$wrongword->save();
			
			// Pupil got the word wrong, check for nearwords
			$near = $w->nearwords();
			foreach ($near as $n){
				if ($guess == $n->word){
					// Award half marks
					echo "pupil spelt {$w->word} almost right! ($guess)<br>";
					$count += 1;
					break;
				}
			}
		}
	}
	
	$scores->score = $count;
	$scores->save();
	header("location: view.php?id={$test->id}&user={$current_user->id}&latest=1");
	die("");
}


// Show test player page
showHeader($test->title." - Test Player");
echo "<h2>".$test->title."</h2>";

echo "<p>Created on ".$test->datecreated;

if ($author)
	echo " by ".$author->firstname ." ". $author->surname;
	
echo "</p>";
?>
<form action="take.php?id=<?php echo $_GET['id'];?>" method="post">
<input type="hidden" name="submitted" value="true" />
<table class="tblStripe">

<?php
foreach ($words as $w){
	echo "<tr><td>" .$w->def . "</td><td><input type=\"text\" name=\"word_".$w->id."\" /></td></tr>\n";
}
?>
</table>
<input type="submit" value="Check" />
</form>