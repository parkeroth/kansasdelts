<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/finance/classes/Fine.php';

$user_position_ids = $session->positions;

$user_position = NULL;

$logged_in_user = new Member($session->member_id);

foreach($user_position_ids as $position_ids){
	$position = new Position($position_ids);
	if($position->board != 'committee'){
		$user_position = $position;
	}
}

	$auth_list = array('admin', 'saa', 'honor-board', 'pres');
	if($session->isAuth($auth_list)){
		$query = "
			SELECT status, COUNT(ID)
			FROM writeUps 
			GROUP BY status";
		$result = mysqli_query($mysqli, $query);
		
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if($row[status] == "review")
			{
				$numNewWriteUps = $row['COUNT(ID)'];
			}
			else if($row[status] == "active")
			{
				$numCases = $row['COUNT(ID)'];
			}
		}
		
		$query = "
			SELECT COUNT(ID)
			FROM infractionLog 
			WHERE status='pending'";
		$result = mysqli_query($mysqli, $query);
		
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$numNewMisses = $row['COUNT(ID)'];
			
		}
	}
	$auth_list = array('admin', 'drm');
	if($session->isAuth($auth_list)){
		$query = "
			SELECT soberGentEvents.ID AS eventID, COUNT(soberGentLog.ID) AS numberOfGents
			FROM soberGentLog
			RIGHT JOIN soberGentEvents
			ON soberGentLog.eventID = soberGentEvents.ID
			GROUP BY soberGentLog.eventID
			ORDER BY eventID";
		$result = mysqli_query($mysqli, $query);
		
		$numSober=0;
		
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if($row[numberOfGents] < 1)
			{
				//$numSober++;
			}
		}
	}
	$auth_list = array('admin', 'houseManager', 'vpInternal');
	if($session->isAuth($auth_list)){
		$query = "
			SELECT COUNT(username), type
			FROM volunteer
			WHERE type='house'
			GROUP BY type";
		$result = mysqli_query($mysqli, $query);
		
		$numVol=0;
		
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$numVol += $row['COUNT(username)'];
		}
		
		$query = "
			SELECT ID
			FROM brokenStuff";
		$result = mysqli_query($mysqli, $query);
		
		$numBroken=0;
		
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$numBroken++;
		}
	}
	$auth_list = array('admin', 'recruitment');
	if($session->isAuth($auth_list)){
		$query = "
			SELECT COUNT(ID) as total
			FROM recruits
			WHERE primaryContact IS NULL";
		$result = mysqli_query($mysqli, $query);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$numNewRecruits = $row[total];
	}

	echo "<ul id=\"menu\">\n";
	
			if( 1 == 1){ ?>
			
				<li>
					<a href="#">I Want To</a>
					<ul>
						<li><a href="javascript:MM_openBrWindow('showRoster.php','','width=800,height=600, scrollbars=1');">View Roster</a></li>
						<li><a href="calendar.php">View Calendar</a></li>
                        <li><a href="/food/viewMenu.php">View Meal Menu</a></li>
						<li><a href="schedule.php">View My Classes</a></li>
						<li><a href="risk/baddDutyDates.php">BADD Duty Calendar</a></li>
						<?php 
						if($user_position)
						{
							echo "<li><a href=\"http://www.google.com/a/kansasdelts.org\">Check Apps Emails</a></li>";
						}
						?>
					</ul>
				</li>
				<li>
					<a href="#">Forms</a>
					<ul>
						<li><a href="classSearchForm.php">Search Classes</a></li>
						<li><a href="honor/missedDutyForm.php">Report Missed Duty</a></li>
						<li><a href="honor/writeUpForm.php">Honor Board Write Up</a></li>
						<li><a href="brokenItemForm.php">Report Broken Item</a></li>
						<li><a href="ideaForm.php">Submit Idea</a></li>
					</ul>
				</li>
				<li>
					<a href="#">Information</a>
					<ul>
						<li><a href="records/chapterRecords.php">Chapter Records</a></li>
						<li><a href="documents.php">Document Box</a></li>
						<li><a href="familyTree.php">Family Tree</a></li>
					</ul>
				</li>
				<li>
					<a href="#">My Account</a>
					<ul>
						<li><a href="/core/memberInfo.php">Change Roster Info</a></li>
						<li><a href="passwordChangeForm.php">Change Password</a></li>
						<li><a href="accomplishmentForm.php">My Accomplishments</a></li>
						<?php 
						if($user_position)
						{
							echo "<li><a href=\"records/positionOverview.php?position=$user_position->id\">My Position</a></li>";
							echo "<li><a href=\"viewReportingTasks.php\">My FAAR Tasks</a></li>";
						}?>
					</ul>
				</li>
				
			<? }
	
			$auth_list = array('admin', 'secretary', 'pres');
			if($session->isAuth($auth_list)){ ?>
			
				<li>
					<a href="#">Manage Users</a>
					<ul>
						<li><a href="attendanceRecords.php">Attendance Records</a></li>
						<li><a href="/core/addUserForm.php">Add a User</a></li>
						<li><a href="graduationUpdateForm.php">Graduation Update</a></li>
						<li><a href="/core/electionUpdateForm.php">Election Update</a></li>
						<li><a href="removeUserForm.php">Remove User</a></li>
						<li><a href="/core/memberStatusForm.php">User Status Form</a></li>
						<li><a href="chapterContactForm.php">Send Text</a></li>
					</ul>
				</li>
				<li>
					<a href="#">Manage Records</a>
					<ul>
						<li><a href="records/manageBoard.php">Board Meetings</a></li>
						<li><a href="records/manageChapter.php">Chapter Meetings</a></li>
						<li><a href="records/attendanceRecords.php">Attendance Records</a></li>
					</ul>
				</li>
				
			<?php }
			
			$auth_list = array('admin', 'secretary', 'pres', 'vpInternal', 'vpExternal');
			if($session->isAuth($auth_list)){ ?>
			
			<li>
					<a href="#">Manage Board Meetings</a>
					<ul>
<?php

foreach(Position::$BOARD_ARRAY as $code => $name){
	if($name){
		echo '<li><a href="records/boardOverview.php?board='.$code.'">'.$name.'</a></li>';
	}
}

?>
					</ul>
				</li>
			<?php }
			
			$auth_list = array('admin', 'academics', 'proctor');
			if($session->isAuth($auth_list)){ ?>
				
				<li>
					<a href="#">Manage Academics</a>
					<ul>
						<?php $auth_list = array('admin', 'academics');
						if($session->isAuth($auth_list)){{ ?>
							
						<li><a href="viewCourseHours.php">View Course Hours</a></li>
						<li><a href="changeGradesForm.php">Update Grades</a></li>
						<li><a href="/core/chooseCommittee.php?committee=proctor">Choose Proctors</a></li>
						<li><a href="/academics/addStudyHourUsers.php">Assigned Hours</a></li>
						<li><a href="/academics/viewLogs.php">View Logs</a></li>
						
						<?php } ?>
						
						<li><a href="/academics/manageStudyHours.php">Proctor Hours</a></li>
						
					</ul>
				</li>
				
			<?php }
			
			$auth_list = array('admin', 'treasurer', 'pres');
			if($session->isAuth($auth_list)){  
				$fine_manager = new Fine_Manager();
				$num_pending = $fine_manager->get_number_pending();
				?>
				
				<li>
					<a <?php if($num_pending){ echo "class=\"notify\""; } ?> href="#">Manage Finances</a>
					<ul>
						<li><a href="/finance/newFine.php">Fine Form</a></li>
						<li><a href="/finance/manageFines.php">View Fines <?php if($num_pending){ echo "<span class=\"redHeading\">(".$num_pending.")</span>";} ?></a></li>
					</ul>
				</li>
				
			<?php }
			
			$auth_list = array('admin', 'pres', 'vpInternal', 'vpExternal', 'houseManager', 'communityService', 'philanthropy');
			if($session->isAuth($auth_list)){  ?>
				
				<li>
					<a href="#">Manage Hours</a>
					<ul>
						<?php $auth_list = array('admin', 'pres', 'vpInternal', 'houseManager');
						if($session->isAuth($auth_list)){ ?>
						<li><a href="hours/changeForm.php?type=house">House Hours</a></li>
							
						<?php } $auth_list = array('admin', 'pres', 'vpInternal', 'communityService');
						if($session->isAuth($auth_list)){ ?>
						<li><a href="hours/changeForm.php?type=service">Service Hours</a></li>
						
						<?php } $auth_list = array('admin', 'pres', 'vpExternal', 'philanthropy');
						if($session->isAuth($auth_list)){ ?>
						<li><a href="hours/changeForm.php?type=philanthropy">Philanthropies</a></li></li>
						<?php } ?>
					</ul>
				</li>
				
			<?php }
			
			$auth_list = array('admin', 'pres', 'vpInternal', 'vpExternal', 'houseManager', 'communityService', 'philanthropy', 'brotherhood');
			if($session->isAuth($auth_list)){  ?>
				
				<li>
					<a href="#">Manage Events</a>
					<ul>
						<?php $auth_list = array('admin', 'pres', 'vpInternal', 'houseManager');
						if($session->isAuth($auth_list)){ ?>
						<li><a href="manageEvents.php?type=house">House Cleanings</a></li>
							
						<?php } $auth_list = array('admin', 'pres', 'vpInternal', 'communityService');
						if($session->isAuth($auth_list)){ ?>
						<li><a href="manageEvents.php?type=communityService">Service Events</a></li>
						
						<?php } $auth_list = array('admin', 'pres', 'vpInternal', 'brotherhood');
						if($session->isAuth($auth_list)){ ?>
						<li><a href="manageEvents.php?type=brotherhood">Brotherhood Events</a></li>
						
						<?php } $auth_list = array('admin', 'pres', 'vpExternal', 'philanthropy');
						if($session->isAuth($auth_list)){ ?>
						<li><a href="manageEvents.php?type=philanthropy">Philanthropies</a></li>
						<?php } ?>
					</ul>
				</li>
				
			<?php }
			
			$auth_list = array('admin', 'houseManager');
			if($session->isAuth($auth_list)){ ?>
			
				<li>
					<a <?php if($numBroken){ echo "class=\"notify\""; } ?> href="#">Manage House Work</a>
					<ul>
						<li><a href="manageVolunteers.php">View Volunteers <?php if($numVol){ echo "<span class=\"redHeading\">(".$numVol.")</span>";} ?></a></li>
						<li><a href="manageBrokenItems.php">View Broken Items <?php if($numBroken){ echo "<span class=\"redHeading\">(".$numBroken.")</span>";} ?></a></li>
					</ul>
				</li>
				
			<?php } 
			
			$auth_list = array('admin', 'pres', 'saa', 'honor-board');
			if($session->isAuth($auth_list)){  ?>
			
				<li>
					<a <?php if($numNewWriteUps || $numNewMisses){ echo "class=\"notify\""; } ?> href="#">Manage Honor Board</a>
					<ul>
						<?php	$auth_list = array('admin', 'saa');
								$pres_list = array('admin', 'saa', 'pres');
								if($session->isAuth($auth_list)){  ?>
							
						<li><a href="/core/chooseCommittee.php?committee=honor-board">Select Honor Board</a></li>
						<li><a href="honor/setPunishments.php">Set Punishments</a></li>
						<li><a href="/finance/newFine.php">Fine Form</a></li>
						
						<?php }  if($session->isAuth($pres_list)){ ?>
						
						<li><a href="honor/manageMissedDuties.php">Missed Duty Hopper <?php if($numNewMisses){ echo "<span class=\"redHeading\">(".$numNewMisses.")</span>";} ?></a></li>
						<li><a href="honor/missedDutyLog.php">Missed Duty Log</a></li>
						
						<?php } ?>
						
						<li><a href="honor/manageWriteUps.php">View Write Ups <?php if($numNewWriteUps){ echo "<span class=\"redHeading\">(".$numNewWriteUps.")</span>";} ?></a></li>
						<li><a href="honor/conductList.php">Conduct Summary</a></li>
						
					</ul>
				</li>
				
			<?php }
			
			//include_once($_SERVER['DOCUMENT_ROOT'].'/recruitment/menu.php');
			
			$auth_list = array('admin', 'pres', 'drm');
			if($session->isAuth($auth_list)){  ?>
			
				<li>
					<a <?php if($numSober){ echo "class=\"notify\""; } ?> href="#">Manage DRM Stuff</a>
					<ul>
						<li><a href="risk/manageSoberGentEvents.php">View Events <?php if($numSober){ echo "<span class=\"redHeading\">(".$numSober.")</span>";} ?></a></li>
						<li><a href="risk/viewSoberGentLog.php">View Sober Gent Log</a></li>
						<li><a href="risk/baddDutyDates.php">BADD Duty Dates</a></li>
					</ul>
				</li>
				
			<?php }
			
			$auth_list = array('admin', 'scholarship');
			if($session->isAuth($auth_list)){  ?>
			
				<li>
					<a href="#">Manage Scholarship</a>
					<ul>
						<li><a href="scholarshipReview.php">View Submissions</a></li>
					</ul>
				</li>
				
			<?php }
			
			$auth_list = array('admin', 'publicRel');
			if($session->isAuth($auth_list)){ ?>
			
				<li>
					<a href="#">Manage External Affairs</a>
					<ul>
						<li><a href="manageEvents.php?type=pr">View Events</a></li>
						<li><a href="/blog/manageBlog.php">Front Page Blog</a></li>
					</ul>
				</li>
				
			<?php }
			
			echo "</ul>\n";
