/**
* on_fac_select populates all html elements on the student account edit page
*
* Params & Return: none
*
*/


/**
* gets student info and puts it into the input boxes
*
*/
function on_student_select()
{
	var stuID = $('#student-selection').val();
	$.post("get_student_info", {s_id: stuID} ,function (data) 
	{
		var student = data;

		//splits student info=
		var student_info = data.split("-");

			//find all html elements needed
			var fname = $("#f_name");
			var lname = $("#l_name");
			var grade = $("#grade");
			var classroom = $("#class-select");

			fname.val(student_info[0]);
			lname.val(student_info[1]);
			grade.val(student_info[2]);
			classroom.val(student_info[3]);
	} );//end of post
} // end of on_select function