<?php
$authUsers = array('brother');
include_once('php/authenticate.php');

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

if(isset($_GET['term']) && isset($_GET['year']))
{
	$year = $_GET['year'];
	$term = $_GET['term'];
} else {
	$year = date(Y);
	$month = date(n);
	
	if($month > 0 && $month < 7){
		$term = "spring";
	} else {
		$term = "fall";
	}
}

$userData = "
	SELECT * 
	FROM members
	ORDER BY lastName";
$getUserData = mysqli_query($mysqli, $userData);
	
$memberCount = 0;
while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
{
	$members[$memberCount]['username'] = $userDataArray['username'];
	$members[$memberCount]['firstName'] = $userDataArray['firstName'];
	$members[$memberCount]['lastName'] = $userDataArray['lastName'];
	$memberCount++;
}

$getUserData = "
	SELECT password 
	FROM members 
	WHERE username='".$_SESSION['username']."'";
		
	$getUserData = mysqli_query($mysqli, $getUserData);
	$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
	
$classData = "
	SELECT ID 
	FROM classes 
	WHERE termSeason='$term'
	AND termYear='$year'
	AND username = '".$_SESSION['username']."'";
		
$getClassData = mysqli_query($mysqli, $classData);

if(mysqli_fetch_row($getClassData))
{
	$noClasses = false;
} else {
	$noClasses = true;
}


$ref = $_SERVER['HTTP_REFERER'];

$alertCount = 0;
$errorCount = 0;

if($userDataArray['password'] == "30274c47903bd1bac7633bbf09743149ebab805f"){
	$alert[$alertCount] = "Please change your password! <a href=\"passwordChangeForm.php\">Click Here</a>";
	$alertCount++;
}
if($_GET[from] == "brokenItem"){
	$alert[$alertCount] = "The house manager has been notified of the problem.";
	$alertCount++;
}
if($_GET[from] == "writeUp"){
	$alert[$alertCount] = "Write up filed successfully.";
	$alertCount++;
}
if($noClasses){
	$alert[$alertCount] = "Please submit the classes you are taking this semester.";
	$alertCount++;
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="styles/accordion.css" rel="stylesheet" />
<link type="text/css" href="styles/popUp.css" rel="stylesheet" />

<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="js/popup.js"></script>
<script type="text/javascript" src="js/menu.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	createPopUp("#popupNotification", "#popupNotificationClose", "#notificationButton");
	createPopUp("#popupServiceHours", "#popupServiceHourClose", "#serviceButton");
	createPopUp("#popupHouseHours", "#popupHouseHoursClose", "#houseButton");
	createPopUp("#popupPhilanthropyHours", "#popupPhilanthropyHoursClose", "#philanthropyButton");
	
	$(".avaiableShirt").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('snippet/popupBody.php?type=new&ID=' + id, function(data){
			$("#popupBody").html(data);
		});
		
		//$('#generalPopup').css('width', '420px');
		
		//centering with css
		centerPopup('#generalPopup');
		//load popup
		loadPopup('#generalPopup');
	});
	
	$(".orderedShirt").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('snippet/popupBody.php?type=order&ID=' + id, function(data){
			$("#popupBody").html(data);
		});
		
		//centering with css
		centerPopup('#generalPopup');
		//load popup
		loadPopup('#generalPopup');
		
	});
				
	//CLOSING  POPUP
	//Click the x event!
	$('#popupClose').click(function(){
		disablePopup('#generalPopup');
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup('#generalPopup');
	});
});

</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
	
for($i=0; $i < $alertCount; $i++){ ?>
	<div class="ui-widget">
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;"> 
				<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
				<?php echo $alert[$i]; ?> </p>
			</div>
	</div>
<?php } ?>
	
	<?php include_once('php/login.php');
			$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
			$userData = "
				SELECT * 
				FROM members 
				WHERE username='".$_SESSION['username']."'";
	
			$getUserData = mysqli_query($mysqli, $userData);
	
			$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
			
			if(isset($_GET['term']) && isset($_GET['year']))
			{
				$year = $_GET['year'];
				$term = $_GET['term'];
			} else {
				$year = date(Y);
				$month = date(n);
				
				if($month > 0 && $month < 7){
					$term = "spring";
				} else {
					$term = "fall";
				}
			}
			
			echo '<div style="float:left; width:420px;">';
			?>
			
			<div style="text-align:center; width: 420px;"><h2>
			
			<a href="<? 
	  		
			if($term == "fall"){
				echo "account.php?year=$year&amp;term=spring"; 
			} else {
				$lastYear = $year-1;
				echo "account.php?year=$lastYear&amp;term=fall"; 
			}
			
			?>">&lt;&lt;</a>
			
			<?php echo ucwords($term)." ".$year; ?>
			
			<a href="<? 
	  	
		if($term == "fall"){
				$nextYear = $year+1;
				echo "account.php?year=$nextYear&amp;term=spring"; 
			} else {
				echo "account.php?year=$year&amp;term=fall"; 
			}
		
		?>">&gt;&gt;</a>
			
			</h2></div>
	
<?php
		
// Sober gent notification
include('widgets/myInfo.php');
			
// Sober gent notification
include('widgets/soberGentNotify.php');
			
// Upcoming BADD duties
include('widgets/baddDuty.php');

// Events awaiting reply
include('widgets/eventsAwaitingReply.php');

// My Schedule
include('widgets/mySchedule.php');

// Apparal available for order
include('widgets/apparelOrders.php');

?>
			</div>
			
			
			<div style="float:right;">
		
			<?php include("includes/rightMenu.php"); ?>
					
			</div>
			<div style="clear:both;"><p>&nbsp;</p></div>

<!-- end container div -->
<?php
	function hourPopUp($type, $term, $year, $mysqli) {
		$hoursQuery = "
			SELECT eventID, hours, dateAdded, notes
			FROM hourLog 
			WHERE type='$type'
			AND term ='$term'
			AND year = '$year'
			AND username = '".$_SESSION['username']."'";
		$getEventData = mysqli_query($mysqli, $hoursQuery);
		
		$hours = 0;
		
		?>
		
		<table style="text-align:center;" align="center">
		<?php
		echo "<tr style=\"text-align: center; font-weight: bold;\"><td>Event</td><td>Change</td><td>Date</td></tr>\n";
		
		$count=0;
		while($serviceHourArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC))
		{
			echo "<tr>\n";
			
			if($serviceHourArray[eventID] == 0)
			{
				echo "<td>$serviceHourArray[notes]</td>\n";
			}
			else if($serviceHourArray[eventID] == -1)
			{
				echo "<td>Volunteer Task</td>\n";
			}
			else
			{
				$eventQuery = "
					SELECT title 
					FROM events 
					WHERE ID='$serviceHourArray[eventID]'";
				$getEventQuery = mysqli_query($mysqli, $eventQuery);
				$eventData = mysqli_fetch_array($getEventQuery, MYSQLI_ASSOC);
			
			echo "<td>$eventData[title]</td>\n";
			}
			
			echo "<td>$serviceHourArray[hours]</td>\n";
			echo "<td>$serviceHourArray[dateAdded]</td>\n";
			echo "</tr>\n";
			$count++;
		}
		if($count == 0)
		{
			echo "<tr><td colspan=\"2\"><p>No Records</p></td></tr>\n";
		}
		?> </table> <?php
	}
?>




<div id="popupNotification">
		<?php
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$userData = "
		SELECT * 
		FROM members 
		WHERE username='".$_SESSION['username']."'";

	$getUserData = mysqli_query($mysqli, $userData);

	$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
	
?>
<h2>Notification Settings</h2>

<div id="popupNotificationClose"><a href="#">x</a></div>

<form id="form" name="form" method="post" action="php/changeNotify.php" onsubmit="window.close()">
	
    <table style="text-align:center;" align="center" cellspacing="5">
   		<tr style="font-weight: bold;">
        	<td>&nbsp;</td>
			<td>Email</td>
			<td>Text</td>
        </tr>
		<tr>
			<td>New Events</td>
			<td><input type="checkbox" name="newEvent[]" value="email" 
			<?php
				if(strpos($userDataArray[notifyNewEvent], "mail") != NULL){
					echo "checked";
				}
			?>
			 /></td>
			<td><input type="checkbox" name="newEvent[]" value="text" 
			<?php
				if(strpos($userDataArray[notifyNewEvent], "text") != NULL){
					echo "checked";
				}
			?>
			/></td>
		</tr>
		<tr>
			<td>Reminders</td>
			<td><input type="checkbox" name="reminder[]" value="email" 
			<?php
				if(strpos($userDataArray[notifyReminder], "mail") != NULL){
					echo "checked";
				}
			?>
			/></td>
			<td><input type="checkbox" name="reminder[]" value="text" 
			<?php
				if(strpos($userDataArray[notifyReminder], "text") != NULL){
					echo "checked";
				}
			?>
			/></td>
		</tr>
    </table>

  <p>
      <input type="submit" name="submit" id="submit" value="Submit" />
  </p>
</form>
	</div>
	
<div id="generalPopup">
	<div id="popupClose"><a href="#">x</a></div>
	<div id="popupBody">Body</div>
</div>

<div id="popupServiceHours">
	<h2>Service Hours</h2>
	<div id="popupServiceHourClose"><a href="#">x</a></div>

	<?php hourPopUp("serviceHours", $term, $year, $mysqli);?>
</div>

<div id="popupHouseHours">
	<h2>House Hours</h2>
	<div id="popupHouseHoursClose"><a href="#">x</a></div>

	<?php hourPopUp("houseHours", $term, $year, $mysqli);?>
</div>

<div id="popupPhilanthropyHours">
	<h2>Philanthropies</h2>
	<div id="popupPhilanthropyHoursClose"><a href="#">x</a></div>

	<?php hourPopUp("philanthropyHours", $term, $year, $mysqli);?>
</div>

<div id="backgroundPopup"></div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>