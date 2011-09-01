<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'publicRel', 'communityService', 'houseManager', 'brotherhood', 'recruitment');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		if(isset($_GET['type']))
		{
			$type = $_GET['type'];
		}
		else
		{
			$type = "ERROR";
		}
		
		if(isset($_GET['term']) && isset($_GET['year']))
		{
			$year = $_GET['year'];
			$term = $_GET['term'];
		} else {
			$year = date(Y);
			$month = date(n);
			
			if($month <= 7){
				$term = "spring";
			} else {
				$term = "fall";
			}
		}
		
		if($type == 'communityService')
		{
			$pageHeader = "Community Service Events";
		}
		else if($type == 'house')
		{
			$pageHeader = "House Events";
		}
		else if($type == 'pr')
		{
			$pageHeader = "Public Relations";
		}
		else if($type == 'recruitment')
		{
			$pageHeader = "Recruitment";
		}
		else if($type == 'brotherhood')
		{
			$pageHeader = "Brotherhood Events";
		}
		else if($type == 'philanthropy')
		{
			$pageHeader = "Philanthropies";
		}
		
		$termTable = $term.$year;
		
		$eventData = "
			SELECT * 
			FROM events 
			WHERE type='".$type."'
			AND term ='".$termTable."'
			ORDER BY eventDate ASC";
		$getEventData = mysqli_query($mysqli, $eventData);
	?>
		
	<div style="text-align:center;">
		
		<?php echo "<h2> $pageHeader - ".ucwords($term)." ".$year."</h2>"; ?>
		
		<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr> 
				<td><div align="right"><a href="<? 
	  		
			if($term == "fall"){
				echo "manageEvents.php?year=$year&amp;term=spring&amp;type=$type"; 
			} else {
				$lastYear = $year-1;
				echo "manageEvents.php?year=$lastYear&amp;term=fall&amp;type=$type"; 
			}
			
			?>">&lt;&lt;</a></div></td>
				<td width="200"><div align="center">
					
					<select name="term" id="month" onChange="MM_jumpMenu('parent',this,0)">
						<?
			if($term == "fall"){
		  		echo "<option value=\"manageEvents.php?year=$year&amp;term=spring&amp;type=$type\" >Spring</option>\n";
				echo "<option value=\"manageEvents.php?year=$year&amp;term=fall&amp;type=$type\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"manageEvents.php?year=$year&amp;term=spring&amp;type=$type\" selected>Spring</option>\n";
				echo "<option value=\"manageEvents.php?year=$year&amp;term=fall&amp;type=$type\" >Fall</option>\n";
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
		  	echo "<option value=\"manageEvents.php?term=$term&amp;year=$i&amp;type=$type\" $selected>$i</option>\n";
		  }
		  ?>
						</select>
					</div></td>
				<td><div align="left"><a href="<? 
	  	
		if($term == "fall"){
				$nextYear = $year+1;
				echo "manageEvents.php?year=$nextYear&amp;term=spring&amp;type=$type"; 
			} else {
				echo "manageEvents.php?year=$year&amp;term=fall&amp;type=$type"; 
			}
		
		?>">&gt;&gt;</a></div></td>
				</tr>
			</table>
	</div>
		
	<?php
		echo "<table>";
		$count=0;
		while($eventDataArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC)){
			$date = $eventDataArray['eventDate'];
			$date = strtotime($date);;
			$date = date("D n/j", $date);
			
			$attending = 0;
			$notAttending = 0;
			$excused = 0;
			$limbo = 0;
			
			$data = "
				SELECT status, COUNT(ID) AS num
				FROM eventAttendance 
				WHERE eventID = '$eventDataArray[ID]' 
				GROUP BY status";
			$getData = mysqli_query($mysqli, $data);
			while($dataArray = mysqli_fetch_array($getData, MYSQLI_ASSOC)){
				
				if($dataArray[status] == 'attending'){
					$attending = $dataArray[num];
				} else if($dataArray[status] == 'notAttending'){
					$notAttending = $dataArray[num];
				} else if($dataArray[status] == 'excused'){
					$excused = $dataArray[num];
				} else if($dataArray[status] == 'limbo'){
					$limbo = $dataArray[num];
				}
				
			}			
			
			echo "<tr><td width=\"350px\"><a href=\"eventDetail.php?id=".$eventDataArray['ID']."\">".$eventDataArray['title']."</a> - ".$date;
			
			if($type != "brotherhood" && $eventDataArray['dateAwarded'] != "0000-00-00")
			{
				echo " <b>Hours Awarded</b>";
			}
			
			echo "</td>";
			echo "<td>Attend: ".$attending." Not Attend: ".$notAttending." Excused: ".$excused." ?: ";
			
			if($limbo > 0)
			{
				echo "<span class=\"redHeading\">$limbo</span>";
			}
			else
			{
				echo $limbo;
			}
			
			echo "</td></tr>";
			$count++;
		}
		
		if($count == 0){
			echo "<p>No Events Scheduled</p>";
		}
		
		echo "</table>"
	?>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>