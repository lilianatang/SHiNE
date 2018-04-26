// This method allows you to create an error message for any page! 

/*

To use it, you must include the following code to the html/php page. (Preferrably at the top of the page!)

<div class="alert" style = "visibility: hidden;">
 <!-- Alert stuff will go here! -->
</div>

*/

// This method updates or creates a new alert in the div as specified above
var create_alert = function (message, color)
{
	$("<div class='alert' style = 'visibility: hidden;'> </div>").appendTo("body"); //prepend isn't supported by Edge

	$('.alert')
		.append($( "<span class=\"closebtn\" onclick=\"hide_alert()\">&times;</span>"))
		.append( $("<strong>" + message + " </strong>") ).css("visibility", "visible").css("position", "fixed")
		.css("background-color", color);

}

// This method removes the alert when the user clicks the x 
function hide_alert()
{
	$('.alert').remove();
}