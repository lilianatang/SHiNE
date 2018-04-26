/*----------------------------------------------------------------
* This document contains the javascript & jquery for family calendar
* specific functions.
* 
* Author: Komal 
* -----------------------------------------------------------------*/


/* These functions are declared outside ready for access by other files */

var fillModal;
var create_event;

jQuery(document).ready(function($){
	
	var facilitator_data;
	//get facilitator data so it doesn't have to be queried multiple times
	$.post("get_facilitator_data", function(data)
	{

		// Take the data from the database for this family, and store it in an array
		facilitator_data = data.split(",");

		// Get rid of the empty entry at the end
		facilitator_data.pop();

	});
	

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
		
		// Only display the event if it is today or in the future 
		if (new Date(current_date()) > new Date(event_data['date']))
		{
			return null;
		}

		/* Indicate how many facilitators have signed up for the given slot */
		var slot_info;
		var f_needed = parseInt(event_data['facilitators_needed']);

		if (parseInt(event_data['facilitators_signed_up']) >= f_needed ){
			slot_info = "SLOT FULL - sign up at your own discretion\n\n";
		}
		else {
			slot_info = event_data['facilitators_needed'] - event_data['facilitators_signed_up'] + " positions available\n\n";
		}

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

		
		var is_fieldtrip = parseInt(event_data['is_fieldtrip']);

		if (is_fieldtrip){
	
			/* Create a new event node to add to the calendar html file */
			return 	$(" <li class='single-event' slot-id= " + event_data['slot_id'] + " data-start= '" + event_data['time_start'] + "' data-end='" + event_data['time_end'] + 
				"' data-content='load_sign_up_fieldtrip' data-event='event-" + event_data['class'] + 
				"'><a href='#0'> <em class='event-name'> Fieldtrip </em> <br> <strong class = 'positions'>" +
				 slot_info +"</strong> </a> </li>");
		}

		else {
			/* Create a new event node to add to the calendar html file */
			return 	$(" <li class='single-event' slot-id= " + event_data['slot_id'] + " data-start= '" + event_data['time_start'] + "' data-end='" + event_data['time_end'] + 
				"' data-content='load_sign_up' data-event='event-" + event_data['class'] + 
				"'><a href='#0'> <em class='event-name'> Facilitation Slot </em> <br> <strong class = 'positions'>" +
				 slot_info +"</strong> </a> </li>");
		}

	}

	// Retrieve the current date in string format 
	// source: https://stackoverflow.com/questions/1531093/how-do-i-get-the-current-date-in-javascript
	var current_date = function()
	{

		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();

		if(dd<10) {
		    dd = '0'+dd - 1;
		} 

		if(mm<10) {
		    mm = '0'+mm;
		} 

		today = mm + '/' + dd + '/' + yyyy;
		return today;

	}

	/* -------------------------------------------------------------------------------
	* fillModal
	* This method edits the contents of the file loaded into the modal (family-sign-up.html) 
	* to cater to whichever family is logged in
	*
	* Parameters & login: None
	*-----------------------------------------------------------------------------*/
	fillModal = function(slot_id, event_element){
		
		// Get the fieldtrip information from the database and display it 
		$.post('get_fieldtrip_info', { s_id: slot_id }, function (data) 
		{

			// Parse data 0 -> location 1-> description 2-> full_description
			data = data.split(',');


			event_element.modalBody.find('#location').text(data[0]);

			if (is_full(event_element))
			{
				event_element.modalBody.find('#description').text(data[2]); // display description for when the slot is full
			}
			else
			{
				event_element.modalBody.find('#description').text(data[1]); // display description for when the slot is not full 
			}

		});


		// Find the insertion point in the document
		var select_facilitator = event_element.modalBody.find("#select-facilitator");
		
		// Clear any facilitator data that may be present already
		select_facilitator.html("");

		// Go through all facilitators from the query and add them as a drop-down option
		for (var i = 0; i < facilitator_data.length; i += 2){
			
			// Create an option for a facilitator 
			var $selection = $(" <option value = " + facilitator_data[i] + ">" + 
				facilitator_data[i+1] + "</option>" );
			
			// Add the option to the form 
			$selection.appendTo(select_facilitator);
			
		}
		
		/* Creates an action event to send data from the form to the database */
		$("#submit-booking").click(function (event) {
			
			/* Prevent the page from reloading */			
			event.preventDefault();
			
			if (event_element.processingBooking === false) {
				
				// Indicate that a booking is currently being processed 
				event_element.processingBooking = true;
			
				/* Slot id is already stored in slot_id */
				
				/* Get notes */
				var notes = "";
				notes = notes + $('#comments').val();

				
				/* Get facilitator id */
				var facilitator_id = $("#select-facilitator").val();
				
				/* Initiate query to update the database */
				$.post("book_facilitation", { s_id: slot_id, comments: notes, f_id : facilitator_id }, function (data) 
					{ 
						/* Close the modal window */
						event_element.closeModal(event_element.eventsGroup.find('.selected-event'));
						
						if (data.includes("unsuccessful"))
						{
							create_alert(data, "red");
						}
						else
						{
							create_alert(data, "green");
						}


						/* Update calendar */
						updateCalendar();
						
						// Indicate that a booking is no longer being processed
						event_element.processingBooking = false;
					}
				); 
			}
		});
			
		
	}
 
 	var is_full = function (event_element)
 	{
 		return event_element.modalHeader.find('.positions').text().includes('SLOT FULL');
 	}
 
 
});