<?php

session_start();
$authUsers = array('admin','saa','academics','treasurer');
include_once('php/authenticate.php');
include_once('php/login.php');

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

include_once('snippet/missedDuties.php');

/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$type = $_POST[type];
	$status = 'pending';
	
	$now = date("Y-m-d H:i:s");
	$occured = strtotime($_POST[dateOccured]);
	$dateOccured = date("Y-m-d", $occured);
	
	$add_sql = "INSERT INTO infractionLog (offender, reporter, type, dateReported, dateOccured, description, status) 
				VALUES ('$_POST[offender]', '$_SESSION[username]', '$type', '$now', '$dateOccured', '$_POST[description]', '$status')";			
				
	//$add_res = mysqli_query($mysqli, $add_sql);
	
	//header("location: ../account.php?from=writeUp");

} 

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");?>

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

	<h2 align="center">New Suspension Form</h2>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    	<table border="0" cellpadding="4" align="center">
    		<tr>
    			<th>Name: </th>
    			<td><select name="offender">
    				<option value="select">Select One</option>
    				
    				<?php
		
		$userData = "
			SELECT * 
			FROM members
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
		
			while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
			{
				echo "<option value=\"$userDataArray[username]\">$userDataArray[firstName] $userDataArray[lastName]</option>";
			}
		
		
		?>
    				
    				</select></td>
    			</tr>
			<tr>
    			<th>Type: </th>
    			<td>
    				<select name="type">
    					<option value="select">Select One</option>
						<option value="social">Social</option>
						<option value="academic">Academic</option>
						<option value="financial">Financial</option>
    				</select>
    				</td>
    			</tr>
    		<div id="socialOptions">
    		<tr>
    			<th>Date of Infraction: </th>
    			<td><input name="dateOccured" type="text" id="datepicker" size="10" /></td>
    			</tr>
			</div>
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