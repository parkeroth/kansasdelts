<?php
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

if(file_get_contents("http://www.opencaptcha.com/validate.php?ans=".$_POST['code']."&img=".$_POST['img'])=='pass') {
	$userData = "
		SELECT email 
		FROM members
		WHERE accountType LIKE '%webmaster%'
		LIMIT 1";
	$getUserData = mysqli_query($mysqli, $userData);
	$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
		
	$body =  "<html><head>";
	$body .= "<style> tr {text-align: right;} td {text-align: left;} </style>";
	$body .= "</head><body>";
	$body .= "<h2>Recruit's Info</h2>";
	$body .= "<table>";
	$body .= "<tr><th>Name:</th><td>$_POST[recruitFirstName] $_POST[recruitLastName]</td></tr>";
	$body .= "<tr><th>Address:</th><td>$_POST[recruitAddress]</td></tr>";
	$body .= "<tr><th>City:</th><td>$_POST[recruitCity]</td></tr>";
	$body .= "<tr><th>State:</th><td>$_POST[recruitState]</td></tr>";
	$body .= "<tr><th>ZIP:</th><td>$_POST[recruitZIP]</td></tr>";
	$body .= "<tr><th>Phone:</th><td>$_POST[recruitPhone]</td></tr>";
	$body .= "<tr><th>Eamil:</th><td>$_POST[recruitEmail]</td></tr>";
	$body .= "<tr><th>Phone:</th><td>$_POST[recruitSchool]</td></tr>";
	$body .= "<tr><th>Comments:</th><td>$_POST[recruitComments]</td></tr>";
	$body .= "</table>";
	$body .= "<h2>Alumni Info</h2>";
	$body .= "<table>";
	$body .= "<tr><th>Name:</th><td>$_POST[alumniFirstName] $_POST[alumniLastName]</td></tr>";
	$body .= "<tr><th>Chapter:</th><td>$_POST[alumniChapter]</td></tr>";
	$body .= "<tr><th>Graduation Year:</th><td>$_POST[alumniGradYear]</td></tr>";
	$body .= "<tr><th>Phone:</th><td>$_POST[alumniPhone]</td></tr>";
	$body .= "<tr><th>Email:</th><td>$_POST[alumniEmail]</td></tr>";
	$body .= "</table></body></html>";
	
	include('mailTo.php');
	
	//mailTo($userDataArray[email], $_POST[alumniEmail], $body);
	
	$query = "INSERT 	INTO recruits (firstName, lastName, currentSchool, yearInSchool, phoneNumber, email, address, city, state, zip, interestLevel, referredBy)
						VALUES ('$_POST[recruitFirstName]', '$_POST[recruitLastName]', '$_POST[recruitSchool]', '$_POST[yearInSchool]', '$_POST[recruitPhone]', '$_POST[recruitEmail]', '$_POST[recruitAddress]', '$_POST[recruitCity]', '$_POST[recruitState]', '$_POST[recruitZIP]', '7', '$_POST[alumniFirstName] $_POST[alumniLastName]')";
	
	echo $query;
	
	header("location: ../success.php");
} else {
  header("LOCATION: ".$_SERVER['HTTP_REFERER']."?opencaptcha=failed");
}







?>