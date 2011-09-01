<?php

session_start();
	
/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	include_once('../../php/login.php');
	include_once('../util.php');
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$type = $_POST[type];
	
	/**
	 * Validate and clean Input
	 */
	 
	$validInput = true;
	$errors = array();
	
	if($_POST[firstName] == ""){
		$errors[] = "Please provide your first name.<br>";
		$validInput = false;
	} else {
		$firstName = mysql_real_escape_string($_POST[firstName]);
	}
	
	if($_POST[lastName] == ""){
		$errors[] = "Please provide your last name.<br>";
		$validInput = false;
	} else {
		$lastName = mysql_real_escape_string($_POST[lastName]);
	}
	
	if($_POST[type] == ""){
		$errors[] = "Please indicate if you are a high school or college student.<br>";
		$validInput = false;
	} else {
		$type = $_POST[type];
	}
	
	$questions = mysql_real_escape_string($_POST[questions]);
	
	if($type == "highSchool")
	{
		if($_POST['H-hsGPA'] == ""){
			$errors[] = "Please provide your high school GPA.<br>";
			$validInput = false;
		} else {
			$gpa = mysql_real_escape_string($_POST['H-hsGPA']);
		}
		
		if($_POST['H-gradYear'] == ""){
			$errors[] = "Please provide your high school class year.<br>";
			$validInput = false;
		} else {
			$gradYear = mysql_real_escape_string($_POST['H-gradYear']);
		}
		
		if($_POST['H-act'] == ""){
			$errors[] = "Please provide your ACT score.<br>";
			$validInput = false;
		} else {
			$act = mysql_real_escape_string($_POST['H-act']);
		}
		
		if($_POST['H-highSchool'] == ""){
			$errors[] = "Please indicate which high school you attended.<br>";
			$validInput = false;
		} else {
			$currentSchool = mysql_real_escape_string($_POST['H-highSchool']);
		}
		
		if($_POST['H-intendedMajor'] == ""){
			$errors[] = "Please provide your intended major.<br>";
			$validInput = false;
		} else {
			$major = mysql_real_escape_string($_POST['H-intendedMajor']);
		}
	}
	else if($type == "college")
	{
		if($_POST['C-highSchool'] == ""){
			$errors[] = "Please indicate which high school you attended.<br>";
			$validInput = false;
		} else {
			$highSchool = mysql_real_escape_string($_POST['C-highSchool']);
			$bio .= 'High School: '.$highSchool.'\n';
		}
		
		if($_POST['C-hsGradYear'] == ""){
			$errors[] = "Please indicate which year you graduated high school.<br>";
			$validInput = false;
		} else {
			$gradYear = mysql_real_escape_string($_POST['C-hsGradYear']);
		}
		
		if($_POST['C-currentSchool'] == ""){
			$errors[] = "Please inidicate where you currently go to school.<br>";
			$validInput = false;
		} else {
			$currentSchool = mysql_real_escape_string($_POST['C-currentSchool']);
		}
		
		if($_POST['C-gpa'] == "Please provide your college GPA"){
			$errors[] = "<br>";
			$validInput = false;
		} else {
			$gpa = mysql_real_escape_string($_POST['C-gpa']);
		}
		
		if($_POST['C-major'] == "Plesae provide your current major."){
			$errors[] = "<br>";
			$validInput = false;
		} else {
			$major = mysql_real_escape_string($_POST['C-major']);
		}
		
		$act = 0;
	}
	
	
	
	if($_POST[email] == ""){
		$errors[] = "Please provide your email address.<br>";
		$validInput = false;
	} else {
		$email = mysql_real_escape_string($_POST[email]);
	}
		
	if($_POST[phone] == ""){
		$errors[] = "Please provide your phone number.<br>";
		$validInput = false;
	} else {
		$phone = strip_phone(mysql_real_escape_string($_POST[phone]));
	}
	
	if($_POST[address] == ""){
		$errors[] = "Please provide your address.<br>";
		$validInput = false;
	} else {
		$address = mysql_real_escape_string($_POST[address]);
	}
	
	if($_POST[city] == ""){
		$errors[] = "Please provide your current city.<br>";
		$validInput = false;
	} else {
		$city = mysql_real_escape_string($_POST[city]);
	}
	
	if($_POST[state] == "select"){
		$errors[] = "Please provide your current state.<br>";
		$validInput = false;
	} else {
		$state = mysql_real_escape_string($_POST[state]);
	}
	
	if($_POST[ZIP] == ""){
		$errors[] = "Please provide your current ZIP code.<br>";
		$validInput = false;
	} else {
		$zip = mysql_real_escape_string($_POST[ZIP]);
	}
	
	
	if(file_get_contents("http://www.opencaptcha.com/validate.php?ans=".$_POST['code']."&img=".$_POST['img']) != 'pass') {
		$errors[] = "Please enter the correct security code.<br>";
		$validInput = false;
	}
	
	if($validInput) {
		
		if($type == "highSchool"){
			$typeText = "High School Student";
		} else if($type == "college"){
			$typeText = "College Student";
		}
		
		include('/php/mailTo.php');
		
		$to = '';
		$toQuery = "SELECT email FROM members WHERE accountType LIKE '%recruitment%'";
		$toResult = mysqli_query($mysqli, $toQuery);
		
		while($toData = mysqli_fetch_array($toResult, MYSQLI_ASSOC)) {
			
			$to .= $toData[email].', ';
		}
		
		//mailTo($to, $_POST[alumniEmail], 'New recruit added to website.', "Online Rush Form");
		
		$now = date( 'Y-m-d H:i:s');
			
		$query = "INSERT INTO recruits 
			(firstName, lastName, currentSchool, hsGradYear,
			status, phoneNumber, email, questions,
			address, city, state, zip, 
			gpa, actScore, intendedMajor, referredBy, dateAdded)
			VALUES ('$firstName', '$lastName', '$currentSchool', '$gradYear',
					'7', '$phone', '$email', '$questions',
					'$address', '$city', '$state', '$zip',
					'$gpa', '$act', '$major', 'self', '$now')";
		
		$insertRecord = mysqli_query($mysqli, $query);
		
		header("location: /success.php?page=rushSelf");
		
	}
}

/**
 * Form Section
 */
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Interested in Delt?</h1>
      <p>Please fill out this form as completely as possible and a recruitment member will contact you.</p>
	  <div class="errorBlock">
	<?php 
	
		if(!$validInput && $_SERVER['REQUEST_METHOD'] == "POST"){
			foreach($errors as $value){
				echo $value;
			}
		}
	
	?>
	</div>
      <form name="form" method="post" action="<?php echo $_SERVER['../../PHP_SELF']; ?>" id="form"> 
      	<h2>General Information</h2> 
      	<table cellspacing="2" cellpadding="2" width="100%" border="0"> 
      		<tbody> 
      			<tr> 
      				<th valign="middle" align="left" width="215"><span id="lblFirstName" class="ph_text">First Name:</span></th> 
      				<td width="465" align="left" valign="middle"><input name="firstName" type="text" maxlength="50" size="40" value="<?php echo $_POST[firstName] ;?>" /></td> 
      				</tr> 
      			<tr> 
      				
      				<th valign="middle" align="left" width="215"><span id="lblLastName" class="ph_text">Last Name:</span></th> 
      				<td valign="middle" align="left"><input name="lastName" type="text" maxlength="50" size="40"  value="<?php echo $_POST[lastName] ;?>" /></td> 
      				</tr> 
      			<tr> 
      				<td valign="top" align="left" colspan="2"><h3>&nbsp;</h3>
      					<h2>Schooling Information</h2></td> 
      				</tr> 
      			
      			<tr> 
      				<th valign="middle" align="left">Type of Student:</th> 
      				<td>
      					<label>
      						<input type="radio" name="type" value="highSchool" <?php if($_POST[type] == "highSchool"){ echo "checked=\"checked\""; } ?> />
      						High School Student</label><br />
      					<label>
      						<input type="radio" name="type" value="college" <?php if($_POST[type] == "college"){ echo "checked=\"checked\""; } ?> />
      						College Student</label></td> 
      				</tr> 
      			<tr> 
      				<td valign="top" align="left" colspan="2"><h3>If High School Student</h3></td> 
      				</tr>
      			<tr> 
      				<th valign="middle" align="left"><span id="lblRefChapter" class="ph_text">GPA (unweighted):</span></th> 
      				<td><input name="H-hsGPA" type="text" size="5" value="<?php echo $_POST['H-hsGPA'] ;?>" /></td> 
      				</tr> 
      			<tr> 
      				<th valign="middle" align="left"><span id="lblRefGradYear" class="ph_text">ACT Score:</span></th> 
      				<td><input name="H-act" type="text" size="5" value="<?php echo $_POST['H-act'] ;?>" /></td> 
      				</tr> 
      			<tr> 
      				<th valign="middle" align="left"><span id="lblRefLastName" class="ph_text">High School:</span></th> 
      				<td><input name="H-highSchool" type="text" size="40" value="<?php echo $_POST['H-highSchool'] ;?>"  /></td> 
      				</tr>
				<tr> 
      				<th valign="middle" align="left"><span id="lblRefLastName" class="ph_text">HS Class Year:</span></th> 
      				<td><input name="H-gradYear" type="text" size="40" value="<?php echo $_POST['H-gradYear'] ;?>"  /></td> 
      				</tr>
      			<tr> 
      				<th valign="middle" align="left"><span id="lblRefGradYear" class="ph_text">Intended Major:</span></th> 
      				<td><input name="H-intendedMajor" type="text" size="40" value="<?php echo $_POST['H-intendedMajor'] ;?>"  /></td> 
      			</tr>
      			<tr> 
      				<td valign="top" align="left" colspan="2"><h3>If College Student</h3></td> 
      			</tr>
				<tr> 
      				<th valign="middle" align="left"><span id="lblRefPhone" class="ph_text">High School Attended:</span></th> 
      				<td><input name="C-highSchool" type="text" size="40" value="<?php echo $_POST['C-highSchool'] ;?>"  /></td> 
      			</tr>
				<tr> 
      				<th valign="middle" align="left"><span id="lblRefPhone" class="ph_text">HS Graduation Year:</span></th> 
      				<td><input name="C-hsGradYear" type="text" size="40" value="<?php echo $_POST['C-hsGradYear'] ;?>" /></td> 
      			</tr>
				<tr> 
      				<th valign="middle" align="left"><span class="ph_text">Current University:</span></th> 
      				<td><input name="C-currentSchool" type="text" size="40" value="<?php echo $_POST['C-currentSchool'] ;?>" /></td> 
      				</tr>
      			<tr> 
      				<th valign="middle" align="left"><span id="lblRefPhone" class="ph_text">College GPA:</span></th> 
      				<td><input name="C-gpa" type="text" size="5" value="<?php echo $_POST['C-gpa'] ;?>" /></td> 
      			</tr>
      			<tr> 
      				<th valign="middle" align="left"><span id="lblRefEmail" class="ph_text">Current Major:</span></th> 
      				<td><input name="C-major" type="text" size="40" value="<?php echo $_POST['C-major'] ;?>" /></td> 
      				</tr>
      			<tr> 
      				<td valign="top" align="left" colspan="2"><h3>&nbsp;</h3>
      					<h2>Contact Information</h2></td> 
      				</tr> 
      			<tr> 
      				<th valign="middle" align="left" width="215"><span id="lblFirstName" class="ph_text">Email:</span></th> 
      				<td width="465" align="left" valign="middle"><input name="email" type="text" maxlength="50" size="40" value="<?php echo $_POST[email] ;?>" /></th> 
      					</tr> 
      			<tr>
      				<th valign="middle" align="left" width="215"><span id="lblLastName" class="ph_text">Phone:</span></th> 
      				<td valign="middle" align="left"><input name="phone" type="text" maxlength="50" size="40" value="<?php echo $_POST[phone] ;?>" /></th> 
      					</tr>
      			<tr>
      				<th valign="middle" align="left" width="215"><br /></th> 
      				<td valign="middle" align="left"><br /></th> 
      					</tr> 
      			<tr>
      				<th valign="middle" align="left" width="215"><span id="lblLastName" class="ph_text">Address:</span></th> 
      				<td valign="middle" align="left"><input name="address" type="text" maxlength="50" size="40" value="<?php echo $_POST[address] ;?>" /></th> 
      					</tr>
      			<tr>
      				<th valign="middle" align="left" width="215"><span id="lblLastName" class="ph_text">City:</span></th> 
      				<td valign="middle" align="left"><input name="city" type="text" maxlength="50" size="40" value="<?php echo $_POST[city] ;?>" /></th> 
      					</tr>
      			<tr>
      				<th valign="middle" align="left" width="215"><span id="lblLastName" class="ph_text">State:</span></th> 
      				<td valign="middle" align="left"><select name="state">
      					<option value="select">Select One</option>
      					<option value="AK">Alaska</option>
      					<option value="AL">Alabama</option>
      					<option value="AR">Arkansas</option>
      					<option value="AZ">Arizona</option>
      					<option value="CA">California</option>
      					<option value="CO">Colorado</option>
      					<option value="CT">Connecticut</option>
      					<option value="DC">District of Columbia</option>
      					<option value="DE">Delaware</option>
      					<option value="FL">Florida</option>
      					<option value="GA">Georgia</option>
      					<option value="HI">Hawaii</option>
      					<option value="IA">Iowa</option>
      					<option value="ID">Idaho</option>
      					<option value="IL">Illinois</option>
      					<option value="IN">Indiana</option>
      					<option value="KS">Kansas</option>
      					<option value="KY">Kentucky</option>
      					<option value="LA">Louisiana</option>
      					<option value="MA">Massachusetts</option>
      					<option value="MD">Maryland</option>
      					<option value="ME">Maine</option>
      					<option value="MI">Michigan</option>
      					<option value="MN">Minnesota</option>
      					<option value="MO">Missouri</option>
      					<option value="MS">Mississippi</option>
      					<option value="MT">Montana</option>
      					<option value="NC">North Carolina</option>
      					<option value="ND">North Dakota</option>
      					<option value="NE">Nebraska</option>
      					<option value="NH">New Hampshire</option>
      					<option value="NJ">New Jersey</option>
      					<option value="NM">New Mexico</option>
      					<option value="NV">Nevada</option>
      					<option value="NY">New York</option>
      					<option value="OH">Ohio</option>
      					<option value="OK">Oklahoma</option>
      					<option value="OR">Oregon</option>
      					<option value="PA">Pennsylvania</option>
      					<option value="PR">Puerto Rico</option>
      					<option value="RI">Rhode Island</option>
      					<option value="SC">South Carolina</option>
      					<option value="SD">South Dakota</option>
      					<option value="TN">Tennessee</option>
      					<option value="TX">Texas</option>
      					<option value="UT">Utah</option>
      					<option value="VA">Virginia</option>
      					<option value="VT">Vermont</option>
      					<option value="WA">Washington</option>
      					<option value="WI">Wisconsin</option>
      					<option value="WV">West Virginia</option>
      					<option value="WY">Wyoming</option>
      					</select></th> 
      					</tr>
      			<tr>
      				<th valign="middle" align="left" width="215"><span id="lblLastName" class="ph_text">ZIP:</span></th> 
      				<td valign="middle" align="left"><input name="ZIP" type="text" maxlength="50" size="40" value="<?php echo $_POST[ZIP] ;?>" /></th> 
      					</tr>
      			<tr>
      				<th valign="middle" align="left" width="215"><br /></th> 
      				<td valign="middle" align="left"><br /></th> 
      					</tr> 
      			<tr> 
      				<td valign="top" align="left" colspan="2">
      					<p>Questions and extra information (optional):</p></td> 
      				</tr> 
      			<tr> 
      				<td valign="top" align="left" colspan="2"><textarea name="questions" cols="69" rows="10"><?php echo $_POST[questions] ;?></textarea></td> 
      				</tr>
				<tr>
      				<td style="text-align:right">Please type the characters you see to the left.</td>
      				<td style="text-align:center;">
      					<p>&nbsp;</p>
      					<?
					$date = date("Ymd");
					$rand = rand(0,9999999999999);
					$height = "40";
					$width  = "110";
					$img    = "$date$rand-$height-$width.jpgx";
					echo "<input type='hidden' name='img' value='$img'>";
					echo "<a href='http://www.opencaptcha.com'><img src='http://www.opencaptcha.com/img/$img' height='$height' alt='captcha' width='$width' border='0' /></a><br />";
					echo "<input type=text name=code value='' size='16' />";
						?>
      					<p>&nbsp;</p>
      					</td>
      				</tr> 
      			<tr> 
      			<tr> 
      				<td style="width: 100px" valign="middle" align="left"></td> 
      				<td valign="middle" align="left"> 
      					<input type="submit" name="btnSubmit" value="Submit" /> 
      					<input type="submit" name="btnClear" value="Reset" /></td> 
      				</tr> 
      			</tbody> 
      		</table> 
      	</form>
		
      <div align="center">
      	<h3><a href="../../unique.php">Why Delt? | </a><a href="../../heritage.php">Our Heritage | </a><a href="../../parents.php">Info for Parents | </a><a href="../../values.php">Our Values</a> | <a href="submitSelf.php">Rush Form </a></h3>
      	</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>