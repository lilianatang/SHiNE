

/*
* function is populates facililators based on family selection
*/

function on_select(){

	$('#punch_in').attr('disabled', true); 
	$('#punch_out').attr('disabled', true);

	//clear selection list
	$("#facilitator-selection").empty();

	$("#facilitator-selection").append("<option disabled selected value>Select A Facilitator</option>");

	// Gets the user ID from family select
	var userID = $('#family-selection').val();

	/*
	* updates the facilitator selection based on family selected value.
	*
	*/
	$.post("get_facilitators_name_ids", {u_id: userID} ,function (data) 
	{
		//gets facilitators based on family_id
		var facilitators = data.split(",");
		facilitators.pop();

		//find selection tag
		f_list = $("#facilitator-selection");

		// goes through array of facilitators and their ids. seperates the facilitator from id. 
		// Creates variable html to add to selection list. appends that variable at end.
		for (var i = 0; i < facilitators.length; i ++){

			//splits facilitators into id and name
			var facilitator_info = facilitators[i].split("-");


			// Create a new facilitator to add to the selection
			var add_facilitator = $("<option value = " + facilitator_info[1] + ">" + facilitator_info[0]   + "</option>");
					
			// Add facilitator name to facilitator selection list
			f_list.append(add_facilitator);
		}


	} );


} // end of on_select function
