<?php class validate_select_menu extends validator {
	
public function validate() {
		$check_array = ($this->form_entry_array['option_array_list']) ? $this->form_entry_array['option_array_list'] : $this->validate_against;
	foreach ( $check_array as $check) {
		
	if (($check['value'] == $this->validate_value)) {
			
		return array('validate_value'=>$this->validate_value);
	} 
	
	}
	
	return array('validate_value'=>$this->validate_value, 
				 'error'=>true,
				 'error_message'=>'That selection is not valid.'
				 ); 		
	}	
}
