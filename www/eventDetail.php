<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'publicRel', 'communityService', 'brotherhood', 'houseManager', 'recruitment');
include_once('php/authenticate.php');

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$updateType = $_POST[action];
	
	$typeList = array('attending', 'notAttending', 'invited', 'limbo', 'excused');
	
	$userData = '
		SELECT username
		FROM members';
	$getUserData = mysqli_query($mysqli, $userData);
	
	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
		
		foreach($typeList as $type) {
			
			$name = $userDataArray[username].$type;
			
			if(isset($_POST[$name]) && isset($updateType)){
			
				$modify = "	UPDATE eventAttendance 
							SET status = '$updateType' 
							WHERE eventID = '$_POST[eventID]'
							AND username = '$userDataArray[username]'";
				$doModify = mysqli_query($mysqli, $modify);
				
			}
			
		}
	}
	
	$_GET[id] = $_POST[eventID];	
}
 
/**
 * Body Section
 */

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");?>

<style>
ul.nameList {
	list-style: none;
	margin: 0px;
}

ul.nameList li {
	margin-left: 0px;
}

.nameCol {
	float: left; 
	width: 150px;
}

#editOptions {
	display: none;
}
</style>

<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
	
	function setCheckBoxes(type){

		$('li.' + type).each(function(index) {
			var html = $(this).html();
			var user = $(this).attr('id');
			
			html = '<input name="' + user + type + '" type="checkbox" value="1" />' + html;
			
			$(this).html(html);
		});
		
	}
	
	$(function() {
		
		var editSet = false;
		var statusTypes = new Array('attending', 'notAttending', 'invited', 'limbo', 'excused');
		
		$("#editButton").click(function() {
			
			if(!editSet){
				
				for(var i=0; i< statusTypes.length; i++) {
					setCheckBoxes(statusTypes[i]);
				}
				
				$('#editOptions').show();
				
				editSet = true;
			}
			
		});
	});

</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<?php 
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$eventData = "
			SELECT * 
			FROM events
			WHERE ID='".$_GET['id']."'";
	
		$getEventData = mysqli_query($mysqli, $eventData);
		
		$eventDataArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC);
		
		$date = $eventDataArray['eventDate'];
		$date = strtotime($date);;
		$date = date("D n/j", $date);
			
		$attending = substr_count($eventDataArray['attending'],"|");
		$notAttending = substr_count($eventDataArray['notAttending'],"|");
		$invited = substr_count($eventDataArray['invited'],"|")-($attending - $notAttending);
			
		echo $eventDataArray['title']." - ".$date." - ".$eventDataArray['time'];
		
		if($eventDataArray[type] == "communityService" || $eventDataArray[type] == "house" || $eventDataArray[type] == "philanthropy")
		{
			echo " <a href=\"awardHoursForm.php?id=".$_GET['id']."\">Award Hours</a> |";
		}
		
		echo " <a id=\"editButton\" href=\"#\">Edit</a> |";
		echo " <a href=\"php/deleteEvent.php?ID=".$_GET['id']."\">Delete</a>";
		
		echo "<br>";
					
		$attendingArray = explode("|",$eventDataArray['attending']);
		$length=count($attendingArray);
		echo "<p>";
		$excuseData = "
			SELECT *
			FROM messages
			WHERE eventID=".$eventDataArray['ID']."
		";
		$getExcuseData = mysqli_query($mysqli, $excuseData);
		
		
		$first = true;
		
		while($excuseDataArray = mysqli_fetch_array($getExcuseData, MYSQLI_ASSOC)){
			$userData = "
				SELECT firstName, lastName, username 
				FROM members
				WHERE username='".$excuseDataArray['from']."'";
	
			$getUserData = mysqli_query($mysqli, $userData);
			
			$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
			
			if($first == true){
				echo "<h2>Excuses:</h2>";
				$true = false;
			}
			
			echo $userDataArray['firstName']." ".$userDataArray['lastName'].": ".$excuseDataArray['content']."<br>";
			echo " <form method=\"post\" action=\"php/moderate.php\">";
			echo " <input type=\"radio\" name=\"decision\" value=\"accept\" /> Accept ";
			echo " <input type=\"radio\" name=\"decision\" value=\"reject\" /> Reject ";
			echo " <input type=\"hidden\" name=\"eventID\" value=\"".$excuseDataArray['eventID']."\" />";
			echo " <input type=\"hidden\" name=\"messageID\" value=\"".$excuseDataArray['ID']."\" />";
			echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />";
			echo " </form>";
		}
		echo "</p>";
?>
		<form id="form1" name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<div style="width: 600px;">
		<div class="nameCol"><b>Attending:</b> <br>
		<ul class="nameList">
<?php 
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
				
				echo "<li id=\"".$userDataArray[username]."\" class=\"attending\">".$userDataArray[firstName]." ".$userDataArray[lastName]."</li>";
			}
?>		
		</ul>
		</div>
		<div class="nameCol"><b>Not Attending:</b> <br>
		<ul class="nameList">
		
<?php 
			$userData = "
					SELECT firstName, lastName, members.username AS username 
					FROM members
					JOIN eventAttendance
					ON members.username = eventAttendance.username
					WHERE 	eventAttendance.status = 'notAttending'
					AND		eventAttendance.eventID = '$_GET[id]'
					ORDER BY lastName";
			$getUserData = mysqli_query($mysqli, $userData);
				
			while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
				
				echo "<li id=\"".$userDataArray[username]."\" class=\"notAttending\">".$userDataArray[firstName]." ".$userDataArray[lastName]."</li>";
			}
?>		
		</ul>
		</div>		
		<div class="nameCol"><b>Awaiting Reply:</b> <br>
		<ul class="nameList">
<?php 
			$userData = "
					SELECT firstName, lastName, members.username AS username 
					FROM members
					JOIN eventAttendance
					ON members.username = eventAttendance.username
					WHERE 	eventAttendance.status = 'invited'
					AND		eventAttendance.eventID = '$_GET[id]'
					ORDER BY lastName"; //echo $userData;
			$getUserData = mysqli_query($mysqli, $userData);
				
			while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
				
				echo "<li id=\"".$userDataArray[username]."\" class=\"invited\">".$userDataArray[firstName]." ".$userDataArray[lastName]."</li>";
			}
?>		
		</ul>
		</div>
		<div class="nameCol"><b>Excused:</b> <br>
		<ul class="nameList">
<?php 
			$userData = "
					SELECT firstName, lastName, members.username AS username  
					FROM members
					JOIN eventAttendance
					ON members.username = eventAttendance.username
					WHERE 	eventAttendance.status = 'excused'
					AND		eventAttendance.eventID = '$_GET[id]'
					ORDER BY lastName";
			$getUserData = mysqli_query($mysqli, $userData);
				
			while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
				
				echo "<li id=\"".$userDataArray[username]."\" class=\"excused\">".$userDataArray[firstName]." ".$userDataArray[lastName]."</li>";
			}
?>		
		</ul>
		</div>
		

		
	</div>
	<div style="clear: both;"><p>&nbsp;</p></div>
		<div class="nameCol"><b>Limbo:</b> <br>
		<ul class="nameList">
<?php 
			$userData = "
					SELECT firstName, lastName, members.username AS username 
					FROM members
					JOIN eventAttendance
					ON members.username = eventAttendance.username
					WHERE 	eventAttendance.status = 'limbo'
					AND		eventAttendance.eventID = '$_GET[id]'
					ORDER BY lastName";
			$getUserData = mysqli_query($mysqli, $userData);
				
			while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
				
				echo "<li id=\"".$userDataArray[username]."\" class=\"limbo\">".$userDataArray[firstName]." ".$userDataArray[lastName]."</li>";
			}
?>		
		</ul>
		</div>
	
	<div style="clear: both;">
		<p>&nbsp;</p>
	</div>
	
	<div id="editOptions">
		<h3>Update Names:</h3>
		
			<p>
				<label>
					<input type="radio" name="action" value="attending" id="action_0" />
					Attending</label>
				
				<label>
					<input type="radio" name="action" value="notAttending" id="action_1" />
					Not Attending</label>
				
				<label>
					<input type="radio" name="action" value="invited" id="action_2" />
					Awaiting Reply</label>
				
				<label>
					<input type="radio" name="action" value="limbo" id="action_3" />
					Limbo</label>
				
				<label>
					<input type="radio" name="action" value="excused" id="action_4" />
					Excused</label>
					
				<input name="eventID" type="hidden" value="<?php echo $_GET[id] ; ?>" />
				<input name="submit" type="submit" value="Submit Changes"/>
			</p>
		</form>
	</div>
		
	<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>