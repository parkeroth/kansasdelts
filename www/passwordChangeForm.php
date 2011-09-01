<?php
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<script language="JavaScript" type="text/javascript">
	function checkForm()
	{
		var newPass = document.forms.passwordChange.newPass.value;
		var repeatPass = document.forms.passwordChange.repeatPass.value;
		if( newPass != repeatPass){
			alert('New passwords do not match!');
			return false;
		}
		return true;
	}
	</script>
    <?php 
		if(isset($_GET['error'])){
			echo "<div style=\"text-align:center; color:#F00;\"><p>Incorrect password! Plese try again.</p></div>";
		}
	?>
    
    <form name="passwordChange" action="php/changePassword.php" onsubmit="return checkForm();" method="post">
    	<table width="400" border="0" cellpadding="4" align="center">
    		<tr>
    			<td>Old Password</td>
    			<td><input name="oldPass" type="password" /></td>
    			</tr>
    		<tr>
    			<td>New Password</td>
    			<td><input name="newPass" type="password" /></td>
    			</tr>
    		<tr>
    			<td>Repeat Password</td>
    			<td><input name="repeatPass" type="password" /></td>
    			</tr>
    		<tr>
    			<td>&nbsp;</td>
    			<td><input type="submit" name="submit" id="submit" value="Submit" />
    				<input type="reset" name="Reset" id="Reset" value="Reset" />
				</td>
			</tr>
		</table>
	</form>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>