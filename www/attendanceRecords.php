<?php
	$authUsers = array('admin','saa','secretary');
	include_once('php/authenticate.php');
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>
	
<script language="JavaScript" src="js/gen_validatorv31.js" type="text/javascript"></script>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<div style="text-align:center;">
	<?php
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
	
	
	
	echo "<h2>Chapter Absenses - ".ucwords($season)." ".$year."</h2>";
	?>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($season == "fall"){
				echo "attendanceRecords.php?year=$year&amp;season=spring"; 
			} else {
				$lastYear = $year-1;
				echo "attendanceRecords.php?year=$lastYear&amp;season=fall"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($season == "fall"){
		  		echo "<option value=\"attendanceRecords.php?year=$year&amp;season=spring\" >Spring</option>\n";
				echo "<option value=\"attendanceRecords.php?year=$year&amp;season=fall\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"attendanceRecords.php?year=$year&amp;season=spring\" selected>Spring</option>\n";
				echo "<option value=\"attendanceRecords.php?year=$year&amp;season=fall\" >Fall</option>\n";
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
		  	echo "<option value=\"attendanceRecords.php?season=$season&amp;year=$i\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($season == "fall"){
				$nextYear = $year+1;
				echo "attendanceRecords.php?year=$nextYear&amp;season=spring"; 
			} else {
				echo "attendanceRecords.php?year=$year&amp;season=fall"; 
			}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
		</table>
</div>
	<?php 
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		if($season == "fall") {
			$startDate = $year."-08-01";
			$endDate = $year."-12-31";
		} else if($season == "spring") {
			$startDate = $year."-01-01";
			$endDate = $year."-05-31";
		}
		
		$attendanceDates = "
			SELECT DISTINCT date
			FROM attendance
			WHERE date > '$startDate'
			AND date < '$endDate'
			ORDER BY date DESC";
		$getDates = mysqli_query($mysqli, $attendanceDates);
		while($dateArray = mysqli_fetch_array($getDates, MYSQLI_ASSOC)){
			echo "<h2>".date("F j, Y",strtotime($dateArray['date']))."</h2>";
			
			echo "<table align=\"center\">";
			$attendanceData = "
				SELECT members.firstName AS firstName, members.lastName AS lastName, attendance.status, attendance.ID AS ID 
				FROM members 
				JOIN attendance on members.username = attendance.username 
				WHERE attendance.date = '".$dateArray['date']."'
				ORDER BY members.lastName";
			$getAttendanceData = mysqli_query($mysqli, $attendanceData);
			while($attendanceArray = mysqli_fetch_array($getAttendanceData, MYSQLI_ASSOC)){
							
				echo "<tr><th>";
				echo $attendanceArray['firstName']." ".$attendanceArray['lastName'].": </th>\n";
				echo "<td ";
				
				
				if($attendanceArray['status'] == 'absent')
				{
					echo "class=\"redHeading\" ";	
				}
				
				
				echo ">";
				echo ucwords($attendanceArray['status']);
				echo "</td><td>";
				echo "<input 	type=\"button\" 
								name=\"remove-".$attendanceArray[ID]."\" 
								value=\"Remove\"
								onclick=\"window.location.href='php/attendance.php?action=remove&amp;ID=".$attendanceArray[ID]."'\" />";
				echo "<input 	type=\"button\" 
								name=\"toggle-".$attendanceArray[ID]."\" 
								value=\"Toggle\"
								onclick=\"window.location.href='php/attendance.php?action=toggle&amp;ID=".$attendanceArray[ID]."&amp;status=".$attendanceArray['status']."'\" />";
				echo "</td></tr>\n";
			}
			echo "</table>";
			
			echo "<form action=\"php/attendance.php\" method=\"POST\">";
			
			echo "<table align=\"center\">";
			echo "<tr>";
			
			echo "<td>";
			$nameQuery = "
				SELECT firstName, lastName, username
				FROM members 
				WHERE username 
				NOT IN(	SELECT username 
						FROM attendance 
						WHERE date = '".$dateArray['date']."')
				ORDER BY lastName";
			$getNames = mysqli_query($mysqli, $nameQuery);
			echo "<select name=\"name\">";
			while($nameArray = mysqli_fetch_array($getNames, MYSQLI_ASSOC)){
				echo "<option value=\"".$nameArray[username]."\">".ucwords($nameArray[firstName])." ".ucwords($nameArray[lastName])."</option>";
			}
			echo "</select>:";
			echo "</td><td>";
			?>
			
			<select name="status">
				<option value="select">Select One</option>
				<option value="excused">Excused</option>
				<option value="absent">Absent</option>
			</select>
			
			<?php
			echo "</td><td>";
			?>
			
			<input name="Submit" type="submit" />
			
			<input type="hidden" name="action" value="add" />
			<input type="hidden" name="date" value="<?php echo $dateArray['date']; ?>" />
			
			<?php
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "</form>";
		}
		
	?>
	<p>&nbsp;</p>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>