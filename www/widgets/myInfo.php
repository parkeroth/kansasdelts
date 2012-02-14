<h2>My Info</h2>

<?php
			$serviceHoursQuery = "
			SELECT hours 
			FROM hourLog 
			WHERE type='service'
			AND term ='$term'
			AND year = '$year'
			AND member_id = '".$session->member_id."'";
		$getEventData = mysqli_query($mysqli, $serviceHoursQuery);
		$serviceHours = 0;
		
		while($serviceHourArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC))
		{
			$serviceHours += $serviceHourArray[hours];
		}
		
		$houseHoursQuery = "
			SELECT hours 
			FROM hourLog 
			WHERE type='house'
			AND term ='$term'
			AND year = '$year'
			AND member_id = '".$session->member_id."'";
		$getEventData = mysqli_query($mysqli, $houseHoursQuery);
		$houseHours = 0;
		
		while($houseHourArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC))
		{
			$houseHours += $houseHourArray[hours];
		}
		
		$philanthropyQuery = "
			SELECT hours 
			FROM hourLog 
			WHERE type='philanthropy'
			AND term ='$term'
			AND year = '$year'
			AND member_id = '".$session->member_id."'";
		$getPhilData = mysqli_query($mysqli, $philanthropyQuery);
		$philanthropies = 0;
		
		while($philanthropyArray = mysqli_fetch_array($getPhilData, MYSQLI_ASSOC))
		{
			$philanthropies += $philanthropyArray[hours];
		}
		
		if($term == "fall")
		{
			$soberYear = $year+1;
		}
		else
		{
			$soberYear = $year;
		}
		
		$soberQuery = "
			SELECT ID 
			FROM soberGentLog 
			WHERE year='$soberYear'
			AND username ='".$_SESSION[username]."'";
		$getSoberData = mysqli_query($mysqli, $soberQuery);
		$soberGents = 0;
		
		while($soberArray = mysqli_fetch_array($getSoberData, MYSQLI_ASSOC))
		{
			$soberGents++;
		}
		
			$check = "SELECT ID 
				FROM volunteer
				WHERE type = 'house'
				AND username = '".$_SESSION[username]."'";
			$checkTable = mysqli_query($mysqli, $check);
			
			if(!mysqli_fetch_row($checkTable))
			{
				$houseButton = "<INPUT TYPE=\"BUTTON\" VALUE=\"Volunteer\" ONCLICK=\"window.location.href='php/volunteer.php?type=house&amp;action=add'\">";
			}
			else
			{
				$houseButton = "Waiting for task. <input type=\"button\" value=\"Undo\" ONCLICK=\"window.location.href='php/volunteer.php?type=house&amp;action=remove'\">";
			}
			$check = "SELECT ID 
				FROM volunteer
				WHERE type = 'sober'
				AND username = '".$_SESSION[username]."'";
			$checkTable = mysqli_query($mysqli, $check);
			
			if(!mysqli_fetch_row($checkTable))
			{
				$soberButton = "<INPUT TYPE=\"BUTTON\" VALUE=\"Volunteer\" ONCLICK=\"window.location.href='php/volunteer.php?type=sober&amp;action=add'\">";
			}
			else
			{
				$soberButton = "In short list. <input type=\"button\" value=\"Undo\" ONCLICK=\"window.location.href='php/volunteer.php?type=sober&amp;action=remove'\">";
			}
			
			echo "<table>\n";
			echo "<tr><td height=\"20\"><b>Service Hours</b> This Semester: </td><td><span id=\"serviceButton\"><a href=\"#service\">$serviceHours</a></span></td><td></td></tr>";
			echo "<tr><td height=\"20\"><b>House Hours</b> This Semester: </td><td><span id=\"houseButton\"><a href=\"#\">$houseHours</a></span> </td><td>$houseButton</td></tr>";
			echo "<tr><td height=\"20\"><b>Philanthropies</b> This Semester: </td><td><span id=\"philanthropyButton\"><a href=\"#\">$philanthropies</a></span> </td><td></td></tr>";
			echo "<tr><td height=\"20\"><b>Sober Gents</b> This Year: </td><td><a href=\"#\">$soberGents</a> </td><td>$soberButton</td></tr>";
			
			$query = "
				SELECT date
				FROM baddDutyLog
				WHERE username='$_SESSION[username]'
					AND date >= '".date('Y-m-d')."'";
			$result = mysqli_query($mysqli, $query);
			if($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$linkMonth = date("n", strtotime($row['date'])) + 1;
				$linkYear = date("Y", strtotime($row['date']));
				echo "<tr><td height=\"20\">Your next <b>BADD Duty</b>:</td><td colspan=\"2\"><a href=\"baddDutyDates.php?month=$linkMonth&year=$linkYear\">".date('M j, Y', strtotime($row['date']))."</a></td></tr>";
			}
			else
			{
				echo "<tr><td height=\"20\">Your next <b>BADD Duty</b>:</td><td colspan=\"2\">None Scheduled</td></tr>";
			}
			
			//Study Hour Stuff
			$query = "SELECT hoursRequired FROM studyHourRequirements WHERE username = '$_SESSION[username]'";
			
			if($result = mysqli_query($mysqli, $query)){
				
				$row = mysqli_fetch_object($result);
				$requiredHours = $row->hoursRequired;
				
				if($requiredHours > 0){
					
					$hoursStartDate = date('Y-m-d', strtotime('last Sunday'));
					$hoursStopDate = date('Y-m-d', strtotime('this Sunday'));
					
					$signed_in_query = "SELECT * FROM studyHourLogs WHERE username='$_SESSION[username]' AND open = 'yes' ";
					$result = mysqli_query($mysqli, $signed_in_query);
					$row = mysqli_fetch_object($result);
					if($row){
						$signed_in = true;
						$started = strtotime($row->timeStamp);
						$diference = strtotime('now')-$started;
						$additional = '+'.round($diference/3600,2);
					} else {
						$signed_in = false;
						$additional = '';
					}
					
					$completedQuery = "SELECT SUM(duration) AS total FROM studyHourLogs WHERE username = '$_SESSION[username]' AND timeStamp BETWEEN '$hoursStartDate' AND '$hoursStopDate' ";
					if($result = mysqli_query($mysqli, $completedQuery)){
					
						$row = mysqli_fetch_object($result);
						
						if($row->total != NULL){
							$completedHours = $row->total;
						} else {
							$completedHours = 0;
						}
					
					}
					
					if(($requiredHorus - $completedHours) >=0 ){
						$hourClass = "rankRed";
					} else {
						$hourClass = "rankGreen";
					}
					
					if($_SESSION['username'] == "faljay"){
						$completedHours *= -1;
					}
					
					echo "<tr><td><b>Study Hours</b> this week:</td><td class=\"$hourClass\">".round($completedHours, 2)."</td>";
					echo '<td>';
					if($signed_in){
						echo '<strong>'.$additional.'</strong>';
					}
					echo '</td></tr>';
				}
				
				mysqli_free_result($result);
			}
			
			
			echo "</table>\n";
?>