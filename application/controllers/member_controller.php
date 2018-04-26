<?php

class Family_controller extends CI_Controller {


		/*-------------------------------------------------------
		* __construct()
		* This constructor sets up needed material for this class
		*------------------------------------------------------*/
		public function __construct()
        {
                parent::__construct();

                // Loads models for connecting with the database 
                $this->load->model('family_model');
                $this->load->model('calendar_model');
                // This is a tool used to generate a URL and link a new page 
                $this->load->helper('url_helper');
                $this->load->helper('form');
              	$this->load->library('session');
              	 $this->load->library('form_validation');
              	// These variables just hold the names of the folders that contain their respective views
              	// in case we want to change the folders later
              	$this->family_views = "family/";
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
		 	if($this->session->userdata('role_id') != 2){
            	redirect('login');
       		 }
		}

        /*-------------------------------
        * index()
        * This method is automatically called. It sets up the page!
        *-------------------------------*/
        public function index()
        {

       		// PLEASE NOTE: If you want another page to show up first, links will need to be changed! PLEASE BE CAREFUL!!!
			redirect('family_controller/mybookings');
			
		}

		/*--------------------------------------------------------------------------------------------------
		* calendar()
		* This method loads the family calendar view
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function calendar()
		{
			// Check if someone is currently logged in 
		 	$this->check_login();
			$classroom_data = $this->calendar_model->getClasses();
			$data['classroom_data'] = $classroom_data;
			$data['js'] = "script/family-calendar.js";
			$data['title'] = 'Book Facilitation';
			$data['user_message'] = 'Select a week, classroom, and then a facilitation slot.';
			$data['error_message'] = null;

			$this->load->view($this->family_views . 'family_sidebar');
			$this->load->view(  $this->calendar_views . 'calendar_body', $data);
		}


		/*--------------------------------------------------------------------------------------------------
		* logout()
		* This method redirects the user to the login page and removes all their session data 
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function logout(){
			redirect('login');
		}


		
		/*--------------------------------------------------------------------------------------------------
		* load_header()
		* This method loads the header view
		*
		* Params & Returns: None
		* Note: Called by the calendar file
		*--------------------------------------------------------------------------------------------*/
		private function load_header(){
			$this->load->view('templates/familyHeader');
		}


		/*--------------------------------------------------------------------------------------------------
		* load_sign_up()
		* This method loads the sign up form view for the modal
		*
		* Params & Returns: None
		* Note: Called by the family calendar js file
		*--------------------------------------------------------------------------------------------*/
		public function load_sign_up(){
			$this->load->view(  $this->family_views . 'facilitation-sign-up');
		}

		/*--------------------------------------------------------------------------------------------------
		* load_sign_up_fieldtrip()
		* This method loads the sign up form view for the modal for fieldtrips
		*
		* Params & Returns: None
		* Note: Called by the family calendar js file
		*--------------------------------------------------------------------------------------------*/
		public function load_sign_up_fieldtrip(){
			$this->load->view(  $this->family_views . 'fieldtrip-sign-up');
		}

		/*----------------------------------------------------------------------------------------
		* THE FOLLOWING METHODS ARE USED SOLEY FOR THE PURPOSE OF COMMUNICATING WITH THE DATABASE
		*----------------------------------------------------------------------------------------*/

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
		* get_facilitator_data()
		* This method gathers the facilitator days and names of the corresponding family id logged in
		*
		* Params & Returns: None
		* PLEASE NOTE: This method echos out the data for the corresponding javascript recipient file
		*--------------------------------------------------------------------------------------------*/
		public function get_facilitator_data(){

			// Gather data from the caller file 
			$user_id = $this->session->userdata('user_id');
			$result = $this->family_model-> get_facilitators($user_id);

			// Echo data 
			foreach ($result as $row){
				echo $row["facilitator_id"] . "," . $row["name"] . ",";
			}

		}

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


		/*--------------------------------------------------------------------------------------------------
		* book_facilitation()
		* This method books a facilitation booking based on the information in the facilitation sign up form
		*
		* Params & Returns: None
		* Note: Called by the family calendar js file
		*--------------------------------------------------------------------------------------------*/
		public function book_facilitation(){

			// Gather form data 
			$slot_id = $this->input->post('s_id');
			$notes = $this->input->post('comments');
			$facilitator_id = $this->input->post('f_id');

			// Insert into database if there aren't any conflicts 
			$this->family_model->book($slot_id, $notes, $facilitator_id);
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
		* mybookings code below -> by wesley
		*--------------------------------------------------------------------------------------------------*/


		public function mybookings($message = "")
		{
			// just a place holder for data 
			$start_date = Date("Y-m-d");
			$end_date = Date("2099-12-31");
			echo "this is the message: " + $message;

			// gets the family id
			$user_id = $this->session->userdata('user_id');
			$data['message'] = $message;
			// gets all the facilitators corresponding to the family id
			$facilitator_ids = $this->family_model-> get_facilitators($user_id);
			$mybookings_data = $this->family_model->my_bookings_data($facilitator_ids,$start_date,$end_date);
			$data['mybookings_data'] = $this->convert_displayable($mybookings_data);
			// Check if someone is currently logged in 
			if (empty($mybookings_data))
			{
						$data['message'] = 'You are not signed up for any slots in this time range!';
			}

		 	$this->check_login();
			//$this->load->view(  $this->family_views . 'my_booking_head');  
			//$this->load_header();
			$this->load->view($this->family_views . 'family_sidebar');
			$this->load->view(  $this->family_views . 'my_booking_body', $data);
		}

		public function date_submit()
		{
			
			$this->form_validation->set_rules('start_date', 'start date', 'required');
			$this->form_validation->set_rules('end_date', 'end date', 'required');

			// If all fields have been validated
			if ($this->form_validation->run()) 
			{ 
				// gets the start date specified by user with html date picker
				$s_date = new DateTime($this->input->post('start_date'));
				// gets the end dat specified by user with html date picker
				$e_date = new DateTime($this->input->post('end_date'));

				// Make sure dates are valid
				if ($s_date <= $e_date)
				{

					// gets the family id
					$user_id = $this->session->userdata('user_id');
					
					// gets all the facilitators corresponding to the family id
					$facilitator_ids = $this->family_model-> get_facilitators($user_id);

					// convert date object into a string i can use for my queries.
					$start_date = $s_date->format('Y-m-d');
					$end_date = $e_date->format('Y-m-d');

					// call model method to retrieve data for the html table
					$mybookings_data = $this->family_model->my_bookings_data($facilitator_ids,$start_date,$end_date);
					$data['mybookings_data'] = $this->convert_displayable($mybookings_data);
					$data['message'] = '';
					
					if (empty($mybookings_data))
					{
						$data['message'] = 'You are not signed up for any slots in this time range!';
					}
					// pass new data into table
					//$this->load->view(  $this->family_views . 'my_booking_head');  
					//$this->load_header();
					$this->load->view($this->family_views . 'family_sidebar');
					$this->load->view(  $this->family_views . 'my_booking_body', $data);

				}
				else
				{
					$this->mybookings('Invalid start date and end date!');
				}

			}
			else
			{
				$this->mybookings('Invalid start date and end date!');
			}
		}

		private function convert_displayable($booking_info)
		{
			if (!empty($booking_info))
			{

				foreach( $booking_info as $info){
					$info['date_scheduled'] = Date("d F D Y", intVal(preg_replace("/[0-9]/", "", $info['date_scheduled'])));
					$info['time_start'] = explode(" ", $info['time_start'])[1];
					$info['time_end'] = explode(" ", $info['time_end'])[1];
				}
			}
			return $booking_info;
		}

		private function get_displayable_date ($date)
		{
			return Date('d F D Y', $date);
		}
		

		/* DONATION */

		/*--------------------------------------------------------------------------------------------------
		* donation()
		* This method displays the first page for user to choose the month they want to donate their extra hours 
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/

		public function donation($message = "")
		{
			$this->check_login();
			//$this->load->view($this->family_views .'donate_hours_head');
			//$this->load_header();
			$this->load->view($this->family_views . 'family_sidebar');
			$this->load->view($this->family_views .'donate_hours_body');
		}

		/*--------------------------------------------------------------------------------------------------
		* donation_main_page()
		* After the user chooses a month to donate and clicks on "Donate" button, they will be redirected to this page
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/

		public function donation_main_page($month, $message = "")
		{
			$user_id = $this->session->userdata('user_id');
			$data['max_donation'] = $this->family_model->get_donatable_hours($user_id, $month);

			if ($data['max_donation'] == 0)
			{
				//$this->load->view($this->family_views .'donation_main_head');
				//$this->load_header();
				$this->load->view($this->family_views . 'family_sidebar');
				$this->load->view($this->family_views .'donation_main_body_no_hours');
			}

			else{
				$data['family_info'] = $this->family_model->getFamilyUserIds($user_id, $month);
				$data['month'] = $month;
				$data['message'] = $message;

				//$this->load->view($this->family_views .'donation_main_head');
				//$this->load_header();
				$this->load->view($this->family_views . 'family_sidebar');
				$this->load->view($this->family_views .'donation_main_body', $data);
			}		
		}
	

		/*--------------------------------------------------------------------------------------------------
		* donation_button()
		* After the user clicks on "Donate" button, form validation process begins here
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function donate_button(){

			$this->form_validation->set_rules('month', 'month', 'required');

			if ($this->form_validation->run()){
				
				//open another form to redirect to another page
				$this->donation_main_page($this->input->post('month'));
				
			}
			else {
				
				$this->donation();

			}
		}

		/*--------------------------------------------------------------------------------------------------
		* donate
		* After the user clicks on submit button on the donation form validation process begins here and hours are
		* donated if input is valid
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function donate()
		{
			$this->form_validation->set_rules('user_id', 'family', 'required');
			$this->form_validation->set_rules('donation', 'hour donation', 'required');
			$month = $this->input->post('month'); // hidden field on the form

			if ($this->form_validation->run()){
				
				$donor = $this->session->userdata('user_id');
				$recipient = $this->input->post('user_id');
				$donation = $this->input->post('donation');

				$this->family_model->donate_hours($month, $donor, $recipient, $donation);
				redirect('family_controller/donation_success');
				
			}
			else {
				
				$this->donation_main_page($month);

			}
		}


		/*--------------------------------------------------------------------------------------------------
		* donation_success
		* This method displays a success message for donation success
		*
		* Params & Returns: None
		*--------------------------------------------------------------------------------------------*/
		public function donation_success()
		{
			$this->check_login();
			//$this->load->view($this->family_views .'donation_main_head');
			//$this->load_header();
			$this->load->view($this->family_views . 'family_sidebar');
			$this->load->view($this->family_views .'donation_main_body_success');
		}

}