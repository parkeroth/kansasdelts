<?php
	$authUsers = array('admin', 'pres', 'saa', 'secretary');
	include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
	
	$super_list = array('admin', 'saa');
	$haz_super_powers = $session->isAuth($super_list);
?>
	
<script>

function confirmRevert(URL){
	
	if(confirm("Are you want to revert this punishment?")){
		window.location.href=URL;
	}
}

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
	
	
	
	echo "<h2>Missed Duties - ".ucwords($season)." ".$year."</h2>";
	?>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($season == "fall"){
				echo "missedDutyLog.php?year=$year&amp;season=spring"; 
			} else {
				$lastYear = $year-1;
				echo "missedDutyLog.php?year=$lastYear&amp;season=fall"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($season == "fall"){
		  		echo "<option value=\"missedDutyLog.php?year=$year&amp;season=spring\" >Spring</option>\n";
				echo "<option value=\"missedDutyLog.php?year=$year&amp;season=fall\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"missedDutyLog.php?year=$year&amp;season=spring\" selected>Spring</option>\n";
				echo "<option value=\"missedDutyLog.php?year=$year&amp;season=fall\" >Fall</option>\n";
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
		  	echo "<option value=\"missedDutyLog.php?season=$season&amp;year=$i\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($season == "fall"){
				$nextYear = $year+1;
				echo "missedDutyLog.php?year=$nextYear&amp;season=spring"; 
			} else {
				echo "missedDutyLog.php?year=$year&amp;season=fall"; 
			}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
		</table>
</div>
	<?php 
		include_once('../php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		if($season == "fall") {
			$startDate = $year."-08-01";
			$endDate = $year."-12-31";
		} else if($season == "spring") {
			$startDate = $year."-01-01";
			$endDate = $year."-05-31";
		}
		
		$offenderQuery = "
			SELECT DISTINCT l.offender, m.firstName, m.lastName
			FROM infractionLog AS l
			JOIN members AS m
			ON l.offender = m.username
			WHERE l.dateOccured BETWEEN '$startDate' AND '$endDate'
			AND l.status = 'approved'
			ORDER BY m.lastName";
		$getOffenders = mysqli_query($mysqli, $offenderQuery);
		$records = false;
		while($offenderArray = mysqli_fetch_array($getOffenders, MYSQLI_ASSOC)){
			$records = true;
			echo "<h2>$offenderArray[firstName] $offenderArray[lastName]</h2>";
			
			echo "<table align=\"center\">";
			$infractionQuery = "
				SELECT 
					t.name,
					l.dateOccured,
					l.ID
				FROM infractionLog as l
				JOIN infractionTypes as t
				ON l.type = t.code 
				WHERE l.offender = '$offenderArray[offender]'
				AND l.status = 'approved'
				ORDER BY l.dateOccured";
			$getInfractions = mysqli_query($mysqli, $infractionQuery);
			$first = true;
			
			echo "<table style=\"text-align:center;\" width=\"480\" align=\"center\" cellspacing=\"0\">";
			
			while($infractionArray = mysqli_fetch_array($getInfractions, MYSQLI_ASSOC)){
				
				if($first){
					echo '<tr class="tableHeader"><td>Infraction</td><td>Date</td><td></td></tr>';
					$first = false;
				}				
				echo "<tr><td>";
				echo "$infractionArray[name]</t>\n";
				echo "<td>";
				echo date('M j, Y', strtotime($infractionArray[dateOccured]));
				echo "</td><td>";
				if($haz_super_powers){
					echo "<input 	type=\"button\"  
								value=\"Revert\"
								onclick=\"javascript: confirmRevert('missedDuty.php?type=revert&amp;id=".$infractionArray[ID]."')\" />";
				}
				echo "</td></tr>\n";
			}
			echo "</table>";
		}
		if(!$records){
			echo '<p>&nbsp;</p>';
			echo "<p style=\"text-align:center;\">Records to display.</p>";
		}
		
	?>
	<p>&nbsp;</p>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>