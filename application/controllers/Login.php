<?php

class Login extends CI_Controller {


		/*-------------------------------------------------------
		* __construct()
		* This constructor sets up needed material for this class
		*------------------------------------------------------*/
		public function __construct()
        {
                parent::__construct();

                // Initiates model connection (not sure if we need this)
                $this->load->model('login_model');
                // This is a tool used to generate a URL and link a new page 
                $this->load->helper('url_helper');

                $this->load->helper('form');
				$this->load->library('form_validation');
				$this->load->library('session');
				
				/* Destroy user session data if the user has logged out */
				if ($this->session->has_userdata('user_id')){
              		$this->session->unset_userdata('user_id');
              	}

        }

        /*-------------------------------
        * index()
        * This function is automatically called. It sets up the login page!
        *-------------------------------*/
        public function index()
        {
			redirect('login');
		}
		

		/*------------------------------------------
		* login
		* Loads the login view
		*
		* parameters: Message - takes a specific error message to display beneath the submit button
		*------------------------------------------*/
		private function login($message = "")
		{

			$data['error'] = $message;
			$this->load->view('login', $data);
		}

		/*----------------------------
		* check_users
		* This is called when the user clicks the login submit. It checks which user is logging in and redirects them accordingly
		*---------------------------*/
		public function check_users()
		{

			// Set form rules
			$this->form_validation->set_rules('username', 'User ID:', 'required');
			$this->form_validation->set_rules('password', 'Password', 'required');

			// If all fields are full ...
			if ($this->form_validation->run())
			{
				// Check if they exist in the database 
				$result = $this->login_model->check_credentials();

				if (empty($result['user_id'])){
					$this->login('Invalid Login Credentials');
				}

				else {

						switch ($result['role_id']){

							// Admin Login
							case 1: 

								$this->session->set_userdata($result);

								redirect('admin_controller');

								break;

							// Family Login
							case 2: 
								
								$this->session->set_userdata($result);

								redirect('family_controller');
								break;

							// boardmember login
							case 3: echo 'BOARDMEMBER'; break;


							// Teacher login
							case 4: 

								$this->session->set_userdata($result);
								redirect('teacher_controller');
							
								break;

							// General login
							case 5: 

								$this->session->set_userdata($result);
								redirect('general_user_controller');
					
								break;
							
							// Display error message
							default:  
								$this->login("Invalid Login Credentials");
							}
					} // end else
	       	 	}// end if 

			}
		}