<?php
require "common/common.php";

auth(AUTH_PUPIL);

showHeader("Dashboard");

if ($_GET['404']==1){
?>
<div style="background:red;color:white;padding:1em;margin:0.5em;margin-bottom:1em;border-radius:2px;">
	That's a 404; We could not find that page :/
</div>
<?php
}
?>
<h2>Dashboard</h2>

<?php
if ($current_user->isPupil()){
	echo "<table id=\"testlist\">";
	$ass = $current_user->tests();
	
	foreach ($ass as $a){
		$res = Score::getfromusertest($current_user->id,$a->testID);
		echo "<tr><td>".$a->test()->title."</td><td class=\"take\" style=\"width:120px;\">";
		
		if (!$res || count($res)==0)
			echo "<a class=\"button\" href=\"".burl("test/take.php?id=".$a->test()->id)."\">Take</a>";
		else
			echo "<a class=\"button\" href=\"".burl("test/take.php?id=".$a->test()->id)."\">Retake</a>";
			
		echo "</td></tr>";
	}
}else if ($current_user->isStaff()){
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
				echo "\t\t\t<li><a href=\"".burl("admin.php")."\">Admin Tools</a></li>";
			?>
			
		</ul>
	</div>
</div>
<?php
}
?>

</table>