<?php
//include_once($_SERVER['DOCUMENT_ROOT'].'/testScripts/showDebug.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'academics', 'proctor');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
include_once 'classes/studyHours.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';

/**
 * Processing Section
 */

//this just handles hour adjustment.  opening and closing sessions is NOT handled on this page
if($_SERVER['REQUEST_METHOD'] == "POST") {
        $new_session = new Study_Hour_Logs();
        $new_session->proctorIn = $_SESSION[uid];
        $new_session->proctorIn = $_SESSION[uid];
        $new_session->timeIn = date( 'Y-m-d H:i:s', strtotime( $_POST['date'] ) );
        $new_session->duration = $_POST[hours];
        $new_session->open = 'no';
        $new_session->insert();

	$successMessage = $_POST[hours]." hour(s) added to logs.";
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");

// This is going to be one mother of a page
// Sit down class and take notes
// Cause the show's about to start
// Let's begin
?>

<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript">

	$(function() {
		$("#datepicker").datepicker();
	});

</script>

<style type="text/css">
	table.proctor {
		margin-left: auto;
		margin-right: auto;
		text-align: center;
		}
	table.proctor th {
		font-size: 14px;
		text-align: center;
		padding: 5px;
		}
	table.proctor td {
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
	.studyHrButton {
		width: 50px;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Manage User Study Hours</h1>

<form id="chooseProctor" name="chooseProctor" method="post" action="logStudyHourSession.php" onSubmit="return Confirm();">
<table class="proctor" border="1">
    <tr>
            <th style="width: 250px;">
                    Name
            </th>
            <th style="width: 50px;">
                    Required Hours
            </th>
            <th style="width: 60px;">
                    Check Out
            </th>
            <th style="width: 60px;">
                    Check In
            </th>
    </tr>

<?php
        //initialize some class instances here
        $sh_log_manager = new Study_Hour_Log_Manager();
        $sh_manager = new Study_Hour_Manager();

        $sh_users = $sh_manager->get_all_sh_users();

        foreach($sh_users as $cur_user) {
                $mem_info  = new Member($cur_user->member_id);
                echo '
			<tr>
				<td style="width: 250px;">
					'.$mem_info->first_name.' '.$mem_info->last_name.'
				</td>
				<td style="width: 50px;">
					'.$cur_user->week_required.'
				</td>';
			if($cur_user->status == "in")
			{
				//Set up our variables for redirect
				$URL = 'logStudyHourSession.php?uid='.$cur_user->member_id.'&action=out';
				echo '
				<td>
					<input type="button" name="'.$cur_user->member_id."\" value=\"Log Out\" class=\"studyHrButton\" onclick=\"javascript: window.location.href='$URL'\" />
				</td>
				<td>

				</td>
				";

			} else {
				//Set up our variables for redirect
				$URL = 'logStudyHourSession.php?user='.$cur_user->member_id.'&action=in';
				echo '
				<td>

				</td>
				<td>
					<input type="button" name="'.$cur_user->member_id."\" value=\"Log In\" class=\"studyHrButton\" onclick=\"javascript: window.location.href='$URL'\" />
				</td>
				";
			}

			echo "</tr>\n";
		} //end foreach($sh_users as $cur_user)
		echo '</table>
	</form>';

	// Give Director of Academic Affairs the ability to make an acception on hours

        //TODO: parker make sure this uses the new account type checker
	if(strpos($session->accountType, 'academics') /*|| strpos($session->accountType, 'admin') */) {

	?>
		<h2>Make Adjustment</h2>

		<?php if(isset($successMessage)) {
			echo "<p align=\"center\">$successMessage</p>";
		 } ?>

		<form name="manualAdjustment" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
		<table align="center">
			<tr>
				<th>Name:</th>
				<td>
                                        <select name="username">
						<option value="select">Select One</option>
                                                <?php
                                                        foreach($sh_users as $cur_user) {
                                                                $mem_info  = new Member($cur_user->member_id);
                                                                echo "<option value=\"$cur_user->member_id\">$mem_info->first_name $mem_info->last_name</option>";
                                                        }
                                                ?>
                                        </select>
                                </td>
			</tr>
			<tr>
				<th>Hour Adjustment:</th>
				<td>
					<input name="hours" type="text" size="3" />
				</td>
			</tr>
			<tr>
				<th>Date of studying:</th>
				<td>
					<input name="date" type="text" id="datepicker" size="11" />
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<input name="submit" type="submit" />
				</td>
		</table>
		</form>

	<?php } //end if(strpos($session->accountType, 'academics') ?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>