<?
session_start();
include_once('php/login.php');
$authUsers = array('admin');
include_once('php/authenticate.php');

/**
 * Processing Section
 */
 
if(isset($_GET[ID])) {
	
	$modify = "
		UPDATE reportingTasks
		SET status = '$_GET[status]'
		WHERE ID = '$_GET[ID]'";
	$doModify = mysqli_query($mysqli, $modify);
	
}
 
/**
 * Form Section
 */

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");?>

<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>

<link type="text/css" href="css/reportingTasks.css" rel="stylesheet" />

<script type="text/javascript">
	$(function() {
		$("td.status").click(function() {
			
			var ID = $(this).attr('id');
			
			var status = $(this).html();
			
			var newContent = '<td><select class="statusSelect" name="status' + ID + '">';
			newContent += '<option value="Incomplete"'; if(status == 'Incomplete'){ newContent += 'selected'; } newContent += '>Incomplete</option>';
			newContent += '<option value="Review"'; if(status == 'Review'){ newContent += 'selected'; } newContent += '>Review</option>';
			newContent += '<option value="Revisit"'; if(status == 'Revisit'){ newContent += 'selected'; } newContent += '>Revisit</option>';
			newContent += '<option value="Complete"'; if(status == 'Complete'){ newContent += 'selected'; } newContent += '>Complete</option>';
			newContent += '</select></td>';
			
			$(this).replaceWith(newContent);
			
			$('.statusSelect').change(function() {
				
				var URL = 'manageReportingTasks.php';
				URL += '?ID=' + ID;
				URL += '&status=' + $(this).val();
				
				window.location.href=URL
			});
			
		});
		
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>
<h1>Reporting Tasks</h1>

<?php 	$documentQuery = "
			SELECT DISTINCT document
			FROM reportingTasks
			ORDER BY document";
		$documentResult = mysqli_query($mysqli, $documentQuery);
		while($documentData = mysqli_fetch_array($documentResult, MYSQLI_ASSOC)) { ?>

<h2><?php echo $documentData[document]; ?></h2>

	<table align="center" cellpadding="10px">
		
	<?php	$first = true;
	
			$ownedQuery = "
				SELECT section, document, status, task, description, title, reportingTasks.ID AS ID
				FROM reportingTasks 
				JOIN positions
				ON owner = type
				WHERE document = '$documentData[document]'
				ORDER BY section";
			//echo $ownedQuery;
			$ownedResult = mysqli_query($mysqli, $ownedQuery);
			while($ownedData = mysqli_fetch_array($ownedResult, MYSQLI_ASSOC)) {
				
				if($first) { 
					$first = false;
				} 
				echo "<tr>\n";
				
					echo "<td class=\"task\">$ownedData[task] ($ownedData[section])</td>";
					echo "<td id=\"$ownedData[ID]\" class=\"status $ownedData[status]\">$ownedData[status]</td>";
					
				echo "</tr>";
				
				echo "<tr><td colspan=\"2\">Owner: $ownedData[title]</td></tr>";
				
				echo "<tr class=\"description\">";
				
					echo "<td colspan=\"2\">";
					
					echo str_replace(' - ','<br> - ',$ownedData[description]);
					
					echo "</td>";
				
				echo "</tr>\n";
					
			} 
			
			if($first) {
				echo "<tr><td colspan=\"2\">No Tasks</td></tr>";
			} ?>
			
	</table>

<?php 
		}
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>