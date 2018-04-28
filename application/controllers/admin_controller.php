<?php

class Admin_controller extends CI_Controller {


		/*-------------------------------------------------------
		* __construct()
		* This constructor sets up needed material for this class
		*------------------------------------------------------*/
		public function __construct()
        {
                parent::__construct();
                $this->file_path = realpath(APPPATH . '../assets/csv');

                $this->load->model('admin_model');
                $this->load->model('calendar_model');
                // This is a tool used to generate a URL and link a new page 
                $this->load->helper('url_helper');
                $this->load->helper('form');
                $this->load->helper('download');
                $this->load->library('form_validation');
              	$this->load->library('session');
               
              	// These variables just hold the names of the folders that contain their respective views
              	// in case we want to change the folders later
              	$this->admin_views = "admin/";
				$this->calendar_views = "calendar/";

        }

         /*--------------------------------------------------
        * check_login()
        * This method make sure the user isn't page skipping!
        * PLEASE CALL THIS METHOD FOR ANY NEW PAGE REDIRECTION
        *-------------------------------------------------------*/
        private function check_login() 
		{
			// Check if someone is currently logged in 
		 	if($this->session->userdata('role') != 1){
            	redirect('login');
       		 }
		}

        /*-------------------------------
        * index()
        * This method is automatically called. It sets up the page!
        *-------------------------------*/
        public function index()
        {

       		redirect('admin_controller/member_statistics');
		}

		/*--------------------------------------------------------------------------------------------------
		* member_statistics()
		* This method loads the member_statistics view
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function member_statistics()
		{
			// Check if someone is currently logged in 
		 	$this->check_login();

			$data['data'] = $this->admin_model->getmemberUserIds();
			
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view( $this->admin_views . 'admin_member_statistics_body', $data);


		}

		/*--------------------------------------------------------------------------------------------------
		* fieldtrip_creation
		* This method creates pulls data from the DB and loads the fieldtrip creation view
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function fieldtrip_creation($message = '')
		{
			// Check if someone is currently logged in 
		 	$this->check_login();

		 	// Retrieves classroom data to populate the select
		 	$classroom_data = $this->calendar_model->getClasses();
			$data['classroom_data'] = $classroom_data;
			$data['message'] = $message;
			$data['title'] = 'Fieldtrip creation';

			//$this->load->view($this->admin_views . 'generic_head', $data);
			//$this->load_header();
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'fieldtrip_creation', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* create_fieldtrip
		* This method creates get all the information from the view, collects it and sends it to the model to
		* get a field trip facilitation slot created. Works for a single class and all classrooms
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function create_fieldtrip()
		{

			// Set form rules
			$this->form_validation->set_rules('date', 'date', 'required');
			$this->form_validation->set_rules('start_time', 'start time', 'required');
			$this->form_validation->set_rules('end_time', 'end time', 'required');
			$this->form_validation->set_rules('num_facilitators', 'number of facilitators', 'required');
			$this->form_validation->set_rules('location', 'location', 'required');
			$this->form_validation->set_rules('desc', 'description', 'required');
			$this->form_validation->set_rules('full_desc', 'description', 'required');
			$this->form_validation->set_rules('classroom_id', 'classroom', 'required');
			$this->form_validation->set_rules('confirmation', 'confirmation checkbox', 'required');

			$class_id = $this->input->post('classroom_id');

			// If all fields have been validated
			if ($this->form_validation->run()) 
			{ 
				if($class_id == "*") // check if we're looking at all classes 
				{
					$classes = $this->calendar_model->getClasses();

					foreach ($classes as $class) 
					{
						$c_color = $class['classroom_id'];
				
						// Get form information
						$fieldtrip_info['date'] = $this->input->post('date');
						$fieldtrip_info['start_time'] =  $this->input->post('start_time');
						$fieldtrip_info['end_time'] = $this->input->post('end_time');
						$fieldtrip_info['num_facilitators'] = $this->input->post('num_facilitators');
						$fieldtrip_info['location'] = $this->input->post('location');
						$fieldtrip_info['desc'] = $this->input->post('desc');
						$fieldtrip_info['full_desc'] = $this->input->post('full_desc');
						$fieldtrip_info['classroom'] =  $c_color;
						$fieldtrip_info['is_fieldtrip'] = 1;

						// Make sure the time inputs are valid 
						if ( $this->is_valid_range($fieldtrip_info['start_time'], $fieldtrip_info['end_time']) ){

							$message = $this->admin_model->create_fieldtrip($fieldtrip_info);

						}
						else {

							$message = 'Invalid input for start time and end time.';
							
						}

						
						
					}

					// Redirect to the time slot page 
					$this->fieldtrip_creation($message);
				}
				else
				{
					// Get form information
					$fieldtrip_info['date'] = $this->input->post('date');
					$fieldtrip_info['start_time'] =  $this->input->post('start_time');
					$fieldtrip_info['end_time'] = $this->input->post('end_time');
					$fieldtrip_info['num_facilitators'] = $this->input->post('num_facilitators');
					$fieldtrip_info['location'] = $this->input->post('location');
					$fieldtrip_info['desc'] = $this->input->post('desc');
					$fieldtrip_info['full_desc'] = $this->input->post('full_desc');
					$fieldtrip_info['classroom'] =  $class_id;
					$fieldtrip_info['is_fieldtrip'] = 1;

					// Make sure the time inputs are valid 
					if ( $this->is_valid_range($fieldtrip_info['start_time'], $fieldtrip_info['end_time']) ){

						$message = $this->admin_model->create_fieldtrip($fieldtrip_info);

					}
					else {

						$message = 'Invalid input for start time and end time.';
						
					}

					// Redirect to the time slot page 
					$this->fieldtrip_creation($message);	
				}			
			}
			else
			{
					$this->fieldtrip_creation();
			}		
		}

		/*--------------------------------------------------------------------------------------------------
		* get_fieldtrip_info()
		* This method retrieves information regarding a specific fieldtrip 
		*
		* Parameters: A valid slot id
		* 
		* Returns: An associative array containing the query result (location, description, full_description)
		*--------------------------------------------------------------------------------------------*/
		public function get_fieldtrip_info ()
		{
			$slot_id = $this->input->post('s_id');
			$f_info = $this->calendar_model->get_fieldtrip_info($slot_id);
			echo $f_info['location'] . ',' . $f_info['description']  . ',' . $f_info['full_description'] ;
		}


		/*--------------------------------------------------------------------------------------------------
		* get_facilitators()
		* This method retrieves the facilitators signed up for a given slot id 
		*
		* Params & Returns: None
		* Note: Information is echoed to the caller script
		*--------------------------------------------------------------------------------------------*/
		public function getFacilitatorsSignedUp(){

			$slot_id = $this->input->post('s_id');

			$this->calendar_model->getFacilitatorsSignedUp($slot_id);
		}

		/*--------------------------------------------------------------------------------------------------
		* time_slot_management
		* This method loads the time_slot_management view
		*
		* Params: Takes a message to displey under the title 
		*--------------------------------------------------------------------------------------------*/
		public function time_slot_management( $message = null)
		{

			// Check if someone is currently logged in 
		 	$this->check_login();

		 	// Retrieves classroom data to populate the select
		 	$classroom_data = $this->calendar_model->getClasses();
			$data['classroom_data'] = $classroom_data;
			
			// This javascript file defines the fillModal and getEvents functions the calendar requires
			$data['js'] = "script/timeslot-management-calendar.js";

			// Title and user message to be displayed
			$data['title'] = 'Time Slot Management';
			$data['user_message'] = "Choose a single timeslot you would like to edit or delete. Alternatively, you may create or delete multiple time slots using the create and delete buttons.";
			$data['error_message'] = $message;

			// Load the views
		 	//$this->load->view($this->calendar_views . 'calendar_head', $data);
		 	//$this->load_header();
		 	$this->load->view($this->admin_views . 'sidebar');

		 	$this->load->view($this->calendar_views . "calendar_body" , $data);

		}

		public function time_slot_creation()
		{ 
			$data = array ('title' => 'Timeslot Creation');
			//$this->load->view($this->admin_views . 'generic_head', $data);
			//$this->load_header();
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'time_slot_choosing');
		}

		/*--------------------------------------------------------------------------------------------------
		* calendar_modal_edit_remove
		* This method loads the edit_remove_single_slot view which appears inside any modal 
		*
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function calendar_modal_edit_remove()
		{
			$this->load->view($this->admin_views . "edit_remove_single_slot");
		}


		/*--------------------------------------------------------------------------------------------------
		* calendar_modal_edit_remove_fieldtrip
		* This method loads the edit_remove_fieldtrip view which appears inside any modal of a fieldtrip
		*
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function calendar_modal_edit_remove_fieldtrip()
		{
			$this->load->view($this->admin_views . "edit_remove_fieldtrip");
		}

		/*--------------------------------------------------------------------------------------------------
		* edit_slot
		* This method handles the form submission and validation for the editing a single slot
		* (The form inside the modal)
		*
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function edit_slot()
		{

			// Set form rules
			$this->form_validation->set_rules('start_time', 'start time', 'required');
			$this->form_validation->set_rules('end_time', 'end time', 'required');
			$this->form_validation->set_rules('num_facilitators', 'number of facilitators', 'required|is_natural');


			// If all fields are full ...
			if ($this->form_validation->run()) 
			{

				// Get all the form info 
				$start_time = $this->input->post('start_time');
				$end_time =  $this->input->post('end_time');
				$num_facilitators =  $this->input->post('num_facilitators');
				$slot_id = $this->input->post('slot_id');
				$message = '';

				// Make sure the time inputs are valid 
				if ( $this->is_valid_range($start_time, $end_time) ){

					$message = $this->admin_model->edit_slot($slot_id, $start_time, $end_time, $num_facilitators);

				}
				else {

					$message = 'Creation unsuccessful. Input for start time and end time is invalid.';
				}

				// Redirect to the time slot page 
				$this->time_slot_management($message);	

			}

			else
			{
				$this->time_slot_management('Invalid form input. Please try again.');	
			}
		}


		/*--------------------------------------------------------------------------------------------------
		* edit_fieldtrip
		* This method handles the form submission and validation for the editing a fieldtrip
		* (The form inside the modal)
		*
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function edit_fieldtrip ()
		{
			$this->form_validation->set_rules('start_time', 'start time', 'required');
			$this->form_validation->set_rules('end_time', 'end time', 'required');
			$this->form_validation->set_rules('num_facilitators', 'number of facilitators', 'required|is_natural');
			$this->form_validation->set_rules('location', 'location', 'required');
			$this->form_validation->set_rules('desc', 'description', 'required');
			$this->form_validation->set_rules('full_desc', 'description', 'required');
			

			// If all fields are full ...
			if ($this->form_validation->run()) 
			{

				// Get all the form info 
				$start_time = $this->input->post('start_time');
				$end_time =  $this->input->post('end_time');
				$num_facilitators =  $this->input->post('num_facilitators');
				$slot_id = $this->input->post('slot_id');
				$desc = $this->input->post('desc');
				$full_desc = $this->input->post('full_desc');
				$location = $this->input->post('location');
				
				$message = 'Invalid input for start time and end time.';

				// Make sure the time inputs are valid 
				if ( $this->is_valid_range($start_time, $end_time) ){
					// Edit the basic slot information
					$message = $this->admin_model->edit_slot($slot_id, $start_time, $end_time, $num_facilitators);
					// Edit the fieldtrip-related fields if the previous edit was successful
					if ($message == 'Slot successfully updated!') {
						$this->admin_model->edit_fieldtrip($slot_id, $desc, $full_desc, $location);
					}
				}

				// Redirect to the time slot page 
				$this->time_slot_management($message);	

			}

			else
			{
				$this->time_slot_management('Invalid form input. Please try again.');	
			}

		}

		/*
		* is_valid_range
		* Checks if start time is before end time
		*
		* parameters: String times in the following format: '10:00' 
		* return true if they are and false otherwise
		*/
		private function is_valid_range($start_time, $end_time)
		{
			return strtotime($start_time) < strtotime($end_time);
		}

		/*--------------------------------------------------------------------------------------------------
		* delete_single_slot_confirmation
		* This method loads the single slot deletion confirmation form view which appears inside any modal 
		* upon clicking DELETE THIS SLOT
		* 
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function delete_single_slot_confirmation()
		{
		 	$this->check_login();
			$this->load->view($this->admin_views . "delete_single_slot_confirmation");
		}


		/*--------------------------------------------------------------------------------------------------
		* delete_single_slot_confirmation
		* This method loads the single slot deletion confirmation form view which appears inside any modal 
		* upon clicking DELETE THIS SLOT
		* 
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function delete_single_slot ()
		{
			$slot_id = $this->input->post('slot_id');

			$this->admin_model->delete_slot($slot_id);

			$this->time_slot_management('Slot successfully deleted!');
		}

		/*--------------------------------------------------------------------------------------------------
		* delete_range_slot
		* This method is called when someone selects submit for deleting a range of slots. Input is validated 
		* and then slots are deleted
		* 
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function delete_range_slot()
		{
			$this->form_validation->set_rules('start_date', 'start date', 'required');
			$this->form_validation->set_rules('end_date', 'end date', 'required');

			if ($this->form_validation->run())
			{
				
				$s_date = $this->input->post('start_date');
				$e_date = $this->input->post('end_date');
				$classroom = $this->input->post("classroom_id");

				$message = $this->delete_range($s_date, $e_date, $classroom);			
				$this->time_slot_management($message);
			}
			else
			{
				$this->time_slot_range_deletion();
			}
		}



		/*--------------------------------------------------------------------------------------------------
		* delete_range
		* This method deletes a range of slots with that fall on and between the start date, end date, and classroom provided
		* 
		* Params  $s_date & $e_date - The start date and end date to delete slots between 
		*
		* returns: An error message where applicable
		*--------------------------------------------------------------------------------------------*/
		private function delete_range($s_date, $e_date, $classroom)
		{
			if ( $this->is_valid_range($s_date, $e_date) || $s_date == $e_date){

				// get all slot ids that fall in time range
				$date_ids = $this->calendar_model->getSlotIds($s_date,$e_date,$classroom);

				//run them through a for each loop and call delete_single_slot(from model)
				foreach ($date_ids as $id)
				{
					$this->admin_model->delete_slot($id['slot_id']);
				}
				return "All slots successfully deleted!";
			}
			else
			{
				return "Deletion unsuccessful. Start date was not after end date."; 
			}
		}

		/*--------------------------------------------------------------------------------------------------
		* time_slot_range_deletion
		* This method loads the timeslot range deletion form 
		* 
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function time_slot_range_deletion()
		{
			$this->check_login();
			$data['classroom_data'] = $this->calendar_model->getClasses();
			$data['title'] = 'Time Slot Deletion';
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'delete_slot_range',$data);
		}
		
		/*--------------------------------------------------------------------------------------------------
		* create_slot_range
		* This method creates a facilitation slots over a range of days
		* 
		* 
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/

		public function create_slot_range()
		{

			// Set form rules
			$this->form_validation->set_rules('start_date', 'start date', 'required');
			$this->form_validation->set_rules('end_date', 'end date', 'required');
			
			$this->form_validation->set_rules('morn_starttime', 'Morning start time', 'required');
			$this->form_validation->set_rules('morn_endtime', 'Morning end time', 'required');
			$this->form_validation->set_rules('morn_facnumber', 'Morning facilitator number', 'required|is_natural');
			
			$this->form_validation->set_rules('lun_starttime', 'Lunch start time', 'required');
			$this->form_validation->set_rules('lun_endtime', 'Lunch end time', 'required');
			$this->form_validation->set_rules('lun_facnumber', 'Lunch facilitator number', 'required|is_natural');
			
			$this->form_validation->set_rules('aft_starttime', 'Afternoon start time', 'required');
			$this->form_validation->set_rules('aft_endtime', 'Afternoon end time', 'required');
			$this->form_validation->set_rules('aft_facnumber', 'Afternoon facilitator number', 'required|is_natural');
			$this->form_validation->set_rules('confirmation', 'confirmation checkbox', 'required');

			if ($this->form_validation->run())
			{
				/*
				// Some things you might want to consider: 
				// - You might want a message for when someone chooses a saturday facilitation or something saying invalid
				// but we can do that when you're connected to the rest of the page 

				*/
				$s_date = new DateTime($this->input->post('start_date'));
				$e_date = new DateTime($this->input->post('end_date'));

				// Get all the form info 
				$start_time = $this->input->post('morn_starttime');
				$end_time =  $this->input->post('morn_endtime');
				$num_facilitators =  $this->input->post('morn_facnumber');
 
				$lstart_time = $this->input->post('lun_starttime');
				$lend_time =  $this->input->post('lun_endtime');
				$lnum_facilitators =  $this->input->post('lun_facnumber');

				$astart_time = $this->input->post('aft_starttime');
				$aend_time =  $this->input->post('aft_endtime');
				$anum_facilitators =  $this->input->post('aft_facnumber');

				$class = $this->input->post('classroom_id');

				$message = 'All slots were successfully created';
				//Facilitation time adding -- Note: seperating into 3 for loops didnt work, reason is unknown
				for ($i = $s_date; $i <= $e_date; $i->modify('+1 day'))
				{
					if ($this->is_valid_range($start_time, $end_time) && $this->is_valid_range($lstart_time, $lend_time) && $this->is_valid_range($astart_time, $aend_time))
					{
						$this->admin_model->create_timeslot($class, $start_time,$end_time,$num_facilitators,$i->format("Y-m-d"),0); 
								
						$this->admin_model->create_timeslot($class, $lstart_time,$lend_time,$lnum_facilitators,$i->format("Y-m-d"),0); 
					
						$this->admin_model->create_timeslot($class, $astart_time,$aend_time,$anum_facilitators,$i->format("Y-m-d"),0);
					}
					else
					{
						$message = 'Slot creation was unsuccessful due to invalid form input. ';
					}
				}
				$this->time_slot_management($message);
			}
			else 
			{		
				$this->time_slot_range_creation();
			}
		}
		/*--------------------------------------------------------------------------------------------------
		* create_single_slot
		* This method calls the view and creation methods for a single time slot
		* 
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function create_single_slot()
		{
			$this->form_validation->set_rules('facnumber', 'Facilitators needed', 'required');
			$this->form_validation->set_rules('start_time', 'Start time', 'required');
			$this->form_validation->set_rules('end_time', 'End time', 'required');

			if ($this->form_validation->run())
			{
				// Get all the form info 
				$start_time = $this->input->post('start_time');
				$end_time =  $this->input->post('end_time');
				$num_facilitators =  $this->input->post('facnumber');
				$s_date = $this->input->post('start_date');
				$e_date = $this->input->post('end_date');
				$class = $this->input->post('classroom_id');
			
				// Make sure the time inputs are valid 
				if ($this->is_valid_range($start_time, $end_time))
				{
					$message = $this->admin_model->create_timeslot($class, $start_time,$end_time,$num_facilitators,$s_date,0); 
					$this->time_slot_management($message);	 
				}
				else
				{
					$this->single_time_slot_creation("Creation unsuccessful due to invalid start time and end time.");
				}
			}
			else 
			{		
				$this->single_time_slot_creation();
			}
		}

		/*--------------------------------------------------------------------------------------------------
		* single_time_slot_creation
		* This method loads the view for creating a time slot.
		* 
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function single_time_slot_creation($message = "")
		{
			$this->check_login();
			
			$data['classroom_data'] = $this->calendar_model->getClasses();
			$data['title'] = 'Timeslot Creation';
			$data['message'] = $message;
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'create_single_timeslot_body',$data);
		}

		/*--------------------------------------------------------------------------------------------------
		* time_slot_range_creation
		* This method loads the view for creating a range of time slots
		* 
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function time_slot_range_creation()
		{
			$this->check_login();
			
			$data ['slot_times'] = $this->admin_model->get_defaults();
			$data['classroom_data'] = $this->calendar_model->getClasses();
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'create_timeslot_range_body',$data);
		}

		/*--------------------------------------------------------------------------------------------------
		* default_times
		* This method loads the preset times form 
		* 
		* Params: Message - A user message to be displayed atop the screen 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function default_times($message = "Edit the slot details below should you wish to change the default timings.")
		{
			$this->check_login();
			
			$data ['slot_times'] = $this->admin_model->get_defaults();
			$data['title'] = 'Default Time Settings';
			$data['message'] = $message;
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . "set_default_timings", $data);
		}


		/*--------------------------------------------------------------------------------------------------
		* default_times
		* This method is called when the preset times form is submitted. It updates the database based on the submission
		* 
		* Params & returns: None
		*--------------------------------------------------------------------------------------------*/
		public function set_default()
		{

			// Set form rules
			$times = array ('Morning', 'Lunch', 'Afternoon');
			foreach ($times as $time )
			{
				$this->form_validation->set_rules($time . '_start_time', 'start time', 'required');
				$this->form_validation->set_rules($time . '_end_time', 'end time', 'required');
				$this->form_validation->set_rules($time . '_num_facilitators', 'number of facilitators', 'required|is_natural');
			}

			// If all fields are full ...
			if ($this->form_validation->run()) 
			{

				// Make sure the times given are ok
				$form_info = array();
				for ($i = 0; $i < 3; $i ++)
				{
					$time = $times[$i];
					$start_time = $this->input->post($time . '_start_time');
					$end_time =  $this->input->post($time . '_end_time');
					$num_facilitators =  $this->input->post($time . '_num_facilitators');

					if (strtotime($start_time) < strtotime($end_time))
					{

						$form_info[$i] = array( 
							'start_time' => $start_time,
							'end_time' => $end_time,
							'facilitators_needed' => $num_facilitators,
						);
					}
				}

				$message = "Update unsuccessful due to invalid form input.";
				if (count($form_info) == 3)
				{
					$this->admin_model->update_defaults($form_info);
					$message = "Update successful!";
				}

				$this->time_slot_management($message);
			}
			else
			{
				$this->default_times();
			}

		}

		/*--------------------------------------------------------------------------------------------------
		* load_header()
		* This method loads the header view
		*
		* Params & Returns: None
		* Note: Called by the calendar file
		*--------------------------------------------------------------------------------------------*/
		public function load_header(){
			$this->load->view('templates/adminHeader');
		}

		/*--------------------------------------------------------------------------------------------------
		* logout()
		* This method redirects the user to the login page and removes all their session data 
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function logout(){
			redirect('general_controller');
		}


		/* ADMIN CALENDAR */

		/*--------------------------------------------------------------------------------------------------
		* get_events()
		* This method gathers event data for the sleected events in the calendar
		*
		* Params & Returns: None
		* PLEASE NOTE: This method echos out the data for the corresponding javascript recipient file
		*--------------------------------------------------------------------------------------------*/
		public function get_events(){

			// Gather data from the caller javascript file 
			$days = $this->input->post('days');
			$id = $this->input->post('classid');
			
			// Gather and echo data 
			$this->calendar_model->findEvents($days, $id);
		}



		/* ADMIN member STATISTICS */

		/*------------------------------------------------------------
		* get_facilitators
		* This method retirieves all the facilitator associated with a given user id. 
		* 
		* Params & Return: NONE
		* NOTE: This method echos information for the caller function (admin-member-stats.js)
		*------------------------------------------------------------*/
		public function get_facilitators()
		{

			$member_id = $this->input->post('u_id');
			$result = $this->admin_model->get_facilitators($member_id);

			foreach ($result as $row){
				echo $row["name"] . ',';
			}

		}

		/*------------------------------------------------------------
		* get_students
		* This method retirieves all the students associated with a given user id. 
		* 
		* Params & Return: NONE
		* NOTE: This method echos information for the caller function (admin-member-stats.js)
		*------------------------------------------------------------*/
		public function get_students()
		{
			$user_id = $this->input->post('u_id');
			$result = $this->admin_model->get_students($user_id);

			foreach ($result as $row){
				echo $row["first_name"] . " " . $row["last_name"] . ',';
			}
		}

		/*------------------------------------------------------------
		* get_history
		* This method retirieves all the history data for a given member at a given month and year 
		* 
		* Params & Return: NONE
		* NOTE: This method echos information for the caller function (admin-member-stats.js)
		*------------------------------------------------------------*/
		public function get_history()
		{
			$userID = $this->input->post('u_id');
			$month = $this->input->post('month');
			$year = $this->input->post('year');
			$this->db->order_by('start_date', 'ASC');

			$result = $this->admin_model->getmemberHistory($userID,$year,$month);


			foreach ($result as $row){
				echo Date("F d", strtotime($row["start_date"])) . ',' . Date("F d", strtotime($row["end_date"])) . ',' . $row["required_hours"] . ',' . $row["completed_hours"] . ',' . $row["hours_given"] . ',' . $row["hours_received"] . ',' . $row["history_id"] . '~' ; 
			}
		}

		/*------------------------------------------------------------
		* get_yearly_hours
		* This method retirieves the number of hours completed in a given school year based on the year and month provided
		* 
		* Params & Return: NONE
		* NOTE: This method echos information for the caller function (admin-member-stats.js)
		*------------------------------------------------------------*/
		public function get_yearly_hours()
		{

			$userID = $this->input->post('u_id');
			$year = $this->input->post('y');
			$month = $this->input->post('m');

			$result = $this->admin_model->getYearlyHours($userID, $year, $month);
			echo $result;

		}

		/*------------------------------------------------------------
		* get_monthly_hours
		* This method retirieves the number of hours completed in a given month
		* 
		* Params & Return: NONE
		* NOTE: This method echos information for the caller function (admin-member-stats.js)
		*------------------------------------------------------------*/
		public function get_monthly_hours()
		{
			$userID = $this->input->post('u_id');
			$year = $this->input->post('y');
			$month = $this->input->post('m');

			echo $this->admin_model->getMonthlyHours($userID, $month, $year);
		}

		/*------------------------------------------------------------
		* preset_requirements
		* This method displays the member hourly requirements page 
		* 
		* Params & Return: NONE
		*------------------------------------------------------------*/
		public function preset_requirements()
		{
			$data['title'] = "Edit Requirements";

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'preset_requirements');
		}


		/*------------------------------------------------------------
		* get_member_requirements
		* This method retrieves the existing hourly requirements from the database 
		* 
		* Params & Return: NONE
		* NOTE: This method echos information for the caller function (member_requirements_table.js)
		*------------------------------------------------------------*/
		public function get_member_requirements()
		{
			$data = $this->admin_model->get_preset_requirements();

			foreach ($data as $row)
	        {
	          
	            echo $row['number_of_students'] . " " . $row['required_hours'] . " " . $row['rule_id'] . ",";

	        }
		}

		/*------------------------------------------------------------
		* insert_member_requirement
		* This method inserts a new rule for member requirements
		* 
		* Params & Return: NONE
		* NOTE: This method echos information for the caller function (member_requirements_table.js)
		*------------------------------------------------------------*/
		public function insert_member_requirement()
		{
			
			$num_students = $this->input->post('num');
			$hours = $this->input->post('hours');

			echo $this->admin_model->insert_member_requirement($num_students, $hours);
		}

		/*------------------------------------------------------------
		* update_member_requirement
		* This method updates the existing hourly requirements
		* 
		* Params & Return: NONE 
		*------------------------------------------------------------*/
		public function update_member_requirement()
		{
			
			$num_students = $this->input->post('num');
			$hours = $this->input->post('hours');
			$id = $this->input->post('id');

			echo $this->admin_model->update_member_requirement($id, $num_students, $hours);
		}

		/*------------------------------------------------------------
		* delete_member_requirement
		* This method deleted an hourly requirement
		* 
		* Params & Return: NONE 
		*------------------------------------------------------------*/
		public function delete_member_requirement()
		{

			$num_students = $this->input->post('num');
			$hours = $this->input->post('hours');
			$id = $this->input->post('id');

			echo $this->admin_model->delete_member_requirement($id, $num_students, $hours);
		}

		/*------------------------------------------------------------
		* update_history
		* This method collects the data to update a history entry
		* 
		* Params & Return: NONE 
		*------------------------------------------------------------*/
		public function update_history()
		{
			$hourG = $this->input->post('hours_given');
			$hourR = $this->input->post('hours_received');
			$hourC = $this->input->post('hours_completed');
			$hourReq = $this->input->post('hours_required');
			$id = $this->input->post('id');

			$this->admin_model->update_history_entry($id,$hourG,$hourR,$hourC,$hourReq);
		}

		/* ACCOUNT CREATION AND DELETION*/

		/*--------------------------------------------------------------------------------------------------
		* admin_account_creation
		* This method displays the account creation view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function admin_account_creation()
		{
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Creation/admin_account_creation_body');
		}

		/*--------------------------------------------------------------------------------------------------
		* admin_account_removal
		* This method displays the account removalview
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function admin_account_removal()
		{
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Removal/admin_account_removal_body');
		}

		
		/* ADMIN REMOVAL */

     	/*--------------------------------------------------------------------------------------------------
		* admin_removal
		* This method displays the remove admin view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function admin_removal($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['admin_info'] = $this->admin_model->getUsernames(1);

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Removal/remove_admin_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* remove_admin
		* This method removes a admin based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function remove_admin()
		{
			$this->form_validation->set_rules('username', 'username', 'required');
		
			if ($this->form_validation->run()){

				$username = $this->input->post('username');
				$this->admin_model->remove_admin_account($username); 
				$this->admin_removal('Admin sucessfully removed!');
			}
			else {
				$this->admin_removal();
			}
		}

		/* GENERAL REMOVAL */

     	/*--------------------------------------------------------------------------------------------------
		* general_removal
		* This method displays the remove general account view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function general_removal($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['admin_info'] = $this->admin_model->getUsernames(5);

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Removal/remove_general_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* remove_general
		* This method removes a general account based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function remove_general()
		{
			$this->form_validation->set_rules('username', 'username', 'required');
		
			if ($this->form_validation->run()){

				$username = $this->input->post('username');
				$this->admin_model->remove_general_account($username); 
				$this->general_removal('This general account was sucessfully removed!');
			}
			else {
				$this->general_removal();
			}
		}

		/* BOARD MEMBER REMOVAL */

     	/*--------------------------------------------------------------------------------------------------
		* board_member_removal
		* This method displays the remove board member account view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function board_member_removal($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['boardmember_info'] = $this->admin_model->getUsernames(3);

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Removal/remove_board_member_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* remove_board_member
		* This method removes a board member  based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function remove_board_member()
		{
			$this->form_validation->set_rules('username', 'username', 'required');
		
			if ($this->form_validation->run()){

				$username = $this->input->post('username');
				$this->admin_model->remove_board_member_account($username); 
				$this->board_member_removal("Board member successfully deleted!");
			}
			else {
				$this->board_member_removal();
			}
		}

		/* TEACHER REMOVAL */

     	/*--------------------------------------------------------------------------------------------------
		* teacher_removal
		* This method displays the remove teacher account view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function teacher_removal($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['teacher_info'] = $this->admin_model->getUsernames(4);

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Removal/remove_teacher_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* remove_teacher
		* This method removes a teacher based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function remove_teacher()
		{
			$this->form_validation->set_rules('username', 'username', 'required');
		
			if ($this->form_validation->run()){

				$username = $this->input->post('username');
				$this->admin_model->remove_teacher_account($username); 

				$this->teacher_removal('Account successfully deleted!');
			}
			else {
				$this->teacher_removal();
			}
		}

		/* STUDENT REMOVAL */

     	/*--------------------------------------------------------------------------------------------------
		* student_removal
		* This method displays the remove student view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function student_removal($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['member_info'] = $this->admin_model->getmemberUserIds();
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Removal/remove_student_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* remove_student
		* This method removes a student based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function remove_student()
		{
			$this->form_validation->set_rules('student_id', 'student', 'required');
			$this->form_validation->set_rules('user_id', 'member', 'required');
		
			if ($this->form_validation->run()){

				$student_id = $this->input->post('student_id');
				$this->admin_model->remove_student_account($student_id); 
				$this->student_removal('This student account was successfully removed!');
			}
			else {
				$this->student_removal();
			}
		}

		/*--------------------------------------------------------------------------------------------------
		* get_students_name_ids
		* This method retrieves facilitator names and ids 
		*
		* Params & Returns: None
		* NOTE: Echos out information for the caller (js)
		*--------------------------------------------------------------------------------------------*/
		public function get_students_name_ids()
		{

			$user_id = $this->input->post('u_id');
			$result = $this->admin_model->get_students($user_id);

			foreach ($result as $row){
				echo $row["first_name"] . " " . $row["last_name"] . "-" . $row["student_id"] . ',';
			}
		
		}

		/* FACILITATOR REMOVAL */

     	/*--------------------------------------------------------------------------------------------------
		* facilitator_removal
		* This method displays the remove facilitator view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function facilitator_removal($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['member_info'] = $this->admin_model->getmemberUserIds();
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Removal/remove_facilitator_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* remove_facilitator
		* This method removes a facilitator  based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function remove_facilitator()
		{

			$this->form_validation->set_rules('user_id', 'username', 'required');
			$this->form_validation->set_rules('facilitator_id', 'facilitator', 'required');
		
			if ($this->form_validation->run()){

				$facilitator_id = $this->input->post('facilitator_id');
				$this->admin_model->remove_facilitator_account($facilitator_id); 
				$this->facilitator_removal('Account successfully deleted!');

			}
			else {
				$this->facilitator_removal();
			}
		}

		/*--------------------------------------------------------------------------------------------------
		* get_facilitators_name_ids
		* This method retrieves facilitator names and ids 
		*
		* Params & Returns: None
		* NOTE: Echos out information for the caller (js)
		*--------------------------------------------------------------------------------------------*/
		public function get_facilitators_name_ids()
		{

			$user_id = $this->input->post('u_id');
			$result = $this->admin_model->get_facilitators($user_id);

			foreach ($result as $row){
				echo $row["name"] . "-" . $row["facilitator_id"] . ',';
			}
		
		}

		/* member REMOVAL */

     	/*--------------------------------------------------------------------------------------------------
		* member_removal
		* This method displays the remove member view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function member_removal($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['member_info'] = $this->admin_model->getmemberUserIds();
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Removal/remove_member_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* remove_member
		* This method removes a famiy account based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function remove_member()
		{
			$this->form_validation->set_rules('username', 'username', 'required');
		
			if ($this->form_validation->run()){

				$user_id = $this->input->post('username');
				$this->admin_model->remove_member_account($user_id); 
				$this->member_removal('Account successfully removed');

			}
			else {
				$this->member_removal();
			}
		}

		/*------------------------------------------------------------
		* export
		* This method loads the export view
		*------------------------------------------------------------*/
		public function export()
		{
			$this->check_login();
			
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'export_body');
		}

		/*------------------------------------------------------------
		* export_data
		* This method performs the export
		*------------------------------------------------------------*/
		public function export_data()
		{
			$this->load->dbutil();
			$this->load->helper('file');

			/* get the object */
			$report = $this->admin_model->get_history_data();

			$delimiter = ",";
			$newline = "\r\n";

			/* pass it to db utility funciton */
			$new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);

			write_file($this->file_path . '/csv_file.csv', $new_report);

			/* force download from the server */

			$this->load->helper('download');
			$data = file_get_contents($this->file_path . '/csv_file.csv');
			$name = 'Facilitation History-' . date('d-m-Y').'.csv';

			force_download($name, $data);
     }
		
     	/* GENERAL  CREATION */

     	/*--------------------------------------------------------------------------------------------------
		* general_creation
		* This method displays the create general account view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
     	public function general_creation($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Creation/create_general_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* create_general
		* This method creates a new general account based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
     	public function create_general()
		{
			$this->form_validation->set_rules('username', 'User ID', 'trim|required|min_length[4]');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');
			
			if ($this->form_validation->run()){
				
				$message = $this->admin_model->create_general_account($this->input->post('username'), $this->input->post('password')); 
				$this->general_creation($message);
			}
			else {
				
				$this->general_creation();

			}

		}


     	/* ADMIN CREATION */

     	/*--------------------------------------------------------------------------------------------------
		* admin_creation
		* This method displays the create admin view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
     	public function admin_creation($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Creation/create_admin_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* create_admin
		* This method creates a new admin based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function create_admin()
		{
			$this->form_validation->set_rules('username', 'User Name', 'trim|required|min_length[4]');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');
			
			if ($this->form_validation->run()){
				
				$message = $this->admin_model->create_admin_account($this->input->post('username'), $this->input->post('password')); 
				$this->admin_creation($message);
			}
			else {
				
				$this->admin_creation();

			}

		}


		/* BOARD MEMBER CREATION */

		/*--------------------------------------------------------------------------------------------------
		* board_member_creation
		* This method displays the create board member view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function board_member_creation($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Creation/create_board_member_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* create_board_member
		* This method creates a new board member based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function create_board_member()
		{
			$this->form_validation->set_rules('username', 'User Name', 'trim|required|min_length[4]');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');
			
			if ($this->form_validation->run()){
				
				$message = $this->admin_model->create_board_member_account($this->input->post('username'), $this->input->post('password'));
				$this->board_member_creation($message);
			}
			else {
				
				$this->board_member_creation();

			}

		}

		/* FACILITATOR CREATION */

		/*--------------------------------------------------------------------------------------------------
		* facilitator_creation
		* This method displays the create facilitator view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function facilitator_creation($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['member_info'] = $this->admin_model->getmemberUserIds();
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Creation/create_facilitator_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* create_facilitator
		* This method creates a new facilitator  based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function create_facilitator()
		{
			$this->form_validation->set_rules('first_name', 'First Name', 'required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'required|regex_match[/^[0-9]{10}$/]');
			$this->form_validation->set_rules('email', 'Email', 'required');
			$this->form_validation->set_rules('address', 'Address', 'required');
			$this->form_validation->set_rules('user_id', 'member Username', 'required');

			if ($this->form_validation->run()){
				
				$data = array (
				'user_id' => $this->input->post('user_id'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
			 	'phone_number' => $this->input->post('phone_number'),
			 	'email' => $this->input->post('email'),
				'address' => $this->input->post('address'),
				'phone_number' => $this->input->post('phone_number')
			 	);

				$message = $this->admin_model->create_facilitator_account($data); 
				$this->facilitator_creation($message);
				
			}
			else {
				
				$this->facilitator_creation();

			}

		}

		/* STUDENT CREATION */

		/*--------------------------------------------------------------------------------------------------
		* student_creation
		* This method displays the create student view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function student_creation($message = "")
		{
			$this->check_login();

			$data['classroom_data'] = $this->calendar_model->getClasses();
			$data['member_info'] = $this->admin_model->getmemberUserIds();	
			$data['message'] = $message;		

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Creation/create_student_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* create_student
		* This method creates a new student based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function create_student()
		{
			$this->form_validation->set_rules('first_name', 'First Name', 'required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');

			$this->form_validation->set_rules('grade', 'Grade', 'required');
			$this->form_validation->set_rules('user_id', 'member', 'required');
			$this->form_validation->set_rules('classroom_id', 'Classroom', 'required');
			
			
			if ($this->form_validation->run()){
				
				$data = array(
					'user_id' => $this->input->post('user_id'),
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'grade' => $this->input->post('grade'),
					'classroom_id' => $this->input->post('classroom_id')
				);

				$message = $this->admin_model->create_student_account($data); 
				$this->student_creation($message); 
			} 

			else {
				
				$this->student_creation();

			} 

		}

		/* TEACHER CREATION */

		/*--------------------------------------------------------------------------------------------------
		* teacher_creation
		* This method displays the create teacher view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function teacher_creation($message = "")
		{
			$this->check_login();
			
			$classroom_data = $this->calendar_model->getClasses();
			$data['classroom_data'] = $classroom_data;
			$data['message'] = $message;

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Creation/create_teacher_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* create_teacher
		* This method creates a new member based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function create_teacher()
		{
			$this->form_validation->set_rules('first_name', 'First Name', 'required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');
			$this->form_validation->set_rules('classroom_id', 'Classroom', 'required');

			if ($this->form_validation->run())
			{
				$data = array (
				'username' => $this->input->post('username'),
				'password' => $this->input->post('password'),	
				'fname' => $this->input->post('first_name'),
				'lname' => $this->input->post('last_name'),
				'class_id' => $this->input->post('classroom_id') );

				$message = $this->admin_model->create_teacher_account($data);
				$this->teacher_creation($message);
			} 

			else {
				
				$this->teacher_creation();

			}

		}

		/* member CREATION */

		/*--------------------------------------------------------------------------------------------------
		* member_creation()
		* This method displays the create member view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function member_creation($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Creation/create_member_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* create_member
		* This method creates a new member based on the form data
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function create_member()
		{
			$this->form_validation->set_rules('username', 'User Name', 'required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');

			// If form validation succeeds
			if ($this->form_validation->run()){
				
				$username = $this->input->post('username');
				$password = $this->input->post('password');
				$message = $this->admin_model-> create_member_account($username, $password);

				$this->member_creation($message);

			} 

			else {

				$this->member_creation();

			}

		}

		/* ADMIN EDIT */

		/*--------------------------------------------------------------------------------------------------
		* admin_account_edit
		* This method displays the account edit view
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function admin_account_edit()
		{
			$this->check_login();
			
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Edit/admin_account_edit');
		}

		/*--------------------------------------------------------------------------------------------------
		* member_edit 
		* This method loads the member edit view with all needed data
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function member_edit($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['member_info'] = $this->admin_model->getmemberUserIds();
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Edit/edit_member_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* edit_member

		* This method validates the edit_member form and then passes the info to the model for updating
		*
		* Params: None
		* Returns: A user message to display 
		*--------------------------------------------------------------------------------------------*/
		public function edit_member()
		{
			$this->form_validation->set_rules('user_id', 'Username', 'required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');
			
			if ($this->form_validation->run())
			{
				$user_id = $this->input->post('user_id');
				$pw = $this->input->post('password');

				$message = $this->admin_model->edit_account($user_id,$pw);
				$this->member_edit($message);
			}
			else 
			{	
				$this->member_edit();
			}
		}

		/*--------------------------------------------------------------------------------------------------
		* facilitator_edit 
		* This method loads the facilitator edit view with all needed data
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function facilitator_edit($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['member_info'] = $this->admin_model->getmemberUserIds();

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Edit/edit_facilitator_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* edit_facilitator

		* This method validates the edit_facilitator form and then passes the info to the model for updating
		*
		* Params: None
		* Returns: A user message to display 
		*--------------------------------------------------------------------------------------------*/
		public function edit_facilitator()
		{
			$this->form_validation->set_rules('user_id', 'Username', 'required');
			$this->form_validation->set_rules('facilitator_id', 'Facilitator', 'required');
			$this->form_validation->set_rules('f_name', 'First Name', 'trim|required');
			$this->form_validation->set_rules('l_name', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('p_num', 'Phone Number', 'trim|required|regex_match[/^[0-9]{10}$/]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('address', 'Address', 'trim|required');
			
			if ($this->form_validation->run())
			{
				$fac_id = $this->input->post('facilitator_id');
				$first_name = $this->input->post('f_name');
				$last_name = $this->input->post('l_name');
				$phone = $this->input->post('p_num');
				$email = $this->input->post('email');
				$address = $this->input->post('address');

				$message = $this->admin_model->edit_facilitator_account($fac_id,$first_name,$last_name,$phone,$email,$address);
				$this->facilitator_edit($message);
			}
			else 
			{	
				$this->facilitator_edit();
			}
		}

		/*--------------------------------------------------------------------------------------------------
		* get_fac_info
	
		* gets data on a single facilitator based on their id
		*
		* Params: None
		* Returns: echoed facilitator information
		*--------------------------------------------------------------------------------------------*/
		public function get_fac_info()
		{
			$fac_id = $this->input->post('f_id');
			$result = $this->admin_model->find_facilitator_info($fac_id);

			echo $result->first_name . "-" . $result->last_name . "-" . $result->email . "-" . $result->address . "-" . $result->phone_number;
		}

		/*--------------------------------------------------------------------------------------------------
		* teacher_edit 
		* This method loads the teacher_edit view with all needed data
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function teacher_edit($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['teacher_info'] = $this->admin_model->getUsersBasedOnId(4);
			$data['classroom_data'] = $this->calendar_model->getClasses();
			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Edit/edit_teacher_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* edit_teacher

		* This method validates the teacher_edit form and then passes the info to the model for updating
		*
		* Params: None
		* Returns: A user message to display 
		*--------------------------------------------------------------------------------------------*/
		public function edit_teacher()
		{

			$this->form_validation->set_rules('user_id', 'Username', 'required');
			$this->form_validation->set_rules('classroom_id', 'Classroom', 'required');
			$this->form_validation->set_rules('f_name', 'First Name', 'trim|required');
			$this->form_validation->set_rules('l_name', 'Last Name', 'trim|required');

			$checked = (isset($_POST['checkBox']))?true:false;
    		if($checked)
        	{
				$this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[4]|max_length[32]');
				$this->form_validation->set_rules('passwordcon', 'Password Confirmation', 'required|trim|matches[password]');
			}
			
			if ($this->form_validation->run())
			{
				$teacher_id = $this->input->post('user_id');
				$first_name = $this->input->post('f_name');
				$last_name = $this->input->post('l_name');
				$classroom = $this->input->post("classroom_id");

				$message = $this->admin_model->edit_teacher_account($teacher_id,$first_name,$last_name,$classroom);
				if($checked)
				{
					$password = $this->input->post('password');
					$message = $this->admin_model->edit_account($teacher_id,$password);
				}

				$this->teacher_edit($message);
			}
			else 
			{	
				$this->teacher_edit();
			}
		}

		/*--------------------------------------------------------------------------------------------------
		* get_teacher_info
	
		* gets data on a single teacher based on their id
		*
		* Params: None
		* Returns: echoed teacher information
		*--------------------------------------------------------------------------------------------*/
		public function get_teacher_info()
		{
			$teacher_id = $this->input->post('t_id');
			$result = $this->admin_model->find_teacher_info($teacher_id);

			echo $result->first_name . "-" . $result->last_name . "-" . $result->classroom_id;
		}

		/*--------------------------------------------------------------------------------------------------
		* admin_edit 
		* This method loads the admin edit view with all needed data
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function admin_edit($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['admin_info'] = $this->admin_model->getUsersBasedOnId(1);

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Edit/edit_admin_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* edit_admin

		* This method validates the admin_edit form and then passes the info to the model for updating
		*
		* Params: None
		* Returns: A user message to display 
		*--------------------------------------------------------------------------------------------*/
		public function edit_admin()
		{
			$this->form_validation->set_rules('user_id', 'Username', 'required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');

			if ($this->form_validation->run())
			{
				$admin_id = $this->input->post('user_id');
				$password = $this->input->post('password');

				$message = $this->admin_model->edit_account($admin_id,$password);

				$this->admin_edit($message);
			}
			else 
			{	
				$this->admin_edit();
			}
		}

		/*--------------------------------------------------------------------------------------------------
		* get_student_info
	
		* gets data on a single student based on their id
		*
		* Params: None
		* Returns: echoed student information
		*--------------------------------------------------------------------------------------------*/
		public function get_student_info()
		{
			$student_id = $this->input->post('s_id');
			$result = $this->admin_model->find_student_info($student_id);
			
			echo $result->first_name . "-" . $result->last_name . "-" . $result->grade . "-" . $result->classroom_id;
		}
		

		/*--------------------------------------------------------------------------------------------------
		* admin_edit 
		* This method loads the admin edit view with all needed data
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function student_edit($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['member_info'] = $this->admin_model->getmemberUserIdsWithStudents();
			$data['classroom_data'] = $this->calendar_model->getClasses();

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Edit/edit_student_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* edit_admin

		* This method validates the admin_edit form and then passes the info to the model for updating
		*
		* Params: None
		* Returns: A user message to display 
		*--------------------------------------------------------------------------------------------*/
		public function edit_student()
		{
			$this->form_validation->set_rules('user_id', 'member', 'required');
			$this->form_validation->set_rules('student_id', 'Student', 'required');
			$this->form_validation->set_rules('classroom_id', 'Classroom', 'required');
			$this->form_validation->set_rules('f_name', 'First Name', 'trim|required');
			$this->form_validation->set_rules('l_name', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('grade', 'Grade', 'trim|required');

			if ($this->form_validation->run())
			{
				$student_id = $this->input->post('student_id');
				$first_name = $this->input->post('f_name');
				$last_name = $this->input->post('l_name');
				$classroom = $this->input->post("classroom_id");
				$grade = $this->input->post('grade');

				$message = $this->admin_model->edit_student_account($student_id,$first_name,$last_name,$classroom,$grade);

				$this->student_edit($message);
			}
			else 
			{	
				$this->student_edit();
			}
		}

		/*--------------------------------------------------------------------------------------------------
		* general_edit 
		* This method loads the general edit view with all needed data
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function general_edit($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['general_info'] = $this->admin_model->getUsersBasedOnId(5);

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Edit/edit_general_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* edit_general

		* This method validates the general_edit form and then passes the info to the model for updating
		*
		* Params: None
		* Returns: A user message to display 
		*--------------------------------------------------------------------------------------------*/
		public function edit_general()
		{
			$this->form_validation->set_rules('user_id', 'Username', 'required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');

			if ($this->form_validation->run())
			{
				$general_id = $this->input->post('user_id');
				$password = $this->input->post('password');

				$message = $this->admin_model->edit_account($general_id,$password);

				$this->general_edit($message);
			}
			else 
			{	
				$this->general_edit();
			}
		}

		/*--------------------------------------------------------------------------------------------------
		* general_board
		* This method loads the general board view with all needed data
		*
		* Params: A user message to display 
		* Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function board_edit($message = "")
		{
			$this->check_login();
			
			$data['message'] = $message;
			$data['board_info'] = $this->admin_model->getUsersBasedOnId(3);

			$this->load->view($this->admin_views . 'sidebar');
			$this->load->view($this->admin_views . 'Account Edit/edit_board_body', $data);
		}

		/*--------------------------------------------------------------------------------------------------
		* edit_board

		* This method validates the board_edit form and then passes the info to the model for updating
		*
		* Params: None
		* Returns: A user message to display 
		*--------------------------------------------------------------------------------------------*/
		public function edit_board()
		{
			$this->form_validation->set_rules('user_id', 'Username', 'required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');

			if ($this->form_validation->run())
			{
				$board_id = $this->input->post('user_id');
				$password = $this->input->post('password');

				$message = $this->admin_model->edit_account($board_id,$password);

				$this->board_edit($message);
			}
			else 
			{	
				$this->board_edit();
			}
		}

}