<?php

/*--------------------------------------------------------------------------------------------------------
* This Class allows for database access. Any time a database query needs to be made, create a method here!
---------------------------------------------------------------------------------------------------------*/

class Family_model extends CI_Model {
	
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
	
	/*--------------------------------------------------- Getter methods to fill dropdown menus  ----------------------------------------------------------*/	

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



	/*--------------------------------------------------- Booking Facilitation  ----------------------------------------------------------*/	

	/*---------------------------------------------------------------------------------
	* getBookingInfo
	* This method retrieves booking information for a given slot including time start, time end, and facilitators needed
	*
	* Parameters: $slot_id - A facilitation slot slot id
	*
	* Return: The results of the query in the form of an associative array where indices 'time_start', 'time_end',
	* and 'facilitators_needed' have their respective information
	*------------------------------------------------------------------------------------------*/
	public function getBookingInfo($slot_id){

		/*SELECT time_start, time_end, facilitators_needed
		  FROM facilitation_times 
		  WHERE slot_id = $slot_id*/

		$this->db->select("time_start, time_end, facilitators_needed");
		$this->db->where('slot_id =' . $slot_id);
		$query = $this->db->get(' facilitation_times ');
		return $query->row_array();
		// Includes time_start, time_end, and facilitators_needed

	}

	/*---------------------------------------------------------------------------------
	* not_double_booking
	* This method determines whether or not the given facilitator is not double booking themselves if they sign up for 
	* a given slot.
	*
	* Parameters: $facilitator_id - A facilitator's id 
	*             $booking_info - An associative array containing indices 'time_start', 'time_end', and 'facilitators_needed' 
	*                             (time values are in SQL datetime format)
	*
	* Return: True if the person is NOT double booking
	*------------------------------------------------------------------------------------------*/
	public function not_double_booking($booking_info, $facilitator_id){

		/*SELECT count(*) as same_times
		  FROM facilitating, facilitation_times
		  WHERE 
			facilitating.slot_id = facilitation_times.slot_id and
			facilitator_id = $facilitator_id and 
			time_start < '$time_end' and 
			time_end > '$time_start'*/

		$time_end = $booking_info['time_end'];
		$time_start = $booking_info['time_start'];

		/*Run the query */ 
		$this->db->select("count(*) as same_times");
		$this->db->from('facilitating');
		$this->db->join('facilitation_times', "facilitating.slot_id = facilitation_times.slot_id");
		$this->db->where('facilitator_id', $facilitator_id);
		$this->db->where("time_start < DATE_SUB('" . $time_end  . "', INTERVAL 10 MINUTE)"); 
		$this->db->where("time_end   > DATE_SUB('" . $time_start  . "', INTERVAL -10 MINUTE)");

		$query = $this->db->get();
		$result = $query->row_array();

		// Returns true if the person is NOT double booking 
		return $result['same_times'] == '0';
	}


	/*---------------------------------------------------------------------------------
	* book
	* This method books the given facilitator for a time slot. 
	*
	* Parameters: $facilitator_id - A facilitator's id 
	*             $slot_id - The id of the slot the facilitator wishes to sign up for 
	*             $notes - Any additional notes provided by the facilitator upon sign up - may be null
	*
	* Return: None
	*
	* PLEASE NOTE: This method echos out success and error messages for the caller script
	*------------------------------------------------------------------------------------------*/
	public function book($slot_id, $notes, $facilitator_id){

		/* Get the time information from the slot already booked */
		$booking_info = $this->getBookingInfo($slot_id);

		/* Check if the facilitator is already booked at the same time */
		$not_double_booking = $this->not_double_booking($booking_info, $facilitator_id);

		/* Check for double booking */
		if ($not_double_booking ){

			/*INSERT INTO facilitating (slot_id, facilitator_id, notes)
			 VALUES ($slot_id, $facilitator_id, '$notes')*/
			
			 $data = array(
				'slot_id' => $slot_id,
				'facilitator_id' => $facilitator_id,
				'notes' => $notes
			);

			$this->db->insert('facilitating', $data);

			 echo "Sign up successful!";
			

		}
		else {
			
			echo "Sign up unsuccessful due to time conflicts.";
		}


	}

	/*--------------------------------------------------- My Bookings  ----------------------------------------------------------*/	

	public function my_bookings_data($facilitator_ids_array,$date_start,$date_end){
		/*
		* Creating the query to run. I ended up merging 3 tables to get the right information. For now it seems fine but could be slow in future.
		*/
		$this->db->select("facilitator.first_name,facilitation_times.date_scheduled,facilitation_times.time_start,facilitation_times.time_end,classroom.class_color")
			->from('facilitation_times')
			->join('facilitating', "facilitating.slot_id = facilitation_times.slot_id")
			->join('classroom', "classroom.classroom_id = facilitation_times.classroom_id")
			->join('facilitator',"facilitator.facilitator_id = facilitating.facilitator_id")
			//using group feature for advanced where query building. basically puts brackets between where statements
			->group_start()
				// finds info base on whats between the dates given by the user
				->where('facilitation_times.date_scheduled BETWEEN "'. $date_start . '" and "'. $date_end .'"')
			->group_end();
			//collects the data for all facilitators who are a part of that family.
			$this->db->group_start();
			
			foreach ($facilitator_ids_array as $row){
				$this->db->or_where('facilitating.facilitator_id', $row["facilitator_id"]);
			}
			$this->db->group_end();

		$query = $this->db->get();
		return $query->result_array();

	}

	/*--------------------------------------------------- Donations ----------------------------------------------------------*/	

	/*

	DONATION ALGORITHM

	Get all the tuples with donatable hours - donator
	Get all the tuples that are missing hours - recipient

	RECIPIENT DOESN'T HAVE ANY MISSING HOURS
	-> Go to current week and add all the donations to this week
	-> DONE

	RECIPIENT HAS MISSING HOURS
	-> Loop through the donator tuples and donate what you can

	NO MORE DONATIONS
	-> update donated slots 
	-> DONE

	MORE DONATIONS LEFT BUT NO MORE MISSING WEEKS
	-> Go to current week and add all the donations to this week
	-> DONE 

	*/

	/*-----------------------------------------------
	* getFamilyUserIds
	* This method retrieves all user ids that are family users along with the number of hours they need to satisfy their required hours 
	*-----------------------------------------------*/
	public function getFamilyUserIds($user_id, $month)
	{
		$this->db->select("SUM(required_hours - completed_hours + hours_given - hours_received) as total");
		$this->db->from('history');
		$this->db->where('MONTH(start_date)', $month);
		$this->db->where('user_id = users.user_id');
		$this->db->where('(completed_hours + hours_received - hours_given) < required_hours');
		$subQuery =  $this->db->get_compiled_select();

		$this->db->select("username, user_id, ($subQuery) as missing_hours");
		$this->db->from('users');
		$this->db->where('role_id', 2);
		$this->db->where('user_id !=', $user_id);
		$query = $this->db->get();

		return $query->result_array();
	}

	/*---------------------------------------------------------------------------------------------
	* get_donatable_records
	* This method retrieves all the records in the history table that include donatable hours for the given month number
	*
	* Parameters: $user_id = Family's user id of the family we're looking at
	*             $month = An integer representation of month (ex. 2 = February)
	*
	* Return: Query result 
	*------------------------------------------------------------------------------------------*/
	private function get_donatable_records($user_id, $month)
	{

		$this->db->select("history_id");
		$this->db->select("(completed_hours + hours_received - hours_given - required_hours) as total_hours");
		$this->db->from('history');
		$this->db->where('MONTH(start_date)', $month);
		$this->db->where('user_id', $user_id);
		$this->db->where('(completed_hours + hours_received - hours_given) > required_hours');
		$this->db->order_by("start_date", "desc");
		$query = $this->db->get();

		return $query->result_array();
	}


	/*---------------------------------------------------------------------------------
	* get_donatable_hours
	* This method retrieves the total number of hours the user can donate 
	*
	* Parameters: $user_id = Family's user id of the family we're looking at
	*             $month = An integer representation of month (ex. 2 = February)
	*
	* Return: An integer total
	*------------------------------------------------------------------------------------------*/
	public function get_donatable_hours($user_id, $month)
	{

		$this->db->select("SUM(completed_hours + hours_received - hours_given - required_hours) as total");
		$this->db->from('history');
		$this->db->where('MONTH(start_date)', $month);
		$this->db->where('user_id', $user_id);
		$this->db->where('(completed_hours + hours_received - hours_given) > required_hours');

		$query = $this->db->get();

		return floatval($query->row_array()['total']);
	}

	/*---------------------------------------------------------------------------------
	* get_missing_hours
	* This method retrieves the total number of hours the user needs
	*
	* Parameters: $user_id = Family's user id of the family we're looking at
	*             $month = An integer representation of month (ex. 2 = February)
	*
	* Return: An integer total
	*------------------------------------------------------------------------------------------*/
	public function get_missing_hours($user_id, $month)
	{
		$this->db->select("SUM(required_hours - completed_hours + hours_given - hours_received) as total");
		$this->db->from('history');
		$this->db->where('user_id', $user_id);
		$this->db->where('MONTH(start_date)', $month);
		$this->db->where('(completed_hours + hours_received - hours_given) < required_hours');

		$query = $this->db->get();

		return intval($query->row_array()['total']);
	}

	/*---------------------------------------------------------------------------------
	* get_missing_weeks
	* This method retrieves all the records in the history table that include missing hours for the given month number
	*
	* Parameters: $user_id = Family's user id of the family we're looking at
	*             $month = An integer representation of month (ex. 2 = February)
	*
	* Return: Query result 
	*------------------------------------------------------------------------------------------*/
	private function get_missing_weeks($user_id, $month)
	{

		$this->db->select("history_id");
		$this->db->select("(required_hours - completed_hours + hours_given - hours_received) as missing_hours");
		$this->db->from('history');
		$this->db->where('user_id', $user_id);
		$this->db->where('MONTH(start_date)', $month);
		$this->db->where('(completed_hours + hours_received - hours_given) < required_hours');
		$this->db->order_by("start_date", "asc");
		$query = $this->db->get();

		return $query->result_array();
	}

	/*---------------------------------------------------------------------------------
	* give_to_current_week
	* This method donates the given number of hours to the recipient for the current facilitation week
	*
	* Parameters: $recipient_id = Family's user id of the receiving family
	*             $num_hours = An integer representation of the number of hours being donated to the family
	*
	* Return: None
	*------------------------------------------------------------------------------------------*/
	private function give_to_current_week($recipient_id, $num_hours)
	{

		$this_monday = date( 'Y-m-d', strtotime( 'monday this week' ) );

		$this->db->select("history_id");
		$this->db->from('history');
		$this->db->where('user_id', $recipient_id);
		$this->db->where('start_date', $this_monday);
		$query = $this->db->get();
		$result = $query->row_array();

		if (empty($result))
		{
	
			$data = array(
				'user_id' => $recipient_id,
				'completed_hours' => 0,
				'required_hours' => $this->get_required_hours($recipient_id),
				'start_date' => $this_monday,
				'end_date' => date( 'Y-m-d', strtotime( 'friday this week' ) ),
				'hours_received' => $num_hours
			);
			$this->db->insert('history', $data);
		}

		else {
			$this->db->set('hours_received', 'hours_received+' . $num_hours, FALSE);
			$this->db->where('history_id', $result['history_id']);
			$this->db->update('history');
		}

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

	/*---------------------------------------------------------------------------------
	* update_donor_hours
	* This method updates the donor's facilitation hours based on what they donated
	*
	* Parameters: $donor = Family's user id of the donating family
	*             $hours_donated = The number of hours the family donated
	*
	* Return: None
	*------------------------------------------------------------------------------------------*/
	private function update_donor_hours($donor, $hours_donated, $month)
	{

		$donate_data = $this->get_donatable_records($donor, $month);

		$current_row = 0;
		$num_rows = count($donate_data);
		$amount_donated = $hours_donated;
		while ($amount_donated != 0 && $current_row < $num_rows)
		{
			$donatable_hours = $donate_data[$current_row]['total_hours']; // hours to donate for this week

			$hours_donated = 0;
			// donate what we can 
			if ($donatable_hours > $amount_donated)
			{
				$hours_donated = $amount_donated;
				$amount_donated = 0;
			} 
			else //$donatable_hours <= $amount_donated
			{
				$hours_donated = $donatable_hours;
				$amount_donated = $amount_donated - $donatable_hours;
			}

			// Update recipient values
			$this->db->set('hours_given', 'hours_given + ' . $hours_donated, FALSE);
			$this->db->where('history_id', $donate_data[$current_row]['history_id']);
			$this->db->update('history'); 

			$current_row ++;
		}

	}


	/*---------------------------------------------------------------------------------
	* donate_hours
	* This method allows for donation of hours from one family to another 
	*
	* Parameters: $donor = Family's user id of the donating family
	*             $recipient_id = Family's user id of the receiving family
	*             $donation = The number of hours the donor family donated
	*             $month = the month the family wishes to donate for 
	*
	* Return: None
	*------------------------------------------------------------------------------------------*/
	public function donate_hours($month, $donor, $recipient, $donation) 
	{

		//$this->test($month, $donor, $recipient, $donation);
		//return;
		// Retrieve all the tuples in history that are missing hours
		$missing_weeks = $this->get_missing_weeks($recipient, $month);

		$hours_left = $donation; // Hours left to donate

		// If they're not missing any hours, donate to the current week 
		if (empty($missing_weeks))
		{
			$this->give_to_current_week($recipient, $donation);
			$hours_left = 0;
		}

		else {

			$num_rows = count($missing_weeks);
			
			$current_row = 0;
			while ($hours_left != 0 && $current_row < $num_rows)
			{
				$hours_needed = $missing_weeks[$current_row]['missing_hours']; // look at how much we need

				$hours_to_donate = 0;
				// donate what we can 
				if ($hours_needed > $hours_left)
				{
					$hours_to_donate = $hours_left;
					$hours_left = 0;
				} 
				else //$hours_needed <= $hours_left
				{
					$hours_to_donate = $hours_needed;
					$hours_left = $hours_left - $hours_needed;
				}

				// Update recipient values
				$this->db->set('hours_received', 'hours_received + ' . $hours_to_donate, FALSE);
				$this->db->where('history_id', $missing_weeks[$current_row]['history_id']);
				$this->db->update('history'); 

				$current_row ++;
			}
		}

		if ($hours_left > 0){
			$this->give_to_current_week($recipient, $hours_left);
		}
		
		$this->update_donor_hours($donor, $donation, $month);
	}

	
}

?>
