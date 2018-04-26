
<h1 style = "text-align: center"> Fieldtrip Creation</h1>

<div style = "text-align: center":>

	<?php echo validation_errors('<p class="error" style = "color: red; ">'); echo $message; ?>
	<?php echo form_open('admin_controller/create_fieldtrip'); ?> 
	
		<p class="spacing"style="font-weight: bold">Step #1: Select a Date: </p>
		<p class="spacing"> Date: <input type="date" name="date" /> </p>
		
		<p class="spacing" style="font-weight: bold">Step #2: Select Facilitation Timings: </p>
		
		<p class="spacing"> Start Time:   <input type="time" name="start_time" /> </p>
		
		<p class="spacing"> End Time:   <input type="time" name="end_time" /> </p>
		
		<p class="spacing" style="font-weight: bold">Step #3: Select Number of Facilitators: </p>
		
		<p class="spacing"> Number of Facilitators: <input type="number" name="num_facilitators" min = '0' style="width: 50px"  /> </p>
		
		<p class="spacing" style="font-weight: bold">Step #4: Enter Fieldtrip Location: </p>
		
		<p class="spacing"> Location: <input type = 'text' name = 'location' style = "width: 250px;" />  </p> 
		
		<p class="spacing" style="font-weight: bold">Step #5: Enter Fieldtrip Description: </p>
		
		<p class="spacing"> Description: (Include Details Such as Parental Role) </p>
		<textarea name = 'desc' style = "width: 400px; height: 200;"></textarea>

		<p class="spacing" style="font-weight: bold">Step #6: Enter Fieldtrip Full Description: </p>
		
		<p class="spacing"> Description: This Description Will be Displayed Should a Fieldtrip be Fully Booked. <br> Include Details Such as Additional Fees. </p>
		<textarea name = 'full_desc' style = "width: 400px; height: 200;"></textarea>
		
		<p class="spacing" style="font-weight: bold">Step #7: Select a Classroom: 
			<select class="section_bar_sizing" style = "font-weight: normal" name="classroom_id" >
					<option disabled selected value>Select A Classroom</option>
					<option value = '*'> All Classrooms </option>
					<?php  
						foreach ( $classroom_data as $value ){
							echo "<option value=" . $value['classroom_id'] . ">" . 	$value['class_color'] . "</option>";
						}  
					?> 
			</select>
		</p>

		<input type = 'checkbox' name = 'confirmation' value = 'agree'>  I understand that by creating this fieldtrip, all existing time slots with time conflicts will be removed.  

		<br>

		<input style = "margin-bottom: 100px" class = "spacing" type="submit" value="Submit" name="Submit" />


</div>

</body>
</html>