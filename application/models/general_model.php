<?php

class General_model extends CI_Model {
	public function __construct()
	{
		$this->load->database();
	}

		/*-----------------------------------------------------------------------------
	* check_credentials() 
	* This method determines whether the inputted login credentials are valid 
	*
	* Parameters: None
	*
	* Return: The user information if the login is valid, and 'no result' otherwise 
	*-----------------------------------------------------------------------------*/
	public function check_credentials($username, $password) 
	{

		// Get login data 
		$data = array(
			/*'username' => $this->input->post('username'),
			'password' => sha1($this->input->post('password')*/
			'username' => $username,
			'password' => sha1($password)
	    );

	    // Run query to get login info 
	    $this->db->where('username', $data['username']);
	    $this->db->where('encrypted_password', $data['password']);
	    $query = $this->db->get('users');
	    $result = $query->row_array();

	    if ( !$result  ){
	    	return 'Error occurs!';
	    }
	    else{
	    	
	    	return $result;
	    }
	    
	}
}