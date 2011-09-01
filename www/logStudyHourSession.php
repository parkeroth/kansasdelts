<?php

	//Lets do our post procesing here
	//this is going to look a little strange, as we're looping over the users and 
	//checking our multiple submit buttons
	//set up our user info
	$getSHUsersQ = '
		SELECT *
		FROM studyHourRequirements
		';
	$getSHUsers = mysqli_query($mysqli, $getSHUsersQ);
	
	//Now loop over our shit
	$count = 0;
	while($SHuserDataArray = mysqli_fetch_array($getSHUsers, MYSQLI_ASSOC))
	{
		
		//lets's build our user data array
		//$userSHData[$count]['username'] = $SHuserDataArray['username'];
		$memberUName = $SHuserDataArray['username'];

		//we're going to check and see if the user was set to be checked in
		if(isset($_POST[$memberUName.'_in']))
		{
						
			//well shit
			//we're checking a user in
			//this means they've just started study hours
			
			//let's make a new log in our studyHourLogs with the data
			//from this study hour session
			//First, we need to generate a timestamp
			$timeStamp = time();
			$newSHLogQ = '
					INSERT INTO studyHourLogs
					(username, proctorIn, timeStamp, open)
					VALUES ("'.$userSHData[$x]['username'].'", "'.$_SESSION['username'].'", "'.$timeStamp.'", "yes")
					';
			//have query. now execute that shit
			$writeSHLog = mysqli_query($mysqli, $newSHLogQ);
			
			//We also need to update their status in the studyHourRequirements table to say they
			//That they're checked out
			$updateSHStatusQ = '
				UPDATE studyHourRequirements
				SET status="in"
				WHERE username="'.$userSHData[$x]['username'].'"
				';
			$updateSHStatus = mysqli_query($mysqli, $updateSHStatusQ);
		} 
		elseif(isset($_POST[$memberUName.'_out'])) 
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
				WHERE username="'.$memberUName.'" AND open="yes"
				';
			//echo 'Removing from DB<br />Query: '.$deleteSHUser;
			//and execute the query
			$getLoggedInSessions = mysqli_query($mysqli, $getLoggedInSessionsQ);
			while($sessionData = mysqli_fetch_array($getLoggedInSessions, MYSQLI_ASSOC))
			{
				//get the session info and calculate the duration
				//then write duration and proctorOut to database
				$proctorOut = $_SESSION['username'];
				$sesID = $sessionData['ID'];
				$duration = $timeStamp - $sessionData['timeStamp'];
				//now convert to elapsed hours
				$elapsedHrs = float($duration/60/60);
				
				//Now set up our update query
				$logUserOutQ = '
					UPDATE studyHourLogs
					SET proctorOut="'.$proctorOut.', duration="'.$elapsedHrs.'", open="no"
					WHERE username="'.$memberUName.'" AND ID="'.$sesID.'"
					';
				//execute the query
				$writeUserLogOut = mysqli_query($mysqli, $logUserOutQ);
				
			}
			//end while $sessionData
			//we're done with updating the studyHourLog table,
			//now set the satus in the studyHourRequirements table to out
			$setStatusOutQ = '
				UPDATE studyHourRequirements
				SET status="out"
				WHERE username="'.$memberUName.'"
				';
			$setStatusOut = mysqli_query($mysqli, $setStatusOutQ);
		} //End if(issset($memberUName.'_in']))
	} //end while($SHuserDataArray = mysqli_fetch_array($getSHUsers, MYSQLI_ASSOC))

?>