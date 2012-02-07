<?php
session_start();
include_once('../php/login.php');
$authUsers = array('admin', 'saa');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once 'classes/Infraction_Log.php';

/**
 * Processing Section
 */

$errors = array();

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	if($_POST[action] == 'remove') {
		
		$query = "DELETE FROM punishments WHERE ID = '$_POST[id]'";
			
		$submitQuery = mysqli_query($mysqli, $query);
		
	}
	else if($_POST[action] == 'add') {
		
		$query = "SELECT ID FROM punishments WHERE offenceNum = '$_POST[offenceNum]' AND type = '$_POST[type]'";
		$getData = mysqli_query($mysqli, $query);
		
		
		
		if($_POST[offenceNum] == NULL)
		{
			$errors[] = 'Sepcify an offence number.';
		}
		if ($data = mysqli_fetch_array($getData, MYSQLI_ASSOC)) 
		{
			$errors[] = 'Record already exists for Type = '. $_POST[type] . ', Offence # = ' . $_POST[offenceNum];
		}
		if($_POST[hours] > 0 && ($_POST[hourType] == 'select' || $_POST[hourType] == 'NULL')) 
		{
			$errors[] = 'Need to specify the type of hours.';
		}
		if($_POST[suspension] == 'select')
		{
			$errors[] = 'Select option for suspensions.';
		}
		if($_POST[expel] == 'select')
		{
			$errors[] = 'Select option for expel.';
		}
				
		if(count($errors) == 0)
		{
			$query = "INSERT INTO punishments (offenceNum, type, fine, hours, hourType, suspension, expel)
						VALUES ('$_POST[offenceNum]', '$_POST[type]', '$_POST[fine]', '$_POST[hours]', '$_POST[hourType]', '$_POST[suspension]', '$_POST[expel]')";
			
			$submitQuery = mysqli_query($mysqli, $query);
		}
		
	}
	
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<style>

.newItem {
	text-align: center;
	with: 200px;
	margin-left: auto;
	margin-right: auto;
	padding-top: 20px;
}

td {
	text-align:center;
}

.left {
	text-align:left;

}

</style>

<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="../styles/popUp.css" rel="stylesheet" />

<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="../js/popup.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	
	$("a.order").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('snippet/popupBody.php?type=viewOrder&ID=' + id, function(data){
			$("#popupBody").html(data);
		});
		
		//$('#generalPopup').css('width', '420px');
		
		//centering with css
		centerPopup('#generalPopup');
		//load popup
		loadPopup('#generalPopup');
	});
	
	$(".add").click(function(){
		
		var id = $(this).attr('id');
		
		var html = '<h3>New Record</h3>'
		html += '<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">';
		html += '<table align="center">';
		html += '<tr><th>Offence #: </th><td class="left"><input type="text" size="2" name="offenceNum" /></td></tr>';
		html += '<tr><th>Fine: </th><td class="left"><input type="text" size="3" name="fine" /></td></tr>';
		html += '<tr><th>Hours: </th><td class="left"><input type="" size="2" name="hours" /></td></tr>';
		html += '<tr><th>Hour Type: </th><td class="left"><select name="hourType"><option value="select">Select One</option><option value="NULL">N/A</option><option value="houseHours">House</option><option value="serviceHours">Service</option></td></tr>';
		html += '<tr><th>Suspension: </th><td class="left"><select name="suspension"><option value="select">Select One</option><option value="none">None</option><option value="social">Social</option><option value="financial">Financial</option></td></tr>';
		html += '<tr><th>Expel: </th><td class="left"><select name="expel"><option value="select">Select One</option><option value="1">Yes</option><option value="0">No</option></td></tr>';
		html += '<tr><th>&nbsp;</th><td class="left"></td></tr>';
		html += '<tr><th>&nbsp;</th><td class="left"><input type="submit" value="Submit" /></td></tr>';
		html += '</table>';
		html += '<input type="hidden" name="action" value="add" />';
		html += '<input type="hidden" name="type" value="' + id + '" />';
		html += '</form>';
		
		$('#' + id + 'Add').html(html);
	});
		
	//CLOSING  POPUP
	//Click the x event!
	$('#popupClose').click(function(){
		disablePopup('#generalPopup');
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup('#generalPopup');
	});
});

</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1 align="center">Punishment Configuration</h1>
	<?php
		foreach($errors as $error){
			echo '<p class="redHeading" style="text-align:center;">' . $error . '</p>';
		}
		
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$tableHeader = '<tr class="tableHeader"><td>Offence #</td><td>Fine</td><td>Hours</td><td>Hour Type</td><td>Suspension</td><td>Expel</td><td></td></tr>';
		
		foreach(Infraction_Log::$INFRACTION_TYPES as $type => $title)
		{
			echo "<h2>$title</h2>";
			echo '<table cellspacing="0">';
			
			$punishmentData = "
				SELECT * 
				FROM punishments
				WHERE type = '$type'
				ORDER BY offenceNum";
				
			$getPunishments = mysqli_query($mysqli, $punishmentData);
			
			$first = true;
			while($punishmentArray = mysqli_fetch_array($getPunishments, MYSQLI_ASSOC))
			{
				if($first){
					echo $tableHeader;
					$first = false;
				}
				
				echo '<tr>';
				
				echo "<td>$punishmentArray[offenceNum]</td>";
				echo "<td>$punishmentArray[fine]</td>";
				echo "<td>$punishmentArray[hours]</td>";
				echo "<td>";
				
					if($punishmentArray[hourType] == 'houseHours') {
						echo "House";
					} else if($punishmentArray[hourType] == 'serviceHours') {
						echo "Service";
					} else if($punishmentArray[hourType] == 'NULL') {
						echo "N/A";
					} else {
						echo "ERROR";
					}
					
				echo "</td>";
				echo "<td id=\"suspension$punishmentArray[ID]\">";
				
					if($punishmentArray[suspension] == 'NULL') {
						echo "None";
					} else if($punishmentArray[suspension] == 'financial') {
						echo "Financial";
					} else if($punishmentArray[suspension] == 'none') {
						echo "None";
					} else if($punishmentArray[suspension] == 'social') {
						echo "Social";
					} else {
						echo "ERROR";
					}
					
				echo "</td>";
				echo "<td>";
				
					if($punishmentArray[expel]) {
						echo "Yes";
					} else {
						echo "No";
					}
					
				echo '</td>';
				echo '<td>';
				echo '<form action="'. $_SERVER['PHP_SELF'] .'" method="post">';
					echo '<input type="hidden" name="id" value="'.$punishmentArray[ID].'" />';
					echo '<input type="hidden" name="action" value="remove" />';
					echo "<input id=\"$punishmentArray[ID]\" class=\"edit\" type=\"submit\" value=\"Remove\" />";
				echo '</form>';
				echo '</td>';
				
				echo '</tr>';
			}
			echo '<tr><td>&nbsp;</td></tr>';
			echo '<tr id="'.$categoryArray[code].'Row"></tr>';
			
			echo '</table>';
			
			echo '<div id="'.$categoryArray[code].'Add" class="newItem"><a id="'.$categoryArray[code].'" class="add" href="#">Add New Item</a></div>';
		}
	?>
	<div id="generalPopup">
		<div id="popupClose"><a href="#">x</a></div>
		<div id="popupBody">Body</div>
	</div>
	
	<div id="backgroundPopup"></div>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>