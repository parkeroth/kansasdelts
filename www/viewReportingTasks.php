<?
session_start();
include_once('php/login.php');
$authUsers = array('brother');
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


$user = $session->accountType;

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");?>

<link type="text/css" href="styles/popUp.css" rel="stylesheet" />
<link type="text/css" href="css/fileuploader.css" rel="stylesheet" />
<link type="text/css" href="css/reportingTasks.css" rel="stylesheet" />

<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>

<script src="js/fileuploader.js" type="text/javascript"></script>

<script type="text/javascript" src="js/popup.js"></script>


<script type="text/javascript">

	function createUploader(dir){
		
		var uploader = new qq.FileUploader({
			element: document.getElementById('fileUploader'),
			action: '/php/fileUploader.php?dir=' + dir,
			debug: true
		});           
	}
	
	function myfunct(dir){
		
		if(confirm('Are you sure you want to remove this file?')){
			window.location = 'php/removeFile.php?name=' + dir + '&return=viewReportingTasks.php';
		}
	}
	
	$(function() {
		
		$("td.task").click(function() {
			
			var ID = $(this).attr('id');
			
			$.get("reportingTaskDetail.php?ID=" + ID, function(data){
				$("#detailContent").html(data);
			});
			
			var dir = $('.fileDirectory', this).html();
			
			//centering with css
			centerPopup('#taskDetail');
			
			//load popup
			loadPopup('#taskDetail');
			
			//CLOSING  POPUP
			//Click the x event!
			$('#closeTask').click(function(){
				disablePopup('#taskDetail');
			});
			//Click out event!
			$("#backgroundPopup").click(function(){
				disablePopup('#taskDetail');
			});
			
			createUploader(dir);
			
			$("#submitSection").click(function() {
				window.location = 'viewReportingTasks.php?ID=' + ID + '&status=Review';
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
				SELECT * 
				FROM reportingTasks 
				WHERE document = '$documentData[document]'";
			//echo $ownedQuery;
			$ownedResult = mysqli_query($mysqli, $ownedQuery);
			while($ownedData = mysqli_fetch_array($ownedResult, MYSQLI_ASSOC)) {
				
				if(strpos($user, $ownedData[owner])){
					$status = 'owner';
				} else if(strpos($user, $ownedData[helper])){
					$status = 'helper';
				} else {
					$status = 'none';
				}
				
				
				if($status == 'owner'){
					
					if($first) { 
						$first = false;
					} 
					
					$directory = '/var/www/reporting/';
					$directory .= str_replace(' ','-',$ownedData[document]).'/'.str_replace(' ','-',$ownedData[section]).'/';
					
					if($ownedData[status] == "Review"){
						$taskClass = "review";
					} else {
						$taskClass = "task";
					}
					
					echo "<tr>\n";
					
						echo "<td class=\"$taskClass\" id=\"$ownedData[ID]\"><a href=\"#\">$ownedData[task] ";
						echo "($ownedData[section])</a><span class=\"fileDirectory\">$directory</span></td>";
						echo "<td class=\"status $ownedData[status]\">$ownedData[status]</td>";
						
					echo "</tr><tr class=\"description\">";
					
						echo "<td colspan=\"2\">";
						
						echo str_replace(' - ','<br> - ',$ownedData[description]);
						
						echo "</td>";
					
					echo "</tr>\n";
					
				}
			} 
			
			if($first) {
				echo "<tr><td colspan=\"2\">No Tasks</td></tr>";
			} ?>
			
	</table>

<?php } ?>

	<div id="taskDetail" class="popup">
		<h2>Task Info</h2>
		
		<input id="submitSection" type="button" value="Submit Section" />
		
		<div id="closeTask" class="popupClose"><a>x</a></div>
		<div id="detailContent"></div>
		<div id="fileUploader"></div>
	</div>
	
	<div id="backgroundPopup"></div>

<?php	include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>