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
	$getUName = $_GET['uname'];
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
		SELECT timeStamp, proctorIn, proctorOut, duration
		FROM studyHourLogs
		WHERE username='.$getUName.'
		ORDER BY timeStamp
		';
	$getMemberLog = mysqli_query($mysqli, $getLogQuery);	
	//now we test to make sure there are actually rows, which means log for the user exists
	if(mysqli_num_rows($getMemberLog))
	{
		//We have a user and we have a log, we can now display the results
		$results = 0;
		while($userDataArray = mysqli_fetch_array($getMemberLog, MYSQLI_ASSOC))
			{
				$userSHData[$results]['proctorIn'] = $userDataArray['proctorIn'];
				$userSHData[$results]['proctorOut'] = $userDataArray['proctorOut'];
				$userSHData[$results]['timeStamp'] = $userDataArray['timeStamp'];	
				$userSHData[$results]['duration'] = $userDataArray['duration'];	
				//Let's get the names of our proctors with a few simple query
				$getPInQuery = '
					SELECT firstName, lastName
					FROM members
					WHERE username='.$userDataArray['proctorIn'].'
					';
				$getPOutQuery = '
					SELECT firstName, lastName
					FROM members
					WHERE username='.$userDataArray['proctorOut'].'
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
			}
		//ain't that some shit?
		//Lets's get the name of our actual user
		$getNameQuery = '
					SELECT firstName, lastName
					FROM members
					WHERE username='.$getUName.'
					';
		//execute querys
		$getMemName = mysqli_query($mysqli, $getNameQuery);
		while($uName = mysqli_fetch_array($getMemName, MYSQLI_ASSOC))
		{
			$FName = $uName['firstName'];
			$LName = $uName['lastName'];
		}
		//Finally done with set up.  Now we can work on the output.
?>
		<h2>Member: <?php echo $FName.' '.$LName; ?></h2>
        <table border="1">
        	<tr>
        		<th>
                	Timestamp
                </th>
                	Proctor In
                <th>
                	Proctor Out
                </th>
                <th>
                	Duration
                </th>
        	</tr>
<?php
	for($i=1; $i < $results; $i++)
	{
		echo '
		<tr>
			<td>
				'.date("m-d-Y h:i:s A", $userSHData[$i]['timeStamp']).'
			</td>
			<td>
				'.$userSHData[$i]['pInName'].'
			</td>
			<td>
				'.$userSHData[$i]['pOutName'].'
			</td>
			<td>
				'.$userSHData[$i]['duration'].'
			</td>
		</tr>';	
	}
?>
        </table>
<?php
	} // end if(mysqli_num_rows($getMemberLog))
	else
	{
		//tell user no logs exist for that person
		echo '<p class="dataError">No logs exist for user '.$_GET['uname'].'.  This is either an invalid username or the user has no logs.</p>';
	}
	
}
?>


<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>