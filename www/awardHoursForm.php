<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'communityService', 'brotherhood', 'houseManager');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<style>

td {
	text-align: center;
}

td.name {
	text-align: left;
}

</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<form name="hours" method="post" action="php/awardHours.php?id=<?php echo $_GET['id']; ?>">
	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$eventData = "
			SELECT * 
			FROM events
			WHERE ID='".$_GET['id']."'";
	
		$getEventData = mysqli_query($mysqli, $eventData);
		
		$eventDataArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC);
		
		echo "<p><h2>".$eventDataArray['title']."</h2>".date('F j, Y', strtotime($eventDataArray[eventDate]))." - ".$eventDataArray['time'];
		
		echo "<br><br>Hours to award: <input name=\"hours\" type=\"text\" size=\"3\" /><p>";
	?>
	<table style="text-align:center;">
		<tr><td><h3>Check To<br />Award</h3></td><td><h3>Name</h3></td></tr>
		<?php
		$array = explode('|', $eventDataArray['attending']);
		$ID = $_GET['id'];
		
		$userData = "
				SELECT firstName, lastName, members.username AS username 
				FROM members
				JOIN eventAttendance
				ON members.username = eventAttendance.username
				WHERE 	eventAttendance.status = 'attending'
				AND		eventAttendance.eventID = '$_GET[id]'
				ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
			
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
			
			echo "<tr><td><input name=\"".$userDataArray[username]."\" type=\"checkbox\" value=\"true\" checked /></td>";
			echo "<td class=\"name\">".$userDataArray[firstName]." ".$userDataArray[lastName]."</td></tr>";
			
		}
		
		echo "<input type=\"hidden\" name=\"ID\" value=\"$ID\" >";
	?>
	<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
		</table>
	<input type="submit" name="submit" id="submit" value="Submit" />
</form>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>