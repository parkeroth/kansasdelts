<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
	$authUsers = array('admin', 'academics', 'proctor');
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	//Lets do our post procesing here
	//set up our user info by looking at the GET variables
	if(isset($_GET['user']) && isset($_GET['action']))
	{
		$member = htmlspecialchars($_GET['user']);
		$action = htmlspecialchars($_GET['action']);
		
		//echo $member.' '.$action;
		
		$getSHUsersDataQ = '
			SELECT *
			FROM studyHourRequirements
			WHERE username="'.$member.'"
			';
		$getSHUserData = mysqli_query($mysqli, $getSHUsersDataQ);
		
		$timeCompleted = 0.0;
		
		while($userSHData = mysqli_fetch_array($getSHUserData, MYSQLI_ASSOC))
		{
			//variables necessary regardless the action
			$timeCompleted = $userSHData['hoursCompleted'];
			
			//we're going to check and see if the user was set to be checked in
			if($action == "in")
			{
							
				//well shit
				//we're checking a user in
				//this means they've just started study hours
				
				//let's make a new log in our studyHourLogs with the data
				//from this study hour session
				//First, we need to generate a timestamp
				$timeStamp = time();
				//echo $timeStamp;
				$newSHLogQ = '
						INSERT INTO studyHourLogs
						(username, proctorIn, timeStamp, open)
						VALUES ("'.$userSHData['username'].'", "'.$_SESSION['username'].'", "'.date('Y-m-d H:i:s', $timeStamp).'", "yes")
						';
				//have query. now execute that shit
				
				//echo $newSHLogQ;
				
				$writeSHLog = mysqli_query($mysqli, $newSHLogQ);
				
				//We also need to update their status in the studyHourRequirements table to say they
				//That they're checked out
				$updateSHStatusQ = '
					UPDATE studyHourRequirements
					SET status="in"
					WHERE username="'.$userSHData['username'].'"
					';
				$updateSHStatus = mysqli_query($mysqli, $updateSHStatusQ);
			} 
			elseif($action == "out") 
			{
				//they were in, so we have work to do
				
				//The easiest way to do this is to search for any logs with the
				//"open" status, close them, and calculate the duration
				//NOTE: this will cause problems if more them one session is open, but
				//that shouldn't be the case.  working the solution another way will be more
				//time/resource comsuming, so i'll just do it the "drity" way until it causes problems
				
				$timeStamp = time();
				
				$getLoggedInSessionsQ = '
					SELECT *
					FROM studyHourLogs
					WHERE username="'.$member.'" AND open="yes"
					';
				//and execute the query
				$getLoggedInSessions = mysqli_query($mysqli, $getLoggedInSessionsQ);
				
				while($sessionData = mysqli_fetch_array($getLoggedInSessions, MYSQLI_ASSOC))
				{
					//echo "in while <br />";
					//get the session info and calculate the duration
					//then write duration and proctorOut to database
					$proctorOut = $_SESSION['username'];
					$sesID = $sessionData['ID'];
					$timeIn = $sessionData['timeStamp'];
					$duration = $timeStamp - strtotime($timeIn);
					
					//echo $proctorOut.' ' .$sesID.' '.$timeIn.' '.$duration.' ';
					
					//now convert to elapsed hours
					$elapsedHrs = floatval($duration/60/60);
					
					//now update our totalHours Counter
					$timeCompleted += $elapsedHrs;
					
					//Now set up our update query
					$logUserOutQ = '
						UPDATE studyHourLogs
						SET proctorOut="'.$proctorOut.'", duration="'.$elapsedHrs.'", open="no"
						WHERE username="'.$member.'" AND ID="'.$sesID.'"
						';
					//execute the query
					$writeUserLogOut = mysqli_query($mysqli, $logUserOutQ);
					
				}
				//end while $sessionData
				
				
				//we're done with updating the studyHourLog table,
				//now set the satus in the studyHourRequirements table to out
				$setStatusOutQ = '
					UPDATE studyHourRequirements
					SET status="out", hoursCompleted="'.$timeCompleted.'"
					WHERE username="'.$member.'"
					';
				$setStatusOut = mysqli_query($mysqli, $setStatusOutQ);
				
			} //End if($action == "in"))
		}//end while ($row=get shit)
	} //end if(isset($_GET['user']) && isset($_GET['action']))
	
	
	header("location: manageStudyHours.php");

?>