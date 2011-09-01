<?php
session_start();
include_once('../php/login.php');
$authUsers = array('admin', 'recruitment', 'recruitCom');
include_once('../php/authenticate.php');
include_once('classes/Recruit.php');
include_once('util.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 

if(strpos($session->accountType, "admin") || strpos($session->accountType, "recruitment")) {
	$super = true;
} else {
	$super = false;
}

$id =mysql_real_escape_string( $_GET[ID]);
$recruit = new Recruit($mysqli,$id);

$recruitQuery = "
	SELECT 	r.firstName as recruitFirst, 
			r.lastName as recruitLast,
			s.status as status,
			referredBy,
			m.firstName as ownerFirst,
			m.lastName as ownerLast
			
	FROM recruits r
	JOIN recruitStatus s
	ON r.status = s.ID
	JOIN members m 
	ON r.primaryContact = m.username
	WHERE r.ID = '$id'"; //echo $recruitQuery;
$getRecruit = mysqli_query($mysqli, $recruitQuery);
$recruitArray = mysqli_fetch_array($getRecruit, MYSQLI_ASSOC);

?>

<link type="text/css" href="css/styles.css" rel="stylesheet" />

<link type="text/css" href="/styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="/styles/popUp.css" rel="stylesheet" />
<link type="text/css" href="css/tabs.css" rel="stylesheet" />

<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="/js/popup.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	$(".editCall").click(function(){
		var id = $(this).attr('id');
		
		$.get('popups/call.php?ID=' + id, function(data){
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
		
	//CLOSING  POPUP
	//Click the x event!
	$('#popupClose').click(function(){
		disablePopup('#generalPopup');
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup('#generalPopup');
	});

	//When page loads...
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content

	//On Click Event
	$("ul.tabs li").click(function() {

		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
	
});

</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div id="details_container">
    <div id="details_name"><?php echo $recruit->getName(); ?></div>
     <ul class="tabs">
        <li><a href="#tab1">Overview</a></li>
        <li><a href="#tab2">Info</a></li>
        <li><a href="#tab3">History</a></li>
    </ul>
    <div class="tab_container">
        <div id="tab1" class="tab_content">
            <div class="tab_left">
                <table class="details" align="center">
                    <tr>
                        <th>Status:</th>
                        <td><?php echo $recruit->getStatus(); ?></td>
                    </tr>
                    <tr>
                        <th>Referred By:</th>
                        <td><?php echo $recruit->referred_by(); ?></td>
                    </tr>
                    <tr>
                        <th>Owner:</th>
                        <td><?php echo $recruit->get_owner(); ?></td>
                    </tr>
                    <tr>
                        <th>Last Called:</th>
                        <td><?php echo $recruit->last_contact_date(); ?></td>
                    </tr>
                    <?php if($recruit->last_contact_date() != 'Never'){ ?>
                    <tr>
                        <th>By:</th>
                        <td><?php echo $recruit->last_contact_name(); ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="tab_right">
            <h2>Outstanding Tasks</h2>
                <table class="details" align="center">
                	<?php 
					$numOutstanding = 0;
					foreach($recruit->get_calls('pending') as $call){
						echo '<tr>';
						echo '<th>Call:</th>';
						echo '<td>'.ucwords($call->type).'</td>';
						echo '<td><input id="'.$call->id.'" class="editCall" value="Details" type="button" /></td>';
						echo '</tr>';
						$numOutstanding++;
					} 
					if(!$numOutstanding){
						echo '<tr><td colspan="3" style="text-align:center">None</td></tr>';
					}
					?>
                   	<tr>
                    	<td></td>
                    </tr>
                </table>
            </div>
            <div class="tab_clear"></div>
        </div>
        <div id="tab2" class="tab_content">
           <div class="tab_left">
                <table class="details" align="left">
                    <tr>
                        <th width="80">Phone:</th>
                        <td><?php echo $recruit->phoneNumber; ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo $recruit->email; ?></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td><?php echo $recruit->address?></td>
                    </tr>
                    <tr>
                        <th>City:</th>
                        <td><?php echo $recruit->city; ?></td>
                    </tr>
                    <tr>
                        <th>State:</th>
                        <td><?php echo $recruit->state; ?></td>
                    </tr>
                    <tr>
                        <th>ZIP:</th>
                        <td><?php echo $recruit->zip; ?></td>
                    </tr>
                </table>
            </div>
            <div class="tab_right">
                <table class="details" align="center">
                    <tr>
                        <th>Current School:</th>
                        <td><?php echo $recruit->currentSchool; ?></td>
                    </tr>
                    <tr>
                        <th>HS Grad Year:</th>
                        <td><?php echo $recruit->hsGradYear; ?></td>
                    </tr>
                    <tr>
                        <th>GPA:</th>
                        <td><?php echo $recruit->gpa?></td>
                    </tr>
                    <tr>
                        <th>ACT Score:</th>
                        <td><?php echo $recruit->actScore; ?></td>
                    </tr>
                    <tr>
                        <th>Major:</th>
                        <td><?php echo $recruit->intendedMajor; ?></td>
                    </tr>
                </table>
            </div>
            <div class="tab_clear">
            	<table class="details" align="left">
                    <tr>
                        <th width="80">Bio:</th>
                        <td><?php echo nl2br($recruit->bio); ?></td>
                    </tr>
                    <tr>
                        <th>Questions:</th>
                        <td><?php echo nl2br($recruit->questions); ?></td>
                    </tr>
                    <tr>
                        <th>Interests:</th>
                        <td><?php echo nl2br($recruit->interests); ?></td>
                    </tr>
                </table>
                <p>&nbsp;</p>
            </div>
        </div>
        <div id="tab3" class="tab_content">
        	<table class="details">
			<?php
                //$calls = array_reverse(sortArrayofObjectByProperty($recruit->get_calls(), 'dateCompleted'));
                $calls = $recruit->get_calls();
                foreach($calls as $call){
                    echo '<tr><th>Call:</th><td colspan="2">'.$call->get_hist_string().'</td></tr>';
					if($call->notes != '' && $call->notes != NULL){
						echo '<tr class="comment">';
						echo '<td> </td>';
						echo '<td width="40">Comments: </td>';
						echo '<td>'.$call->notes.'</td>';
						echo '</tr>';
					}
                }
            ?> 
        	</table>
            <hr />
            <table class="details" align="center">
           		<tr>
                	<th>Add: </th>
                	<td><input type="button" value="Call" /></td>
                    <td><input type="button" value="Visit" /></td>
                    <td><input type="button" value="Dinner" /></td>
                    <td><input type="button" value="Event" /></td>
                </tr>
            </table>	
        </div>
    </div>
        
       
    	
        
    <div id="details_lower">
        <p><a href="recruitList.php">Back to List</a></p>
    </div>
</div>


	<div id="generalPopup">
		<div id="popupBody"></div>
	</div>
	
	<div id="backgroundPopup"></div>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>