<?php
	$authUsers = array('brother');
	include_once('php/authenticate.php');
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<div style="text-align:center;">
	<?php
	if(isset($_GET['term']) && isset($_GET['year']))
	{
		$year = $_GET['year'];
		$season = $_GET['term'];
	} else {
		$year = date(Y);
		$month = date(n);
		
		if($month > 0 && $month < 7){
			$season = "spring";
		} else if($month > 7 && $month < 13){
			$season = "fall";
		}
	}
	
	$type = $_GET[type];
	
	$userData = "
		SELECT * 
		FROM members 
		WHERE username='$_SESSION[username]'";

	$getUserData = mysqli_query($mysqli, $userData);

	$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
	
	echo "<h2>";
	
	if($type == "service"){
		echo "Community Service Hours";	
	}
	else if($type == "house"){
		echo "House Hours";	
	}
	
	echo "</h2>";
	//echo "<h3>".ucwords($userDataArray[firstName])." ".ucwords($userDataArray[lastName])." - ".ucwords($season)." ".$year."</h2>";
	?>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($season == "fall"){
				echo "hourDetail.php?year=$year&amp;term=spring&amp;type=$type"; 
			} else {
				$lastYear = $year-1;
				echo "hourDetail.php?year=$lastYear&amp;term=fall&amp;type=$type"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($season == "fall"){
		  		echo "<option value=\"hourDetail.php?year=$year&amp;term=spring&amp;type=$type\" >Spring</option>\n";
				echo "<option value=\"hourDetail.php?year=$year&amp;term=fall&amp;type=$type\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"hourDetail.php?year=$year&amp;term=spring&amp;type=$type\" selected>Spring</option>\n";
				echo "<option value=\"hourDetail.php?year=$year&amp;term=fall&amp;type=$type\" >Fall</option>\n";
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
		  	echo "<option value=\"hourDetail.php?year=$i&amp;term=$season&amp;type=$type\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($season == "fall"){
				$nextYear = $year+1;
				echo "hourDetail.php?year=$nextYear&amp;term=spring&amp;type=$type"; 
			} else {
				echo "hourDetail.php?year=$year&amp;term=fall&amp;type=$type"; 
			}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
		</table>
</div>
	
    <?php
    
	$serviceHoursQuery = "
		SELECT eventID, hours 
		FROM hourLog 
		WHERE type='$type'
		AND term ='$season'
		AND year = '$year'
		AND username = '".$_SESSION['username']."'";
	$getEventData = mysqli_query($mysqli, $serviceHoursQuery);
	
	$hours = 0;
	
	
	
	?>
    <p>&nbsp;</p>
	<table style="text-align:center;" align="center">
	<?php
	echo "<tr style=\"text-align: center; font-weight: bold;\"><td>Event</td><td>Hours</td></tr>";
	
	$count=0;
	while($serviceHourArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC))
	{
		echo "<tr>";
		
		if($serviceHourArray[eventID] == 0)
		{
			echo "<td>Manual Adjustment</td>";
		}
		else
		{
			$eventQuery = "
				SELECT title 
				FROM events 
				WHERE ID='$serviceHourArray[eventID]'";
			$getEventQuery = mysqli_query($mysqli, $eventQuery);
			$eventData = mysqli_fetch_array($getEventQuery, MYSQLI_ASSOC);
		
		echo "<td>$eventData[title]</td>";
		}
		
		echo "<td>$serviceHourArray[hours]</td>";
		echo "</tr>";
		$count++;
	}
	if($count == 0)
	{
		echo "<tr><td colspan=\"2\"><p>No Records</p></td></tr>";
	}
	
	echo "</table>";
	
	?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>