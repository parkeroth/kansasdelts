<?
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database)
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>PHPCalendar - <? echo $info['title']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="images/cal.css" rel="stylesheet" type="text/css">
<link href="images/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<table>
	<?php
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
			
		$userData = "
			SELECT * 
			FROM scholarshipResults
			WHERE ID = '$_GET[ID]'
			LIMIT 1";
		$getUserData = mysqli_query($mysqli, $userData);
		
		$rowColor = "white";
		
		$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
		
		echo "<tr><th>Name:</th><td>$userDataArray[name]</td></tr>";
		echo "<tr><th>Address:</th><td>$userDataArray[address]</td></tr>";
		echo "<tr><th>City:</th><td>$userDataArray[city]</td></tr>";
		echo "<tr><th>State:</th><td>$userDataArray[state]</td></tr>";
		echo "<tr><th>ZIP:</th><td>$userDataArray[zip]</td></tr>";
		echo "<tr><th>Phone:</th><td>$userDataArray[phone]</td></tr>";
		echo "<tr><th>Email:</th><td>$userDataArray[email]</td></tr>";
		echo "<tr><th>GPA:</th><td>$userDataArray[gpa]</td></tr>";
		echo "<tr><th>High School:</th><td>$userDataArray[highSchool]</td></tr>";
		echo "<tr><th>Class Rank:</th><td>$userDataArray[classRank]</td></tr>";
		echo "<tr><th>Intended Major:</th><td>$userDataArray[intendedMajor]</td></tr>";
		echo "<tr><th>Honors:</th><td>$userDataArray[honors]</td></tr>";
		echo "<tr><th>Extracurricular:</th><td>$userDataArray[extracurricular]</td></tr>";
		echo "<tr><th>Work:</th><td>$userDataArray[work]</td></tr>";
		
		if($userDataArray[essayOption] == "1"){
			echo "<tr><th>Essay Option:</th><td>In 150 words or less, describe how you are personally committed to life-long learning and growth.</td></tr>";
		} else if($userDataArray[essayOption] == "2"){
			echo "<tr><th>Essay Option:</th><td>In 150 words or less, describe a leadership experience and how it has positively affected your life.</td></tr>";
		}
		
		echo "<tr><th>Essay Answer:</th><td>$userDataArray[essayAnswer]</td></tr>";
	?>
	</table>
</body>
</html>