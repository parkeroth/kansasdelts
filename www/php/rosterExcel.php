<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=excel.xls");

include_once('login.php');

?>

<table border="1" cellspacing="0">
<tr style="font-weight:bold; background-color:#000; color:#FFF;"><td>First Name</td><td>Last Name</td><td>Major</td><td>Email</td><td>Phone Number</td><td>Home Town</td><td>Graduation Year</td><td>Class</td><td>Shirt Size</td><td>Parent's Name</td><td>Parent's Address</td><td>Parent's Email</td></tr>
<?php
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$userData = "
			SELECT * 
			FROM members
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
		
		$rowColor = "white";
		
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
		{
			echo "<tr class=\"$rowColor\">";
			echo "<td>".$userDataArray['firstName']."</td>";
			echo "<td>".$userDataArray['lastName']."</td>";
			echo "<td>".$userDataArray['major']."</td>";
			echo "<td>".$userDataArray['email']."</td>";
			echo "<td>".$userDataArray['phone']."</td>";
			echo "<td>".$userDataArray['homeTown']."</td>";
			echo "<td>".$userDataArray['gradYear']."</td>";
			echo "<td>".$userDataArray['class']."</td>";
			echo "<td>".$userDataArray['shirtSize']."</td>";
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