<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
include_once('login.php');

$ID = $_POST['eventID'];
$action = $_POST['attending'];
$mandatory = $_POST['mandatory'];
$type = $_POST['type'];
$reason = $_POST['reason'];

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

if($action == "attending"){

	$modify = "	UPDATE eventAttendance
				SET status='attending'
				WHERE eventID = '$ID'
				AND username = '$_SESSION[username]'";

	$doModify = mysqli_query($mysqli, $modify);

} else if($action == "notAttending"){
	if($mandatory == 0){
		
		$modify = "	UPDATE eventAttendance
					SET status='notAttending'
					WHERE eventID = '$ID'
					AND username = '$_SESSION[username]'";

		$doModify = mysqli_query($mysqli, $modify);
		
	} else if($mandatory == 1){ //end notMandatory
		$modify = "	UPDATE eventAttendance
					SET status='limbo'
					WHERE eventID = '$ID'
					AND username = '$_SESSION[username]'";

		$doModify = mysqli_query($mysqli, $modify);
	}//end isMandatory
}//end notAttending

if($_GET['source'] == "account"){
	header("location: ../account.php");
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Calendar - Add Event</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language='javascript' type="text/javascript">
<!--
                     function redirect_to(where, closewin)
                     {
                             opener.location= '../calendar.php';
                             
                             if (closewin == 1)
                             {
                                     self.close();
                             }
                     }
                      //-->
</script>
</head>
<body onLoad="javascript:redirect_to('month=<? echo $_POST['month']."&year=".$_POST['year']; ?>',1);">
</body>
</html>