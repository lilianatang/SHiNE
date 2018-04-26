/*--------------------------------------------------------------------------------------------------------
* This file containers JS and JQ for interacting with the adminFamilyStatistics URI
*
* Author Wesley
* Modified by : Joe
*--------------------------------------------------------------------------------------------------------*/

//Sets the month dropdown and the year drop down to current date
$( document ).ready(function() {
 	var d = new Date();
	$("#month-selection").val( d.getMonth() + 1 );
	$("#year-selection").val( d.getFullYear() );
});

/*-------------------------------------------------------------------GATHER HISTORY DATA ------------------------------------------------------------*/


/* This method updates the yearly total display */
function update_yearly_total(userID, month, year)
{
	// Update yearly hours
	$.post("get_yearly_hours", {u_id: userID, m: month, y: year}, function (data) {

		year_span = $('#yearly-total');
		year_span.empty();
		year_span.append(data);

	});
}

/* This method updates the monthly total display */
function update_monthly_total(userID, month, year)
{
	$.post("get_monthly_hours", {u_id: userID, m: month, y: year}, function (data) {

		month_span = $('#monthly-total');
		month_span.empty();
		month_span.append(data);

	});
}

// This method updates the facilitator display
function update_facilitators(userID)
{
	$("#Facilitators-list").empty();
	/*
	* updates the family information facilitator list based on family selected value.
	*/
	$.post("get_facilitators", {u_id: userID} ,function (data) 
		{
			//gets facilitators based on family_id
			var facilitators = data.split(",");
			facilitators.pop();

			//find un-ordered list tag
			f_list = $("#Facilitators-list");

			for (var i = 0; i < facilitators.length; i ++){

				// Create a new facilitator to add to info list
				var add_facilitator = $("<li>" + facilitators[i] + "</li>");
					
				// Add facilitator name to info list 
				f_list.append(add_facilitator);
			}

		}
	);
}

// This method updates the students display
function update_students(userID)
{
	$("#Students-list").empty();
	/*
	* updates the family information student list based on family select value.
	*/
	$.post("get_students", {u_id: userID}, function (data) 
		{
			//gets familitators based on family_id
			var students = data.split(",");
			students.pop();

			//find un-ordered list tag
			s_list = $("#Students-list");

			for (var i = 0; i < students.length; i ++){

				// Create a new facilitator to add to info list
				var add_student = $("<li>" + students[i] + "</li>");
					
				// Add facilitator name to info list 
				s_list.append(add_student);
			}

		//end of callback function for populating family info -> students
		}
	// end of post for populating family info -> students
	);
}

// This method is called when the user clicks submit on the admin stats page 
function submit_button() {

	requirement_data = [];
	$("#main-info-container").fadeOut(10);
	$("#jsGrid").fadeOut(10);

	// Gets data from the form
	var userID = $('#family-selection').val();
	var month_selector = $('#month-selection').val();
	var year_selector = $('#year-selection').val();
	
	// Update family information
	update_yearly_total(userID, month_selector, year_selector);
	update_monthly_total(userID, month_selector, year_selector);
	update_facilitators(userID);
	update_students(userID);

	$.post("get_history", {u_id: userID, month: month_selector, year: year_selector}, function (data) 
	{
		/*Find the Table selector*/
		var table_selector = $("#stats-table");

		/*extract weekly data -> creates array of strings with weekly data information inside in string*/
		var weekly_data = data.split("~");
		weekly_data.pop();
		
		/*Go through the weekly data retrieved from the database and populate html table*/
		for (var i = 0; i < weekly_data.length; i ++)
		{
			// Extract weekyly info
			weekly_info = weekly_data[i].split(",");

			// net hours - completed hours + recieved hours - given hours
			var nethours = parseFloat(weekly_info[3]) + parseFloat(weekly_info[5]) - parseFloat(weekly_info[4]);
			
			//puts data into global holder
			if ((parseFloat(weekly_info[3]) + parseFloat(weekly_info[5])) >= parseFloat(weekly_info[2]) )// required = 2 , completed = 3
			{
				requirement_data.push({"Week": weekly_info[0] + " to " + weekly_info[1], "Completed Hours" : weekly_info[3], "Required Hours": weekly_info[2], "Hours Given": weekly_info[4],
					"Hours Received": weekly_info[5], "Net Hours": nethours, "Requirements Met": "&#10003;", editable: true, id: weekly_info[6]});
			}
			else
			{
				requirement_data.push({"Week": weekly_info[0] + " to " + weekly_info[1], "Completed Hours" : weekly_info[3], "Required Hours": weekly_info[2], "Hours Given": weekly_info[4],
					"Hours Received": weekly_info[5], "Net Hours": nethours, "Requirements Met": "&#10005;", editable: true, id: weekly_info[6]});
			}

		}
		
		// Update/create the grid
  		initGrid();

  		$("#main-info-container").fadeIn();
  		$("#main-info-container").css("display", "inline-block");

	}); // end post
} // end submit button function

/*---------------------------------------------------------------------JSGRID----------------------------------------------------------------------------*/

var requirement_data = [];
create_decimal_field();

// This creates a validation tool for donation hours
jsGrid.validators.donation = { 
  message: "The amount donated must be less than or equal to the net hours earned.", 

  validator: function(value, item) { 
    
    if ( ( !isNaN(value) && parseFloat(value) >= 0 ) && value <= item['Net Hours']) 
    {
      return true;
    }

    return false;
  }

}; 

// This is a validation tool for hours to ensure the input is >= 0
jsGrid.validators.hours = {

	message: "Invalid input.", 

  validator: function(value, item) { 
    
    if ( ( !isNaN(value) && parseFloat(value) >= 0 )) 
    {
      return true;
    }

    return false;
  }
}

// This method creates the grid 
var initGrid = function()
{
	$("#jsGrid").jsGrid({

		width: "1000px",

		editing: true,
		paging: false,

		//insert data
		data: requirement_data,

		// Create columns
		fields: 
		[
			{name: "Week", type: "text", width: 30, validate: "required", readOnly: true},
			{name: "Completed Hours", type: "decimal", width: 10, validate: "required", validate: "hours"},
			{name: "Required Hours", type: "decimal", width: 10, validate: "required", validate: "hours"},
			{name: "Hours Given", type: "decimal", width: 10, validate: "required", validate: "donation"},
			{name: "Hours Received", type: "decimal", width: 10, validate: "required", validate: "hours"},
			{name: "Net Hours", type: "decimal", width: 10, validate: "required", readOnly: true, validate: "hours"},
			{name: "Requirements Met", type: "text", width: 10, readOnly: true},
			{type: "control", width: 10, 
				itemTemplate: function(value,item) 
				{
					var $result = $([]);

					if(item.editable)
					{
						$result = $result.add(this._createEditButton(item));
					}

					return $result
				}
			}
		],

		// Update the database when a user edits an item
		onItemUpdated: function(args)
		{
			var updated_item = args.item;

			$.post("update_history", {hours_given: updated_item['Hours Given'], hours_received: updated_item['Hours Received'], hours_completed: updated_item['Completed Hours'], 
					hours_required: updated_item['Required Hours'], id: updated_item['id'] });

			/* UPDATE THE DISPLAY */

			updated_item['Net Hours'] = parseFloat(updated_item['Completed Hours']) + parseFloat(updated_item["Hours Received"]) - parseFloat(updated_item["Hours Given"]);
			
			if (parseFloat(updated_item['Net Hours']) >= updated_item['Required Hours'])
			{
				updated_item['Requirements Met'] = "&#10003;";
			}
			else
			{
				updated_item['Requirements Met'] = "&#10005;";
			}

			$("#jsGrid").jsGrid("refresh"); // Translates changes above to the grid

			// Update family information
			var userID = $('#family-selection').val();
			var month = $('#month-selection').val();
			var year = $('#year-selection').val();

			update_monthly_total(userID, month, year);
			update_yearly_total(userID, month, year);

		}


	});

       $("#jsGrid").fadeIn("slow");
	   $("#jsGrid").css("display", "inline-block");
}

// This bit of code allows for the creation of a float number type 
function create_decimal_field()
{

    function DecimalField(config) {
            jsGrid.fields.number.call(this, config);
    }

    DecimalField.prototype = new jsGrid.fields.number({

        filterValue: function() {
            return this.filterControl.val()
                ? parseFloat(this.filterControl.val() || 0, 10)
                : undefined;
        },

        insertValue: function() {
            return this.insertControl.val()
                ? parseFloat(this.insertControl.val() || 0, 10)
                : undefined;
        },

        editValue: function() {
            return this.editControl.val()
                ? parseFloat(this.editControl.val() || 0, 10)
                : undefined;
        }
    });

    jsGrid.fields.decimal = jsGrid.DecimalField = DecimalField;

}
