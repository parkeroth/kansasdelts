<?
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'secretary');
include_once('php/authenticate.php');

/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$date = $_POST['date'];
	
	$query = "SELECT type FROM positions";
	$result = mysqli_query($mysqli, $query);
	
	while($positionArray = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$modify = "UPDATE reports
					SET minutes = '".$_POST[$positionArray[type]]."'
					WHERE type = '$positionArray[type]'
					AND dateMeeting = '$date'";
		$doModification = mysqli_query($mysqli, $modify);
		echo $modify."<br>";
	}
	
	?>

		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		<head>
		<title>Board Minutes</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<script language='javascript' type="text/javascript">
		
					   
						  
		</script>
		</head>
		<body onLoad="javascript: self.close()">
		</body>
		</html>
		
	<?
	
} else {

/**
 * Form Section
 */


$date = $_GET['date'];

$report = "
	SELECT * 
	FROM reports 
	WHERE dateMeeting='$date'";
$getReport = mysqli_query($mysqli, $report);
$reportData = mysqli_fetch_array($getReport, MYSQLI_ASSOC);

$userData = "
	SELECT * 
	FROM members
	WHERE username='$reportData[username]'";
$getUserData = mysqli_query($mysqli, $userData);
$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);

$name = $userDataArray[firstName]." ".$userDataArray[lastName];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Edit Board Minutes</title>
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
	
	.execPosition {
		font-weight: bold;
	}
	
	.adminSection {
		padding: 20px;
	}
	
	.adminPosition {
		font-weight: bold;
	}
	
	.noReport {
		padding: 10px;
	}
	
	.header {
		text-align: center;
		font-size: 32px;
	}
	
	
</style>

</head>
<body>
<div class="header">
Board Minutes
</div>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<?php

$formattedDate = date("F j, Y", strtotime($date));

echo "<p><b>Date:</b> $formattedDate</p>";

	function getReport($type, $date, $mysqli){
		$reportData = "
			SELECT minutes
			FROM reports
			WHERE type = '$type'
			AND dateMeeting = '$date'";
		$getReportData = mysqli_query($mysqli, $reportData);
		$reportArray = mysqli_fetch_array($getReportData, MYSQLI_ASSOC);
		
		echo "<div class=\"report\">";
		echo "<textarea name=\"$type\" cols=\"40\" rows=\"10\">";
		
		echo $reportArray[minutes];
		
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

<?php } ?>