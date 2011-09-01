<?php
	$authUsers = array('admin','academics');
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

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div style="text-align:center;">
	<?php
	if(isset($_GET['season']) && isset($_GET['year']))
	{
		$year = $_GET['year'];
		$season = $_GET['season'];
	} else {
		$year = date(Y);
		$month = date(n);
		
		if($month > 0 && $month < 6){
			$season = "spring";
		} else if($month > 7 && $month < 13){
			$season = "fall";
		}
	}
	
	$username = $_GET['username'];
	
	$userData = "
		SELECT * 
		FROM members 
		WHERE username='$username'";

	$getUserData = mysqli_query($mysqli, $userData);

	$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
	
	
	echo "<h2>".ucwords($userDataArray[firstName])." ".ucwords($userDataArray[lastName])." - ".ucwords($season)." ".$year."</h2>";
	?>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($season == "fall"){
				echo "viewSchedule.php?year=$year&amp;season=spring&amp;username=$username"; 
			} else {
				$lastYear = $year-1;
				echo "viewSchedule.php?year=$lastYear&amp;season=fall&amp;username=$username"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($season == "fall"){
		  		echo "<option value=\"viewSchedule.php?year=$year&amp;season=spring&amp;username=$username\" >Spring</option>\n";
				echo "<option value=\"viewSchedule.php?year=$year&amp;season=fall&amp;username=$username\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"viewSchedule.php?year=$year&amp;season=spring&amp;username=$username\" selected>Spring</option>\n";
				echo "<option value=\"viewSchedule.php?year=$year&amp;season=fall&amp;username=$username\" >Fall</option>\n";
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
		  	echo "<option value=\"viewSchedule.php?season=$season&amp;year=$i&amp;username=$username\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($season == "fall"){
				$nextYear = $year+1;
				echo "viewSchedule.php?year=$nextYear&amp;season=spring&amp;username=$username"; 
			} else {
				echo "viewSchedule.php?year=$year&amp;season=fall&amp;username=$username"; 
			}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
		</table>
</div>
	
    <?php
    $username = $_GET['username'];
    
	$scheduleData = "
		SELECT *
		FROM classes
		WHERE username = '$username'
		AND termSeason = '$season'
		AND termYear = '$year'";
		
	$getScheduleData = mysqli_query($mysqli, $scheduleData);
	
	?>
    <p>&nbsp;</p>
	<table style="text-align:center;" align="center">
	<?php
	echo "<tr style=\"text-align: center; font-weight: bold;\"><td>Class</td><td>Hours</td></tr>";
	
	while($scheduleDataArray = mysqli_fetch_array($getScheduleData, MYSQLI_ASSOC)){
		
		echo "<tr>";
		echo "<td>".$scheduleDataArray[department]." ".$scheduleDataArray[section]." </td>";
		echo "<td>".$scheduleDataArray[hours]."</td>";
		echo "</tr>";
		
	}
	echo "</table>";
	
	echo "<p><a href=\"viewCourseHours.php?year=$year&amp;season=$season\">View List</a></p>"
	
	?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>