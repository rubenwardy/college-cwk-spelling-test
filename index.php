<?php
require "common/common.php";

// Require log in
auth(AUTH_PUPIL);

// Display page
showHeader("Dashboard");


// Display 404 - page not found message if necessary
if ($_GET['404']==1){
?>
<div style="background:red;color:white;padding:1em;margin:0.5em;margin-bottom:1em;border-radius:2px;">
	That's a 404; We could not find that page :/
</div>
<?php
}

// Show the dashboard
?>
<h2>Dashboard</h2>

<?php
if ($current_user->isPupil()){
	// Show pupil dash board
	echo "<table id=\"testlist\">";
	
	// Get all assignments this pupil has
	$ass = $current_user->tests();
	foreach ($ass as $a){
		// Look for submitted results from this pupil, for the test.
		// ie: see if the pupil has done this test yet
		$res = Score::getfromusertest($current_user->id,$a->testID);
		echo "<tr><td>".$a->test()->title."</td><td class=\"take\" style=\"width:120px;\">";
		
		// Show correct button - retake or take.
		if (!$res || count($res)==0)
			echo "<a class=\"button\" href=\"".burl("test/take.php?id=".$a->test()->id)."\">Take</a>";
		else
			echo "<a class=\"button\" href=\"".burl("test/take.php?id=".$a->test()->id)."\">Retake</a>";
			
		echo "</td></tr>";
	}
}else if ($current_user->isStaff()){
	// Show staff dash board
?>
<div class="dashbox">
	<div class="dashbox_title">
		Staff Panel
	</div>
	<div class="dashbox_content">
		<ul>
			<li><a href="<?php echo burl("users.php?rank=1");?>">Pupils</a></li>
			<li><a href="<?php echo burl("test/?user=".$current_user->id);?>">My Tests</a></li>
			<?php 
				if ($current_user->isAdmin())
					echo "\t\t\t<li><a href=\"".burl("admin.php")."\">Admin Tools</a></li>";
			?>
			
		</ul>
	</div>
</div>
<?php
}
?>

</table>