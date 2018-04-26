/*----------------------------------------------------------------
* This document contains the javascript & jquery for admin calendar
* specific functions.
* 
* Author: Komal 
* -----------------------------------------------------------------*/


/* These functions are declared outside ready for access by other files */

var fillModal;
var create_event;

jQuery(document).ready(function($){

	/*------------------------------------------------------------------------------------------------------------------
	* create_event
	* This method creates a new DOM element including event data to be inserted into the DOM by the caller
	*
	* Parameters: event_data: An event object with the following fields: 
	*             slot_id - unique slot id in the database 
	*			  classroom_id - unique classroom id 
	*			  date -  the date the event applies to (SQL format)
	*			  time_start & time-end - a date-time string (SQL format)
	*			  facilitators_needed - number of facilitators needed 
	*			  facilitators_signed_up - the facilitators signed up already
	*             is_fieldtrip 
	*             classroom id 
	*
	* Return: A new jQuery element created to be inserted into the calendar 
	*-------------------------------------------------------------------------------------------------------------------*/
	create_event = function(event_data) {
		
		/* Indicate how many facilitators have signed up for the given slot */
		var slot_info;
		slot_info = event_data['facilitators_needed'] + " positions\n\n";
		
		var is_fieldtrip = parseInt(event_data['is_fieldtrip']);

		var f_needed = parseInt(event_data['facilitators_needed']);

		// List facilitators in the modal
		$.post("getFacilitatorsSignedUp", { s_id: event_data['slot_id'] }, function (data){


			var facilitator_info = data.split(",");
			facilitator_info.pop();
			
			// Add each to the list 
			for (var i = 0; i < facilitator_info.length; i ++){

				if (i + 1 > f_needed)
				{
					slot_info = slot_info + facilitator_info[i] + "(EXTRA) \n";
				}
				else {
					slot_info = slot_info + facilitator_info[i] + "\n";
				}
			}

			if (facilitator_info.length > 0){
				$('[slot-id = "' + event_data['slot_id'] + '"]').find(".positions").text(slot_info);
			}
		});

		if (is_fieldtrip){
	
			return 	$(" <li class='single-event' slot-id= " + event_data['slot_id'] + " data-start= '" + event_data['time_start'] + "' data-end='" + event_data['time_end'] + 
			"' data-content='calendar_modal_edit_remove_fieldtrip' data-event='event-" + event_data['class'] + 
			"'><a href='#0'> <em class='event-name'> Fieldtrip </em> <br> <strong class = 'positions'>" +
			 slot_info +"</strong> </a> </li>");

		}

		else {
		/* Create a new event node to add to the calendar html file */
		return 	$(" <li class='single-event' slot-id= " + event_data['slot_id'] + " data-start= '" + event_data['time_start'] + "' data-end='" + event_data['time_end'] + 
			"' data-content='calendar_modal_edit_remove' data-event='event-" + event_data['class'] + 
			"'><a href='#0'> <em class='event-name'> Facilitation Slot </em> <br> <strong class = 'positions'>" +
			 slot_info +"</strong> </a> </li>");
		}
		
	}

	/* -------------------------------------------------------------------------------
	* fillModal
	* This method edits the contents of the file loaded into the modal (family-sign-up.html) 
	* to cater to whichever family is logged in
	*
	* Parameters & login: None
	*-----------------------------------------------------------------------------*/
	fillModal = function(slot_id, event_element){

		// If the slot is a fieldtrip, fill the fiildtrip comonents fo the field
		if (event_element.modalHeader.find('.event-name').text().includes('Fieldtrip'))
		{

			$.post('get_fieldtrip_info', {s_id: slot_id}, function (data) {

				var info = data.split(',');
				event_element.modalBody.find("[name= 'desc' ]").val(info[1]);
				event_element.modalBody.find("[name= 'full_desc' ]").val(info[2]);
				event_element.modalBody.find("[name= 'location' ]").val(info[0]);
			});

		}
		
		/* This bit of code finds the start time, end time, and faciliators needed for a given slot and updates the form fields with default values */

		// Get start time and end time 
		var time_range = event_element.modalHeader.find('.event-date').text(); // Now holds a string with start_time - end_time (ex. 08:45 - 12:00)
		time_range = time_range.replace(/\s+/g, ''); // got from: https://stackoverflow.com/questions/5963182/how-to-remove-spaces-from-a-string-using-javascript
		time_range = time_range.split("-");
		var start_time = time_range[0];
		var end_time = time_range[1];

		// Retrieve number of facilitators needed
		var facilitator_message = event_element.modalHeader.find('.positions').text(); // Holds the number of facilitators signed up (Ex. '5 facilitators currently signed up')
		num_facilitators = facilitator_message.split(" ")[0];
		
		// Make sure the value we got is indeed a number (just safety precautions)
		if (isNaN(num_facilitators))
		{
			num_facilitators = 0;
		}
		else 
		{
			num_facilitators = parseInt(num_facilitators);
		}

		// Update the form fields!
		event_element.modalBody.find("[name= 'start_time' ]").val(start_time + ":00");
		event_element.modalBody.find("[name= 'end_time' ]").val(end_time + ":00");
		event_element.modalBody.find("[name= 'num_facilitators' ]").val(num_facilitators);
		event_element.modalBody.find("[name= 'slot_id' ]").val(slot_id);
	
		// Create confirmation form when the person clicks delete
		event_element.modalBody.find("[name= 'delete' ]").click(function() {

			//load the confirmation form and the slot_id for deletion purposes
			event_element.modalBody.find('.event-info').load( 'delete_single_slot_confirmation', function (data) {
				
				event_element.modalBody.find("[name= 'slot_id' ]").val(slot_id);
		
			});

		});

	}
 
 
});