<?php

session_start();

/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {

	include_once('../util.php');
	include_once('../../php/login.php');
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

	$gpa = mysql_real_escape_string($_POST[gpa]);
	$act = mysql_real_escape_string($_POST[act]);
	$currentSchool = mysql_real_escape_string($_POST[currentSchool]);
	$gradYear = mysql_real_escape_string($_POST[gradYear]);
	$major = mysql_real_escape_string($_POST[major]);

	$email = mysql_real_escape_string($_POST[email]);
	$phone = strip_phone(mysql_real_escape_string($_POST[phone]));
	$address = mysql_real_escape_string($_POST[address]);
	$city = mysql_real_escape_string($_POST[city]);
	$state = mysql_real_escape_string($_POST[state]);
	$zip = mysql_real_escape_string($_POST[ZIP]);

	$bio = mysql_real_escape_string($_POST[bio]);

    $a = 'pass';
    $b = file_get_contents("http://www.opencaptcha.com/validate.php?ans=".$_POST['code']."&img=".$_POST['img']);

    echo "http://www.opencaptcha.com/validate.php?ans=".$_POST['code']."&img=".$_POST['img'];
    echo $a.'<br>';
    echo $b.'<br>';
    echo strcmp($a, $b);

	if(strcmp(trim(file_get_contents("http://www.opencaptcha.com/validate.php?ans=".$_POST['code']."&img=".$_POST['img'])),"pass")) {
        $errors[] = "Please enter the correct security code.<br>";
		$validInput = false;
	}

	if($validInput) {
		$now = date( 'Y-m-d H:i:s');

		include_once('../classes/Recruit.php');

		$recruit = new Recruit($mysqli);
		$recruit->firstName = $firstName;
		$recruit->lastName = $lastName;
		$recruit->currentSchool = $currentSchool;
		$recruit->hsGradYear = $gradYear;
		$recruit->status = 7;
		$recruit->bio = $bio;
		$recruit->phoneNumber = $phone;
		$recruit->email = $email;
		$recruit->address = $address;
		$recruit->city = $city;
		$recruit->state = $state;
		$recruit->zip = $zip;
		$recruit->gpa = $gpa;
		$recruit->actScore = $act;
		$recruit->intendedMajor = $major;
		$recruit->referredBy = $_SESSION[username];
		$recruit->dateAdded = $now;
		$recruit->insert();

		header("location: /success.php?page=rushMember");

	}
}

/**
 * Form Section
 */
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Refer a Man</h1>
      <p>Thank you for taking the time to refer a man to our chapter. Please fill out the form below to the best of your ability. If you do not know or do not wish to disclose any of the details below simply leave the field blank.</p>
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
      	<h2>Recruits Information</h2>
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
      				<th valign="middle" align="left">GPA (unweighted):</th>
      				<td><input name="gpa" type="text" size="5" value="<?php echo $_POST['gpa'] ;?>" /></td>
      				</tr>
      			<tr>
      				<th valign="middle" align="left">ACT Score:</th>
      				<td><input name="act" type="text" size="5" value="<?php echo $_POST['act'] ;?>" /></td>
      				</tr>
      			<tr>
      				<th valign="middle" align="left">Current School:</th>
      				<td><input name="currentSchool" type="text" size="40" value="<?php echo $_POST['currentSchool'] ;?>"  /></td>
      				</tr>
				<tr>
      				<th valign="middle" align="left">HS Class Year:</th>
      				<td><input name="gradYear" type="text" size="40" value="<?php echo $_POST['gradYear'] ;?>"  /></td>
      				</tr>
      			<tr>
      				<th valign="middle" align="left">Major:</th>
      				<td><input name="major" type="text" size="40" value="<?php echo $_POST['major'] ;?>"  /></td>
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
      					</select></td>
      					</tr>
      			<tr>
      				<th valign="middle" align="left" width="215"><span id="lblLastName" class="ph_text">ZIP:</span></th>
      				<td valign="middle" align="left"><input name="ZIP" type="text" maxlength="50" size="40" value="<?php echo $_POST[ZIP] ;?>" /></td>
      					</tr>
      			<tr>
      				<td valign="top" align="left" colspan="2">
      					<p>Extra Info:</p></td>
      				</tr>
      			<tr>
      				<td valign="top" align="left" colspan="2"><textarea name="bio" cols="69" rows="10"><?php echo $_POST[bio] ;?></textarea></td>
      				</tr>
				<tr>
      				<td style="text-align:right">Please type the characters you see to the left.</td>
      				<td style="text-align:center;">
      					<p>&nbsp;</p>
      					<?php
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