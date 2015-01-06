<?php
// Master class for the validation modules:
// $this->post_array is a key/pair array of every single POST variable from the form
//
// $this->validate_value is the value being passed into the validator. While variable comes from the post_aray before being passed to the module
// this, and error messages the the only thing passed back from the validator. No part of the initial form class is modified at the end of this validator. 
//
// this->form_entry_array contains all the data relevant to the form element being handled, such as a a text box etc. Each element is
// is run through the validator before hitting the form. 

if (!class_exists('validator')) {
class validator extends aidee_formz {

	public function __construct($array = array()) {
		
		$this -> form_entry_array = $array['form_entry_array'];
		$this -> validate_value = $array['validate_value'];
		$this -> post_array = $array['post_array'];
		
		if($array['manual_validate']) {
		$this -> validate_against = $array['validate_against'];
		$this -> form_entry_array['validate'] = $array['validate'];
		$this -> form_entry_array['required'] = $array['required'];
		}
	}

	public function validator() {

		if($this -> form_entry_array['required'] && (!$this->validate_value || $this->validate_value == " ")) {
		$this -> add_error_message(array('error_message' => "This field can not be empty."));			
		}
		
		if (($this -> form_entry_array['validate']) && is_array($this -> form_entry_array['validate']))
			foreach ($this->form_entry_array['validate'] as $function) {
				
				$validator_function = "validate_" . $function;

				if (class_exists($validator_function)) {

					$validate = new $validator_function( 
					array(
					'form_entry_array' => $this -> form_entry_array, 
					'validate_value' => $this -> validate_value, 
					'post_array' => $this -> post_array,
					'validate_against'=>$this->validate_against
					)
					);

					$data = $validate -> validate();
			
					$this -> validate_value = $data['validate_value'];
					
					
					
						($data['error']) ? $this -> add_error_message(array('error_message' => $data['error_message'])) : "";
				} else {
					$this -> add_error_message(array('error_message' => "Validator {$function} not found."));
				}
														
			}
		
		return array('errors' => $this -> errors, 'validate_value' => $this -> validate_value);

	}

	private function add_error_message($array = array()) {
		$this -> errors['errors']['error'] == true;
		$this -> errors['errors']['error_messages'][] = $array['error_message'];
	}

}
}