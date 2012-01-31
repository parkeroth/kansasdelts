<?php
session_start();
$authUsers = array('brother');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once '../php/login.php';	// TODO: Get rid of this

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$today = date("Y-m-d");
	$occured = strtotime($_POST[dateOccured]);
	$dateOccured = date("Y-m-d", $occured);
	
	$description = mysqli_real_escape_string($mysqli, $_POST[description]);
	
	$add_sql = "INSERT INTO writeUps (dateFiled, partyFiling, partyResponsible, urgency, dateOccured, description, status, category, verdict) 
				VALUES ('$today', '".$_SESSION[username]."', '".$_POST[partyResponsible]."', '".$_POST[urgency]."', '$dateOccured', '".$description."', 'review', '".$_POST[category]."', 'innocent')";
	
	$add_res = mysqli_query($mysqli, $add_sql);
	
	/*require 'class.phpmailer.php';
		
	try {
		$mail = new PHPMailer(true); //New instance, with exceptions enabled
	
		$mail->IsSMTP();                           			// tell the class to use SMTP
		$mail->SMTPAuth   = true;                  			// enable SMTP authentication
		$mail->Port       = 25;                    			// set the SMTP server port
		$mail->Host       = "smtp.gmail.com"; 				// SMTP server
		$mail->Username   = "dtdadmin@kansasdelts.org";     // SMTP server username
		$mail->Password   = "DTD1856GammaTau";           	// SMTP server password
	
		$mail->IsSendmail();  								// tell the class to use Sendmail
	
		$mail->From       = "noreply@kansasdelts.org";
		$mail->FromName   = "Delt Website";
		
		
		$toQuery = "SELECT username, type FROM notifications where accountType LIKE '%|".$position[$i]."%' LIMIT 1";
		$getTo = mysqli_query($mysqli, $toQuery);
		$toArray = mysqli_fetch_array($getTo, MYSQLI_ASSOC);
		
		$mail->AddAddress($to, 'Webmaster');
		
		
		
		$mail->Subject  = "New Honor Board Write Up";
	
		$mail->WordWrap   = 80; // set word wrap
	
		$mail->MsgHTML($body);
	
		$mail->IsHTML(true); // send as HTML
	
		$mail->Send();
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
	}*/
	
	header("location: ../account.php?from=writeUp");

} 

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");?>

<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<h2 align="center">Honor Board Write Up Form</h2>
    
    <form name="passwordChange" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    	<table border="0" cellpadding="4" align="center">
    		<tr>
    			<th>Party Responsible for Infraction: </th>
    			<td><select name="partyResponsible">
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
    			<th>Category: </th>
    			<td>
    				<select name="category">
    					<option value="select">Select One</option>
						
						<?php 
							$catQuery = "SELECT * FROM writeUpCategories ORDER BY ID";
							$getCat = mysqli_query($mysqli, $catQuery);
		
							while($catArray = mysqli_fetch_array($getCat, MYSQLI_ASSOC)){
								echo "<option value=\"$catArray[code]\">$catArray[title]</option>";
							}
							
						?>
						
    					</select>
    				</td>
    			</tr>
    		<tr>
    			<th>Matter of Urgency: </th>
    			<td>
    				<select name="urgency">
    					<option value="select">Select One</option>
    					<option value="Urgent">Urgent</option>
    					<option value="Not Urgent">Not Urgent</option>
    					</select>
    				</td>
    			</tr>
    		
    		<script type="text/javascript">
			$(function() {
				$("#datepicker").datepicker();
			});
		</script>
    		
    		<tr>
    			<th>Date of Complaint: </th>
    			<td><input name="dateOccured" type="text" id="datepicker" size="10" /></td>
    			</tr>
    		<tr>
    			<th>Description of Alleged Infraction: </th>
    			<td><textarea name="description" cols="40" rows="10"></textarea></td>
    			</tr>
    		<tr>
    			<td colspan="2"></td>
    			</tr>
    		</table>
    	<p>I do hereby affirm and declare on my oath as a Delt and as God is my witness that the statement above is the truth, the whole truth and nothing but the truth.  I understand that if Honor Board needs any clarification on the alleged infraction, they will confidentially speak with me before meeting with the party responsible.</p>
    	<p style="text-align:center">
    		<input type="submit" name="submit" id="submit" value="Submit" />
    		<input type="reset" name="Reset" id="Reset" value="Reset" />
    		</p>
    	
    </form>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>