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
				echo "viewAttendance.php?year=$year&amp;season=spring"; 
			} else {
				$lastYear = $year-1;
				echo "viewAttendance.php?year=$lastYear&amp;season=fall"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($season == "fall"){
		  		echo "<option value=\"viewAttendance.php?year=$year&amp;season=spring\" >Spring</option>\n";
				echo "<option value=\"viewAttendance.php?year=$year&amp;season=fall\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"viewAttendance.php?year=$year&amp;season=spring\" selected>Spring</option>\n";
				echo "<option value=\"viewAttendance.php?year=$year&amp;season=fall\" >Fall</option>\n";
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
		  	echo "<option value=\"viewAttendance.php?season=$season&amp;year=$i\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($season == "fall"){
				$nextYear = $year+1;
				echo "viewAttendance.php?year=$nextYear&amp;season=spring"; 
			} else {
				echo "viewAttendance.php?year=$year&amp;season=fall"; 
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
		
		$attendanceData = "
			SELECT members.firstName AS firstName, members.lastName AS lastName, count(attendance.status) AS count 
			FROM members 
			JOIN attendance on members.username = attendance.username 
			WHERE attendance.status='absent'
			AND attendance.date > '$startDate' 
			AND attendance.date < '$endDate'
			AND residency != 'limbo'
			GROUP BY attendance.username";
		$getAttendanceData = mysqli_query($mysqli, $attendanceData);
		echo "<table align=\"center\">";
		while($attendanceArray = mysqli_fetch_array($getAttendanceData, MYSQLI_ASSOC)){
						
			echo "<tr><th>";
			echo $attendanceArray['firstName']." ".$attendanceArray['lastName'].": </th>\n";
			echo "<td ";
			
			
			if($attendanceArray['count'] > 2)
			{
				echo "class=\"redHeading\" ";	
			}
			
			
			echo ">&nbsp;&nbsp;";
			echo $attendanceArray['count'];
			echo "</td></tr>\n";
		}
		echo "</table>";
	?>
	<p>&nbsp;</p>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>