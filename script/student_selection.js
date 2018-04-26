
/*
* function is populates students based on family selection
*/

function on_select(){
	//clear selection list
	$("#student-selection").empty();
	$("#student-selection").append("<option disabled selected value>Select A Student</option>");

	// Gets the user ID from family select
	var userID = $('#family-selection').val();

	/*
	* updates the facilitator selection based on family selected value.
	*/
	$.post("get_students_name_ids", {u_id: userID} ,function (data) 
	{
		//gets facilitators based on family_id
		var students = data.split(",");
		students.pop();
		if(students.length > 0)
		{
			//find selection tag
			s_list = $("#student-selection");

			// goes through array of facilitators and their ids. seperates the facilitator from id. 
			// Creates variable html to add to selection list. appends that variable at end.
			for (var i = 0; i < students.length; i ++){

				//splits facilitators into id and name
				var student_info = students[i].split("-");


				// Create a new facilitator to add to the selection
				var add_student = $("<option value = " + student_info[1] + ">" + student_info[0]   + "</option>");
						
				// Add facilitator name to facilitator selection list
				s_list.append(add_student);
			}
		}
	} ); // post end


} // end of on_select function