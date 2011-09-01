<?php
$authUsers = array('brother');
include_once('../../php/authenticate.php');

include_once('../../php/login.php');
	
$id =mysql_real_escape_string( $_GET[ID]);

$recruitQuery = "
	SELECT *
	FROM recruits 
	WHERE ID = '$id'";
$getRecruit = mysqli_query($mysqli, $recruitQuery);
$recruitArray = mysqli_fetch_array($getRecruit, MYSQLI_ASSOC);
?>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<h1 style="text-align:center">Recruit History</h1>

<div id="outstandingCalls">
    <table align="center" cellspacing="0">
    <?php 
        $first = true;
        $outstandingQuery = "
            SELECT *
            FROM recruitCalls
            WHERE recruitID = '$id'
            AND (status = 'leftMessage' OR status='pending')";
        $getOutstanding = mysqli_query($mysqli, $outstandingQuery);
        while($outstandingArray = mysqli_fetch_array($getOutstanding, MYSQLI_ASSOC)){
            if($first){ $first = false;?>
                <tr class="tableHeader">
                    <td width="80">Type</td>
                    <td>Date Requested</td>
                    <td></td>
                </tr>
            <?php }
            
            echo '<tr>';
            echo "<td>$outstandingArray[status]</td>";
            echo "<td>$outstandingArray[dateRequested]</td>";
			echo '<td><input class="editCall" id="'.$outstandingArray[ID].'" type="button" value="Edit" /></td>';
            echo '</tr>';
        }
    ?>
    </table>
</div>
	
	<div id="viewControls">
		<input type="button" value="Edit"  />
		<input class="closeWindow" type="button" value="Close" />
	</div>
</div>