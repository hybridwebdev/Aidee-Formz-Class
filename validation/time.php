<?php class validate_time extends validator {

	public function validate() {
		
		return (preg_match("/(0?\d|1[0-2]):(0\d|[0-5]\d) (AM|PM)/i", $this->validate_value))
		 
			? array('validate_value' => $this -> validate_value)
			: array('validate_value' => $this -> validate_value, 'error' => true, 'error_message' => 'Invalid time format.');
	
	}
}

