<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'academics');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/studyHours.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<script language="jscript" type="text/javascript">
function Confirm()
{
return confirm ("Are you sure you want want to make these changes?");
}
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<?php
        $member_manager = new Member_Manager();
        $memList = $member_manager->get_all_members();
        //TODO: use the position log table
?>

<h1>Choose Proctors</h1>

	<form id="chooseProctor" name="chooseProctor" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return Confirm();">

		<p>
        	Put a check next to any user who is a proctor. This will give them access to the management of study hours.
		</p>
		<p>&nbsp;</p>
		<?php
                echo "<table align=\"center\">";
                foreach($memList as $curMem)
                {
                    	echo "<tr><td><label>".$curMem->first_name." ".$curMem->last_name." </td>\n";
			echo "<td><input name=\"".$curMem->id."\" type=\"checkbox\" value=\"checked\" ";
			if( strpos($members[$i]['accountType'], "proctor") ){
				echo "checked=\"checked\"";
			}
			echo ">";
			echo "</td></tr>\n";
                }
		echo "</table>";
                ?>
		<p>&nbsp;</p>
		<div style="text-align:center;"><input type="submit" name="submit" id="submit" value="Submit" /></div>
	</form>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>