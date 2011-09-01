<?php
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script src="js/gen_validatorv31.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<h1 style="text-align:center">Add Class Form</h1>
	<form id="form1" name="form" method="post" action="php/addClass.php">
		<table align="center">
			<tr>
				<th>Term:</th>
				<td>
					<select name="term" id="term">
						<?php
                    if($_GET['season'] == fall){
                        echo "<option value=\"fall\" selected>Fall</option>";
                        echo "<option value=\"spring\">Spring</option>";
                    } else {
                        echo "<option value=\"fall\">Fall</option>";
                        echo "<option value=\"spring\" selected>Spring</option>";
                    }
                  ?>
						</select>
					</td>
				</tr>
			<tr>
				<th>Year:</th>
				<td>
					<select name="year" id="year">
						<?
                  $yearLoop = date("Y");
                  
                  for ($i = $yearLoop+1; $i >= $yearLoop-3; $i--) {
                    if($i == $_GET['year']){
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    echo "<option value=\"$i\" $selected>$i</option>\n";
                  }
                  ?>
						</select>
					</td>
				</tr>
			<tr>
				<th>Department Code:</th><td><input type="text" name="department" id="department" /></td>
				</tr>
			<tr>
				<th>Course Number:</th><td><input type="text" name="section" id="section" /></td>
				</tr>
			<tr>
				<th>Credit Hours:</th><td><input type="text" name="hours" id="hours" /></td>
				</tr>
			<tr>
				<td colspan="2"><p>For those like Pat, for ECON 142 type ECON in <br /> department code and 142 in course number.</p></td>
				</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="submit" name="submit" id="submit" value="Submit" /> <input type="reset" name="Reset" id="Reset" value="Reset" /></td>
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