<div  style = "text-align: center;" >
    
	<div id="main-selection-container">
			
		<h1 class = "page-title"> Family Statistics </h1>
			
		<div class = "centered">  <!-- Selection Containers -->
				
			<!-- Family Selection -->
			<div class = "selection">
				<label>Select a family: </label>
				<select name = "family" class = "admin_stats_select" id = "family-selection" >
				<?php
					// This bit of code populates the selection with family usernames 
					foreach( $data as $family_user){

						echo "<option value = " . $family_user['user_id'] . ">" . $family_user['username'] . "</option>";
					}

				?>
				</select>
			</div>
			<br>
				
			<!-- Month Selection -->
			<div class = "selection" >
				<label>Select a month: </label>	
				<select name = "month" class = "admin_stats_select" id = "month-selection">
					<option value = 1> January </option>
					<option value = 2> February </option>
					<option value = 3> March </option>
					<option value = 4> April </option>
					<option value = 5> May </option>
					<option value = 6> June </option>
					<option value = 7> July </option>
					<option value = 8> August </option>
					<option value = 9> September </option>
					<option value = 10> October </option>
					<option value = 11> November </option>
					<option value = 12> December </option>
				</select>
			</div>
			<br>
			
			<!-- Year Selection -->
			<div class = "selection">
				<label>Select a year: </label>
				<select name = "year" class = "admin_stats_select" id ="year-selection">

					<?php 
						// Displays all years from start year to current year
						// Assuming start year is 2018
						$start_year = 2018;
						$present_year = date("Y"); 

						for ($year = $start_year; $year <= $present_year; $year ++)
						{
							echo "<option value = " . $year . ">" . $year . "</option>";
						}

					?>
				</select>
			</div>
			
		</div> <!-- centered End -->

		<div class = "spacing">

			<button onclick="submit_button()" style = "margin-right: 50px;">View Family Statistics</button>

			<input type="button" value="Edit Family Requirements" onclick="window.location.href='preset_requirements'"/>

		</div>


	</div> <!-- main selection container -->

	<div id="main-info-container" style = "display: none;">
		
		
		<!-- Text that appears upon selection of a family -->
		<div id="family-info" class="main-info-sub-container">
				
			<h2> Family Information </h2>
			<br>
			<!-- List of facilitators in the family -->
			<h3> Facilitators: </h3>
			<br>
			<ul id = "Facilitators-list">
				<!--List of facilitators goes here. --> 
			</ul>
			
			<br>	
			<!-- List of students in the family -->
			<h3> Students: </h3>
			<br>
			<ul id = "Students-list">
				<!--List of students goes here -->
			</ul>
				
			<br>
			<!-- Yearly and monthly totals for the family -->
			<h3> Monthly Total: <span id = "monthly-total"> </span> </h3> 
			<br>
			<h3> Yearly Total: <span id = "yearly-total"> </span> </h3>
				
		</div>

	</div>

	<div class = "jsgrid-container">

		<div style = "display: none;" id="jsGrid"></div>

	</div>
	
</div> <!-- End first div -->
</div> <!-- End Sidebar div -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo  base_url(); ?>script/jsgrid/jsgrid.min.js"></script>
<script type="text/javascript" src = "<?php echo  base_url(); ?>script/admin_family_stats.js"> </script>

</body>