<?php
$noload = true;
require "common/common.php";

showHeader("Dashboard");

echo "<h2>Unit Tests</h2>";

echo "<span style=\"font-family:'Courier New','Arial',sans-serif;\">";

// Get by id
echo "User get by id: ";

try{
	$user = User::get(1);
	
	if ($user && $user->username == "aw")
		echo "Passed.<br>\n";
	else
		echo "Failed.<br>\n";
} catch (Exception $e) {
	echo "Failed.<br>";
}
	
// Get by username
echo "User get by username: ";


try{ 
	$user = User::getUsername("aw");
	
	if ($user && $user->id == 1)
		echo "Passed.<br>\n";
	else
		echo "Failed.<br>\n";
} catch (Exception $e) {
	echo "Failed.<br>";
}
	
// Get by id
echo "User create: ";

try{
	$user = new User();
	$user->username = "testx";
	$user->firstname = "first";
	$user->surname = "last";
	$user->year = 5;
	$user->group = "a";
	$user->rank = 1;
	$user->save();
	
	$user = User::getUsername("testx");
	if ($user && $user->firstname == "first")
		echo "Passed.<br>\n";
	else
		echo "Failed.<br>\n";
} catch (Exception $e) {
	echo "Failed.<br>";
}
	
			
// Clean up
$handle->query("DELETE FROM user WHERE username = 'testx';");
	
// Get by id
echo "Test get by id: ";

try{
	$test = Test::get(1);
	
	if ($test && $test->title == "Test One")
		echo "Passed.<br>\n";
	else
		echo "Failed.<br>\n";
} catch (Exception $e) {
	echo "Failed.<br>";
}
	
// Get by id
echo "Word get by id: ";

try {
	$word = Word::get(1);
	
	if ($word && $word->word == "potato")
		echo "Passed.<br>\n";
	else
		echo "Failed.<br>\n";
		
} catch (Exception $e) {
	echo "Failed.<br>";
}

// Get test words
echo "Test get words: ";
try{
	$words = $test -> words();
	
	echo ($words==null || count($words)==0) ? "Failed." : "Passed.";
	
	
	echo ($words==null) ? "(null)<br>" : " (".count($words)." words)<br>";
} catch (Exception $e) {
	echo "Failed.<br>";
}


// Get nearwords
echo "nearword get by id: ";

try {
	$word = Nearword::get(1);
	
	echo ($word==null || $word->word!="potatoe") ? "Failed.<br>" : "Passed.<br>";
} catch (Exception $e) {
	echo "Failed.<br>";
}

// Getting assignment...
echo "Getting assignments: ";

$user = User::get(2);
$ass = $user->tests();

if (!$ass){
	echo "Failed.<br>";
}else{
	echo "Passed. ";
	echo "(".count($ass)." tests)<br>";
	foreach($ass as $a){
		$test = $a->test();
		echo "--> {$test->title}<br>";
	}
}
echo "</span>";