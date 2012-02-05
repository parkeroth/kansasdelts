<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'academics', 'pledgeEd');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/studyHours.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';

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

		<table border="1">
		<tr>
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
		</tr>
           <?php
                $sh_log_manager = new Study_Hour_Log_Manager();
                $sh_manager = new Study_Hour_Manager();
                $userList = $sh_manager->get_all_sh_users();

                foreach($userList as $shUser) {
                        if(!$shUser->hoursRequired || $shUser->hoursRequired == 0 || $shUser->hoursRequired == '') {
                                //User has no required study hours.
				//don't need to do anything!
                        } else {
                                $user_info = new Member($shUser->id);
                                $week_completed = $sh_log_manager->get_current_week_data($shUser->id);
                                if($week_completed < $shUser->hoursRequired){
                                        //not met requirement
                                        $overUnder = '<span style="color: #E00000;">'.round($shUser->hoursRequired-$week_completed).'</span>';
                                } else {
                                        //met requirement
                                        $overUnder = '<span style="color: #00CC33;">'.round($week_completed-$shUser->hoursRequired).'</span>';
                                }
                                echo "<tr>
						<td>
							<label>".$user_info->first_name." ".$user_info->last_name."</label>
						</td>\n";
				echo '
						<td>
							'.round($shUser->hoursRequired,2).'
						</td>
						<td>
							'.round($week_completed,2).'
						</td>
						<td>
							'.$overUnder.'
						</td>
						<td>
							'.round($shUser->totalHrs,2).'
						</td>
						<td>
							'.$shUser->startDate.'<br />
							'.$shUser->stopDate.'
						</td>
						<td>
							<input type="button" name="'.$shUser->userID.'" value="View Log" class="studyHrButton" onclick="javascript: window.location.href=\'memberLog.php?uid='.$shUser->userID.'\'" />
						</td>
						';
				echo '</tr>';
                        }
                }
                ?>
		</table>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>