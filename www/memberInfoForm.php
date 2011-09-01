<?php
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$memberInfo = "
			SELECT * 
			FROM members 
			WHERE username = '".$_SESSION['username']."'";
	
		$getMemberInfo = mysqli_query($mysqli, $memberInfo);
	
		$memberInfoArray = mysqli_fetch_array($getMemberInfo, MYSQLI_ASSOC);
		
		$query = "SELECT name FROM schools WHERE code = '$memberInfoArray[school]'";
		if($result = mysqli_query($mysqli, $query)){
			
			$row = mysqli_fetch_object($result);
			$school = $row->name;
			
			mysqli_free_result($result);
		}
		
		echo '<div style="float:left;">';
		echo "<p><h2>Account Info</h2><table width=\"300px\">";
		echo "<tr><td><b>Name: </b></td><td>".$memberInfoArray['firstName']." ".$memberInfoArray['lastName']."<br></td></tr>";
		echo "<tr><td><b>Username: </b></td><td>".$memberInfoArray['username']."<br></td></tr>";
		echo "<tr><td><b>Class: </b></td><td>".ucwords($memberInfoArray['class'])."<br></td></tr>";
		echo "</table></p>";
		
		echo "<p><h2>Security</h2><table width=\"300px\">";
		echo '<tr><td><a href="passwordChangeForm.php">Change Password</a></td><td></td></tr>';
		echo '<tr><td><p>&nbsp;</p></td><td></td></tr>';
		echo '<tr><td><p>&nbsp;</p></td><td></td></tr>';
		echo '<tr><td><p>&nbsp;</p></td><td></td></tr>';
		echo '<tr><td><a href="changeRosterForm.php">Click to edit info.</a></td><td></td></tr>';
		echo '<tr><td><a href="account.php">Click to return to account.</a></td><td></td></tr>';
		echo "</table></p>";
		echo '</div>';
		
		echo '<div style="float: right;">';
		echo "<p><h2>Personal Info</h2><table width=\"300px\">";
		echo "<tr><td>Major: </td><td>".$memberInfoArray['major']."<br></td></tr>";
		echo "<tr><td>School: </td><td>$school<br></td></tr>";
		echo "<tr><td>Graduation Year: </td><td>".$memberInfoArray['gradYear']."<br></td></tr>";
		echo "<tr><td>Home Town: </td><td>".$memberInfoArray['homeTown']."<br></td></tr>";
		echo "<tr><td>Shirt Size: </td><td>".strtoupper($memberInfoArray['shirtSize'])."<br></td></tr>";
		echo "</table></p>";
		
		echo "<p><h2>Contact Info</h2><table width=\"300px\">";
		echo "<tr><td>Email: </td><td>".$memberInfoArray['email']."<br></td></tr>";
		echo "<tr><td>Phone Number: </td><td>".$memberInfoArray['phone']."<br></td></tr>";
		echo "<tr><td>Carrier: </td><td>".$memberInfoArray['carrier']."<br></td></tr>";
		echo "</table></p>";
		
		echo "<p><h2>Parent's Info</h2><table width=\"300px\">";
		echo "<tr><td>Name: </td><td>".$memberInfoArray['parentName']."<br></td></tr>";
		echo "<tr><td>Address: </td><td>".$memberInfoArray['parentAddress']."<br></td></tr>";
		echo "<tr><td>Email: </td><td>".$memberInfoArray['parentEmail']."<br></td></tr>";
		echo "</table></p>";
		echo '</div>';
		
		echo '<div style="clear:both;"><p>&nbsp;</p></div>';
	?>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>