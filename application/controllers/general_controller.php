<?php

class General_controller extends CI_Controller {

	public function __construct() {

		parent::__construct();

		 // Initiates model connection (not sure if we need this)
	    $this->load->model('general_model');
	    // This is a tool used to generate a URL and link a new page 
	    $this->load->helper('url_helper');
	    $this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('session');

		/* Destroy user session data if the user has logged out */
		if ($this->session->has_userdata('user_id')){
              		$this->session->unset_userdata('user_id');
        }

		$this->general_views = "general/";

}

	/* Home page is where the audience can see SHINE's mission statements, etc */

	public function index(){
		redirect('general_controller/home');
	}

	public function home(){
		$this->load->view($this->general_views . 'Home_head');
		$this->load->view($this->general_views . 'Home_body');
	}


	public function login_validation(){
		$this->form_validation->set_rules('uname', 'uname', 'required');
		$this->form_validation->set_rules('psw', 'psw', 'required');
		echo 'form validation';

		if ($this->form_validation->run()){
			$username = $this->input->post('uname');
			$pwd = $this->input->post('psw');
			$result = $this->general_model->check_credentials($username, $pwd);
			echo 'i am here';

			if (empty($result['user_id'])){
					//$this->login('Invalid Login Credentials');
					echo 'empty';
					return 'Invalid Login Credentials';
				}

			else {

					switch ($result['role']){

						// Admin Login
						case 1: 
							echo 'admin';

							$this->session->set_userdata($result);

							redirect('admin_controller');

							break;

						// Member Login
						case 2: 
							
							$this->session->set_userdata($result);

							redirect('member_controller');
							break;

						
						
						// Display error message
						default:  
							return "Invalid Login Credentials";
						}
				} // end else
	       	}// end if 
	    return "Invalid Login Credentials";
		}

	}


