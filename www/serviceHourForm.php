<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'communityService');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<style type="text/css">
<!--
#container #content #beef h2 {
	text-align: center;
}
-->
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h2>Change Service Hours</h2>
	<form id="serviceHour" name="serviceHour" method="post" action="php/serviceHour.php">
		<?php 
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
		$userData = "
			SELECT * 
			FROM members
			ORDER BY lastName";
	
		$getUserData = mysqli_query($mysqli, $userData);
		echo "<table align=\"center\">";
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
			echo "<tr><td><label>".$userDataArray['firstName']." ".$userDataArray['lastName']." </td>\n";
			echo "<td><input type=\"text\" name=\"".$userDataArray['username']."\" value=\"".$userDataArray['serviceHours']."\" size=\"3\" /></label></td></tr>\n";
		}
		echo "</table>";
	?>
		<p style="text-align:center;">
			<input type="submit" name="submit" id="submit" value="Submit" />
			<label>
				<input type="reset" name="Reset" id="Reset" value="Reset" />
				</label>
			</p>
</form>
	<p>&nbsp;</p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>