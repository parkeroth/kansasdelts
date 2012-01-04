<?php
session_start();
$authUsers = array('brother');
include_once 'authenticate.php';

include_once 'classes/Member.php';

$member = new Member($session->member_id);
$dad = new Member($member->dad_id);

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

		
<div style="float:left;">
	<p><h2>Account Info</h2>
	<table width="300px">
		<tr><td><b>Name: </b></td><td><?php echo $member->first_name; ?> <?php echo $member->last_name; ?><br></td></tr>
		<tr><td><b>Class: </b></td><td><?php echo ucwords($member->get_class()); ?><br></td></tr>
	</table></p>
	
	<p><h2>Delt Info</h2>
	<table width="300px">
		<tr><td><b>Pledge Dad: </b></td><td><?php echo $dad->first_name; ?> <?php echo $dad->last_name; ?><br></td></tr>
	</table></p>
		
	<p><h2>Security</h2><table width="300px">
		<tr><td><a href="passwordChangeForm.php">Change Password</a></td><td></td></tr>
		<tr><td><p>&nbsp;</p></td><td></td></tr>
		<tr><td><p>&nbsp;</p></td><td></td></tr>
		<tr><td><p>&nbsp;</p></td><td></td></tr>
		<tr><td><a href="changeRosterForm.php">Click to edit info.</a></td><td></td></tr>
		<tr><td><a href="../account.php">Click to return to account.</a></td><td></td></tr>
	</table></p>
</div>
		
<div style="float: right;">
	<p><h2>Personal Info</h2>
	<table width=\"300px\">
		<tr><td><b>Major: </b></td><td><?php echo $member->major; ?><br></td></tr>
		<tr><td><b>School: </b></td><td><?php echo ucwords($member->school); ?><br></td></tr>
		<tr><td><b>Graduation Year: </b></td><td><?php echo $member->grad_year; ?><br></td></tr>
		<tr><td><b>Home Town: </b></td><td><?php echo $member->home_town; ?><br></td></tr>
		<tr><td><b>Shirt Size: </b></td><td><?php echo strtoupper($member->shirt_size); ?><br></td></tr>
	</table></p>
		
	<p><h2>Contact Info</h2><table width=\"300px\">
		<tr><td><b>Email: </b></td><td><?php echo $member->email; ?><br></td></tr>
		<tr><td><b>Phone Number: </b></td><td><?php echo $member->get_phone_number(); ?><br></td></tr>
		<tr><td><b>Carrier: </b></td><td><?php echo ucwords($member->phone_carrier); ?><br></td></tr>
	</table></p>
		
	<p><h2>Parent's Info</h2><table width=\"300px\">
		<tr><td><b>Name: </b></td><td><?php echo $member->parent_name; ?><br></td></tr>
		<tr><td><b>Address: </b></td><td><?php echo $member->parent_address; ?><br></td></tr>
		<tr><td><b>Email: </b></td><td><?php echo $member->parent_email; ?><br></td></tr>
	</table></p>
</div>
		
<div style="clear:both;"><p>&nbsp;</p></div>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>