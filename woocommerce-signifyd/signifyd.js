jQuery(document).ready( function($) {

	var post_id = $("#signifyd_request").attr("post_id");

	var data1 = {
		action: 'get_score',
		postid: post_id,
	};
	
	$.post(the_ajax_script.ajaxurl, data1, function(response1) {
		if (response1 !== '' && !isNaN(response1)) {
			$("#signifyd_score").text(Math.floor(response1)) ;
			$("#signifyd_score").css('textDecoration','none');
			$("#signifyd_score").css('font-size','40px');
			if (response1 >= 500) {
			    $("#signifyd_score").css("color","green");
				$("#signifyd_recommendation").text("Approve Transaction");
			} else if (response1 < 500 && response1 > 300){
			    $("#signifyd_score").css("color","yellow");
				$("#signifyd_recommendation").text("Review Transaction");
			} else {
			    $("#signifyd_score").css("color","red");	
				$("#signifyd_recommendation").text("Reject Transaction");				
			}
			//$("#signifyd_request").attr('disabled','disabled');
		} else {
			$("#signifyd_score").text('N/A') ;
			$("#signifyd_score").css('textDecoration','none');
			$("#signifyd_score").css('font-size','40px');
			$("#signifyd_recommendation").text("Score Not Calculated");
		}
	}); 

});