//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
	$("#filterCommunityService").change(function() {
		$("a.communityServiceFilter").toggle();
		
	});
	
	$("#filterMemberEducation").change(function() {
		$("a.educationFilter").toggle();
		
	});
	
	$("#filterSocial").change(function() {
		$("a.socialFilter").toggle();
		
	});

	
});

