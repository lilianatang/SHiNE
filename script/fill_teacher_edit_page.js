/**
* on_fac_select populates all html elements on the teacher account edit page
*
* Params & Return: none
*
*/

/**
* gets teacher info and puts it into the input boxes
*
*/
function on_teach_select()
{

	var teachID = $('#teacher-selection').val();
	$.post("get_teacher_info", {t_id: teachID} ,function (data) 
	{
		var teacher = data;

		var teacher_info = data.split("-");

		//find all html elements needed
		var fname = $("#f_name");
		var lname = $("#l_name");

		fname.val(teacher_info[0]);
		lname.val(teacher_info[1]);
		$("#class-select").val(teacher_info[2]);

	} );//end of post
} // end of on_select function

// This bit of code enables or disables the password text boxes based on whether the teacher password should be updated
function on_pass_update()
{

	var is_disabled = $('#password').prop("disabled");

	if (is_disabled)
	{
		$('#password').attr("disabled", false);
		$('#passwordcon').attr("disabled", false);
	}
	else
	{
		$('#password').attr("disabled", true);
		$('#passwordcon').attr("disabled", true);
	}
}