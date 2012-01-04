<?php
session_start();
$authUsers = array('brother');
include_once 'authenticate.php';

include_once 'classes/Member.php';

$member = new Member($session->member_id);

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$member->email = $_POST['email'];	// TODO: Validate the email address string
	$member->set_phone_number($_POST[phone_number]);
	$member->phone_carrier = $_POST[phone_carrier];
	
	$member->major = $_POST[major];
	$member->school = $_POST[school];
	$member->grad_year = $_POST[grad_year];
	$member->home_town = $_POST[home_town];
	$member->shirt_size = $_POST[shirt_size];
	
	$member->parent_name = $_POST[parent_name];
	$member->parent_address = $_POST[parent_address];
	$member->parent_email = $_POST[parent_email];
	
	$member->dad_id = $_POST[dad_id];
	
	$member->save();
	
	header('location: ../account.php');
}

$member_manager = new Member_Manager();
$member_list = $member_manager->get_all_members();

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");

?>		
	<form name="rosterChange" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<div style="float:left;">
			<p><h2>Contact Info</h2>
			<table width="300px">
				<tr><td>Email: </td><td><input name="email" type="text" value="<?php echo $member->email; ?>" /><br></td></tr>
				<tr><td>Phone Number: </td><td><input name="phone_number" type="text" value="<?php echo $member->get_phone_number(); ?>" /><br></td></tr>
				<tr><td>Carrier: </td><td><select name="phone_carrier" />
					<?php foreach(Member_Manager::$PHONE_CARRIERS as $code => $name){ ?>
						<option value="<?php echo $code; ?>" <?php if($member->phone_carrier == $code){echo "selected";} ?> >
							<?php echo $name; ?>
						</option>
					<?php } ?>
				</select><br></td></tr>
			</table></p>
		
			<p><h2>Personal Info</h2>
			<table width="300px">
				<tr><td>Major: </td><td><input name="major" type="text" value="<?php echo $member->major; ?>" /><br></td></tr>
				<tr><td>School: </td><td><select name="school" />";
					<?php foreach(Member_Manager::$SCHOOLS as $code => $name){ ?>
						<option value="<?php echo $code; ?>" <?php if($member->school == $code){echo "selected";} ?> >
							<?php echo $name; ?>
						</option>
					<?php } ?>		
				</select><br></td></tr>
				<tr><td>Graduation Year: </td><td><input name="grad_year" type="text" value="<?php echo $member->grad_year; ?>" /><br></td></tr>
				<tr><td>Home Town: </td><td><input name="home_town" type="text" value="<?php echo $member->home_town; ?>" /><br></td></tr>
				<tr><td>Shirt Size: </td><td><select name="shirt_size" />";
					<option value="none"
					<?php if($member->shirt_size == NULL){echo "selected";} ?>
					></option>
					<option value="s"
					<?php if($member->shirt_size == 's'){echo "selected";} ?>
					>S</option>
					<option value="m"
					<?php if($member->shirt_size == 'm'){echo "selected";} ?>
					>M</option>
					<option value="l"
					<?php if($member->shirt_size == 'l'){echo "selected";} ?>
					>L</option>
					<option value="xl"
					<?php if($member->shirt_size == 'xl'){echo "selected";} ?>
					>XL</option>
					<option value="xxl"
					<?php if($member->shirt_size == 'xxl'){echo "selected";} ?>
					>XXL</option>
				</select><br></td></tr>
			</table></p>
			<p><input type="submit" name="submit" id="submit" value="Submit" /></p>
		</div>
		<div style="float: right;">
			<p><h2>Parent's Info</h2>
			<table width="300px">
				<tr><td>Name: </td><td><input name="parent_name" type="text" value="<?php echo $member->parent_name; ?>" /><br></td></tr>
				<tr><td>Address: </td><td><input name="parent_address" type="text" value="<?php echo $member->parent_address; ?>" /><br></td></tr>
				<tr><td>Email: </td><td><input name="parent_email" type="text" value="<?php echo $member->parent_email; ?>" /><br></td></tr>
			</table></p>
			<p><h2>Delt Info</h2>
			<table width="300px">
				<tr><td>Pledge Dad: </td><td><select name="dad_id" />";
					<option value="0">None</option>
					<?php foreach($member_list as $dad){ ?>
						<option value="<?php echo $dad->id; ?>" <?php if($member->dad_id == $dad->id){echo "selected";} ?> >
							<?php echo $dad->first_name.' '.$dad->last_name; ?>
						</option>
					<?php } ?>		
				</select><br></td></tr>
			</table></p>
		</div>
		<div style="clear:both;"><p>&nbsp;</p></div>
	</form>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>