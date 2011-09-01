<?php
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script language="JavaScript" src="js/gen_validatorv31.js" type="text/javascript"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h2 style="text-align:center;">Class Search Form</h2>
	<?php 
		if(isset($_POST[department])){
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$department = $_POST['department'];
$department = strtoupper($department);

if($_POST['section'] != NULL){
	
$ClassSearch = "
			SELECT username, termYear, termSeason 
			FROM classes 
			WHERE department='".$department."'
			AND section='".$_POST['section']."'";
	
		$getClassSearch = mysqli_query($mysqli, $ClassSearch);
		$counter = 0;
		
		echo "<table>";
		echo '<tr><td valign="top" style="padding-right:10px; font-weight: bold;">'.$department." ".$_POST['section']."</td><td>";
		while ($searchResults = mysqli_fetch_array($getClassSearch, MYSQLI_ASSOC)){
			
			$NameSearch = "
				SELECT firstName, lastName 
				FROM members 
				WHERE username='".$searchResults['username']."'";
			
			$getNameSearch = mysqli_query($mysqli, $NameSearch);
			$nameResults = mysqli_fetch_array($getNameSearch, MYSQLI_ASSOC);
			
			echo $nameResults['firstName']." ".$nameResults['lastName']." | ".ucfirst($searchResults['termSeason'])." ".$searchResults['termYear']."<br>";
			$counter++;
		}
	if ($counter == 0){
		echo "No recods for ".$_POST['department']." ".$_POST['section'];
	}
	echo '</td></tr></table>';
} else {
	$ClassSearch = "
			SELECT DISTINCT section 
			FROM classes 
			WHERE department='".$department."'
			ORDER BY section";
	
		$getClassSearch = mysqli_query($mysqli, $ClassSearch);
		
		while ($searchResults = mysqli_fetch_array($getClassSearch, MYSQLI_ASSOC)){
			
			echo "<table>";
			
			$SectionSearch = "
				SELECT username, termYear, termSeason
				FROM classes
				WHERE department='".$department."'
				AND section='".$searchResults['section']."'";
				
			$getSectionSearch = mysqli_query($mysqli, $SectionSearch);
			$counter = 0;
			
			
			
			echo '<tr><td valign="top" style="padding-right:10px; font-weight: bold;">'.$department." ".$searchResults['section']."</td><td>";
			
			while ($sectionResults = mysqli_fetch_array($getSectionSearch, MYSQLI_ASSOC)){
				
				$NameSearch = "
					SELECT firstName, lastName 
					FROM members 
					WHERE username='".$sectionResults['username']."'";
			
				$getNameSearch = mysqli_query($mysqli, $NameSearch);
				$nameResults = mysqli_fetch_array($getNameSearch, MYSQLI_ASSOC);
			
				echo $nameResults['firstName']." ".$nameResults['lastName']." | ".ucfirst($sectionResults['termSeason'])." ".$sectionResults['termYear']."<br>";
				$counter++;
			}
			echo '</td></tr></table>';
			
			if ($counter == 0){
				echo "No recods for ".$_POST['department']." ".$_POST['section'];
			}
		}
}
		
		}
	?>
	<p>To search for who has taken a class provide the department code and section number below. To search for all courses under a department code leave the section number field blank.</p>
	
	<form id="form1" name="form1" method="post" action="classSearchForm.php">
		<table align="center" style="margin-top: 20px;">
			<tr>
				<th>Department Code:</th>
				<td><input type="text" name="department" id="department" /></td>
				</tr>
			<tr>
				<th>Section Number:</th>
				<td><input type="text" name="section" id="section" /></td>
				</tr>
			<tr>
				<th></th>
				<td><input type="submit" name="submit" id="submit" value="Submit" /> <input type="reset" name="Reset" id="Reset" value="Reset" /></td>
				</tr>  
			</table>
	</form>
    <p>&nbsp;</p>
<script language="JavaScript" type="text/javascript">
 var frmvalidator  = new Validator("form1");
 
 frmvalidator.addValidation("department","req","Please input a department");
 frmvalidator.addValidation("department","alpha","Please input only letters for the department");
 
 frmvalidator.addValidation("section","num","Please only input numbers for the section");
 
 frmvalidator.setAddnlValidationFunction("DoCustomValidation");
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>