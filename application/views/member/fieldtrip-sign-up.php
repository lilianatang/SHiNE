<!--------------------------------------------------------------------------------------------------------
* This file holds the facilitation sign up form that will be inserted into the modal
*
* Author: Komal 
*-------------------------------------------------------------------------------------------------------->

<div class="event-info">

	<form id = "facilitation-sign-up" style = "text-align: center; margin: 6%; ">
		
		<!-- Page Title -->
		<h2 style = "size: 15px; font-size: 35px; color: black; "> Book Fieldtrip </h2>
		<br>
		
		<p>
		<label style = "color: black;"> Location: </label>
		<span id = 'location'> </span>
		</p>
		<br>

		<p>
		<label style = "color: black;"> Fieldtrip Description: </label>
		<p id = 'description'> </p>
	</p>

		<!-- Facilitator Selection -->
		<div style = " margin: 20px 0px 20px 0px;">
			<label style = "color: black;">Select a facilitator: </label>
			<br>
			<select	id = "select-facilitator" style = "font-size: 20px; margin: 10px 50px 10px 50px;">
			
				<!-- This will be populated with facilitators to select when the user clicks a modal and a family id is given -->
				
			</select>
			
		</div>
		
		<!-- A spot for comments -->
		<label style = "color: black;" >Add additional comments (optional): </label>
		<textarea id = "comments" style = "width: 300px; height: 500; margin: 10px 20px 10px 20px;"></textarea>
		
		<!-- Submit button -->
		<button 
			id = "submit-booking"
			type = "Submit"
			style = "font-size: 12px;
			margin: 20px 50px 30px 50px;"
		>
			Book Facilitation Time
		</button>
		
	</form>
</div>	