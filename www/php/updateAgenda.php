<?php
	session_start();
	$authUsers = array('secretary', 'admin');
	include_once('authenticate.php');
	include_once('login.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$date=$_POST['date'];
	
	$query = "SELECT type FROM positions";
	$result = mysqli_query($mysqli, $query);
	
	while($positionArray = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$modify = "UPDATE reports
					SET agenda = '".$_POST[$positionArray[type]]."'
					WHERE type = '$positionArray[type]'
					AND dateMeeting = '$date'";
		$doModification = mysqli_query($mysqli, $modify);
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Calendar - Add Event</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language='javascript' type="text/javascript">

                   
                      
</script>
</head>
<body onLoad="javascript: self.close()">
</body>
</html>