<?php
session_start();
$authUsers = array('brother');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/Member.php';
include_once 'classes/Infraction_Log.php';

include_once('snippets.php');

/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$type = $_POST[type];
	$occured = strtotime($_POST[dateOccured]);
	$date_occured = date("Y-m-d", $occured);
	
	$infraction_log = new Infraction_Log();
	$infraction_log->offender_id = $_POST[offender];
	$infraction_log->reporter_id = $session->member_id;
	$infraction_log->type = $type;
	$infraction_log->date_occured = $date_occured;
	$infraction_log->description = $_POST[description];
	$infraction_log->insert();
	
	//header("location: ../success.php?page=MissedDuty");

} 

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");?>

<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

	<h2 align="center">Missed Duty Form</h2>
    
    <form name="missedDuty" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    	<table border="0" cellpadding="4" align="center">
    		<tr>
    			<th>Party Responsible for Infraction: </th>
    			<td><select name="offender">
    				<option value="select">Select One</option>
    				
    				<?php
		$member_manager = new Member_Manager();
		$all_members = $member_manager->get_all_members();
		foreach($all_members as $member){
			echo "<option value=\"$member->id\">$member->first_name $member->last_name</option>";
		}
				?>
    				
    				</select></td>
    			</tr>
			<tr>
    			<th>Infraction Type: </th>
    			<td>
    				<select name="type">
    					<option value="select">Select One</option>
						
				<?php 
		foreach(Infraction_Log::$INFRACTION_TYPES as $type => $title){
			echo "<option value=\"$type\">$title</option>";
		}
				?>
						
    					</select>
    				</td>
    			</tr>
				
    		<script type="text/javascript">
			$(function() {
				$("#datepicker").datepicker();
			});
			</script>
    		
    		<tr>
    			<th>Date of Infraction: </th>
    			<td><input name="dateOccured" type="text" id="datepicker" size="10" /></td>
    			</tr>
    		<tr>
    			<th>Additional Information: </th>
    			<td><textarea name="description" cols="40" rows="10"></textarea></td>
    			</tr>
    		<tr>
    			<td colspan="2"></td>
    			</tr>
    		</table>
		<p>&nbsp;</p>
    	<p style="text-align:center">
    		<input type="submit" name="submit" id="submit" value="Submit" />
    		<input type="reset" name="Reset" id="Reset" value="Reset" />
    		</p>
    	
    </form>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>