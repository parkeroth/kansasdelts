<?php 
	session_start();
	$authUsers = array('brother');
	include_once('php/authenticate.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	$add_sql = "DELETE FROM events WHERE ID='".$_GET['ID']."' LIMIT 1";

	$add_res = mysqli_query($mysqli, $add_sql);
	
	$rem_sql = "DELETE FROM soberGentEvents WHERE eventID='".$_GET['ID']."' LIMIT 1";
	
	$add_res = mysqli_query($mysqli, $rem_sql);
	
	$rem_sql = "DELETE FROM eventAttendance WHERE eventID='".$_GET['ID']."'";
	
	$add_res = mysqli_query($mysqli, $rem_sql);
	
	$month = date('n',strtotime($_GET['date']));
	$year = date('Y',strtotime($info['date']));
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Calendar - Delete Event</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language='javascript' type="text/javascript">
<!--
                     function redirect_to(where, closewin)
                     {
                             opener.location= 'calendar.php';
                             
                             if (closewin == 1)
                             {
                                     self.close();
                             }
                     }
                      //-->
</script>
</head>
<body onLoad="javascript:redirect_to('month=<?php echo $month; ?>&year=<?php echo $year; ?>',1);">
</body>
</html>