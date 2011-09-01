<?php
	session_start();
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>



<style>
	th {
		text-align:right;	
	}
</style>
<script src="js/gen_validatorv31.js"></script>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Online Scholarship Application</h1>
      <form name="application" action="php/scholarshipSubmit.php" method="post">
      	
      	<h2>General Information</h2>
      	
      	<table border="0" cellpadding="0" align="center">
      		<tr>
      			<th width="120">Name:</th>
      			<td><input name="name" type="text" size="40" /></td>
      			</tr>
      		<tr>
      			<th> Address:</th>
      			<td><input name="address" type="text" size="40" /></td>
      			</tr>
      		<tr>
      			<th>City:</th>
      			<td><input name="city" type="text" size="40" /></td>
      			</tr>
      		<tr>
      			<th>State:</th>
      			<td><input name="state" type="text" size="40" /></td>
      			</tr>
      		<tr>
      			<th>ZIP:</th>
      			<td><input name="zip" type="text" size="40" /></td>
      			</tr>
      		<tr>
      			<th>Phone:</th>
      			<td><input name="phone" type="text" size="40" /></td>
      			</tr>
      		<tr>
      			<th>E-Mail:</th>
      			<td><input name="email" type="text" size="40" /></td>
      			</tr>
      		</table>
      	
      	<h2>Scholastic Information</h2>
      	
      	<table border="0" cellpadding="0" align="center">
      		<tr>
      			<th>GPA:</th>
      			<td><input name="gpa" type="text" size="40" /></td>
      			</tr>
      		<tr>
      			<th>High School:</th>
      			<td><input name="highSchool" type="text" size="40" /></td>
      			</tr>
      		<tr>
      			<th>Class Rank:</th>
      			<td><input name="classRank" type="text" size="40" /></td>
      			</tr>
      		<tr>
      			<th>Intended Major:</th>
      			<td><input name="intendedMajor" type="text" size="40" /></td>
      			</tr>
      		</table>
      	
      	<h2>Activities and Leadership Information</h2>
      	
      	<table border="0" cellpadding="0" align="center" width="300px">
      		<tr>
      			<th>Honors, Awards, or Scholarships:</th>
      			<td><textarea name="honors" cols="38" rows="5"></textarea></td>
      			</tr>
      		<tr>
      			<th>Extracurricular Activities:</th>
      			<td><textarea name="extracurricular" cols="38" rows="5"></textarea></td>
      			</tr>
      		<tr>
      			<th>Community Involvement:</th>
      			<td><textarea name="community" cols="38" rows="5"></textarea></td>
      			</tr>
      		<tr>
      			<th>Work Experience:</th>
      			<td><textarea name="work" cols="38" rows="5"></textarea></td>
      			</tr>
      		</table>
      	
      	<h2>Short Essay (150 words or less)</h2>
      	
      	<table border="0" cellpadding="0" align="center" width="600">
      		<tr>
      			<td width="431" style="text-align:right; vertical-align: top;">1. <br /></td>
      			<td width="215" style="padding-right:50px;">In 150 words or less, describe how you are personally committed to life-long learning and growth.</td>
      			</tr>
      		<tr>
      			<td style="text-align:right; vertical-align: top;">2. <br /></td>
      			<td style="padding-right:50px;">In 150 words or less, describe a leadership experience and how it has positively affected your life.</td>
      			</tr>
      		<tr>
      			<td colspan="2" style="text-align:center"><p>&nbsp;</p>
      				<p>Please select either essay option one or two and indicate your choice below:</p>
      				<p><input name="essayOption" type="radio" value="1" />
      					Option 1 
      					<input name="essayOption" type="radio" value="2" />
      					Option 2</p>
      				<p>&nbsp;</p></td>
      			</tr>
      		<tr>
      			<td colspan="2" style="text-align:center;"><textarea name="essayAnswer" cols="90" rows="10"></textarea></td>
      			</tr>
      		<tr>
      			<td colspan="2"><p>&nbsp;</p></td>
      			</tr>
      		<tr>
      			<th width="431">Please type the code you see to the right in the box below:</th>
      			<td style="text-align:center;">
      				<?
				if($_GET['opencaptcha']=='failed') { echo "<script>alert('You Did Not Fill In The Security Code Correctly');</script>";}
				$date = date("Ymd");
				$rand = rand(0,9999999999999);
				$height = "40";
				$width  = "150";
				$img    = "$date$rand-$height-$width.jpgx";
				echo "<input type='hidden' name='img' value='$img'>";
				echo "<a href='http://www.opencaptcha.com'><img src='http://www.opencaptcha.com/img/$img' height='$height' alt='captcha' width='$width' border='0' /></a><br />";
				echo "<input type=text name=code value='' size='25' />";
			?>
      				</td>
      			</tr>
      		<tr>
      			<td colspan="2" style="text-align:center">
      				<p>&nbsp;        </p>
      				<p>
      					<input type="submit" name="btnSubmit" value="Submit" id="btnSubmit" /> 
      					<input type="submit" name="btnClear" value="Reset" id="btnClear" />	
      					</p>
      				</td>
      			</tr>
      		</table>
      	
      	</form>
<script language="JavaScript" type="text/javascript">
 var frmvalidator  = new Validator("application");
 frmvalidator.addValidation("name","req","Please indicate your name.");
 
 frmvalidator.addValidation("address","req","Please indicate your address.");
 
 frmvalidator.addValidation("city","req","Please indicate your city.");
 
 frmvalidator.addValidation("state","req","Please indicate your state.");

 frmvalidator.addValidation("zip","req","Please indicate your ZIP code.");
 
 frmvalidator.addValidation("phone","req","Please indicate your phone number.");
 
 frmvalidator.addValidation("email","req","Please indicate your email address.");

 frmvalidator.addValidation("essayOption","req","Please choose and essay.");
 
 frmvalidator.addValidation("essayAnser","req","Please submit an essay.");
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>