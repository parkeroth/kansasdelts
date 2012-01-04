<?php

////////////// FOR FUTURE WORK /////////////////////////////

session_start();
$authUsers = array('admin', 'secretary');
include_once 'authenticate.php';

include_once 'classes/Member.php';
include_once 'classes/DB.php';

$member_manager = new Member_Manager();
$member_list = $member_manager->get_all_members();


/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	foreach($member_list as $member) {
		if($_POST[$member->id] == 'alumni') {
			
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
	<h2>Remove User Form</h2>
</div>
	
	
	<form id="gap" name="gpa" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table style="text-align:center;" align="center">
			<tr>
				<td><strong>Member</strong></td>
				<td width="100"px><strong>Keep</strong></td>
				<td width="100"px><strong>Remove</strong></td>
				<td width="100"px><strong>Make Alumni</strong></td>
			</tr>
				<?php 
			
		foreach($member_list as $member)
		{
			echo '<tr>';
			echo '<td style="text-align: left;"><label>'.$member->first_name.' '.$member->last_name.' </td>';
			echo '<td><input type="radio" name="'.$member->id.'" value="keep" checked /></label></td>';
			echo '<td><input type="radio" name="'.$member->id.'" value="remove" /></label></td>';
			echo '<td><input type="radio" name="'.$member->id.'" value="alumni" /></label></td>';
			echo '</tr>';
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