
<!-- This file holds the edit/delete form that will be inserted into the modal window -->

<div class="event-info" style = 'overflow: auto;'>

	<div style = "text-align: center;">

		<?php echo form_open('admin_controller/edit_fieldtrip'); ?>
		<!-- Page Title -->
		<h2 style = "font-size: 16px; color: black;"> Edit/Delete Slot </h2>
		<br>

			<p>
				Start Time:
				<input type = 'time' name = 'start_time'>   </input>				
			</p>
			<br>
			<p>
				End Time: 
				<input type = 'time' name = 'end_time'>   </input>
			</p>
			<br>
			<p>
				Number of Facilitators: 
				<input type = "number" name = 'num_facilitators' min = '0' style = 'width: 100px;'> </input>
			</p>
			<br>
			
			<p>
			Location:
			<input type = 'text' name = 'location'>   </input>				
			</p>

			<br>

			<p> Description: </p>
			<textarea name = 'desc' style = "width: 400px; height: 200;"></textarea>

			<br>
			<p> Description for slot full: </p>
			<textarea name = 'full_desc' style = "width: 400px; height: 200; overflow: auto;"></textarea>

			<br>

			<!-- This field is hidden as the admin doesn't need to change it, but the form needs to access the slot id -->
			<input  type = 'number' name = 'slot_id' style = 'visibility: hidden; height: 2px;'>   </input>  

			<div style = 'text-align: center; margin-top: 0px;'>
				<button> Submit Changes </button>
			</div>

		<?php echo form_close(); ?>	

				<button style = 'font-weight: bold; margin-top: 10px;' name = 'delete'> DELETE THIS SLOT </button>
	</div>
</div>	
			
				
