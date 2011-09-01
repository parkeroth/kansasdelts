<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'secretary');
include_once('php/authenticate.php');

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$descriptionError = false;
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$userData = "
		SELECT * 
		FROM members
		ORDER BY lastName";
	$getUserData = mysqli_query($mysqli, $userData);
	
	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
	{
		$modify = "	UPDATE members 
					SET 	standing = '".$_POST[$userDataArray[username]."-standing"]."', 
							residency = '".$_POST[$userDataArray[username]."-residency"]."',
							memberStatus = '".$_POST[$userDataArray[username]."-status"]."'
					WHERE username = '".$userDataArray[username]."'";
		$doModification = mysqli_query($mysqli, $modify);
	}
	
}
	


 
/**
 * Form Section
 */

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<div style="text-align:center;">
	
	<h2>Member Status</h2>
	
</div>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table style="text-align:center;" align="center">
			<tr>
				<td><strong>Member</strong></td><td width="100"px><strong>Residency</strong></td><td width="100"px><strong>Standing</strong></td><td width="100"px><strong>Status</strong></td></tr>
			<?php 
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$userData = "
			SELECT firstName, lastName, username, residency, standing, memberStatus 
			FROM members
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
		
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
			
			echo "<tr>";
			echo "<td style=\"text-align: left;\">";
			echo 	"<label>".$userDataArray['firstName']." ".$userDataArray['lastName']." </td>\n";
			echo "<td><select name=\"".$userDataArray[username]."-residency\">";
			
			echo "<option ";
			if($userDataArray[residency] == "select"){ echo "selected=\"selected\" "; }
			echo "value=\"select\">Select One</option>";
			
			echo "<option ";
			if($userDataArray[residency] == "in"){ echo "selected=\"selected\" "; }
			echo "value=\"in\">Live In</option>";
			
			echo "<option ";
			if($userDataArray[residency] == "out"){ echo "selected=\"selected\" "; }
			echo "value=\"out\">Live Out</option>";
			
			echo "<option ";
			if($userDataArray[residency] == "limbo"){ echo "selected=\"selected\" "; }
			echo "value=\"limbo\">Limbo</option>";
			
			echo "</select></label></td>";
			echo "<td><select name=\"".$userDataArray[username]."-standing\">";
			
			echo "<option ";
			if($userDataArray[standing] == "select"){ echo "selected=\"selected\" "; }
			echo "value=\"select\">Select One</option>";
			
			echo "<option ";
			if($userDataArray[standing] == "good"){ echo "selected=\"selected\" "; }
			echo "value=\"good\">Good</option>";
			
			echo "<option ";
			if($userDataArray[standing] == "suspended"){ echo "selected=\"selected\" "; }
			echo "value=\"suspended\">Suspended</option>";
			
			echo "</select></label></td>";
			echo "<td><select name=\"".$userDataArray[username]."-status\">";
			
			echo "<option ";
			if($userDataArray[memberStatus] == "select"){ echo "selected=\"selected\" "; }
			echo "value=\"select\">Select One</option>";
			
			echo "<option ";
			if($userDataArray[memberStatus] == "active"){ echo "selected=\"selected\" "; }
			echo "value=\"active\">Active</option>";
			
			echo "<option ";
			if($userDataArray[memberStatus] == "pledge"){ echo "selected=\"selected\" "; }
			echo "value=\"pledge\">Pledge</option>";
			
			echo "</select></label></td>";
			echo "</tr>\n";
		}
	?>
			</table>
		<p style="text-align:center;">
			<input type="submit" name="submit" id="submit" value="Submit" />
		</p>
</form>
	<p>&nbsp;</p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>