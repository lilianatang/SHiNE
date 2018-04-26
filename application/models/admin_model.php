<?php

class Admin_model extends CI_Model {
	
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
		$this->load->helper('date');
	}

	/* ------------------------------------------ GETTER METHODS FOR FILLING DROPDOWNS  ---------------------------------------------  */

	/*-----------------------------------------------
	* getFamilyUserIds
	* This method retrieves all user ids that are family users
	*-----------------------------------------------*/
	public function getFamilyUserIds()
	{
		/*"Select username, user_id
				  from users
				  where role = 2"*/
		$this->db->select("username, user_id");
		$this->db->from('users');
		$this->db->where('role', 2);
		$query = $this->db->get();
		return $query->result_array();
	}

	/*-----------------------------------------------
	* getFamilyUserIds
	* This method retrieves all user ids that are family users that have students
	*-----------------------------------------------*/
	public function getFamilyUserIdsWithStudents()
	{
		/*"Select username, user_id
		  from users
		  where role = 2 and
		  "exists (select user_id from students where users.user_id = students.user_id)*/
		$this->db->select("username, user_id");
		$this->db->from('users');
		$this->db->where('role', 2);
		$this->db->where("exists (select user_id from students where users.user_id = students.user_id)");
		$query = $this->db->get();
		return $query->result_array();
	}

	/*------------------------------------------------------------------------------------
	* get_facilitators
	* This method takes the given user id and retrieves all the facilitator ids and names
	*
	* Parameters: A valid user id
	*
	* Return: The query result in the form of an associative array 
	*-------------------------------------------------------------------------------------*/
	public function get_facilitators($user_id)
	{

		$this->db->select("facilitator_id, CONCAT(first_name, \" \", last_name) as name");
		$this->db->where('user_id', $user_id);
		
		/* Perform the query */
		$query = $this->db->get('facilitator');
		return $query->result_array();
		
	}

	/*
	* getFamilyStudents gets all the students that belong to a 
	* family with the specified user id
	*
	* parameters: user_id corresponding to the family
	* return: The query result
	*/
	public function get_students($userID)
	{

		$this->db->select("*");
		$this->db->where('user_id', $userID);
		$query = $this->db->get('students');
		return $query->result_array();
	}

	

	public function get_family_usernames()
	{
		$this->db->select("username, family_id");
		$this->db->from('users, family');
		$this->db->where('users.user_id = family.user_id');
		$query = $this->db->get();
		return $query->result_array();
	}


	/**
	* getFamilyID gets a family id based on a corresponding user id
	*
	* parameters: userID to look for
	* return: none
	*/
	public function getFamilyId ($userID)
	{
		/*
			Select family_id
				  from family
				  where family.user_id = $userID
		*/
		$this->db->select("family_id");
		$this->db->from('family');
		$this->db->where('user_id', $userID);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* ---------------------------------------------- ADMIN FAMILY STATISTICS ---------------------------------------------  */

	/**
	*
	* getFamilyHistory gets all the data from the history table
	* that pertains to a family
	*
	* parameters: userID, year to check in, month to check in
	* return: none
	*/
	public function getFamilyHistory($userID,$year,$month)
	{
		$this->db->select("start_date");
		$this->db->select("end_date"); 
		$this->db->select("required_hours"); 
		$this->db->select("completed_hours");
		$this->db->select("hours_given");
		$this->db->select("hours_received");
		$this->db->select("history_id");
		$this->db->from('history');
		$this->db->where('year(history.start_date)', $year);
		$this->db->where('month(history.start_date)', $month);
		$this->db->where('history.user_id', $userID);
		$query = $this->db->get();
		return $query->result_array();
	}


	/**
	*
	* getYearlyHours gets all facilitation hours for a given school year 
	*
	* parameters: userID, year of the corresponding month, month
	* return: The total hours facilitated
	*/
	public function getYearlyHours($userID,$year, $month)
	{ 
		$total = 0;
		$userID = intval($userID);
		$year = intval($year);

		// If we're looking at a month in the fall
		if (intval($month) >= 9)
		{ 
			// Find total for Sept to Dec of this year
			for ($i = 9; $i < 13; $i ++){
				$total = $total + $this->getMonthlyHours($userID, $i, $year);
			}
			// Find total for Jan to June of the next year ($year + 1)
			for ($i = 1; $i < 7; $i ++){
				$total = $total + $this->getMonthlyHours($userID, $i, $year + 1);
			}
		}

		// We're looking at a month in the winter
		else 
		{
			// Find total for Sept to Dec of last year
			for ($i = 9; $i < 13; $i ++){
				$total = $total + $this->getMonthlyHours($userID, $i, $year - 1);
			}
			// Find total for Jan to June of this year
			for ($i = 1; $i < 7; $i ++){
				$total = $total + $this->getMonthlyHours($userID, $i, $year);
			}
		}
		return $total;
	}


	/**
	*
	* getMonthlyHours gets all facilitation hours for a given month
	*
	* parameters: userID, year of the corresponding month, month
	* return: The total hours facilitated
	*/
	public function getMonthlyHours($userID, $month, $year)
	{
		$this->db->select("SUM(completed_hours + hours_received - hours_given) as value");
		$this->db->from('history');
		$this->db->where('year(start_date)', $year);
		$this->db->where('month(start_date)', $month);
		$this->db->where('user_id', $userID);
		$query = $this->db->get();
		$result = $query->result_array();
		if (empty($result)){
			return 0;
		}
		else{
			return $result[0]['value'];
		}
	}

	/*------------------------------------------------------------
	* get_preset_requirements
	* This method retrieves the existing hourly requirements from the database 
	* 
	* Params: None 
	* 
	* Return: The query result 
	*------------------------------------------------------------*/
	public function get_preset_requirements()
	{
		$this->db->select("*");
		$this->db->from('family_requirements');
		$this->db->order_by('number_of_students', "asc");
		$query = $this->db->get();

		return $query->result_array();
	}

	/*------------------------------------------------------------
	* insert_family_requirement
	* This method inserts a new rule for family requirements	
	* 
	* Params: Num_students - Number of students the rule applies to
	*         required_hours - requred hours to add to the new rule 
	* 
	* Return: The new rule_id generated on insert 
	*------------------------------------------------------------*/
	public function insert_family_requirement($num_students, $required_hours)
	{
		$data = array ('number_of_students' => $num_students, 'required_hours' => $required_hours );
		
		$this->db->insert('family_requirements', $data);

		return $this->db->insert_id();
	}

	/*------------------------------------------------------------
	* update_family_requirement
	* This method updates an existing rule for family requirements
	* 
	* Params: id - the rule_id of the rule 
	*         Num_students - Number of students the rule applies to
	*         required_hours - requred hours to add to the new rule 
	* 
	* Return: None
	*------------------------------------------------------------*/
	public function update_family_requirement($id, $num_students, $required_hours)
	{
		$this->db->set('number_of_students', $num_students);
		$this->db->set('required_hours', $required_hours);
		$this->db->where('rule_id', $id);
		return $this->db->update('family_requirements');
	}

	/*------------------------------------------------------------
	* delete_family_requirement
	* This method deletes an existing rule in family requirements
	* 
	* Params: id - the rule_id of the rule 
	*         Num_students - Number of students the rule applies to
	*         required_hours - requred hours to add to the new rule 
	* 
	* Return: None
	*------------------------------------------------------------*/
	public function delete_family_requirement($id, $num_students, $required_hours)
	{
		$this->db->where('rule_id', $id);
		return $this->db->delete('family_requirements');
	}

	/*------------------------------------------------------------
	* update_history_entry
	* This method updates a single entry in the history table
	* 
	* Params: id - the rule_id of the rule 
	*         hours_given - given hours to be updated
	*         hours_required - requred hours to be updated
	*		  hours_completed - completed hours to be updated
	*		  hours_received - recived hours to be updated
	*		  id - id of the entry to be updated
	* 
	* Return: None
	*------------------------------------------------------------*/
	public function update_history_entry($id,$hours_given,$hours_received,$hours_completed,$hours_required)
	{
		$this->db->set('hours_given',$hours_given);
		$this->db->set('hours_received', $hours_received);
		$this->db->set('completed_hours',$hours_completed);
		$this->db->set('required_hours', $hours_required);		
		$this->db->where('history_id',$id);
		$this->db->update('history');
	}

	/* -------------------------------------------------------- Slot Creation ------------------------------------------------------ */

	/*---------------------------------------------------------
	* get_date
	* Retreives the date based on a given slot_id 
	*
	* param: slot_id - a valid slot id
	* return: Returns the date corresponding to the slot or null if the slot id isn't valid
	*---------------------------------------------------------*/
	private function get_date($slot_id)
	{
		$this->db->select('date_scheduled');
		$this->db->where('slot_id', $slot_id);
		$query = $this->db->get('facilitation_times');
		$result = $query->row_array();
		if (! empty($result)){
			return $result['date_scheduled'];
		}
		else {
			return null;
		}
	}



	/*---------------------------------------------------------
	* get_class_id
	* Retreives the class id based on a given slot_id 
	*
	* param: slot_id - a valid slot id
	* return: Returns the class id corresponding to the slot or null if the slot id isn't valid
	*---------------------------------------------------------*/
	private function get_class_id($slot_id)
	{
		$this->db->select('classroom_id');
		$this->db->where('slot_id', $slot_id);
		$query = $this->db->get('facilitation_times');
		$result = $query->row_array();
		if (! empty($result)){
			return $result['classroom_id'];
		}
		else {
			return null;
		}
	
	}


	/*---------------------------------------------------------
	* has_conflicts_editing
	* Determines if there's a slot that exists with the given parameters but is not the slot with the given slot_id
	*
	* param: slot_id - a valid slot id
	*        start_time & end_time - Time in the datetime format
	*        class - a classroom id 
	*
	* return: Returns true if there's a slot with the same slot id and times and false otherwise
	*---------------------------------------------------------*/
	private function has_conflicts_editing ($slot_id, $class, $start_time, $end_time)
	{
		$this->db->select('slot_id');
		$this->db->group_start()
         ->where('time_start >= ', $start_time) // This makes sure we're not creating a slot within a slot 
         ->where('time_end <= ', $end_time)
         ->or_where('time_start <= ', $start_time)
         ->where('time_end >= ', $end_time)
     	->group_end();
		$this->db->where('classroom_id', $class);
		$this->db->where('slot_id !=' . $slot_id);		
		$query = $this->db->get('facilitation_times');
		return !empty($query->result_array());
	}


	/*---------------------------------------------------------
	* has_conflicts
	* Determines if there's a slot that exists with the given parameters
	*
	* param: 
	*        start_time & end_time - Time in the datetime format
	*        class - a classroom id 
	*
	* return: Returns true if there's a slot with the same slot id and times and false otherwise
	*---------------------------------------------------------*/
	private function has_conflicts ($class, $start_time, $end_time, $date)
	{
		return !empty($this->get_conflicts($class, $start_time, $end_time, $date));
	}



	/*---------------------------------------------------------
	* get_conflicts
	* Determines if there's slots with the given parameters and retrieves their slot ids
	*
	* param: 
	*        start_time & end_time - times in datetime format
	*        class - a classroom id 
	*        date - a date
	*
	* return: Returns true if there's a slot with the same slot id and times and false otherwise
	*---------------------------------------------------------*/
	public function get_conflicts ($class, $start_time, $end_time, $date)
	{
		$this->db->select('slot_id');
		$this->db->group_start()
         ->where('time_start >= ', $start_time) // This makes sure we're not creating a slot within a slot 
         ->where('time_end <= ', $end_time)
         ->or_where('time_start <= ', $start_time)
         ->where('time_end >= ', $end_time)
     	->group_end();
		$this->db->where('classroom_id', $class);	
		$query = $this->db->get('facilitation_times');
		return $query->result_array();
	}


	/*---------------------------------------------------------
	* edit_slot
	* Edits slot information based on the information provided 
	*
	* parameters: slot_id - a valid slot id
	*             start_time & end_time - Time in the following format: '10:00:00'
	*             num_Facilitators - number of facilitators needed for a given slot 
	*
	* return: Success or failure message
	*---------------------------------------------------------*/
	public function edit_slot ($slot_id, $start_time, $end_time, $num_facilitators)
	{
		$date = $this->get_date($slot_id);
		$class = $this->get_class_id($slot_id);
		$new_start = $date . ' ' . $start_time;
		$new_end = $date . ' ' .  $end_time;
		if (!$this->has_conflicts_editing($slot_id, $class, $new_start, $new_end)){
			$this->db->set('time_start', $new_start);
			$this->db->set('time_end', $new_end);
			$this->db->set('facilitators_needed', $num_facilitators);
			$this->db->where('slot_id', $slot_id);
			$this->db->update('facilitation_times');
			return 'Slot successfully updated!';
		}
		return 'Creation unsuccessful. There is already a slot with that start time and end time';
	}

	/*---------------------------------------------------------
	* edit_fieldtrip
	* Edits fieldtrip information based on the information provided 
	*
	* parameters: slot_id - a valid slot id
	*             desc - The fieldtrip description
	*             full_desc - The fieldtrip description displayed when the slot is full
	*
	* return: Success or failure message
	*---------------------------------------------------------*/
	public function edit_fieldtrip($slot_id, $desc, $full_desc, $location)
	{
		$this->db->set('description', $desc);
		$this->db->set('full_description', $full_desc);
		$this->db->set('location', $location);
		$this->db->where('slot_id', $slot_id);
		$this->db->update('field_trips');
	}


	/*---------------------------------------------------------
	* delete_slot
	* Deletes a slot and sign ups associate with the slot
	*
	* parameters: slot_id - a valid slot id
	*
	* return: None
	*---------------------------------------------------------*/
	public function delete_slot($slot_id){
		// Delete the slot itself
		$this->db->where('slot_id', $slot_id);
		$this->db->delete('facilitation_times');
		// Delete sign ups for the slot 
		$this->db->where('slot_id', $slot_id);
		$this->db->delete('facilitating');
		// Delete the slot itself from fieldtrip if it's a fieldtrip
		$this->db->where('slot_id', $slot_id);
		$this->db->delete('field_trips');		
	}


	/*---------------------------------------------------------
	* get_defaults
	* Retrieves the preset times for faciliation slots 
	*
	* parameters: none
	*
	* return: A query result (array of arrays)
	*---------------------------------------------------------*/
	public function get_defaults()
	{
		$query = $this->db->get('preset_times');
		return $query->result_array();
	}


	/*---------------------------------------------------------
	* update_default
	* Updates the preset times for faciliation slots 
	*
	* parameters: An array of arrays with the following indices:
	*              start_time & end_time -> a time (10:00:00)
	*              facilitators_needed -> a number 
	*              slot_type -> 1 - morning , 2 - lunch, or 3 - afternoon
	*              
	*
	* return: none
	*---------------------------------------------------------*/
	public function update_defaults($form_info)
	{
		
		for($i = 0; $i < 3; $i ++)
		{
			$time = $form_info[$i];
			$this->db->set('start_time', $time['start_time']);
			$this->db->set('end_time', $time['end_time']);
			$this->db->set('facilitators_needed', $time['facilitators_needed']);
			$this->db->where('slot_type', $i + 1);
			$this->db->update('preset_times');
		}
	}


	/*---------------------------------------------------------------------------------
	* create_timeslot
	* Creates a single facilitation time slot for a classroom, also prevents insertion on 
	* Saturday or Sunday
	*
	* Parameters: $starttime - start time for block
	*			  $endtime - end time for block
	*             $facnum - number of faciltators needed for the block
	*             $startdate - date that block is on
	*
	* Return: Error messages where applicable
	* 
	*------------------------------------------------------------------------------------------*/
	public function create_timeslot($class, $starttime,$endtime,$facnum,$startdate, $is_fieldtrip)
	{
		$data = array(
			'classroom_id' => $class,
			'date_scheduled' => $startdate,
			'time_start' => $startdate . ' ' . $starttime ,
			'time_end' => $startdate . ' ' .  $endtime ,
			'facilitators_needed' => $facnum,
			'is_fieldtrip' => $is_fieldtrip,
		);
		// Gets the day of the week (Mon, Tue etc..) to prevent a slot being put on weekend
		$temp = date('D', strtotime($startdate));
		if($temp != "Sat" && $temp != "Sun")
		{
			if(!$this->has_conflicts($class, $data['time_start'], $data['time_end'], $data['date_scheduled']))
			{
				
				$this->db->insert('facilitation_times',$data);
				return "Timeslot successfully created!";
			} 
			else
			{
				return 'Creation unsuccessful due to time conflicts with an existing slot.';
			}
		} 
		else {
			return 'Creation unsuccessful. The slot is on a weekend and has not been created.';
		}
	}


	/*---------------------------------------------------------------------------------
	* create_fieldtrip
	* Creates a fieldtrip
	*
	* parameters: An array of arrays with the following indices:
	*              start_time & end_time -> a time (10:00:00)
	*              facilitators_needed -> a number 
	*              slot_type -> 1 - morning , 2 - lunch, or 3 - afternoon
	* 
	* Return: Error & success messages
	*--------------------------------------------------------------------------------------*/
	public function create_fieldtrip($fieldtrip_info)
	{
		// Delete all conflicts
		$conflicts = 
		$this->get_conflicts($fieldtrip_info['classroom'], 
			$fieldtrip_info['date'] . ' ' . $fieldtrip_info['start_time'], $fieldtrip_info['date'] . ' ' .  $fieldtrip_info['end_time'], $fieldtrip_info['date']);
		foreach ($conflicts as $slot)
		{
			$this->delete_slot($slot['slot_id']);
			
		}
		// create the fieldtrip
		$this->create_timeslot($fieldtrip_info['classroom'], $fieldtrip_info['start_time'], $fieldtrip_info['end_time'], $fieldtrip_info['num_facilitators'], $fieldtrip_info['date'], 1);
		$slot_id = $this->db->insert_id();

		if ($slot_id != null)
		{
			$data = array
			(   'slot_id' => $slot_id, 
				'location' => $fieldtrip_info['location'], 
				'description' => $fieldtrip_info['desc'], 
				'full_description' => $fieldtrip_info['full_desc'],
			);
			$this->db->insert('field_trips',$data);
			return 'Fieldtrip successfully created!';
		}
		else
		{
			return 'Creation unsuccessful. There was an error in creating a fieldtrip. ';
		}
	}


	/*---------------------------------------------------------------------------------
	* get_slot_id
	* Gets the slot id of a slot with the given parameters
	*
	* parameters: $class - class id 
	*             $date - date
	*             start_time & end time - time in 10:00 format
	* 
	* Return: The slot id or null if it doesn't exist 
	*--------------------------------------------------------------------------------------*/
	private function get_slot_id($class, $date, $start_time, $end_time)
	{
		$this->db->select('slot_id');
		$this->db->where("date_scheduled", $date); 
		$this->db->where("time_start", $date . ' ' . $start_time . ":00"); 
		$this->db->where("time_end", $date . ' ' . $end_time  . ":00");
		$this->db->where('classroom_id', $class);	
		$query = $this->db->get('facilitation_times');
		
		$results = $results = $query->row_array();
		if (!empty($results))
		{
			return $results['slot_id'];
		}
		else
		{
			return null;
		}
	}


	/* -------------------------------------------------------- EXPORT TO EXCEL -------------------------------------------------------- */

	/*--------------------------------------------------------------------------------------------------
	* get_history_data
	* This method retrieves history data for export purposes 
	*
	* Params: None
	* Returns: The query results for the history table
	*--------------------------------------------------------------------------------------------*/
	public function get_history_data(){
		
		$this->db->select("username as 'family username', start_date as 'start date', end_date as 'end date', completed_hours as 'completed hours', required_hours as 'required hours', hours_given as hours given, hours_received as hours received");
		$this->db->from('history');
		$this->db->join('users', 'history.user_id = users.user_id');
		$this->db->order_by("username", 'asc');
		$this->db->order_by("start_date", 'asc');
		$query = $this->db->get();
		
		return $query;
	}

	/*--------------------------------------------------- ACCOUNT CREATION ----------------------------------------------------------*/

	/*--------------------------------------------------------------------------------------------------
	* checkDuplicateUsername
	* This method checks if the given username already exists in the database
	*
	* Params: Username
	* Returns: True if it's a duplicate and false otherwise
	*--------------------------------------------------------------------------------------------*/
 	public function checkDuplicateUsername($username) {
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		$count_row = $query->num_rows();
		if ($count_row > 0) {
	    	return TRUE;
		} else {
	    	return FALSE;
		}
	}

	/*--------------------------------------------------------------------------------------------------
	* create_admin_account
	* This method creates a new admin account with the provided fields.
	*
	* Params: Username and password of the new account to be created
	* Returns: An error message or success message
	*--------------------------------------------------------------------------------------------*/
	public function create_admin_account($username, $password) {

		// Check for duplicate usernames before inserting in to the database 
		if ($this->checkDuplicateUsername($username)){
			return 'Duplicated username! Please choose another username!';
		}

		$this->create_new_user($username, $password, 1);

		return 'Account sucessfully created!';
	}

	/*--------------------------------------------------------------------------------------------------
	* create_general_account
	* This method creates a new general account with the provided fields.
	*
	* Params: Username and password of the new account to be created
	* Returns: An error message or success message
	*--------------------------------------------------------------------------------------------*/
	public function create_general_account($username, $password) {

		// Check for duplicate usernames before inserting in to the database 
		if ($this->checkDuplicateUsername($username)){
			return 'Duplicated username! Please choose another username!';
		}

		$this->create_new_user($username, $password, 5);

		return 'Account sucessfully created!';
	}

	/*--------------------------------------------------------------------------------------------------
	* create_board_member_account
	* This method creates a new board member account with the provided fields.
	*
	* Params: Username and password of the new account to be created
	* Returns: An error message or success message
	*--------------------------------------------------------------------------------------------*/
	public function create_board_member_account($username, $password) {

		if ($this->checkDuplicateUsername($username)){
			return 'Duplicated username! Please choose another username!';
		}
		
		$this->create_new_user($username, $password, 3);

		return 'Account sucessfully created!';
	}

	/*--------------------------------------------------------------------------------------------------
	* create_facilitator_account
	* This method creates a new facilitator account with the provided fields.
	*
	* Params: An associative array containing the following fields: user_id, first_name, last_name, email, address, phone_number
	* Returns: An error message or success message
	*--------------------------------------------------------------------------------------------*/
	public function create_facilitator_account($data) {
		
		/*
		"INSERT INTO facilitator(user_id, first_name, last_name, email, address, phone_number) VALUES"
			. "($family_id, '$first_name','$last_name', '$phone_number', '$email', '$address')";
		*/
		
		$this->db->insert('facilitator', $data);
		return 'Account sucessfully created!';
	}

	/*--------------------------------------------------------------------------------------------------
	* create_student_account
	* This method creates a new student account with the provided fields.
	*
	* Params: An associative array containing the following fields: username, password, fname, lname, and class_id
	* Returns: An error message or success message
	*--------------------------------------------------------------------------------------------*/
	public function create_student_account($data) {
		
		$this->db->insert('students', $data);
		
		return 'Account sucessfully created!';
	}

	/*--------------------------------------------------------------------------------------------------
	* create_teacher_account
	* This method creates a new teacher account with the provided fields.
	*
	* Params: An associative array containing the following fields: username, password, fname, lname, and class_id
	* Returns: An error message or success message
	*--------------------------------------------------------------------------------------------*/
	public function create_teacher_account($data) 
	{
		if ($this->checkDuplicateUsername($data['username'])){
			return 'Duplicated username! Please choose another username!';
		}

		// insert into users
		$this->create_new_user($data['username'], $data['password'], 4);
		
		$id =  $this->db->insert_id(); //This gets the newly generated id
		
		$data2 = array(
			'first_name' => $data['fname'],
			'last_name' => $data['lname'],
			'user_id' => $id,
			'classroom_id' => $data['class_id'],
		);

		// insert into teachers
		$this->db->insert('teachers', $data2);

		return 'Account successfully created!';
	}

	/*--------------------------------------------------------------------------------------------------
	* create_family_account()
	* This method creates a new family account with the provided fields.
	*
	* Params: A username and password for the new account  
	* Returns: An error message or success message
	*--------------------------------------------------------------------------------------------*/
	function create_family_account($username, $password) {
		
		if ($this->checkDuplicateUsername($username)){
			return 'Duplicated username! Please choose another username!';
		}

		$this->create_new_user($username, $password, 2);

		return "Family successfully created!";
	}

	/*--------------------------------------------------------------------------------------------------
	* create_new_user
	* This method creates a new entry in the users table
	*
	* Params: username, password & role id - (1 for admin, 2 for family, 3 for boardmember, 4 for teacher, and 5 for general)
	* Returns: None
	*--------------------------------------------------------------------------------------------*/
	private function create_new_user($username, $password, $role)
	{
		$data = array(
			'username' => $username,
			'encrypted_password' => sha1($password),
			'role' => $role,
		);
		$this->db->insert('users', $data);
	}

	/*--------------------------------------------------- ACCOUNT DELETION ----------------------------------------------------------*/

	/*--------------------------------------------------------------------------------------------------
	* getUsernames
	* This method gets all the usernames of users of the provided role id
	*
	* Params: role id - (1 for admin, 2 for family, 3 for boardmember, 4 for teacher, and 5 for general)
	* Returns: The query result in the form of an associatve array 
	*--------------------------------------------------------------------------------------------*/
	public function getUsernames($role)
	{
		$this->db->select('username');
		$this->db->from('users');
		$this->db->where('role', $role);
		$query = $this->db->get();
		return $query->result_array();
	}

	/*--------------------------------------------------------------------------------------------------
	* remove_account
	* This method removes users with the given username and role
	*
	* Params: role id - (1 for admin, 2 for family, 3 for boardmember, 4 for teacher, and 5 for general) & username
	* Returns: None
	*--------------------------------------------------------------------------------------------*/
	private function remove_account($username, $role)
	{
		$this->db->where('username', $username);
		$this->db->where('role', $role);
		$this->db->delete('users');
	}	

	/*--------------------------------------------------------------------------------------------------
	* remove_admin_account
	* This method removes admin with the given username
	*
	* Params: username
	* Returns: None
	*--------------------------------------------------------------------------------------------*/	
	function remove_admin_account($username){

		$this->remove_account($username, 1);

	}

	/*--------------------------------------------------------------------------------------------------
	* remove_general_account
	* This method removes a general account with the given username
	*
	* Params: username
	* Returns: None
	*--------------------------------------------------------------------------------------------*/	
	function remove_general_account($username){
		$this->remove_account($username, 5);
	}

	/*--------------------------------------------------------------------------------------------------
	* remove_teacher_account
	* This method removes a teacher account with the given username
	*
	* Params: username
	* Returns: None
	*--------------------------------------------------------------------------------------------*/	
	function remove_teacher_account($username){
		$this->remove_account($username, 4);
	}

	/*--------------------------------------------------------------------------------------------------
	* remove_board_member_account
	* This method removes a board member account with the given username
	*
	* Params: username
	* Returns: None
	*--------------------------------------------------------------------------------------------*/	
	function remove_board_member_account($username){
		$this->remove_account($username, 3);
	}

	/*--------------------------------------------------------------------------------------------------
	* remove_family_account
	* This method removes a famiy account based on the form data
	* This also deletes all associated facilitators and students in the family! (might change when we can edit them)
	*
	* Params & Returns: None
	*--------------------------------------------------------------------------------------------*/
	function remove_family_account($user_id){

		// Delete from users
		$this->db->where('user_id', $user_id);
		$this->db->where('role', 2);
		$this->db->delete('users');

		//delete all the facilitators from that family
		$this->db->where('user_id', $user_id);
		$this->db->delete('facilitator');

		// Delete from students 
		$this->db->where('user_id', $user_id);
		$this->db->delete('students');
	}

	/*--------------------------------------------------------------------------------------------------
	* remove_facilitator_account
	* This method removes a facilitator based on the id given
	*
	* Params & Returns: None
	*--------------------------------------------------------------------------------------------*/
	function remove_facilitator_account($facilitator_id){

		$this->db->where('facilitator_id', $facilitator_id);
		$this->db->delete('facilitator');
	}


	/*--------------------------------------------------------------------------------------------------
	* remove_student_account
	* This method removes a student based on the id given
	*
	* Params & Returns: None
	*--------------------------------------------------------------------------------------------*/
	function remove_student_account($student_id){

		$this->db->where('student_id', $student_id);
		$this->db->delete('students');
	}

	/*--------------------------------------------------- ACCOUNT EDIT ----------------------------------------------------------*/

	/*--------------------------------------------------------------------------------------------------
	* edit_account
	* This method updates a user login account with a new password
	*
	* Params: $username: the user id that is in the user table
	*		  $password: the new password for the user account
	* Return: message on what occured
	*--------------------------------------------------------------------------------------------*/

	function edit_account($username,$password)
	{
		$this->db->set('encrypted_password',sha1($password));
		$this->db->where('user_id', $username);
		
		if($this->db->update('users'))
		{
			return "Account updated.";
		}
		else
		{
			return "Error updating account.";
		}
	}

	/*--------------------------------------------------------------------------------------------------
	* find_facilitator_info
	* This method returns a single facilitator based on id
	*
	* Params: $facilitator_id:
	*		  
	* Return: single record from the database
	*--------------------------------------------------------------------------------------------*/
	public function find_facilitator_info($facilitator_id)
	{
		$this->db->select("*");
		$this->db->from("facilitator");
		$this->db->where('facilitator_id', $facilitator_id);

		$query = $this->db->get();

		return $query->row();

	}

	/*--------------------------------------------------------------------------------------------------
	* edit_facilitator_account
	* This method updates a facilitator account and their login info
	* Params: f_id,fname,lname,phone,email,address
	*		  
	* Return: message of what occured
	*--------------------------------------------------------------------------------------------*/
	public function edit_facilitator_account($f_id,$fname,$lname,$phone,$email,$address)
	{
		$this->db->set('first_name',$fname);
		$this->db->set('last_name',$lname);
		$this->db->set('email',$email);
		$this->db->set('address',$address);
		$this->db->set('phone_number',$phone);
		$this->db->where('facilitator_id',$f_id);
		
		if($this->db->update('facilitator'))
		{
			return "Account updated.";
		}
		else
		{
			return "Error updating account.";
		}
	}

	/*--------------------------------------------------------------------------------------------------
	* edit_facilitator_account
	* This method updates a teacher account and their login info
	* Params: f_id,fname,lname,phone,email,address
	*		  
	* Return: message of what occured
	*--------------------------------------------------------------------------------------------*/
	public function getUsersBasedOnId($roleid)
	{
		$this->db->select('username , user_id');
		$this->db->from('users');
		$this->db->where('role', $roleid);
		
		$query = $this->db->get();
		
		return $query->result_array();
	}

	/*--------------------------------------------------------------------------------------------------
	* find_teacher_info
	* This method returns a single teacher based on id
	*
	* Params: $teacher_id:
	*		  
	* Return: single record from the database
	*--------------------------------------------------------------------------------------------*/
	public function find_teacher_info($teacher_id)
	{
		$this->db->select("*");
		$this->db->from("teachers");
		$this->db->where('user_id', $teacher_id);

		$query = $this->db->get();

		return $query->row();
	}

	/*--------------------------------------------------------------------------------------------------
	* edit_teacher_account
	* This method updates a teacher account and their login info
	* Params: t_id,fname,lname,class
	*		  
	* Return: message of what occured
	*--------------------------------------------------------------------------------------------*/
	public function edit_teacher_account($t_id,$fname,$lname,$class)
	{
		$this->db->set('first_name',$fname);
		$this->db->set('last_name',$lname);
		$this->db->set('classroom_id',$class);
		$this->db->where('user_id',$t_id);
		
		if($this->db->update('teachers'))
		{
			return "Account updated.";
		}
		else
		{
			return "Error updating account.";
		}
	}

	/*--------------------------------------------------------------------------------------------------
	* find_student_info
	* This method returns a single student based on id
	*
	* Params: $student_id:
	*		  
	* Return: single record from the database
	*--------------------------------------------------------------------------------------------*/
	public function find_student_info($student_id)
	{
		$this->db->select("*");
		$this->db->from("students");
		$this->db->where('student_id', $student_id);

		$query = $this->db->get();
		if($this->db->affected_rows())
		{
			return $query->row();
		}
		
	}

	/*--------------------------------------------------------------------------------------------------
	* edit_student_account
	* This method updates a student account and their login info
	* Params: s_id,fname,lname,class,grade
	*		  
	* Return: message of what occured
	*--------------------------------------------------------------------------------------------*/
	public function edit_student_account($s_id,$fname,$lname,$class,$grade)
	{
		$this->db->set('first_name',$fname);
		$this->db->set('last_name',$lname);
		$this->db->set('grade',$grade);
		$this->db->set('classroom_id',$class);
		$this->db->where('student_id',$s_id);
		

		if($this->db->update('students'))
		{
			return "Account updated.";
		}
		else
		{
			return "Error updating account.";
		}
	}

	/*--------------------------------------------------- PUNCH IN AND OUT ----------------------------------------------------------*/	

	/*--------------------------------------------------------------------------------------------------
	* getCurrentDate
	* This method retrieves the current date in yyyy-mm-dd format
	*
	* Params: none
	* Return: String date in yyyy-mm-dd format
	*--------------------------------------------------------------------------------------------*/
	private function getCurrentDate(){
		return date("Y-m-d");
	}

	/*--------------------------------------------------------------------------------------------------
	* convertTime
	* Because the built in DateTime format produces a time in non-readable format, the following method is used
	* to convert the given unixTime into a human readable format 
	*
	* Params: $unixTime - Time in unixTime format
	* Return: Time in humanreadable format (for inserting into the database)
	*--------------------------------------------------------------------------------------------*/
	private function convertTime($unixTime) {
   		$dt = new DateTime("@$unixTime");
   		$dt->setTimezone(new DateTimeZone('America/Edmonton'));
   		return $dt->format('H:i:s');
	}

	/*--------------------------------------------------------------------------------------------------
	* punch_in
	* This method punches the given facilitator in
	*
	* Params: family userid and Id of the facilitator to punch in 
	* Return: An error or success message
	*--------------------------------------------------------------------------------------------*/
	public function punch_in($facilitator_id){
		
		$current_date = $this->getCurrentDate();

		// Prevent double punch in
		if ($this->isPunchedIn($facilitator_id, $current_date))
		{
			return 'Punch in unsuccessful. You are already punched in!';
		}

		$current_time = time();
		$time_punched_in = $this->convertTime($current_time);

  		$data = array(
			'facilitator_id' => $facilitator_id,
			'date_punched' => $current_date,
			'time_start' => $time_punched_in
		);

		$this->db->insert('punching', $data);
		return 'You are punched in!';
	}

	/*--------------------------------------------------------------------------------------------------
	* isPunchedIn
	* This method determines if the facilitator is already punched in
	*
	* Params: Family user id and facilitator id of the facilitator punching in, and current date punched in
	* Return: True if they've punched in already and false otherwise
	*--------------------------------------------------------------------------------------------*/
	public function isPunchedIn($facilitator_id, $current_date){

		$this->db->where('facilitator_id', $facilitator_id);
		$this->db->where('date_punched', $current_date);
		$this->db->where('time_end', NULL); // Added this to avoid punch out when you've already worked the day

		$query = $this->db->get('punching');
		$count_row = $query->num_rows();

    	if ($count_row > 0) {
        	return TRUE;
    	} else {
        	return FALSE;
    	}

	}

	/*--------------------------------------------------------------------------------------------------
	* punch_out
	* This method punches the given facilitator  out
	*
	* Params: family userid and Id of the facilitator to punch in 
	* Return: An error or success message
	*--------------------------------------------------------------------------------------------*/
	public function punch_out($user_id, $facilitator_id){

		$current_date = $this->getCurrentDate();


		if (!($this->isPunchedIn($facilitator_id, $current_date))){
			return 'Punch out unsuccessful. You have not punched in. Please contact the administrator for more info!';
		}

		$current_time = time();
		$time_punched_out = $this->convertTime($current_time);

		$this->db->set('time_end', $time_punched_out);
  		$this->db->where('facilitator_id', $facilitator_id);
		$this->db->where('date_punched', $current_date);
		$this->db->where('time_end', NULL);
  		
  		$this->db->update('punching');

  		// UPDATE THE HISTORY TABLE CODE BELOW
  		//------------------------------------

  		// gets the rounded hours from punching table.
  		$num_hours = $this->time_rounded($facilitator_id,$time_punched_out);

  		// find the nearest monday
  		$this_monday = date( 'Y-m-d', strtotime( 'monday this week' ) );

  		// looks for an existing record to add the hours onto.
		$this->db->select("history_id");
		$this->db->from('history');
		$this->db->where('user_id', $user_id);
		$this->db->where('start_date', $this_monday);
		$query = $this->db->get();
		$result = $query->row_array();

		// if there is no record. creates the record with the hours completed from punching.
		if (empty($result))
		{
	
			$data = array(
				'user_id' => $user_id,
				'completed_hours' => $num_hours,
				'required_hours' => $this->get_required_hours($user_id),
				'start_date' => $this_monday,
				'end_date' => date( 'Y-m-d', strtotime( 'friday this week' ) ),
			);

			$this->db->insert('history', $data);
		}

		// updates the record if it exists.
		else {
			$this->db->set('completed_hours', 'completed_hours+' . $num_hours, FALSE);
			$this->db->where('history_id', $result['history_id']);
			$this->db->update('history');
		}
		
		// success message if all goes well.
  		return 'You are punched out!';
	}

	/*--------------------------------------------------------------------------------------------
	* time rounded
	* Goes into punching table. gets the start and end time using facilitator id and the time they punched out.
	* rounds the time to the nearest half hour and returns it.
	* param: facilitator id and time_end(time the person punched out.)
	* returns total -> (float of the totl hours they have completed from punching in and out)
	*--------------------------------------------------------------------------------------------*/
	public function time_rounded($facilitator_id,$time_end){
		// gets the time start and end from punching table
		$this->db->select("time_start, time_end");
  		$this->db->from('punching');
		$this->db->where('facilitator_id', $facilitator_id);
		$this->db->where('time_end', $time_end);
		$query = $this->db->get();
		$result = $query->row_array();

		// extract the times
		$start_time = $result['time_start'];
		$end_time = $result['time_end'];

		// i find easier to just convert to date_time objects and 
		// then use the function date_diff to do calcutation
		$date2 = date_create($start_time);
		$date1 = date_create($end_time);

		$diff=date_diff($date1,$date2);


		
		// extract hours and minutes from the subtracted datetime object.
		$hours = (int)$diff->format('%h');
		$minutes = (int)$diff->format('%i');
		// round minutes to the nearest half hour.
		$minutes = 0.5 * round(($minutes/60) *2);

		// add them together
		$total = $hours + $minutes;


		return $total;


	}

	/*---------------------------------------------------------------------------------
	* get_required_hours
	* This method retrieves the required number of hours of a family
	*
	* Parameters: $user_id - a family's user id
	*             
	* Return: The number of hours required each facilitation week 
	*------------------------------------------------------------------------------------------*/
	public function get_required_hours($user_id)
	{
		$num_students =  $this->get_number_students($user_id);

		$this->db->select("*");
		$this->db->from('family_requirements');
		$this->db->order_by("number_of_students", "asc");
		$query = $this->db->get();

		$result = $query->result_array();

		foreach ($result as $row)
		{

			$row_number_students = $row['number_of_students'];

			if ($row_number_students == "default" || intval($row_number_students) == $num_students )
			{
				return $row['required_hours'];
			}
		}

	}


	/*---------------------------------------------------------------------------------
	* get_number_students
	* This method retrieves the  number of students of a family
	*
	* Parameters: $user_id - a family's user id
	*             
	* Return: The number of students in the family
	*------------------------------------------------------------------------------------------*/
	private function get_number_students($user_id)
	{
		$this->db->select("*");
		$this->db->from('students');
		$this->db->where('user_id', $user_id);
		
		$query = $this->db->get();

		return $query->num_rows();
	}


}

?>