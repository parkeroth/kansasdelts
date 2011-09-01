<?php
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<h1 style="text-align:center;">Your Accomplishments</h1>
	
	<form method="post" action="php/accomplishment.php">
		<table align="center">
			
			<?php 
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

	$accomplishments = "
		SELECT *
		FROM accomplishmentTypes";
	$getAccomplishments = mysqli_query($mysqli, $accomplishments);
	while ($accomplishArray = mysqli_fetch_array($getAccomplishments, MYSQLI_ASSOC)){
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"$accomplishArray[type][]\" value=\"true\"";
		
		$check = "SELECT ID 
			FROM accomplishments
			WHERE type = '$accomplishArray[type]'
			AND username = '".$_SESSION[username]."'";
		$checkTable = mysqli_query($mysqli, $check);
		
		if(mysqli_fetch_row($checkTable))
		{
			echo "checked = \"checked\"";
		}
	
		echo "></td><td>$accomplishArray[title]</td>\n";
		echo "</tr>\n";
	}
	?>
			<tr>
				<td>&nbsp;</td><td><input type="submit" /></td>
				</tr>
			</table>		
	</form>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>