<?php

/*--------------------------------------------------------------------------------------------------------
* This Class allows for database access. Any time a database query needs to be made, create a method here!
---------------------------------------------------------------------------------------------------------*/

class Login_model extends CI_Model {
	
	/*-----------------------------------------------------
	* __construct
	*
	* This constructor initiates the database connection.  
	*
	* Parameters & Return values: None
	*--------------------------------------------------------*/
	public function __construct()
	
	{
		//database is now available through $this->db
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
	public function check_credentials() 
	{

		// Get login data 
		$data = array(
			'username' => $this->input->post('username'),
			'password' => sha1($this->input->post('password'))
	    );

	    // Run query to get login info 
	    $this->db->where('username', $data['username']);
	    $this->db->where('encrypted_password', $data['password']);
	    $query = $this->db->get('users');
	    $result = $query->row_array();

	    if ( !$result  ){
	    	return 'no result';
	    }
	    else{
	    	return $result;
	    }
	    
	}

	/*-----------------------------------------------------------------------------
	* get_family_id() 
	* This method retrieves the family id from a given user_id if applicable
	*
	* Parameters: user-id 
	*
	* Return: The corresponding family_id if it exists or 'no result'
	*-----------------------------------------------------------------------------*/
	public function get_family_id($user_id)
	{
		// Run query to get family id 
		$this->db->select('family_id');
		$this->db->where('user_id', $user_id);
		$query = $this->db->get('family');
		$result = $query->row_array();

		if ( !$result  ){
	    	return 'no result';
	    }
	    else{
	    	return $result['family_id'];
	    }
	}
}

?>