<?
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'secretary');
include_once('php/authenticate.php');

$date = $_GET['date'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Edit Agenda Details</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="images/cal.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="js/gen_validatorv31.js" type="text/javascript"></script>

<style>
	th {
		text-align: right;
	}
	
	a:link {
		font-weight: bold;
		color: #FDC029;
		text-decoration: none;
	}
	
	a:visited {
		font-weight: bold;
		color: #CF9200;
		text-decoration: none;
	}
	
	a:hover {
		font-weight: bold;
		color: #FFD46F;
		text-decoration: none;
	}
	
</style>

</head>
<body>
<div class="header">
Chapter Minutes
</div>
<form action="php/modifyMinutes.php" method="post">
<?php

$formattedDate = date("F j, Y", strtotime($date));

echo "<p><b>Date:</b> $formattedDate</p>";

	function getReport($type, $date, $mysqli){
		$reportData = "
			SELECT agenda
			FROM reports
			WHERE type = '$type'
			AND dateMeeting = '$date'";
		$getReportData = mysqli_query($mysqli, $reportData);
		$reportArray = mysqli_fetch_array($getReportData, MYSQLI_ASSOC);
		
		echo "<div class=\"report\">";
		echo "<textarea name=\"$type\" cols=\"40\" rows=\"10\">";
		
		echo $reportArray[agenda];
		
		echo "</textarea></p>";
		echo "</div>";
	}
	
	$execData = "
		SELECT * 
		FROM positions
		WHERE board = 'exec'
		AND type != 'secretary'";
	$getExecData = mysqli_query($mysqli, $execData);

	while($execDataArray = mysqli_fetch_array($getExecData, MYSQLI_ASSOC)) {
		
		$nameQuery = "
			SELECT * 
			FROM members
			WHERE accountType LIKE '%|".$execDataArray[type]."%'";
		$getNameData = mysqli_query($mysqli, $nameQuery);
		$nameDataArray = mysqli_fetch_array($getNameData, MYSQLI_ASSOC);
		
		if($execDataArray[type] == "vicePres") {
			
			echo "<span class=\"execPosition\">".$execDataArray[title].":</span> $nameDataArray[firstName] $nameDataArray[lastName]<br>";
			
			getReport($execDataArray[type], $date, $mysqli);
			
			$adminData = "
				SELECT * 
				FROM positions
				WHERE board = 'admin'";
			$getAdminData = mysqli_query($mysqli, $adminData);
			
			echo "<div class=\"adminSection\">\n";
			while($adminDataArray = mysqli_fetch_array($getAdminData, MYSQLI_ASSOC)) {
				
				$nameQuery = "
					SELECT * 
					FROM members
					WHERE accountType LIKE '%|".$adminDataArray[type]."%'";
				$getNameData = mysqli_query($mysqli, $nameQuery);
				$nameDataArray = mysqli_fetch_array($getNameData, MYSQLI_ASSOC);
				
				echo "<span class=\"adminPosition\">".$adminDataArray[title].":</span> $nameDataArray[firstName] $nameDataArray[lastName]<br>";
				
				getReport($adminDataArray[type], $date, $mysqli);
			
			}
			echo "</div>\n";
			
		} else {
			
			echo "<span class=\"execPosition\">".$execDataArray[title].":</span> $nameDataArray[firstName] $nameDataArray[lastName]<br>";
			
			getReport($execDataArray[type], $date, $mysqli);
		
		}
	}
?>
<input type="hidden" name="date" value="<?php echo $date; ?>" >
<input type="submit" value="Submit Changes">
</form>
</body>
</html>