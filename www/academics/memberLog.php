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
	.dataError {
		border-style: solid;
		border-width: 2px;
		background-color: #990000;
		color: white;
		font-size: 16px;
		text-align: center;
		text-transform: uppercase;
		padding: 10px;
		}
	.timeStamp {
		width: 200px;
	}
	.proctor {
		width: 125px;
	}
	.duration {
		width: 80px;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>
<h1>Individual Study Hour Logs</h1>

<?php

//Start our shit here
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
if(empty($_GET) || !isset($_GET['uname']))
{
	//No GET variables!  How are we supposed to know who da hell they're talking about?
	echo '<p class="dataError">No username specified!  Can\'t get logs!</p>';
} else {
	//username specifed, let's see if the user actually exists or if someone is fucking with us
	$getUName = htmlspecialchars($_GET['uname']);
	/*
	//Time for one MASSIVE query
	//We don't actually need this query, but it was so pretty I had to leave it in
	$getLogQuery = '
		SELECT studyHourLogs.timeStamp, studyHourLogs.proctorIn, studyHourLogs.proctorOut, studyHourLogs.duration, members.firstName, members.lastName, members.username
		FROM studyHourLogs LEFT JOIN members
		ON studyHourLogs.username = members.username
		WHERE studyHourLogs.username='.$getUName.'
		ORDER BY members.lastName
		';*/
	$getLogQuery = '
		SELECT timeStamp, proctorIn, proctorOut, duration, ID, username
		FROM studyHourLogs
		WHERE username="'.$getUName.'"
		ORDER BY timeStamp DESC
		LIMIT 50
		';
	$getMemberLog = mysqli_query($mysqli, $getLogQuery);	
	//now we test to make sure there are actually rows, which means log for the user exists
	//echo $getLogQuery;
	$numRows = mysqli_num_rows($getMemberLog);
	if($numRows > 0)
	{
		//We have a user and we have a log, we can now display the results
		$results = 0;
		//set date parameters, so we can break up by week
		$weekStart = mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'));
		while($userDataArray = mysqli_fetch_array($getMemberLog, MYSQLI_ASSOC))
			{
				// grab user data
				$userSHData[$results]['proctorIn'] = $userDataArray['proctorIn'];
				$userSHData[$results]['proctorOut'] = $userDataArray['proctorOut'];
				$userSHData[$results]['timeStamp'] = $userDataArray['timeStamp'];	
				$userSHData[$results]['duration'] = $userDataArray['duration'];
				$userSHData[$results]['username'] = $userDataArray['username'];
				$userSHData[$results]['ID'] = $userDataArray['ID'];
				
				//to get the week it was in, we need to see if the current week
				//works or if we should change it
				while(strtotime($userDataArray['timeStamp']) < $weekStart)
				{
					// 6 days; 24 hours; 60 mins; 60secs till end of week
					$weekStart = $weekStart - (6 * 24 * 60 * 60);
				}
				$userSHData[$results]['inWeek'] = $weekStart;
				//echo $userDataArray['proctorIn'].' '.$userDataArray['proctorOut'].' '.$userDataArray['duration'];
				//Let's get the names of our proctors with a few simple query
				$getPInQuery = '
					SELECT firstName, lastName
					FROM members
					WHERE username="'.$userDataArray['proctorIn'].'"
					';
				$getPOutQuery = '
					SELECT firstName, lastName
					FROM members
					WHERE username="'.$userDataArray['proctorOut'].'"
					';
				//Set up our holder variables
				$userSHData[$results]['pInName'] = '';
				$userSHData[$results]['pOutName'] = '';
				//execute querys
				$getpInName = mysqli_query($mysqli, $getPInQuery);
				while($procInName = mysqli_fetch_array($getpInName, MYSQLI_ASSOC))
				{
					$pInFName = $procInName['firstName'];
					$pInLName = $procInName['lastName'];
					$userSHData[$results]['pInName'] = $pInFName.' '.$pInLName;
				}
				$getpOutName = mysqli_query($mysqli, $getPOutQuery);
				while($procOutName = mysqli_fetch_array($getpOutName, MYSQLI_ASSOC))
				{
					$pOutFName = $procOutName['firstName'];
					$pOutLName = $procOutName['lastName'];
					$userSHData[$results]['pOutName'] = $pOutFName.' '.$pOutLName;
				}
				$results++;
			}//end while($userDataArray = mysqli_fetch_array($getMemberLog, MYSQLI_ASSOC))
		//ain't that some shit?
		//Lets's get the name of our actual user
		$getNameQuery = '
					SELECT firstName, lastName
					FROM members
					WHERE username="'.$getUName.'"
					';
		//execute querys
		$getMemName = mysqli_query($mysqli, $getNameQuery);
		while($uName = mysqli_fetch_array($getMemName, MYSQLI_ASSOC))
		{
			$FName = $uName['firstName'];
			$LName = $uName['lastName'];
		}
		
		//Lets's get the required hours for the user
		$getReqHrsQ = '
					SELECT hoursRequired
					FROM studyHourRequirements
					WHERE username="'.$getUName.'"
					';
		//execute querys
		$getReqHrs = mysqli_query($mysqli, $getReqHrsQ);
		while($hrsNeeded = mysqli_fetch_array($getReqHrs, MYSQLI_ASSOC))
		{
			$requiredHours = $hrsNeeded['hoursRequired'];
		}
		//Finally done with set up.  Now we can work on the output.
?>
		<h2>Member: <?php echo $FName.' '.$LName; ?></h2>
        <table border="1">
        	<tr>
        		<th class="timeStamp">
                	Timestamp
                </th>
                <th class="proctor">
                	Proctor In
                </th>
                <th class="proctor">
                	Proctor Out
                </th>
                <th class="duration">
                	Duration (Hrs)
                </th>
				<th class="action">
                	Action
                </th>
        	</tr>
        </table>
<?php
	$loopWeek = mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'));
	$weeklyHrs = 0.0;
	echo '<h2>Week of '.date('m-d-Y',$loopWeek).'</h2>';
	echo '<table border="1">';
	for($i=0; $i < $results; $i++)
	{
		while($userSHData[$i]['inWeek'] < $loopWeek)
		{
			//we need to close the table for the current week
			echo '</table>';
			//lets see if the met the required hours requirement
			if($weeklyHrs < $requiredHours)
			{
				//didn't meet the requirement
				$hrStatement = '
					<h3 style="color: #E00000 !important;">User Short '.round(floatval($requiredHours-$weeklyHrs),2).' Hours</h3>
					';
			} else {
				//did meet the requirement
				$hrStatement = '
					<h3 style="color: #00CC33 !important;">User Over '.round(floatval($weeklyHrs-$requiredHours),2).' Hours</h3>
					';
			}
			//lets echo the hour information out to the user
			echo "
				<h3>Hours for the Week: ".$weeklyHrs."</h3>
				<h3>Required Hours per Week: ".$requiredHours."</h3>
				".$hrStatement."<br />";
			//now reset for a new week
			$weeklyHrs = 0;
			// 7 days; 24 hours; 60 mins; 60secs till end of week
			$loopWeek = $loopWeek - (7 * 24 * 60 * 60);
			echo '<h2>Week of '.date('m-d-Y',$loopWeek).'</h2>';
			
			//now lets start our new table for the new week
			echo '<table border="1">';
			
		}
		
		//lets add the duration to $weeklyHrs
		$weeklyHrs += $userSHData[$i]['duration'];
		
		//and our actual table data
		echo '
		<tr>
			<td class="timeStamp">
				'.date("m-d-Y h:i:s A", strtotime($userSHData[$i]['timeStamp'])).'
			</td>
			<td class="proctor">
				'.$userSHData[$i]['pInName'].'
			</td>
			<td class="proctor">
				'.$userSHData[$i]['pOutName'].'
			</td>
			<td class="duration">
				'.round($userSHData[$i]['duration'],2).'
			</td>';
			
				echo '<td class="action">';
                echo "<input 	type=\"button\" 
								name=\"remove-".$attendanceArray[ID]."\" 
								value=\"Remove\"
								onclick=\"window.location.href='action.php?type=remove&amp;ID=".$userSHData[$i][ID]."&amp;username=".$userSHData[$i][username]."'\" />";
                echo '</td>';
			
		echo '</tr>';	
		//echo $weeklyHrs;
	}
?>
        </table>
<?php
		//lets get the info from the last session, since it's not going to get tagged
		//on conventionally
		if($weeklyHrs < $requiredHours)
		{
			//didn't meet the requirement
			$hrStatement = '
				<h3 style="color: #E00000 !important;">User Short '.round(floatval($requiredHours-$weeklyHrs),2).' Hours</h3>
				';
		} else {
			//did meet the requirement
			$hrStatement = '
				<h3 style="color: #00CC33 !important;">User Over '.round(floatval($weeklyHrs-$requiredHours),2).' Hours</h3>
				';
		}
		//lets echo the hour information out to the user
		echo "
			<h3>Hours for the Week: ".round($weeklyHrs,2)."</h3>
			<h3>Required Hours per Week: ".$requiredHours."</h3>
			".$hrStatement."<br />";
	} // end if(mysqli_num_rows($getMemberLog))
	else
	{
		//tell user no logs exist for that person
		echo '<p class="dataError">No logs exist for user '.htmlspecialchars_decode($getUName).'.  This is either an invalid username or the user has no logs.</p>';
	}
	
}
?>


<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>