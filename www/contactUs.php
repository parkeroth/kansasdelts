<?php
	session_start();
	
	/**
	 * Processing Section
	 */
	 
	if($_SERVER['REQUEST_METHOD'] == "POST") {
		
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		/**
		 * Validate Input
		 */
		 
		$validInput = true;
		$errors = array();
		
		if(!file_get_contents("http://www.opencaptcha.com/validate.php?ans=".$_POST['code']."&img=".$_POST['img']) == 'pass') {
			$errors[] = "Please enter the correct security code.";
			$validInput = false;
		}
	
		if($validInput) {
	
			$userData = "
				SELECT email 
				FROM members
				WHERE accountType LIKE '%webmaster%'
				LIMIT 1";
			$getUserData = mysqli_query($mysqli, $userData);
			$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
			
			$to = $userDataArray[email];
			
			$from = $_POST[email];
					
			$body =  "<html><head>";
			$body .= "<style> tr {text-align: right;} td {text-align: left;} </style>";
			$body .= "</head><body>";
			$body .= "<table>";
			$body .= "<tr><th>Name:</th><td>$_POST[firstName] $_POST[lastName]</td></tr>";
			$body .= "<tr><th>Email Address:</th><td>$_POST[email]</td></tr>";
			$body .= "<tr><th>Phone Number:</th><td>$_POST[phone]</td></tr>";
			$body .= "<tr><th>Preferred Method of Contact:</th><td>$_POST[contact]</td></tr>";
			$body .= "<tr><th>Question/Comment:</th><td>$_POST[message]</td></tr>";
			$body .= "</table></body></html>";
			
			include('php/mailTo.php');
	
			mailTo($to, $from, $body, "Contact Us Form: kansasdelts.org");	
			
			header("location: success.php?page=ContactUs");
		}
	}
	
	/**
	 * Form Section	
	 */
	 
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<style>
	.form {
		padding: 20px;
		width:400px;
		background-color: #666666;
		position:relative;
		left: 100px;
		box-shadow: 5px 5px 5px #000;
		-webkit-box-shadow: 5px 5px 5px #232323;
		-moz-box-shadow: 5px 5px 5px #000;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Contact Us</h1>
	 <div style="position: relative; width: 400px;";>
	 <div class="errorBlock">
	<?php 
	
		if(!$validInput && $_SERVER['REQUEST_METHOD'] == "POST"){
			foreach($errors as $value){
				echo $value;
			}
		}
	
	?>
	</div>
	 	<p>&nbsp;</p>
	 	<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="form"> 
	 		<table cellspacing="2" cellpadding="2" border="0"> 
	 			<tbody> 
	 				<tr> 
	 					<th valign="middle" align="left"><span id="lblFirstName" class="ph_text">First Name:</span></th> 
	 					<td align="left" valign="middle"><input name="firstName" type="text" maxlength="50" size="40" value="<?php echo $_POST[firstName] ;?>" /></td> 
	 					</tr> 
	 				<tr> 
	 					
	 					<th valign="middle" align="left"><span id="lblLastName" class="ph_text">Last Name:</span></th> 
	 					<td valign="middle" align="left"><input name="lastName" type="text" maxlength="50" size="40" value="<?php echo $_POST[lastName] ;?>" /></td> 
	 					</tr> 
	 				<tr> 
	 					<th valign="middle" align="left"><span id="lblAddress" class="ph_text">Email Address:</span></th> 
	 					<td valign="middle" align="left"><input name="email" type="text" maxlength="50" size="40" value="<?php echo $_POST[email] ;?>" /></td> 
	 					</tr> 
	 				<tr> 
	 					
	 					<th valign="middle" align="left"><span id="lblCity" class="ph_text">Phone Number</span></th> 
	 					<td valign="middle" align="left"><input name="phone" type="text" maxlength="20" size="20" value="<?php echo $_POST[phone] ;?>" /></td> 
	 					</tr>
	 				<tr> 
	 					<th valign="middle" align="left"><span id="lblAddress" class="ph_text">Preferred Method <br />
	 						of Contact:</span></th> 
	 					<td valign="middle" align="left"><p>
	 						<label>
	 							<input type="radio" name="contact" value="phone" />
	 							Phone</label>
	 						
	 						<label>
	 							<input type="radio" name="contact" value="email" />
	 							Email</label>
	 						
	 						<label>
	 							<input type="radio" name="contact" value="neither" />
	 							Neither</label>
	 						
	 						</p></td> 
	 					</tr>  
	 				<tr>
	 					<td>&nbsp;</td><td>&nbsp;</td>
	 					</tr>
	 				<tr> 
	 					<th valign="top" align="left">Question/Comment:</th> 
	 					<td valign="middle" align="left"><textarea name="message" rows="8" cols="30" id="txtComments"><?php echo $_POST[message]; ?></textarea></td> 
	 					</tr> 
	 				<tr>
	 					<td style="text-align:right">Please type the characters you see to the left.</td>
	 					<td>
	 						<p>&nbsp;</p>
	 						<?
								$date = date("Ymd");
								$rand = rand(0,9999999999999);
								$height = "40";
								$width  = "110";
								$img    = "$date$rand-$height-$width.jpgx";
								echo "<input type='hidden' name='img' value='$img'>";
								echo "<a href='http://www.opencaptcha.com'><img src='http://www.opencaptcha.com/img/$img' height='$height' alt='captcha' width='$width' border='0' /></a><br />";
								echo "<input type=text name=code value='' size='35' />";
							?>
	 						<p>&nbsp;</p>
	 						</td>
	 					</tr>
	 				<tr> 
	 					<td style="width: 100px" valign="middle" align="left"></td> 
	 					<td valign="middle" align="left"> 
	 						<input type="submit" name="btnSubmit" value="Submit" id="btnSubmit" /> 
	 						<input type="submit" name="btnClear" value="Reset" id="btnClear" />								</td> 
	 					</tr> 
	 				</tbody> 
	 			</table>
	 		</form>
 	</div>
	  <div style="position:absolute; top: 60px; left: 450px; text-align:center">
	  	<h3>Delta Tau Delta</h3>
	  	<p>1111 W 11th St<br />
	  		Lawrence, KS 66044-2903<br />
	  		(785) 843-6866</p>
	  	<p>&nbsp;</p>
	  	<img style="float:right;" src="img/mapdata.gif" />
  	</div>
      <p>&nbsp;</p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>