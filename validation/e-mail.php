<?php class validate_email extends validator {

	public function validate() {

		return (preg_match('/^[_a-zA-Z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $this->validate_value)) 
			? array('validate_value' => $this -> validate_value)
			: array('validate_value' => $this -> validate_value, 'error' => true, 'error_message' => $this -> error_messages['email_invalid']);
	
	}
}