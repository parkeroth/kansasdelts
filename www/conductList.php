<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'saa');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<div style="text-align:center;">
	<?php
	
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
	$query = "SELECT COUNT(username) AS numUsers FROM members";
	if($result = mysqli_query($mysqli, $query)){
		
		$row = mysqli_fetch_object($result);
		$numMembers = $row->numUsers;
		
		mysqli_free_result($result);
	}
	
	?>
	
	<h2>Chapter Conduct Summary</h2>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($season == "fall"){
				echo "conductList.php?year=$year&amp;season=spring&amp;type=$type"; 
			} else {
				$lastYear = $year-1;
				echo "conductList.php?year=$lastYear&amp;season=fall&amp;type=$type"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($season == "fall"){
		  		echo "<option value=\"conductList.php?year=$year&amp;season=spring&amp;type=$type\" >Spring</option>\n";
				echo "<option value=\"conductList.php?year=$year&amp;season=fall&amp;type=$type\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"conductList.php?year=$year&amp;season=spring&amp;type=$type\" selected>Spring</option>\n";
				echo "<option value=\"conductList.php?year=$year&amp;season=fall&amp;type=$type\" >Fall</option>\n";
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
		  	echo "<option value=\"conductList.php?season=$season&amp;year=$i&amp;type=$type\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($season == "fall"){
				$nextYear = $year+1;
				echo "conductList.php?year=$nextYear&amp;season=spring&amp;type=$type"; 
			} else {
				echo "conductList.php?year=$year&amp;season=fall&amp;type=$type"; 
			}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
		</table>
		
</div>
	
		<table style="text-align:center;" align="center">
			<tr>
				<td><strong>Member</strong></td><td width="100"px><strong>Chapter<br />Absences</strong></td><td width="100"px><strong>Missed<br />Dailies</strong></td><td><strong>Conduct<br />Unbecomings</strong></td><td><strong>Other<br />Write Ups</strong></td></tr>
			<?php 
			
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		if($season == "fall")
		{
			$dateAdded = $year."-08-01";
		}
		else
		{
			$dateAdded = $year."-01-01";
		}
		
		if($season == "fall") {
			$startDate = $year."-08-01";
			$endDate = $year."-12-31";
		} else if($season == "spring") {
			$startDate = $year."-01-01";
			$endDate = $year."-05-31";
		}
		
		$userData = "
			SELECT * 
			FROM members
			WHERE dateAdded <= '$dateAdded'
			ORDER BY lastName"; //echo $userData;
		$getUserData = mysqli_query($mysqli, $userData);
		
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
			
			// Get number of missed chapters
			$attendanceData = "
				SELECT count(attendance.status) AS count 
				FROM members 
				JOIN attendance on members.username = attendance.username 
				WHERE attendance.status='absent'
				AND attendance.date > '$startDate' 
				AND attendance.date < '$endDate'
				AND attendance.username = '$userDataArray[username]'
				AND members.residency != 'limbo'
				GROUP BY members.username";
			//echo $attendanceData."<br>";
			$getAttendanceData = mysqli_query($mysqli, $attendanceData);
			$attendanceArray = mysqli_fetch_array($getAttendanceData, MYSQLI_ASSOC);
			$numAbsences = $attendanceArray['count'];
			
			// Get number of missed dailies
			$dailyData = "
				SELECT count(ID) AS count 
				FROM writeUps 
				WHERE partyResponsible='$userDataArray[username]'
				AND dateOccured > '$startDate' 
				AND dateOccured < '$endDate'
				AND category = 'missedDaily'
				AND verdict = 'guilty'
				GROUP BY partyResponsible";
			//echo $dailyData."<br>";
			$getDailyData = mysqli_query($mysqli, $dailyData);
			$dailyArray = mysqli_fetch_array($getDailyData, MYSQLI_ASSOC);
			$numDailies = $dailyArray['count'];
			
			// Get number of conduct unbecoming
			$unbecomingData = "
				SELECT count(ID) AS count 
				FROM writeUps 
				WHERE partyResponsible='$userDataArray[username]'
				AND dateOccured > '$startDate' 
				AND dateOccured < '$endDate'
				AND category = 'conducUnbecoming'
				AND verdict = 'guilty'
				GROUP BY partyResponsible";
			//echo $unbecomingData."<br>";
			$getUnbecoming = mysqli_query($mysqli, $unbecomingData);
			$unbecomingArray = mysqli_fetch_array($getUnbecoming, MYSQLI_ASSOC);
			$numUnbecoming = $unbecomingArray['count'];
			
			// Get number of other write ups
			$otherData = "
				SELECT count(ID) AS count 
				FROM writeUps 
				WHERE partyResponsible='$userDataArray[username]'
				AND dateOccured > '$startDate' 
				AND dateOccured < '$endDate'
				AND category != 'conducUnbecoming'
				AND category != 'missedDaily'
				AND verdict = 'guilty'
				GROUP BY partyResponsible";
			//echo $unbecomingData."<br>";
			$getOther = mysqli_query($mysqli, $otherData);
			$otherArray = mysqli_fetch_array($getOther, MYSQLI_ASSOC);
			$numOther = $otherArray['count'];
			
			
			if($hours <= 0)
			{
				$class="redHeading";
			}
			else
			{
				$class="normal";
			}
			
			if($numAbsences || $numDailies || $numOther || $numUnbecoming)
			{
				echo "<tr>";
				echo "<td style=\"text-align: left;\">";
				echo 	"<label>".$userDataArray['firstName']." ".$userDataArray['lastName']." </td>\n";
				echo "<td>$numAbsences</td>\n";
				echo "<td>$numDailies</td>";
				echo "<td>$numUnbecoming</td>";
				echo "<td>$numOther</td>";
				echo "<td></td>";
				echo "</tr>\n";
			}
		}
	?>
		</table>
	<p>&nbsp;</p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>