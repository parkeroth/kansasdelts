<?php
session_start();
include_once('../php/login.php');
$authUsers = array('admin', 'recruitment', 'recruitCom');
include_once('../php/authenticate.php');
include_once('classes/Recruit.php');
include_once('classes/RecruitManager.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	if($_POST[action] == 'remove'){	
		$recruit_manager = new RecruitManager($mysqli);
		$recruit_manager->remove_recruit($_POST[id]);
	}
	
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 

if(strpos($session->accountType, "admin") || strpos($session->accountType, "recruitment")) {
	$super = true;
} else {
	$super = false;
}

?>

<link type="text/css" href="css/styles.css" rel="stylesheet" />

<link type="text/css" href="/styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="/styles/popUp.css" rel="stylesheet" />

<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="/js/popup.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	
	$(".view").click(function(){
		window.location = 'recruitDetail.php?ID=' + $(this).attr('id')
	});
	
	$(".invite").click(function(){
		var id = $(this).attr('id');
		
		$.get('popups/invite.php?ID=' + id, function(data){
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

<h1 style="text-align:center;">Master Recruit List</h1>

<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		$recruit_manager = new RecruitManager($mysqli);
		
		foreach($recruit_manager->get_status_list() as $key => $value){
			
			echo '<h2 style="text-align:center;">'.$value.'</h2>';
			
			$recruitList = $recruit_manager->get_recruits_by_status($key);
			
			if(sizeof($recruitList) > 0){
				echo '<table cellspacing="0" align="center">';
				
				$rowColor = "white";
				$count = 1;
				
				echo '<tr class="tableHeader"><td width="120">Name</td><td width="120">Owner</td><td width="100">Last Contact</td><td> </td></tr>';
				
				foreach($recruitList as $recruit){
					$numOutstandingCalls = sizeof($recruit->get_calls('pending'));
					
					if($numOutstandingCalls > 0) {
						$nameClass = 'redHeading';
					} else {
						$nameClass = '';
					}
					
					$num_calls = sizeof($recruit->get_calls())-sizeof($recruit->get_calls('pending'));
					$num_dinners = sizeof($recruit->get_dinners('pending'));
					$num_visits = sizeof($recruit->get_visits('pending'));
					
					echo "<tr class=\"$rowColor recruit\">";
					echo "<td class='$nameClass'><b>$recruit->firstName $recruit->lastName</b></td>";
					echo "<td>".$recruit->get_owner()."</td>";
					echo "<td>".$recruit->last_contact_date()."</td>";
					echo '<td>'; ?>
					<form id="<?php echo $recruit->firstName; ?>" 
                          action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="removeForm">
                        <input class="view" id="<?php echo $recruit->id; ?>" type="button" value="View" />
                    <?php if($super){ ?>
                    	<input class="invite" id="<?php echo $recruit->id; ?>" type="button" value="Invite" />
                        <input type="hidden" name="id" value="<?php echo $recruit->id; ?>" />
                        <input type="hidden" name="action" value="remove" />
                        <input type="submit" value="Remove" />
                    </form>
					<?php }
					echo '</td>';
					echo "</tr>";
					echo "<tr class=\"$rowColor recruit stats\"><td></td>";
					echo "<td colspan=\"5\">";
					echo "Calls: <b>$num_calls</b> | ";
					echo "Dinners: <b>$num_dinners</b> | ";
					echo "Visits: <b>$num_visits</b>";
					echo "</td>";
					echo "</tr>";
							
					$count++;
					
					if($rowColor == "white"){
						$rowColor = "black";
					}
					else
					{
						$rowColor = "white";
					}
				}
			} else {
				echo '<p align="center">No Recruits</p>';
				echo '<p align="center">&nbsp;</p>';
			}
			
			echo "</table>";
		}
		
		
	?>
	<div id="generalPopup">
		<div id="popupBody"></div>
	</div>
	
	<div id="backgroundPopup"></div>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>