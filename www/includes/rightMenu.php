<?php
echo '1';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';

$user_position_ids = $session->positions;
echo '2';

	$auth_list = array('admin', 'saa');
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
	$auth_list = array('admin', 'houseManager');
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
						<li><a href="baddDutyDates.php">BADD Duty Calendar</a></li>
						<?php 
						if($_SESSION["userType"] != "|brother")
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
						<li><a href="missedDutyForm.php">Report Missed Duty</a></li>
						<li><a href="writeUpForm.php">Submit Honor Board <br />Write Up</a></li>
						<li><a href="brokenItemForm.php">Report Broken Item</a></li>
						<li><a href="ideaForm.php">Submit Idea</a></li>
					</ul>
				</li>
				<li>
					<a href="#">Chapter Records</a>
					<ul>
						<li><a href="documents.php">Document Box</a></li>
						<li><a href="familyTree.php">Family Tree</a></li>
						<li><a href="management/chapterAgenda.php">View Agendas</a></li>
						<li><a href="chapterMinutes.php">View Minutes</a></li>
					</ul>
				</li>
				<li>
					<a href="#">My Account</a>
					<ul>
						<li><div id="notificationButton"><a href="#">Notification Settings</a></div></li>
						<li><a href="/core/memberInfo.php">Change Roster Info</a></li>
						<li><a href="passwordChangeForm.php">Change Password</a></li>
						<li><a href="accomplishmentForm.php">My Accomplishments</a></li>
						<?php 
						if($position)
						{
							echo "<li><a href=\"management/positionOverview.php?position=$position->id\">My Position</a></li>";
							echo "<li><a href=\"viewReportingTasks.php\">View FAAR Tasks</a></li>";
						}?>
					</ul>
				</li>
				
			<? }
	
			if(strpos($userDataArray['accountType'],"admin") || strpos($userDataArray['accountType'], "secretary") ){ ?>
			
				<li>
					<a href="#">Manage Users</a>
					<ul>
						<li><a href="attendanceRecords.php">Attendance Records</a></li>
						<li><a href="/core/addUserForm.php">Add a User</a></li>
						<li><a href="graduationUpdateForm.php">Graduation Update</a></li>
						<li><a href="/core/electionUpdateForm.php">Election Update</a></li>
						<li><a href="removeUserForm.php">Remove User</a></li>
						<li><a href="userStatusForm.php">User Status Form</a></li>
						<li><a href="chapterContactForm.php">Send Text</a></li>
					</ul>
				</li>
				<li>
					<a href="#">Manage Meetings</a>
					<ul>
<?php

foreach(Position::$BOARD_ARRAY as $code => $name){
	echo '<li><a href="management/boardOverview.php?board='.$code.'">'.$name.'</a></li>';
}

?>
					</ul>
				</li>
				<li>
					<a href="#">Manage Chapter</a>
					<ul>
						<li><a href="agendaList.php">Chapter Agendas</a></li>
						<li><a href="attendanceExcused.php">Excuse Member</a></li>
						<li><a href="attendanceForm.php">Attendance Update</a></li>
						<li><a href="minutesList.php">Take Minutes</a></li>
					</ul>
				</li>
				
			<?php }
			
			if(strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "academics") || strpos($userDataArray['accountType'], "proctor") ){ ?>
				
				<li>
					<a href="#">Manage Academics</a>
					<ul>
						<?php if( strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "academics") ){ ?>
							
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
			
			if(strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "treasurer") ){ ?>
				
				<li>
					<a href="#">Manage Finances</a>
					<ul>
						<li><a href="manageFines.php">View Fines</a></li>
						<li><a href="newApparelForm.php">New Apparel Order</a></li>
						<li><a href="manageApparelOrders.php">Apparel Orders</a></li>
					</ul>
				</li>
				
			<?php }
			
			if(strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "communityService") ){ ?>
				
				<li>
					<a href="#">Manage Community Service</a>
					<ul>
						<li><a href=" javascript:MM_openBrWindow('AddCalEvent.php?type=communityService','','width=500,height=400, scrollbars=1');">Add Event</a></li>
						<li><a href="manageEvents.php?type=communityService">View Events</a></li>
						<li><a href="changeHoursForm.php?type=communityService">Change Service Hours</a></li>
					</ul>
				</li>
				
			<?php }
			
			if(strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "publicRel") || strpos($userDataArray['accountType'], "philanthropy") ){ ?>
				
				<li>
					<a href="#">Manage Philanthopies</a>
					<ul>
						<li><a href="javascript:MM_openBrWindow('AddCalEvent.php?type=philanthropy','','width=500,height=400, scrollbars=1');">Add Event</a></li>
						<li><a href="manageEvents.php?type=philanthropy">View Events</a></li>
						<li><a href="changeHoursForm.php?type=philanthropy">Change Service Hours</a></li>
					</ul>
				</li>
				
			<?php }
			
			if(strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "brotherhood") ){ ?>
				
				<li>
					<a href="#">Manage Brotherhood</a>
					<ul>
						<li><a href=" javascript:MM_openBrWindow('AddCalEvent.php?type=brotherhood','','width=500,height=400, scrollbars=1');">Add Event</a></li>
						<li><a href="manageEvents.php?type=brotherhood">View Events</a></li>
					</ul>
				</li>
				
			<?php }
			
			if(strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "houseManager") ){ ?>
			
				<li>
					<a <?php if($numBroken){ echo "class=\"notify\""; } ?> href="#">Manage House Work</a>
					<ul>
						<li><a href=" javascript:MM_openBrWindow('AddCalEvent.php?type=house','','width=500,height=400, scrollbars=1');">Add Event</a></li>
						<li><a href="manageEvents.php?type=house">View Events</a></li>
						<li><a href="manageVolunteers.php">View Volunteers <?php if($numVol){ echo "<span class=\"redHeading\">(".$numVol.")</span>";} ?></a></li>
						<li><a href="manageBrokenItems.php">View Broken Items <?php if($numBroken){ echo "<span class=\"redHeading\">(".$numBroken.")</span>";} ?></a></li>
						<li><a href="changeHoursForm.php?type=house">Change House Hours</a></li>
					</ul>
				</li>
				
			<?php } 
			
			if(strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "saa") || strpos($userDataArray['accountType'], "honorBoard") ){ ?>
			
				<li>
					<a <?php if($numNewWriteUps || $numNewMisses){ echo "class=\"notify\""; } ?> href="#">Manage Honor Board</a>
					<ul>
						<?php if( strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "saa") ){ ?>
							
						<li><a href="/core/chooseCommittee.php?committee=honor-board">Select Honor Board</a></li>
						<li><a href="manageMissedDuties.php">Missed Duties <?php if($numNewMisses){ echo "<span class=\"redHeading\">(".$numNewMisses.")</span>";} ?></a></li>
						<li><a href="missedDutyLog.php">Missed Duty Log</a></li>
						
						<?php } ?>
						
						<li><a href="manageWriteUps.php">View Write Ups <?php if($numNewWriteUps){ echo "<span class=\"redHeading\">(".$numNewWriteUps.")</span>";} ?></a></li>
						<li><a href="conductList.php">Conduct Summary</a></li>
						
					</ul>
				</li>
				
			<?php }
			
			//include_once($_SERVER['DOCUMENT_ROOT'].'/recruitment/menu.php');
			
			if(strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "drm") ){ ?>
			
				<li>
					<a <?php if($numSober){ echo "class=\"notify\""; } ?> href="#">Manage DRM Stuff</a>
					<ul>
						<li><a href="manageSoberGentEvents.php">View Events <?php if($numSober){ echo "<span class=\"redHeading\">(".$numSober.")</span>";} ?></a></li>
						<li><a href="viewSoberGentLog.php">View Sober Gent Log</a></li>
						<li><a href="baddDutyDates.php">BADD Duty Dates</a></li>
					</ul>
				</li>
				
			<?php }
			
			if(strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "scholarship") ){ ?>
			
				<li>
					<a href="#">Manage Scholarship</a>
					<ul>
						<li><a href="scholarshipReview.php">View Submissions</a></li>
					</ul>
				</li>
				
			<?php }
			
			if(strpos($userDataArray['accountType'], "admin") || strpos($userDataArray['accountType'], "publicRel") ){ ?>
			
				<li>
					<a href="#">Manage External Affairs</a>
					<ul>
						<li><a href="manageEvents.php?type=pr">View Events</a></li>
						<li><a href="/blog/manageBlog.php">Front Page Blog</a></li>
					</ul>
				</li>
				
			<?php }
			
			echo "</ul>\n";
