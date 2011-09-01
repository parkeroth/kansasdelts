<?php 
	include_once('classes/RecruitManager.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	$recruit_manager = new RecruitManager($mysqli);
	
	$num_new_recruits = sizeof($recruit_manager->get_new_recruits());
	
	if(	strpos($userDataArray['accountType'], "admin") || 
		strpos($userDataArray['accountType'], "recruitment") ){ ?>
			
		<li>
			<a <?php if($numNewRecruits){ echo "class=\"notify\""; } ?> href="#">
            Manage Recruitment</a>
            <ul>
            	<li><a href="/recruitment/overview.php">Overview</a></li>
                <li><a href="/recruitment/newList.php">
                    View New Recruits <?php if($numNewRecruits){ 
                    echo "<span class=\"redHeading\">(".$numNewRecruits.")</span>";} ?>
                    </a></li>
                <li><a href="/recruitment/recruitList.php">View Recruit List</a></li>
                <li><a href="/recruitment/chooseCommittee.php">Select Committee</a></li>
            </ul>
		</li>
<?php } ?>