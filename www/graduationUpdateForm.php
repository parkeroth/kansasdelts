<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'secretary');
include_once('php/authenticate.php');

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$tableArray = array('members', 'classes', 'attendance', 'eventAttendance', 'grades', 'fines',
						'notifications', 'accomplishments', 'brokenStuff', 'hourLog',
						'studyHourRequirements', 'volunteer', 'studyHourLogs', 'soberGentsLog',
						'baddDutyLog');
	$userData = "
		SELECT * 
		FROM members";
	
	$getUserData = mysqli_query($mysqli, $userData);

	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
		
		if($_POST[$userDataArray[username]]) {
			
			foreach($tableArray as $table) {
				
				$query = "DELETE FROM $table WHERE username = '".$userDataArray[username]."'";
				$result = mysqli_query($mysqli, $query);
				
			}
			
			$query = "DELETE FROM infractionLog WHERE offender = '".$userDataArray[username]."'";
			$result = mysqli_query($mysqli, $query);
			
			$query = "DELETE FROM writeUps WHERE partyResponsible = '".$userDataArray[username]."'";
			$result = mysqli_query($mysqli, $query);
			
			$modify = "INSERT INTO alumni
						(username, lastName, firstName, password, major, email, homeTown, phone, class, gradYear, dateGraduated, treeParent, standing)
						VALUES ('$userDataArray[username]', '$userDataArray[lastName]', '$userDataArray[firstName]', '$userDataArray[password]', '$userDataArray[major]', 
								'$userDataArray[email]', '$userDataArray[homeTown]', '$userDataArray[phone]', '$userDataArray[class]', '$userDataArray[gradYear]', '$date', '$userDataArray[treeParent]', 'good')";
								
			$doModification = mysqli_query($mysqli, $modify);
		}		
	}
	
}
	
/**
 * Form Section
 */

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<div style="text-align:center;">
	<?php
	
	echo "<h2>Graduation Update Form</h2>";
	?>
</div>
	
	
	<form id="gap" name="gpa" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table style="text-align:center;" align="center">
			<tr>
				<td><strong>Member</strong></td><td width="100"px><strong>Graduating</strong></td></tr>
			<?php 
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$userData = "
			SELECT * 
			FROM members
			WHERE residency != 'limbo'
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
		
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
			
			echo "<tr><td style=\"text-align: left;\"><label>".$userDataArray['firstName']." ".$userDataArray['lastName']." </td>\n";
			echo "<td><input type=\"checkbox\" name=\"".$userDataArray['username']."\" value=\"true\" /></label></td></tr>\n";
		}
	?>
			</table>
		<p style="text-align:center;">
			<input type="submit" name="submit" id="submit" value="Submit" />
			<label>
				<input type="reset" name="Reset" id="Reset" value="Reset" />
				</label>
			</p>
</form>
	<p>&nbsp;</p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>