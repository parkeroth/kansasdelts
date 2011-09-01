<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'recruitment');
include_once('classes/RecruitManager.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	if($_POST[action] == 'remove'){	
		$recruit_manager = new RecruitManager($mysqli);
		$recruit_manager->remove_recruit($_POST[id]);
	}
	
}

include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="css/styles.css" rel="stylesheet" />

<link type="text/css" href="/styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="/styles/popUp.css" rel="stylesheet" />

<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="/js/popup.js"></script>
<script type="text/javascript" src="/js/jquery.actual.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	
	$(".view").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('popups/view.php?ID=' + id, function(data){
			$("#popupBody").html(data);
			
			$(".closeWindow").click(function(){
				disablePopup('#generalPopup');
			});
		});
		
		//$('#generalPopup').css('width', '420px');
		
		//centering with css
		centerPopup('#generalPopup');
		//load popup
		loadPopup('#generalPopup');
	});
	
	$(".assign").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('popups/assign.php?ID=' + id, function(data){
			$("#popupBody").html(data);
		});
		
		//$('#generalPopup').css('width', '420px');
		
		//centering with css
		centerPopup('#generalPopup');
		//load popup
		loadPopup('#generalPopup');
	});
	
	$(".removeForm").submit(function(){
		var name = $(this).attr('id');
		return confirm('Are you sure you want to remove ' + name + '?');
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

<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		$recruit_manager = new RecruitManager($mysqli);
		
		$recruitList = $recruit_manager->get_new_recruits();
?>
		
	<h1 style="text-align:center">New Recruits</h1>
	<?php
		echo '<table cellspacing="0" align="center">';
		
		$rowColor = "white";
		$count = 0;
		
		echo '<tr class="tableHeader"><td>Name</td><td>Current School</td><td> </td><td> </td><td> </td></tr>';
		
		foreach($recruitList as $recruit){
			
			echo "<tr class=\"$rowColor\">";
			echo "<td>$recruit->firstName $recruit->lastName</td>";
			echo "<td>$recruit->currentSchool</td>";
			echo '<td><input class="view" id="'.$recruit->id.'" type="button" value="View" /></td>';
			echo '<td><input class="assign" id="'.$recruit->id.'" type="button" value="Assign" /></td>';
			echo '<td>'; ?>
            <form id="<?php echo $recruit->firstName; ?>" 
            	  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="removeForm">
            	<input type="hidden" name="id" value="<?php echo $recruit->id; ?>" />
                <input type="hidden" name="action" value="remove" />
				<input type="submit" value="Remove" />
            </form>
			<?php echo '</td></tr>';
			
			$count++;
			
			if($rowColor == "white"){
				$rowColor = "black";
			}
			else
			{
				$rowColor = "white";
			}
		}
		
		if($count == 0){
			echo '<tr><td colspan="5" style="text-align: center;">No New Recruits</td></tr>';
		}
		
		echo "</table>"
	?>
	<div id="generalPopup">
		<div id="popupBody">Body</div>
	</div>
	
	<div id="backgroundPopup"></div>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>