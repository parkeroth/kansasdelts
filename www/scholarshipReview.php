<?php
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<style>
tr.black {
	background-color: #CCC;
	color:#000;
}

tr.white {
	background-color: #fff;
	color: #000;
}
</style>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<p style="text-align:center">
	<a href="php/rosterExcel.php">Download Excel Sheet</a>
</p>
	<table border="0" cellspacing="0" style="overflow:scroll;" align="center" width="600">
		<tr style="font-weight:bold; background-color:#000; color:#FFF;"><td>Name</td><td>Date Submitted</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		<?php
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
			
			$userData = "
				SELECT * 
				FROM scholarshipResults
				ORDER BY 'time'";
			$getUserData = mysqli_query($mysqli, $userData);
			
			$rowColor = "white";
			
			while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
			{
				echo "<tr class=\"$rowColor\">";
				echo "<td style=\"color: black\">".$userDataArray[name]."</td>";
				echo "<td style=\"color: black\">".date("Y-m-d G:i", strtotime($userDataArray['time']))."</td>";
				echo "<td style=\"color: black\"><input type=\"button\" value=\"View\" ONCLICK=\"MM_openBrWindow('viewScholarship.php?ID=$userDataArray[ID]','','width=500,height=400, scrollbars=1')\"></td>";
				echo "<td style=\"color: black\"><input type=\"button\" value=\"Remove\" ONCLICK=\"javascript: if(confirm('Are you sure you want to remove this record?')) { window.location.href='php/scholarshipRemove.php?ID=$userDataArray[ID]' } \"></td>";
				echo "</tr>";
				
				if($rowColor == "white"){
					$rowColor = "black";
				}
				else
				{
					$rowColor = "white";
				}
			}
	?>
	</table>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>