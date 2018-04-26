		<h1> My Facilitation Bookings </h1>

		<div class="centered">
			<p class="space" style = 'color: red;'> <?php echo $message; ?> </p>

			<?php echo form_open('family_controller/date_submit'); ?> 
			<p class="spacing" style="font-weight: bold">Select dates to view schedule:</p>
			<p class="spacing"> Start Date: <input class = "date-picker" type="date" name="start_date" value="<?php echo date('Y-m-d'); ?>"/> </p>
			<p class="spacing"> End Date: <input class = "date-picker" type="date" name="end_date" value="<?php echo date('Y-m-d'); ?>"/> </p>

			<input id="submit" type="submit" value="Submit" name="Submit" />
			<?php 
			echo form_close(); 
			?>
		</div>

		<div class="centered">
			<div class="main-sub-container">
			<table id="bookings-table">
					<thead>
						<th>Facilitator Name</th>
						<th>Date Scheduled</th>
						<th>Start Time</th>
						<th>End Time</th>
						<th>Classroom</th>
					</thead>
					<tbody id="bookings-table-body">
						
						<?php
							if($mybookings_data != NULL){
								foreach( $mybookings_data as $info){


								$info['date_scheduled'] = Date("D F d, Y", strtotime($info['date_scheduled']));
								$info['time_start'] = Date('h:i a', strtotime(explode(" ", $info['time_start'])[1]));
								$info['time_end'] = Date('h:i a', strtotime(explode(" ", $info['time_end'])[1]));

								echo "<tr><td>" . $info['first_name'] . "</td>";
								echo "<td>" . $info['date_scheduled'] . "</td>";
								echo "<td>" . $info['time_start'] . "</td>";
								echo "<td>" . $info['time_end'] . "</td>";
								echo "<td>" . $info['class_color'] . "</td></tr>";
								}

							}
						?>
						<!--Table data will be inserted here -->

					</tbody>
				</table>
			</div>

		</div>

	</body>
</html>