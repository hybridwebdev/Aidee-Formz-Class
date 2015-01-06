<?php
class validate_date extends validator {

	public function validate() {
	
		$valid =  (date('d-m-Y', strtotime($this -> validate_value)) == $this -> validate_value) 
			? true
			: false;
		
		return ($valid) ? 
		
		array('validate_value' => $this -> validate_value) : 
		
		array('validate_value' => $this -> validate_value, 'error' => true, 'error_message' => 'Invalid Date format.');
	}

}