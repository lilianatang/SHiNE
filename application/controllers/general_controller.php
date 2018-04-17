<?php

class General_controller extends CI_Controller {

	public function __construct() {

		parent::__construct();

		 // Initiates model connection (not sure if we need this)
	    //$this->load->model('calendar_model');
	    // This is a tool used to generate a URL and link a new page 
	    $this->load->helper('url_helper');
	    $this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('session');

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

}