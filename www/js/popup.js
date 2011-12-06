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
		"top": 0,
		"left": 240
	});
	//only need force for IE6
	
	$("#backgroundPopup").css({
		"height": windowHeight
	});
	
}

function createPopUp(id, closeID, button){
	
	//LOADING POPUP
	//Click the button event!
	$(button).click(function(){
		//centering with css
		centerPopup(id);
		//load popup
		loadPopup(id);
	});
				
	//CLOSING  POPUP
	//Click the x event!
	$(closeID).click(function(){
		disablePopup(id);
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup(id);
	});
	
}
