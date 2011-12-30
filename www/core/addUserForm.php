<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once 'authenticate.php';

require_once 'classes/Member.php';

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	if(isset($_POST[username]))
	{
		$username = $_POST[username];
	}
	else
	{
		$username = substr(strtolower($_POST['l_name']),0,3).substr(strtolower($_POST['f_name']),0,3);
	}
	
	
	$member = new Member(NULL, $username);
	
	if($member->id)
	{
		header("location: addUserForm.php?taken=$username");
		
	} else {
		$member = new Member();
		$member->first_name = $_POST['f_name'];
		$member->last_name = $_POST['l_name'];
		$member->username = $username;
		$member->accountType = NULL;				//TODO: Remove is deprecated
		$member->class = $_POST['class'];
		$member->set_password('passwd');
		$member->set_date_added();
		$member->standing = 'good';
		$member->status = 'pledge';
		$member->residency = $_POST['residency'];
		$member->insert();
		
		header("location: addUserForm.php?status=success");
	}
	
} else {
	$status = $_GET[status];
}
	

/**
 * Form Section
 */


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="/styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />

 <?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");	?>

<?php if($status == 'success'){ ?>

<div class="ui-widget">
		<div class="ui-state-highlight ui-corner-all" style="padding: .3em .5em;"> 
			<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
			New member added successfully! </p>
		</div>
</div>

<?php } ?>

<h2>New Member Form</h2>

<form id="form1" name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table align="center">
	<tr>
		<th>First Name:</th>
		<td><input type="text" name="f_name" id="f_name" /></td>
	</tr>
	<tr>
		<th>Last Name:</th>
		<td><input type="text" name="l_name" id="l_name" /></td>
	</tr>
	<tr>
		<th>Class:</th>
		<td><input type="text" name="class" id="class" /></td>
	</tr>
	<tr>
		<th>Residency:</th>
		<td><input name="residency" type="radio" value="in" checked="checked" /> In <br />
			<input name="residency" type="radio" value="out" /> Out <br /></td>
	</tr>

<?php 
  	if(isset($_GET[taken]))
  	{
		echo "<tr>";
		echo "<th style=\"color: red;\">Username $_GET[taken] is taken!</th>";
		echo "<td><label>Username:<input type=\"text\" name=\"username\" id=\"username\"></td>";
		echo "</tr>\n";
 	}
?>
	<tr>
		<th>&nbsp;</th>
		<td><input type="submit" name="submit" id="submit" value="Submit" />
			<input type="reset" name="Reset" id="Reset" value="Reset" /></td>
	</tr>

</table>

</form>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>