<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$add_sql = "INSERT INTO chapterMinutes (meetingDate, type, officer, goodOfOrder, oldBusiness, newBusiness, startTime, endTime) VALUES ('".$_POST['date']."', '".$_POST['type']."', '".$_POST['officer']."', '".$_POST['goodOfOrder']."', '".$_POST['oldBusiness']."', '".$_POST['newBusiness']."', '".$_POST['startTime']."', '".$_POST['endTime']."')";

$add_res = mysqli_query($mysqli, $add_sql);

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