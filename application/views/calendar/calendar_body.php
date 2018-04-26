<?php
/*----------------------------------------------------------------------------------------------------------
* This document contains the body portion of the code for the large calendar
* Source: https://codyhouse.co/gem/schedule-template/ 
* 
* Additional alterations to the original code have been made to accommodate the facilitation sign up process.
* Edited by Komal 
*
* PLEASE NOTE: If you want to incorporate a calendar onto the page, you must define the following variables:
* - $title -> The title of the page to be displayed
* - $js -> A string path to a javascript file that implements the calendar javascript interface
* - $classroom_data -> An array of classroom data 
* SEE EXAMPLE USAGE IN FAMILY_CONTROLLER OR TEACHER_CONTROLLER CALENDAR() METHODS 
*--------------------------------------------------------------------------------------------------------*/
?>


	<!-- Page Title  -->
	<h1> <?php echo $title ?>  </h1>

	<div id = "calendar-user-message" style = 'text-align: center;'>
		<!-- This is the black message displayed just above calendar controls -->
		<?php echo $user_message ?>
	</div>

	<!-- Selections for the date and the class color -->
	<div class = "calendar_selections">

		<!-- Class selection -->	
		<p>Classroom Color: 

		 
		<select id = "class-select"> 
			<option disabled selected value>Select Class</option>

			<?php  
				foreach ( $classroom_data as $value ){
					echo "<option value=" . $value['classroom_id'] . ">" . 	$value['class_color'] . "</option>";
				}  
			?>   

		</select>

		<!-- Insert the week picker -->
		Week Selection: 
		<input id = "week-picker" class="week-picker" value = <?php echo date('Y-m-d'); ?> />    </p> 
		
		<?php
			// These buttons will only show if the user logged in is an admin
			if($this->session->userdata('role_id') == 1){
				echo '<input class = "primary_button admin_control" type="button" value="Create Slots" onclick="window.location.href=\'time_slot_creation\'"/>';
				echo '<input class = "primary_button admin_control" type="button" value="Remove multiple slots" onclick="window.location.href=\'delete_range_slot\'"/>';
				echo '<input class = "primary_button admin_control" type="button" value="Set Default Timings" onclick="window.location.href=\'default_times\'"/>';
			}
		?>


	</div> <!-- Selection Containers End -->

	<!-- Creation of Schedule -->
	<div class="cd-schedule loading">

		<!-- This timeline corresponds to the time grid on the left of the page -->
		<div class="timeline">
			<ul>
				<li><span>08:00</span></li>
				<li><span>08:30</span></li>
				<li><span>09:00</span></li>
				<li><span>09:30</span></li>
				<li><span>10:00</span></li>
				<li><span>10:30</span></li>
				<li><span>11:00</span></li>
				<li><span>11:30</span></li>
				<li><span>12:00</span></li>
				<li><span>12:30</span></li>
				<li><span>13:00</span></li>
				<li><span>13:30</span></li>
				<li><span>14:00</span></li>
				<li><span>14:30</span></li>
				<li><span>15:00</span></li>
				<li><span>15:30</span></li>
			</ul>
		</div> <!-- .timeline -->

		
		<!-- All the facilitation events are in the following div. The facilitation events are populated within each unordered list as indicated below -->
		
		<div class="events">
			<ul>
				<li class="events-group">
					<div class="top-info"><span class = "date" >Monday</span></div>
					<ul>
						<!-- Events will be populated here! -->
					</ul>
				</li>

				<li class="events-group">
					<div class="top-info"><span class = "date">Tuesday</span></div>
					<ul>
						<!-- Events will be populated here! -->
					</ul>
						
				</li>

				<li class="events-group">
					<div class="top-info"><span class = "date">Wednesday</span></div>
					<ul>
						<!-- Events will be populated here! -->
					</ul>
				</li>

				<li class="events-group">
					<div class="top-info" ><span class = "date">Thursday</span></div>
					<ul>
						<!-- Events will be populated here! -->
					</ul>
				</li>

				<li class="events-group">
					<div class="top-info"><span class = "date">Friday</span></div>
					<ul>
						<!-- Events will be populated here! -->
					</ul>
				</li>
			</ul>
		</div>
		
		
		<!--
		 * Event Modal Outline
		 * This is the window that pops up when you click an event. The tags below are populated when the user clicks an event. 
		 -->
		<div class="event-modal">
		
			<!-- Modal Header - contains event information (The colored part of the window) -->
			<header class="header">
			
				<div class="content">
				
					<span class="event-date"></span>
					<h3 class="event-name"></h3>
					<strong class = 'positions'> </strong>
					
				</div>

				<div class="header-bg"></div>
			</header>

			<!-- Modal Body - Contains the event form for booking fieldtrip or ordinary events (The white part of the window) -->
			<div class="body">
			
				<div class="event-info"> <!--Information from the html file (Facilitation-sign-up) is inserted here when the user clicks an event! -->  </div>
				<div class="body-bg" style = ""></div>
			
			</div>

			<a href="#0" class="close">Close</a>
		</div> 

		<div class="cover-layer"></div>
		
	</div>

	<!--           JavaScript & jQuery               -->

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>

	<!-- This bit loads the specialized bit of code for this particular calendar (family, teacher, or admin) -->
	<script src="<?php echo  base_url($js);?>"></script>

	<!-- The following are script links for the large calendar -->
	<script src="<?php echo  base_url("script/modernizr.js");?>"></script>

	<script>
		if( !window.jQuery ) document.write(" <script src=' <?php echo  base_url('script/jquery-3.0.0.min.js'); ?> '> <\/script>");
	</script>
	<script type = "text/javascript" src="<?php echo  base_url("script/calendar-main.js"); ?>"></script> <!-- Resource jQuery -->

	<!-- The following are script links for the week picker -->
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script type = "text/javascript" src="<?php echo  base_url('script/week-picker2.js'); ?> "></script>

	<!-- Use error messages by using the method defined in error_message.js -->
    <script type = "text/javascript" src="<?php echo  base_url('script/error_message.js'); ?> "></script> 

	<!-- This creates a new alert at the top of the screen if the php method calling loading this view includes $error_message -->
	<?php if (isset($error_message) & $error_message != null) : ?>

	<script type = "text/javascript"> 
		var message = "<?php echo $error_message ?>";
		var color = "green";
		if (message.includes("unsuccessful"))
		{
			color = "red";
		}
		create_alert(message, color);  
	</script>

	<?php endif; ?>

	</body>

</html> <!-- Created in the header file -->
