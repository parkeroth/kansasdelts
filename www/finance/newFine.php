<?php
session_start();
$authUsers = array('admin', 'pres', 'saa', 'treasurer');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/Member.php';
include_once 'classes/Fine.php';

/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$fine = new Fine();
	$fine->amount = $_POST[amount];
	$fine->member_id = $_POST[payer];
	$fine->description = $_POST[description];
	$fine->insert();
	
	//header("location: ../success.php?page=fine");

} 

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");?>

<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

	<h2 align="center">Fine Submission Form</h2>
    
	<p style="text-align: center">Please include the date of the infraction in the reason.</p>
	
    <form name="newFine" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    	<table border="0" cellpadding="4" align="center">
    		<tr>
    			<th>Payer: </th>
    			<td><select name="payer">
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
    			<th>Amount: </th>
    			<td>
    				<input type="text" name="amount" />
    			</tr>
    		<tr>
    			<th>Reason: </th>
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