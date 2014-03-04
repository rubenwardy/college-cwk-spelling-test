<?php
require "common/common.php";

auth(AUTH_PUPIL);

showHeader("Dashboard");
?>
<h2>Dashboard</h2>

<?php
if ($current_user->isPupil()){
	echo "<table id=\"testlist\">";
	$ass = $current_user->tests();
	
	foreach ($ass as $a){
		echo "<tr><td>".$a->test()->title."</td><td class=\"take\"><a href=\"test/take.php?id=".$a->test()->id."\">Take</a></td></tr>";
	}
}else if ($current_user->isStaff()){
?>
<div class="dashbox">
	<div class="dashbox_title">
		Staff Panel
	</div>
	<div class="dashbox_content">
		<ul>
			<li><a href="users.php?rank=1">Pupils</a></li>
			<li><a href="test/?user=<?php echo $current_user->id;?>">My Tests</a></li>
			<?php 
				echo "\t\t\t<li><a href=\"admin.php\">Admin Tools</a></li>";
			?>
			
		</ul>
	</div>
</div>
<?php
}
?>

</table>