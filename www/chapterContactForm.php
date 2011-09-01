<?php 
	session_start();
	$authUsers = array('admin', 'secretary');
	include_once('php/authenticate.php');
	include_once('php/login.php');

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	require 'php/class.phpmailer.php';
	
	try {
		$mail = new PHPMailer(true); //New instance, with exceptions enabled
	
		$mail->IsSMTP();                           			// tell the class to use SMTP
		$mail->SMTPAuth   = true;                  			// enable SMTP authentication
		$mail->Port       = 25;                    			// set the SMTP server port
		$mail->Host       = "smtp.gmail.com"; 				// SMTP server
		$mail->Username   = "dtdadmin@kansasdelts.org";     // SMTP server username
		$mail->Password   = "DTD1856GammaTau";           	// SMTP server password
	
		$mail->IsSendmail();  								// tell the class to use Sendmail
	
		$mail->From       = "secretary@kansasdelts.org";
		
		if($_POST[residency] == "in")
		{
			$whereQuery = "residency = 'in' ";
		}
		else if($_POST[residency] == "out")
		{
			$whereQuery = "residency = 'out' ";
		}
		else if($_POST[residency] == "both")
		{
			$whereQuery = "(residency = 'in' OR residency = 'out') ";
		}
		
		if($_POST[group] != "all")
		{
			$whereQuery .= " AND (";
			
			$classQuery = "
				SELECT DISTINCT class 
				FROM members 
				ORDER BY ID";
			$getClasses = mysqli_query($mysqli, $classQuery);
			
			$first = true;
			
			while($classArray = mysqli_fetch_array($getClasses, MYSQLI_ASSOC)) {
				if($_POST[$classArray['class']] && $first)
				{
					$whereQuery .= "class = '".$classArray['class']."' ";
					$first = false;
					
				} else if($_POST[$classArray['class']]) {
					
					$whereQuery .= "OR class = '".$classArray['class']."' ";
					
				}
			}
			
			$whereQuery .= " )";
		}
		
		function remove_non_numeric($string) {
			return preg_replace('/\D/', '', $string);
		}
		
		$toQuery = "SELECT firstName, lastName, phone, carrier, email 
					FROM members
					WHERE $whereQuery
					ORDER BY ID";
		
		$getUserData = mysqli_query($mysqli, $toQuery);
	
		while($userData = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
		{
			if($userData[carrier] == "" || $userData[carrier] == "none")
			{
				//$mail->AddAddress($userData[email], $userData[firstName]." ".$userData[lastName]);
			}
			else
			{
				$address = remove_non_numeric($userData[phone]);
				if(strlen($address) > 10){
					$address = substr($address, strlen($address) - 10, 10);
				}
				
				if($userData[carrier] == "verizon"){
					$address .= "@vtext.com";
				} else if($userData[carrier] == "sprint"){
					$address .= "@messaging.sprintpcs.com";
				} else if($userData[carrier] == "tmobile"){
					$address .= "@tmomail.net";
				} else if($userData[carrier] == "att"){
					$address .= "@txt.att.net";
				}
				
				$mail->AddAddress($address, $userData[firstName]." ".$userData[lastName]);
			}
			
			//echo $address."<br>";
			
		}
		
		$mail->Subject  = $subject;
	
		$mail->WordWrap   = 80; // set word wrap
	
		$mail->MsgHTML($_POST[textMessage]);
	
		$mail->IsHTML(true); // send as HTML
	
		$mail->Send();
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
	}
	
	header("location: success.php?page=TextMessage");
	
}
	


 
/**
 * Form Section
 */	

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>
	
<script language="javascript" src="js/jquery-1.4.2.min.js"></script>
<script language="javascript">
	function limitChars(textid, limit, infodiv)
	{
		var text = $('#'+textid).val(); 
		var textlength = text.length;
		if(textlength > limit)
		{
			$('#' + infodiv).html('You cannot write more then '+limit+' characters!');
			$('#'+textid).val(text.substr(0,limit));
			return false;
		}
		else
		{
			$('#' + infodiv).html('You have '+ (limit - textlength) +' characters left.');
			return true;
		}
	}

$(function(){
	$('#comment').keyup(function(){
		limitChars('comment', 160, 'charlimitinfo');
	})
});
</script>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div style="text-align:center">
	<h1>Contact Chapter</h1>
	<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		
		<div style="position:relative; top: 0px; left: 60px; width: 540px;">
			
			<h3 style="text-align:left;">Select Recipients: </h3>
			
			<p>Select which group you would like to contact.</p>
			
			<p>
				<label>
					<input type="radio" name="residency" value="in" id="residency_0" />
					In House</label>
				
				<label>
					<input type="radio" name="residency" value="out" id="residency_1" />
					Out of House</label>
				
				<label>
					<input checked="checked" type="radio" name="residency" value="both" id="residency_2" />
					Both</label>
				
			</p>
			<p>Select which plege class(s) you would like to send the message.</p>
			
			<table align="center">
				<tr>
					<td>All</td>
					<td><input name="group" type="checkbox" value="all" /></td>
					</tr>
				<?PHP
					$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
					
					$classQuery = "
						SELECT DISTINCT class 
						FROM members 
						ORDER BY ID";
					$getClasses = mysqli_query($mysqli, $classQuery);
					while($classArray = mysqli_fetch_array($getClasses, MYSQLI_ASSOC)) {
						echo "<tr>";
						echo "<td>".ucwords($classArray['class'])." </td>";
						echo "<td><input name=\"$classArray[class]\" type=\"checkbox\" value=\"true\" /></td>\n";
						echo "</tr>";
					}
				?>
				</table>
			
			<h3 style="text-align:left;">Message: </h3>
			
			<p>Please type your message below. The text message is limited to 160 characters and will go to those who wish to receive text messages.<br /> <b>NOTE:</b> People not signed up for texting will receive an email.</p>
			
			<table align="center">
				<tr><td>Text Message</td><td><textarea name="textMessage" id="comment" onkeyup="limitChars(this, 160, 'charlimitinfo')" cols="40" rows="4"></textarea></td></tr>
				<tr><td></td><td><div id="charlimitinfo"></div></td></tr>
				</table>
			<p>&nbsp;</p>
			<table align="center">
				<tr><td><input name="Reset" type="reset" /></td><td><input name="Send" type="submit" /></td></tr>
				</table>
			
			</div>
		</form>
</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>