/* This document handles all punch in and punch out functionality */


// This is called when a user clicks punch in
function punch_in()
{
	facilitator_id = $("#facilitator-selection").val();

	// Update db
	$.post("punch_in", {fac_id: facilitator_id }, function(data) {   
		
		// Display message
		if (data.includes("unsuccessful"))
		{
			$("#user-message").text(data).css("color", "red");	
		}
		else
		{
			$("#user-message").text(data).css("color", "green");
		}

	}) ;

}

// This is called when a user clicks punch out
function punch_out()
{
	facilitator_id = $("#facilitator-selection").val();
	family_id = $("#family-selection").val();

	// Update db
	$.post("punch_out", {fac_id: facilitator_id, fam_id: family_id }, function(data) {   
		
		// Display message
		if (data.includes("unsuccessful"))
		{
			$("#user-message").text(data).css("color", "red");	
		}
		else
		{
			$("#user-message").text(data).css("color", "green");
		}

	}) ;
}