/**
* on_fac_select populates all html elements on the facilitator account edit page
*
* Params & Return: none
*
*/


/**
* gets facilitator info and puts it into the input boxes
*
*/
function on_fac_select()
{
	var facID = $('#facilitator-selection').val();

	$.post("get_fac_info", {f_id: facID} ,function (data) 
	{

		var facilitator = data;

		//splits facilitator info
		var facilitator_info = data.split("-");

		//find all html elements needed
		var fname = $("#f_name");
		var lname = $("#l_name");
		var phone = $("#p_num");
		var email = $("#email");
		var address = $("#address");

		fname.val(facilitator_info[0]);
		lname.val(facilitator_info[1]);
		phone.val(facilitator_info[4]);
		email.val(facilitator_info[2]);
		address.val(facilitator_info[3]);

	} );//end of post
} // end of on_select function