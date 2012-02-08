<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'academics');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/studyHours.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script type="text/javascript" src="/js/simpleCalendarWidget.js"></script>

<script language="jscript" type="text/javascript">
function Confirm()
{
return confirm ("Are you sure you want want to make these changes?");
}
</script>
<script language="jscript" type="text/javascript">
<!--
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
//   -->
</script>

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
	.submit {
		margin-left: auto;
		margin-right: auto;
		text-align: center;
		}
	.dataError {
		border-style: solid;
		border-width: 2px;
		background-color: red;
		font-size: 16px;
		text-align: center;
		text-transform: uppercase;
		}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>


<h1>Add User to Study Hours</h1>

		<p>Put a check next to any member who has required study hours.<br />
        In the textbox, place the required number of hours per week.<br />
        Use the datepicker to pick the time period for the required study hours.</p>
		<p>&nbsp;</p>

                <form enctype="multipart/form-data" id="shUsers" name="shUsers" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return Confirm();">
		<table border="1">
                    <tr>
			<th style="width: 100px;">
				Member Name
			</th>
			<th style="width: 35px;">
				Required?
			</th>
			<th style="width: 30px;">
				Blocks/Week
			</th>
			<th style="width: 120px;">
				Start Date
			</th>
			<th style="width: 120px;">
				Stop Date
			</th>
                    </tr>
                <?php
                    $memManager = new Member_Manager();
                    $memList = $memManager->get_all_members();

                    $sh_manager = new Study_Hour_Manager();

                    foreach($memList as $member) {
			echo "<tr>
					<td>
						<label>$member->first_name $member->last_name</label>
					</td>\n";
			if ($sh_manager->is_in_table($member->id))
			{
				//User DOES have required hours, so study hour stuff should be set up
                                $userSHData = $sh_manager->get_user_sh_requirements($member->id);
				echo '
					<td>
						<input type="checkbox" name="'.$member->id.'" id="'.$member->id.'" value="Y" checked="checked" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_week" id="'.$member->id.'_week" value="'.$userSHData->week_required.'" size="5" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_start" id="datepicker" value="'.$userSHData->start_date.'" size="10" readonly="readonly" /> <input type="button" value="    " onclick="scwShow('.$member->id.'_start,event);" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_stop" id="datepicker" value="'.$userSHData->stop_date.'" size="10" readonly="readonly" /> <input type="button" value="    " onclick=scwShow('.$member->id.'_stop,event);" />
					</td>
					';
			} else {
				echo '
					<td>
						<input type="checkbox" name="'.$member->id.'" id="'.$member->id.'" value="Y" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_week" id="'.$member->id.'_week" value=""size="5" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_start" id="datepicker" size="10" readonly="readonly" /> <input type="button" value="    " onclick="scwShow('.$member->id.'_start,event);" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_stop" id="datepicker" size="10" readonly="readonly" /> <input type="button" value="    " onclick="scwShow('.$member->id.'_stop,event);" />
					</td>
					';
			}
			echo '</tr>';
                    }
                ?>
		</table>
		<p>&nbsp;</p>
		<div style="text-align: center;"><input type="submit" name="submit" id="submit" value="  Submit  " /></div>
	</form>

        <?php
       //Now process the shit that needs updating
	if(isset($_POST['submit']))
        {
            foreach($memList as $member) {
                if(isset($_POST[$member->id]))
                {
                    //user has required hours
                    //check to make sure other fields are okay
                    if(!isset($_POST[$member->id.'_week']) || !isset($_POST[$member->id.'_start']) || !isset($_POST[$member->id.'_stop']))
                    {
                        //required variables not set, echo message to user
			$dataErrorMsg = '<p class="dataError">Error: study hour data for '.$member->first_name.' '.$member->last_name.' is incompletely filled out</p>';
			echo $dataErrorMsg;
                    } else {
                        //things check out, data all filled
			//did user have study hours before? this will determine if we run
			//an update or an insert
			if ($sh_manager->is_in_table($member->id))
			{
                            //user did have study hours, so an update is needed
                            $userSHData = $sh_manager->get_user_sh_requirements($member->id);         //grab sh info
                            $userSHData->start_date = $_POST[$member->id.'_start'];
                            $userSHData->stop_date = $_POST[$member->id.'_stop'];
                            $userSHData->week_required = $_POST[$member->id.'_week'];
                            $userSHData->save();
			} else {
                            //user had no study hours, so an insertion is needed
                            $sh_manager->add_sh_user($member->id, $_POST[$member->id.'_week'], $_POST[$member->id.'_start'], $_POST[$member->id.'_stop']);
                        }
                    }
		} else {
                    //user doesn't have required hours
                    //so other fields don't matter.

                    //member id is NOT set, so they're NOT checked
                    //we need to see if we need to delete the user from studyHourRequirements
                    //or if the user already isn't in there, in which case we don't need to
                    //do anything
                    if ($sh_manager->is_in_table($member->id))
                    {
                        //user used to have required hours, so we need to delete them
			//from the studyHourRequirements table
			$user_requirements = $sh_manager->get_user_sh_requirements($member->id);
                        $user_requirements->remove_sh_user();
                    } //end if there ARE hoursRequired
                    //no need for an else statement, cause we don't need to do anything if the user isn't in there
                }
            }   //end foreach
        } //end if(isset
        ?>

	//boom goes the dynamite
?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>