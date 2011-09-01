<?php
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<meta name="robots" content="noindex">
<title>Gamma Tau Roster</title>
<style>
tr.black {
	background-color: #CCC;
	color:#000;
}

tr.white {
	background-color: #fff;
	color: #000;
}
</style>
</head>

<body>
<p>
	<a href="php/rosterExcel.php">Download Excel Sheet</a> <a href="php/rosterCSV.php">Download Contact List</a>
</p>
<table border="0" cellspacing="0" width="3000" style="overflow:scroll;">
<tr style="font-weight:bold; background-color:#000; color:#FFF;"><td>Name</td><td>Major</td><td>Email</td><td>Phone Number</td><td>Home Town</td><td>Graduation Year</td><td>Class</td><td>Shirt Size</td><td>KU ID</td><td>Parent's Name</td><td>Parent's Address</td><td>Parent's Email</td></tr>
<?php
	include_once('php/login.php');
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$userData = "
			SELECT * 
			FROM members
			WHERE residency != 'limbo'
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
		
		$rowColor = "white";
		
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
		{
			echo "<tr class=\"$rowColor\">";
			echo "<td>".$userDataArray['firstName']." ".$userDataArray['lastName']."</td>";
			echo "<td>".$userDataArray['major']."</td>";
			echo "<td>".$userDataArray['email']."</td>";
			echo "<td>".$userDataArray['phone']."</td>";
			echo "<td>".$userDataArray['homeTown']."</td>";
			echo "<td>".$userDataArray['gradYear']."</td>";
			echo "<td>".$userDataArray['class']."</td>";
			echo "<td>".$userDataArray['shirtSize']."</td>";
			echo "<td>".$userDataArray['kuNum']."</td>";
			echo "<td>".$userDataArray['parentName']."</td>";
			echo "<td>".$userDataArray['parentAddress']."</td>";
			echo "<td>".$userDataArray['parentEmail']."</td>";
			echo "</tr>";
			
			if($rowColor == "white"){
				$rowColor = "black";
			}
			else
			{
				$rowColor = "white";
			}
		}
?>
</table>
</body>
</html>
