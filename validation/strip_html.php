<?php
class validate_strip_html extends validator {

	public function validate() {
		

		$check = preg_replace('~[^a-zA-Z0-9\s-_@.]+~', '', $this -> validate_value);
		return array('validate_value' => $check);  
	}

}




