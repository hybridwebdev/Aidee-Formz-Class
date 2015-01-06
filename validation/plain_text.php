<?php
class validate_plain_text extends validator {

	public function validate() {

		$check = preg_replace('~[^a-zA-Z0-9 $.!@#%&*()]+~', '', $this -> validate_value);

		return ($this -> validate_value == $check) ? 
		
		array('validate_value' => $this -> validate_value) : 
		
		array('validate_value' => $this -> validate_value, 'error' => true, 'error_message' => 'Invalid charachters.');
	
	}

}
