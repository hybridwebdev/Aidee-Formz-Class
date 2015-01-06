<?php
class validate_password extends validator {

	public function validate() {
			
		if ($this -> post_array['password']['pw1'] != $this -> post_array['password']['pw2'])
			return array('validate_value' => $this -> validate_value, 'error' => true, 'error_message' => $this -> error_messages['password_mismatch']);

		if ((strlen($this -> post_array['password']['pw1']) > 12))
			return array('validate_value' => $this -> validate_value, 'error' => true, 'error_message' => $this -> error_messages['password_too_long']);

		if ((strlen($this -> post_array['password']['pw1']) <= 5))
			return array('validate_value' => $this -> validate_value, 'error' => true, 'error_message' => $this -> error_messages['password_too_short']);

		return array('validate_value' => $this -> validate_value);
	
	}

}
