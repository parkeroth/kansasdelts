<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
	$authUsers = array('admin', 'academics', 'proctor');
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
        include_once 'classes/studyHours.php';
        include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';

	//Lets do our post procesing here
	//set up our user info by looking at the GET variables
	if(isset($_GET['uid']) && isset($_GET['action']))
	{
		$member = htmlspecialchars($_GET['uid']);
		$action = htmlspecialchars($_GET['action']);

		$sh_log_manager = new Study_Hour_Log_Manager();
                $sh_manager = new Study_Hour_Manager();
                $user_requirements = $sh_manager->get_user_sh_requirements($member);

                       //we're going to check and see if the user was set to be checked in
			if($action == "in")
			{
				//well shit
				//we're checking a user in
				//this means they've just started study hours

				//let's make a new log in our studyHourLogs with the data
				//from this study hour session
                                $new_log = new Study_Hour_Logs();
                                $new_log->start_sh_session();

                                //now set them their status to 'in'
                                $user_requirements->set_user_status('in');
                        }
                        elseif($action == "out")
			{
                                //they were in, so we have work to do

				//The easiest way to do this is to search for any logs with the
				//"open" status, close them, and calculate the duration
				//NOTE: this will cause problems if more them one session is open, but
				//that should never happen.  working the solution another way will be more
				//time/resource comsuming, so i'll just do it the "drity" way until it causes
                                //ideally i'll do some error checking on study hour session start to make sure
                                //their's not already an active session

                                $timeCompleted = 0.0;

                                //first close the open session, storing session duration in the temp var
                                $open_sessions = $sh_log_manager->get_open_sessions($member);
                                foreach($open_sessions as $curSession) {
                                        $timeCompleted += $curSession->end_sh_session();
                                }

                                //now update hours completed
                                $user_requirements->update_hrs_completed($timeCompleted);

                                //and set status to 'out'
                                $user_requirements->set_user_status('out');
                        }
        }

	header("location: manageStudyHours.php");

?>