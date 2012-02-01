<?php
session_start();
$authUsers = array('admin', 'saa');
include_once($_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php');;
include_once('../php/login.php');

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$ID = $_GET[ID];

if($_GET[action] == "consider")
{
	$add_sql = "UPDATE writeUps
			SET status='active',
			updated_user='$session->username'
			WHERE ID='$ID'";

$add_res = mysqli_query($mysqli, $add_sql);

}
else if($_GET[action] == "discard")
{
	$add_sql = "UPDATE writeUps SET status='deleted', updated_user='$session->username' WHERE ID='$ID'";
echo $add_sql;
$add_res = mysqli_query($mysqli, $add_sql);

}
else if(isset($_POST[actionTaken]))
{
	$add_sql = "UPDATE writeUps
			SET actionTaken='$_POST[actionTaken]',
			updated_user='$session->username'
			WHERE ID='$_POST[ID]'";
	$add_res = mysqli_query($mysqli, $add_sql);

}

$add_sql = "UPDATE writeUps
			SET verdict='$_POST[verdict]', category='$_POST[category]', updated_user='$session->username'
			WHERE ID='$_POST[ID]'";

$add_res = mysqli_query($mysqli, $add_sql);


if($_POST[settled] == "yes")
{
	$add_sql = "UPDATE writeUps
			SET status='settled',
			updated_user='$session->username'
			WHERE ID='$_POST[ID]'";
	$add_res = mysqli_query($mysqli, $add_sql);
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
			 opener.location= 'manageWriteUps.php';
			 
			 if (closewin == 1)
			 {
					 self.close();
			 }
	 }
	  //-->
</script>
</head>
<body onLoad="javascript:redirect_to('manageWriteUps.php',1);">
</body>
</html>