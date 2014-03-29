<?php
// Get globals from common.php
global $handle;
global $current_user;
global $level;
$level = ($level==NULL) ? "" : $level;

// Start outputing the header
?><!doctype html>
<html>
<head>
	<title><?php echo $title;?> - Spellin' Bee</title>
	<style>
		/* Navigation bar */
		#navbar{
			position: fixed;
			top:0;
			left:0;
			right:0;
			height:32px;
			background: #333333;
			
		}
		#navbar ul{
			list-style: none;
			padding:0;
			margin:0;
		}
		#navbar ul li{
			display: inline-block;
			margin:0;
			color: white;
			padding:0;
		}
		#navbar ul li a{
			display: inline-block;
			color: white;
			height: 24px;
			text-decoration:none;
			margin:0;
			padding: 4px 10px 4px 10px;
		}
		#navbar ul li a:hover{
			background: #666666;
		}
		.sep{
			display: inline-block;
			color: white;
			height: 24px;
			margin:0;
			padding: 4px 10px 4px 10px;
		}
		
		/* Page containers */
		body{
			font-family: "Arial", sans-serif;
			background: #999999;
		}
		#page{
			background: white;
			margin: auto;
			margin-top: 32px;
			display: block;
			width: 90%;
			max-width: 900px;
			min-width: 400px;
		}
		#header{
			height: 100px;
			background: blue;
			color:white;
			padding: 1em;
			text-align:center;
		}
		.contents{
			padding: 1em 2em 1em 2em;
		}
		
		.contents > h2{
			margin-top: 0;
		}
		
		/* Page formating */
		.code{
			display: block;
			width: 300px;
			background: #dddddd;
			padding: 1em;
			margin: 1em;
			border-radius: 5px;
		}
		
		table{
			width: 100%;
		}
		
		table tr{
			padding: 5px;
			height: 2em;
		}
		
		.button{
			padding: 5px 1em 5px 1em;
			margin: 2px; 
			background: #333333;
			color: white;
			test-decoration: none;
		}
		.button:hover{
			background: #555555;
		}
		.take{
			style="width:128px;";
			text-align: center;
		}
		.resultTable, #testlist{
			border: 1px solid black;
			width: 100%;
		}
		.resultTable th:first-of-type{
			width: 92px;
		}
		.resultTable td,.resultTable th,#testlist td,#testlist th{
			padding: 3px 1em 3px 1em;
			border: 1px solid black;
			text-align: center;
			height: 1.75em;
		}
		table
		{
			border-collapse:collapse;
		}
		.tblStripe tr:nth-child(odd){ background-color:#ffeebb; }
		.tblStripe tr:nth-child(even){ background-color:#ffffff; }
	</style>
</head>
<body>
	<div id="page">
		<div id="navbar">
			<div style="float:left;">
				<ul>
					<?php
						// Show the user's navbar
						if ($current_user){
							echo "<li><a href=\"".$level."index.php\">Dashboard</a></li>\n";
							if ($current_user->isPupil()){
								echo "\t\t\t\t\t<li><a href=\"{$level}report.php\">My Report</a></li>\n";
								echo "\t\t\t\t\t<li><a href=\"{$level}test\">Tests</a></li>\n";
							}else if ($current_user->isStaff()){
								echo "\t\t\t\t\t<li><a href=\"{$level}users.php\">Users</a></li>\n";
								echo "\t\t\t\t\t<li><a href=\"{$level}test\">Tests</a></li>\n";
								echo "\t\t\t\t\t<li><a href=\"{$level}test/add.php\">Add Test</a></li>\n";
							}
							echo "\t\t\t\t\t<li><a href=\"{$level}profile.php?id={$current_user->id}\">Profile</a></li>\n";
						}
					?>
				</ul>
			</div>
			<div style="float:right;">
				<ul>
					<?php
						if ($current_user){
							echo "<li><a href=\"{$level}profile.php?id=".$current_user->id."\">".$current_user->username."</a></li>\n";
							echo "\t\t\t\t\t<li><a href=\"{$level}logout.php\">Log out</a></li>\n";
						}else{
							echo "<li><a href=\"{$level}login.php\">Log in</a></li>\n";
						}
					?>
				</ul>
			</div>
		</div>
		<div id="header">
			<h1>Spellin' Bee</h1>
		</div>
		<div class="contents">