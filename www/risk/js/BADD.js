/***************************/
//@Author: Adrian "yEnS" Mato Gondelle
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

//SETTING UP OUR POPUP
//0 means disabled; 1 means enabled;


//loading popup with jQuery magic!
function loadPopup(id){
	//loads popup only if it is disabled
	
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$(id).fadeIn("slow");
	
}

//disabling popup with jQuery magic!
function disablePopup(id){
	//disables popup only if it is enabled
	
		$("#backgroundPopup").fadeOut("slow");
		$(id).fadeOut("slow");
	
}

//centering popup
function centerPopup(id){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $(id).height();
	var popupWidth = $(id).width();
	
	//centering
	$(id).css({
		"position": "absolute",
		"top": document.body.scrollTop + windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	
	//only need force for IE6
	
	$("#backgroundPopup").css({
		"height": windowHeight
	});
	
}

//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
	$(".tradable").click(function(event) {
		
		var str = event.currentTarget.id;
		var user = str.substr(0,6);
		var dayID = str.substr(6);
		
		var selector = "#userSelect option[value='" + user + "']";
		
		$(selector).attr('selected', 'selected');
		$('#dayID').val ( dayID );
		$('#origPerson').val ( user );
		
		//centering with css
		centerPopup("#popupNotification");
		//load popup
		loadPopup("#popupNotification");
		
	});
	
	$('#popupNotificationClose').click(function(){
		disablePopup('#popupNotification');
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup('#popupNotification');
	});
	
});

