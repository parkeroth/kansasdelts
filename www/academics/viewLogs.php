<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'academics');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 
?>

<style type="text/css">
	table {
		margin-left: auto;
		margin-right: auto;
		text-align: center;
		}
	th {
		font-size: 14px;
		text-align: center;
		padding: 5px;
		}
	td {
		padding: 5px;
	}
	.studyHrButton {
		padding-left: 5px;
		padding-right: 5px;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>View Study Hour Logs</h1>

<h2>Data for week of <?php echo date('m-d-Y', mktime(0, 0, 0, date('m'), date('d')-date('w'), date('Y'))).' to '.date('m-d-Y', mktime(1, 0, 0, date('m'), date('d')-date('w')+6, date('Y'))); ?></h2>

<p>Here you can see the logs for each of the members of whomb study hours are required</p>

<?php

	//Start our shit here
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	//Fist, we'll build an array of the data of people ALREADY in the
	//StudyHourRequirements table
	$getUserDataQ = '
		SELECT members.username, members.firstName, members.lastName, studyHourRequirements.startDate, studyHourRequirements.stopDate, studyHourRequirements.hoursRequired
		FROM studyHourRequirements LEFT JOIN members
		ON members.username = studyHourRequirements.username
		ORDER BY members.lastName';
	//and execute said quiery
	$getUserData = mysqli_query($mysqli, $getUserDataQ);	
	if(!$getUserData)
	{
 		die('Could not get data: ' . mysqli_error());
	}
	//now loop through, build array
	$memberCount = 0;
	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
		{
			$userSHData[$memberCount]['username'] = $userDataArray['username'];
			$userSHData[$memberCount]['firstName'] = $userDataArray['firstName'];
			$userSHData[$memberCount]['lastName'] = $userDataArray['lastName'];	
			$userSHData[$memberCount]['startDate'] = $userDataArray['startDate'];	
			$userSHData[$memberCount]['stopDate'] = $userDataArray['stopDate'];			
			$userSHData[$memberCount]['hoursRequired'] = $userDataArray['hoursRequired'];
			//echo $userDataArray['username'].': '.$userDataArray['hoursRequired'].'<br />';
			
			//We want to get the total number of hours completed this week too
			$getHrQuery = '
				SELECT duration, timeStamp 
				FROM studyHourLogs
				WHERE username="'.$userDataArray['username'].'"
				';
			//Set up our holder variables
			$userSHData[$memberCount]['weeklyHrs'] = 0;
			$userSHData[$memberCount]['totalHrs'] = 0;
			//execute query
			$getHrData = mysqli_query($mysqli, $getHrQuery);
			while($hrData = mysqli_fetch_array($getHrData, MYSQLI_ASSOC))
			{
				$tStamp = strtotime($hrData['timeStamp']);
				$duration = $hrData['duration'];
				//lets check to see if the timestamp was within the last week
				if($tStamp > strtotime('last Sunday') && $tStamp < strtotime('this Sunday'))
				{
					//Within the last week, so let add value to the weekly hours
					$userSHData[$memberCount]['weeklyHrs'] += $duration;
				}
				$userSHData[$memberCount]['totalHrs'] += $duration;
			}
			$memberCount++;
		} // end while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
	//ain't that some shit?
	//that might look like some repeat code from the addStudyHours.php
	//that's cause it IS BITCH.  QUITE YOUR COMPLAINING
	
	//echo $tStamp.' > '.mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y')).' && '.$tStamp.' < '.mktime(1, 0, 0, date('m'), date('d')-date('w')+6, date('Y'));
	
		echo '<table border="1">';
		echo '<tr>
			<th>
				Member Name
			</th>
			<th>
				Required
			</th>
			<th>
				Completed<br />(Week)
			</th>
			<th>
				Over<br />Under
			</th>
			<th>
				Completed<br />(Total)
			</th>
			<th>
				Start/Stop<br />Dates
			</th>
			<th>
				View Log
			</th>
		</tr>';
		for($i=0; $i < $memberCount; $i++)
		{

			if(!$userSHData[$i]['hoursRequired'] || $userSHData[$i]['hoursRequired'] == 0 || $userSHData[$i]['hoursRequired'] == '')
			{
				//User has no required study hours.
				//don't need to do anything!
			} else {
				//calcualte our over/under
				if($userSHData[$i]['weeklyHrs'] < $userSHData[$i]['hoursRequired'])
				{
					//not met requirement
					$overUnder = '<span style="color: #E00000;">'.round($userSHData[$i]['hoursRequired']-$userSHData[$i]['weeklyHrs']).'</span>';
				} else {
					//met reqirement
					$overUnder = '<span style="color: #00CC33;">'.round($userSHData[$i]['weeklyHrs']-$userSHData[$i]['hoursRequired']).'</span>';
				}
				echo "<tr>
						<td>
							<label>".$userSHData[$i]['firstName']." ".$userSHData[$i]['lastName']."</label> 
						</td>\n";
				echo '
						<td>
							'.round($userSHData[$i]['hoursRequired'],2).'
						</td>
						<td>
							'.round($userSHData[$i]['weeklyHrs'],2).'
						</td>
						<td>
							'.$overUnder.'
						</td>
						<td>
							'.round($userSHData[$i]['totalHrs'],2).'
						</td>
						<td>
							'.$userSHData[$i]['startDate'].'<br />
							'.$userSHData[$i]['stopDate'].'
						</td>
						<td>
							<input type="button" name="'.$SHuserDataArray['username'].'" value="View Log" class="studyHrButton" onclick="javascript: window.location.href=\'memberLog.php?uname='.$userSHData[$i]['username'].'\'" />
						</td>
						';
				echo '</tr>';
			}

		}
		echo "</table>";

?>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>