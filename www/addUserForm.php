<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once('php/authenticate.php');


/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	include_once('login.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	if(isset($_POST[username]))
	{
		$username = $_POST[username];
	}
	else
	{
		$username = substr(strtolower($_POST['l_name']),0,3).substr(strtolower($_POST['f_name']),0,3);
	}
	
	$check = "SELECT ID from members WHERE username='$username'";
	$checkTable = mysqli_query($mysqli, $check);
	
	if(mysqli_fetch_row($checkTable))
	{
		header("location: ../addUserForm.php?taken=$username?");
		
	} else {
		
		$add_sql = "
			INSERT INTO members (FirstName, LastName, accountType, class, username, password, dateAdded, standing, memberStatus, residency) 
			VALUES ('".ucwords($_POST['f_name'])."', '".ucwords($_POST['l_name'])."', '|brother', '".$_POST['class']."', '".$username."', SHA('passwd'), '".date("Y-m-d")."', 'good', 'pledge', '$_POST[residency]')";
		
		//echo $add_sql;
		
		$add_res = mysqli_query($mysqli, $add_sql);
		
		header("location: ../addUserForm.php");
	}
	
}
	


 
/**
 * Form Section
 */


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");	
?>

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