<?php
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script src="js/gen_validatorv31.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<h1>Remove Class Form</h1>
    <p>Check the box next to the class you would like to remove from the term.</p>
    <form id="form1" name="form" method="post" action="php/removeClass.php">
    	<table cellspacing="5">
    		<?php 
	include_once('php/login.php');
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	
	$MyClasses = "
		SELECT * 
		FROM classes 
		WHERE termSeason='".$_GET['season']."'
		AND termYear='".$_GET['year']."'
		AND username='".$_SESSION['username']."'";
	
	$getMyClasses = mysqli_query($mysqli, $MyClasses); 
	
	while ($classArray = mysqli_fetch_array($getMyClasses, MYSQLI_ASSOC)){
		echo "<tr><td style=\"text-align:center;\"><input name=\"".$classArray['department'].$classArray['section']."\" type=\"checkbox\" value=\"remove\" /></td>";
		echo "<td>".$classArray['department']." ".$classArray['section']."</td></tr>";
	}
	
    ?>
    		<input type="hidden" name="year" value="<?php echo $_GET['year']; ?>" />
    		<input type="hidden" name="season" value="<?php echo $_GET['season']; ?>" />
    		
    		<tr>
    			<td><input type="submit" name="submit" id="submit" value="Remove" /></td>
    			<td></td>
    			</tr>
    		</table>
    	
</form>
<script language="JavaScript" type="text/javascript">
 var frmvalidator  = new Validator("form1");
 frmvalidator.addValidation("term","req","Please select a term from the list");
 
 frmvalidator.addValidation("year","req","Please input a year");
 frmvalidator.addValidation("year","num","Please only input numbers for the year");
 
 frmvalidator.addValidation("department","req","Please input a department");
 
 frmvalidator.addValidation("section","req","Please input a section");
 frmvalidator.addValidation("section","num","Please only input numbers for the section");
 
 frmvalidator.setAddnlValidationFunction("DoCustomValidation");
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>