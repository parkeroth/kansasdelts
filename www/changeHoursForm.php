<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'communityService', 'house');
include_once('php/authenticate.php');

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$descriptionError = false;
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

	$type = $_POST[type];
	$year = $_POST[year];
	$term = $_POST[term];
	
	$_GET[type] = $type;
	
	$date = date("Y-m-d");
	
	if($type == "communityService"){
		$hourType = "serviceHours";
	} else if($type == "house"){
		$hourType = "houseHours";
	} else if($type == "philanthropy"){
		$hourType = "philanthropyHours";
	}
	
	include_once("snippet/setTermYear.php");
	
	if($term == "fall")
	{
		$dateAdded = $year."-10-01";
	}
	else
	{
		$dateAdded = $year."-04-01";
	}
	
	$userData = "
		SELECT * 
		FROM members
		WHERE dateAdded <= '$dateAdded'
		ORDER BY lastName";
	$getUserData = mysqli_query($mysqli, $userData);
	
	$memberCount = 0;
	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
	{
		$members[$memberCount]['username'] = $userDataArray['username'];
		$members[$memberCount]['firstName'] = $userDataArray['firstName'];
		$members[$memberCount]['lastName'] = $userDataArray['lastName'];
		$members[$memberCount]['accountType'] = $userDataArray['accountType'];
		$members[$memberCount]['class'] = $userDataArray['class'];
		$members[$memberCount]['major'] = $userDataArray['major'];
		$memberCount++;
	}
	
	for($i = 0; $i < $memberCount; $i++)
	{
		$change = $_POST[$members[$i]['username']];
		
		if( $change != NULL )
		{
			if($change != 0 && $_POST[$members[$i]['username']."Description"] == ""){
				$descriptionError = true;
				$errors[] = "<b>".$members[$i]['firstName']." ".$members[$i]['lastName']."'s</b> hours were not changed. Need a description.<br>";
			} else {
				$description = $_POST[$members[$i]['username']."Description"];
				$modify = "INSERT INTO hourLog
					(username, term, year, hours, type, eventID, dateAdded, notes)
					VALUES
					('".$members[$i]['username']."', '$term', '$year', '$change', '$hourType', 'NULL', '$date', '$description')";
				$doModification = mysqli_query($mysqli, $modify);
			}
			
		}
	}
	
}
	


 
/**
 * Form Section
 */
 
 	if(isset($_GET['type']))
	{
		$type = $_GET['type'];
	}
	else
	{
		$type = "ERROR";
	}
	
	if(isset($_GET['season']) && isset($_GET['year']))
	{
		$year = $_GET['year'];
		$season = $_GET['season'];
	} else {
		$year = date(Y);
		$month = date(n);
		
		if($month > 0 && $month < 7){
			$season = "spring";
		} else if($month > 7 && $month < 13){
			$season = "fall";
		}
	}
	
	// Get number of members in roster
	$query = "SELECT COUNT(username) AS numUsers FROM members WHERE residency != 'limbo'";
	if($result = mysqli_query($mysqli, $query)){
		
		$row = mysqli_fetch_object($result);
		$numMembers = $row->numUsers;
		
		mysqli_free_result($result);
	}
	
	// Set headers and types
	if($type == 'communityService')
	{
		$pageHeader = "Community Service Hours";
		$queryType = "serviceHours";
	}
	else if($type == 'house')
	{
		$pageHeader = "House Hours";
		$queryType = "houseHours";
	}
	else if($type == 'philanthropy')
	{
		$pageHeader = "Philanthropies";
		$queryType = "philanthropyHours";
	}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="styles/popUp.css" rel="stylesheet" />

<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="js/popup.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	
	$("a.hour").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('snippet/popupBody.php?type=hours&hourType=<?php echo $queryType; ?>&username=' + id + '&year=<?php echo $year; ?>&term=<?php echo $season; ?>', function(data){
			$("#popupBody").html(data);
		});
		
		//$('#generalPopup').css('width', '420px');
		
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

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div style="text-align:center;">
	<?php
	
	// Get total number of hours for the term
	$query = "	SELECT SUM(hours) AS total 
				FROM hourLog 
				WHERE year='$year' 
				AND term='$season' 
				AND type='$queryType'";
				
	if($result = mysqli_query($mysqli, $query)){
		
		$row = mysqli_fetch_object($result);
		$totalHours = $row->total;
		
		$hoursPerMan = round($totalHours/$numMembers, 2);
		
		mysqli_free_result($result);
	}
	
	echo "<h2>$pageHeader - ".ucwords($season)." ".$year."</h2>";
	?>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($season == "fall"){
				echo "changeHoursForm.php?year=$year&amp;season=spring&amp;type=$type"; 
			} else {
				$lastYear = $year-1;
				echo "changeHoursForm.php?year=$lastYear&amp;season=fall&amp;type=$type"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($season == "fall"){
		  		echo "<option value=\"changeHoursForm.php?year=$year&amp;season=spring&amp;type=$type\" >Spring</option>\n";
				echo "<option value=\"changeHoursForm.php?year=$year&amp;season=fall&amp;type=$type\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"changeHoursForm.php?year=$year&amp;season=spring&amp;type=$type\" selected>Spring</option>\n";
				echo "<option value=\"changeHoursForm.php?year=$year&amp;season=fall&amp;type=$type\" >Fall</option>\n";
			}
			?>
					</select>
				<select name="year" id="year" onChange="MM_jumpMenu('parent',this,0)">
					<?
		  $yearLoop = date("Y");
		  
		  for ($i = $yearLoop+1; $i >= $yearLoop-3; $i--) {
		  	if($i == $year){
				$selected = "selected";
			} else {
				$selected = "";
			}
		  	echo "<option value=\"changeHoursForm.php?season=$season&amp;year=$i&amp;type=$type\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($season == "fall"){
				$nextYear = $year+1;
				echo "changeHoursForm.php?year=$nextYear&amp;season=spring&amp;type=$type"; 
			} else {
				echo "changeHoursForm.php?year=$year&amp;season=fall&amp;type=$type"; 
			}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
		</table>
		
	<p style="text-align:center;">
		Total hours per member: <b><?php echo $hoursPerMan; ?></b>
	</p>
	
	<p style="text-align:center;">
		If you are assigning hours to multiple people, please create an event and add the events through the event details page.
	</p>
	
	<div class="errorBlock">
	<?php 
	
		if($descriptionError){
			foreach($errors as $value){
				echo $value;
			}
		}
	
	?>
	</div>
</div>
	<form id="gap" name="gpa" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table style="text-align:center;" align="center">
			<tr>
				<td><strong>Member</strong></td><td width="100"px><strong>Hours</strong></td><td width="100"px><strong>Adjustment</strong></td><td><strong>Description</strong></td></tr>
			<?php 
			
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		//include_once("snippet/setTermYear.php");
		
		if($season == "fall")
		{
			$dateAdded = $year."-10-01";
		}
		else
		{
			$dateAdded = $year."-05-01";
		}
		
		$userData = "
			SELECT * 
			FROM members
			WHERE dateAdded <= '$dateAdded'
			AND residency != 'limbo'
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
		
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
			
			$hourData = "
				SELECT hours
				FROM hourLog
				WHERE username = '".$userDataArray['username']."'
				AND year='$year'
				AND term='$season'
				AND type='$queryType'";
			$getHourData = mysqli_query($mysqli, $hourData);
			$hours=0;
			
			while($hourDataArray = mysqli_fetch_array($getHourData, MYSQLI_ASSOC))
			{
				$hours += $hourDataArray[hours];
			}
			
			if($hours <= 0)
			{
				$class="redHeading";
			}
			else
			{
				$class="normal";
			}
			
			echo "<tr>";
			echo "<td style=\"text-align: left;\">";
			echo 	"<label>".$userDataArray['firstName']." ".$userDataArray['lastName']." </td>\n";
			echo "<td class=\"$class\"><a class=\"hour\" id=\"$userDataArray[username]\" href=\"#\">$hours</a></td>\n";
			echo "<td><input type=\"text\" name=\"".$userDataArray['username']."\" size=\"2\"/></label></td>";
			echo "<td><input type=\"text\" name=\"".$userDataArray['username']."Description\" size=\"24\"/></td>";
			echo "</tr>\n";
		}
	?>
			</table>
		<p style="text-align:center;">
			<input  type="hidden" name="term" value="<?php echo $season; ?>" />
			<input  type="hidden" name="year" value="<?php echo $year; ?>" />
			<input type="hidden" name="type" value="<?php echo $type; ?>" />
			<input type="submit" name="submit" id="submit" value="Submit" />
			<label>
				<input type="reset" name="Reset" id="Reset" value="Reset" />
				</label>
			</p>
	</form>
	<p>&nbsp;</p>
	<div id="generalPopup">
		<div id="popupClose"><a href="#">x</a></div>
		<div id="popupBody">Body</div>
	</div>
	
	<div id="backgroundPopup"></div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>