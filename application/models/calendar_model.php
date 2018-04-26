<?php

/*--------------------------------------------------------------------------------------------------------
* This Class allows for database access. Any time a database query needs to be made, create a method here!
---------------------------------------------------------------------------------------------------------*/

class Calendar_model extends CI_Model {
	
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
	

	/*---------------------------------------------------------------------------------
	* getClasses
	* This method retrieves all the classes and class ids from the database 
	*
	* Parameters & Return: None
	* 
	* NOTE: This method echoes out class information for access by the caller script
	*------------------------------------------------------------------------------------------*/
	public function getClasses (){
		
		/*SELECT classroom_id, class_color
			 FROM classroom" */
		$this->db->select(" classroom_id, class_color");
		
		/* Perform the query */
		$query = $this->db->get('classroom');
		return $query->result_array();

	}


	/*---------------------------------------------------------------------------------
	* findEvents
	* This method gathers facilitation events for the given list of days and the classroom id 
	*
	* Parameters: $days - A string list of space separated days in datetime SQL format
	*             $id - A classroom id 
	*
	* Return: None
	* 
	* NOTE: This method echoes out information for access by the caller script
	*------------------------------------------------------------------------------------------*/
	public function findEvents ($days, $id){

		if ($days && $id){

			$days_split = explode(" ", $days);

			array_pop($days_split);

			// Find facilitation events for each day 
			foreach ($days_split as $day) {
				$this->getEvent($day, $id);
			}


		}
	}

	/*---------------------------------------------------------------------------------
	* getNumFacilitators
	* This method retrieves the number of facilitators facilitiating in a give slot
	*
	* Parameters: $slot_id - A facilitation slot slot id
	*
	* Return: The total number of facilitators facilitating in the given slot
	*------------------------------------------------------------------------------------------*/
	public function getNumFacilitators ($slot_id){

		
	    /*SELECT count(*) as total 
			 FROM facilitating 
			 WHERE slot_id = $slot_id"; */

		$this->db->select(" count(*) as total");
		$this->db->where('slot_id', $slot_id);
		$query = $this->db->get('facilitating');
		$row = $query->row_array();
		return $row['total'];

	}

	/*---------------------------------------------------------------------------------
	* getEvent
	* This method gathers events for a single day and classroom id 
	*
	* Parameters: $date - A string datetime value in SQL format
	*             $id - A classroom id 
	*
	* Return: None
	* 
	* NOTE: This method echoes out information for access by the caller script
	*------------------------------------------------------------------------------------------*/
	public function getEvent ($date, $classroom_id){

		/*SELECT slot_id, classroom_id, time_start, time_end, facilitators_needed
			 FROM facilitation_times
			 WHERE date_scheduled = '$date' and classroom_id = '$id'"*/
	 	$this->db->select("slot_id, classroom_id, time_start, time_end, facilitators_needed, is_fieldtrip");

	 	$this->db->where('date_scheduled', $date);
	 	$this->db->where('classroom_id', $classroom_id);

	 	/* Perform the query */
		$query = $this->db->get('facilitation_times');
		$result = $query->result_array();

		/* Retrieve and echo results */
		foreach ($result as $row){

			$slot_id = $row["slot_id"];

			/* Perform a sub-query to determine how many facilitators signed up for this slot_id already */
			$num_facilitators = $this->getNumFacilitators ($slot_id);

			echo $slot_id . "," . $row["classroom_id"] . "," . $row["time_start"] . // The commas & tilde are added to make parsing simpler on the caller end
					"," . $row["time_end"] . "," . $row["facilitators_needed"] . "," . $num_facilitators . "," . $row["is_fieldtrip"] . "~";  
		}

	}

	public function getSlotIds($startdate,$enddate,$classroom)
	{
		$this->db->select('slot_id');
		$this->db->where('date_scheduled >=',$startdate);
		$this->db->where('date_scheduled <=',$enddate);
		$this->db->where('classroom_id', $classroom);
		$query = $this->db->get('facilitation_times');
		$results = $query->result_array();
		return $results;
	}


	/*---------------------------------------------------------------------------
	* get_fieldtrip_info
	* This method retreives data for a given fieldtrip. 
	*
	* Parameters: $slot_id - slot id for a valid fieldtrip slot
	*
	* Return: An associative array with fieldtrip information (location, description, & full_description )
	*-------------------------------------------------------------------------*/
	public function get_fieldtrip_info ($slot_id)
	{
		$this->db->select('location');
		$this->db->select('description');
		$this->db->select('full_description');

		$this->db->where('slot_id', $slot_id);

		$query = $this->db->get('field_trips');
		return $query->row_array();

	}

	/*---------------------------------------------------------------------------------
	* getFacilitatorsSignedUp
	* This method gets facilitator names and comments based on slot id. 
	*
	* Parameters: $slot_id 
	*
	* Return: None
	*
	* PLEASE NOTE: This method echos out information for the caller script
	*------------------------------------------------------------------------------------------*/
	public function getFacilitatorsSignedUp($slot_id)
	{

		/*SELECT CONCAT(first_name, \" \", last_name) as name, notes
			 FROM facilitating, facilitator
			 WHERE 
				facilitating.facilitator_id = facilitator.facilitator_id and 
				slot_id = $slot_id*/

		$this->db->select("notes, CONCAT(first_name, \" \", last_name) as name");
		$this->db->from('facilitating');
		$this->db->join('facilitator', "facilitating.facilitator_id = facilitator.facilitator_id");
		$this->db->where('slot_id', $slot_id);

		$query = $this->db->get();
		$result = $query->result_array();

		foreach ($result as $row){

			if ($row['notes'] == null){
				echo $row['name'] . ",";
			}
			else {
				echo $row['name'] . " (note: " . $row['notes'] . "),"; // The commas are added to make parsing simpler on the caller end	
			}

		}

	}

}
?>
