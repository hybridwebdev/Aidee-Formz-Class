<?php 
/* 
 * Version 1.01 Cooper - 10/16/2014.
 * 
 * 6/22/2014 Fixed typo in button class attribute. 
 * 7/25/2014 fixed typo in form_ele_wrap handler. 
 * 7/27/2014 Added Support for textarea. How I missed it until now, is beyond me. 
 * 7/29/2014 Fixed Textarea bug, where I was assigning the input as a value, instead of in the textarea wraps. 
 * 7/30/2014 Fixxed conditional logic in select menus, thanks in part to Ronni Skansing at Stack Overflow. 
 * 10/16/2014 Partial Support for multi-selected added. More work needed on this. 
 * 10/16/2014 Fixed class/id based error reporting when toggle is off. 
 * 
 * Class Developed by Justin Lindsay of Hybrid Web Development.
 * This class is free to use, for non-commercial ventures. This class may not be sold, traded, nor used to aquire any sort of compensation,
 * financial or otherwise. Any distrubtion of this code MUST be done so in it's entirity, including the validation modules, and must be done
 * so with the code completely un-mofidied. Additionally, I must be properly attributed as the author in any instances where this code is distributed.
*/ 

if(!class_exists('aidee_formz')) {
class aidee_formz {

	public function __construct($array = array()) {
			
		$this -> form_option_array = $array;
		$this -> post_array = $_POST;
		$this -> min_type_array = array('number', 'range', 'date', 'datetime', 'datetime-local', 'month', 'time', 'week');
		$this -> max_type_array = array('number', 'range', 'date', 'datetime', 'datetime-local', 'month', 'time', 'week');
		$this -> step_type_array = array('number', 'range', 'date', 'datetime', 'datetime-local', 'month', 'time', 'week');
		
		include_once(dirname(__FILE__)  . '/validation/validation.php'); //loads validation class.
		
		include_once(dirname(__FILE__)  . '/validation/plain_text.php'); //loads validation class.
		include_once(dirname(__FILE__)  . '/validation/date.php'); //loads validation class.
		include_once(dirname(__FILE__)  . '/validation/time.php'); //loads validation class.
		
	}

	public function add($array = array()) {// The function used to take array arguments and build elements.
		
		// merges our contruct options with our current element option. Any contruct options are overridden, if present in the element option.
		// this is to allow an individual element to over-ride options, such as repopulation etc on  per-case basis.
		$this -> form_entry_array = $array+$this -> form_option_array;
		
		// used for checking for and building option arrays to pass to validator before running call. This only gets built in the case of select menus. Seems hacky, but it's a means
		// to propel select menu self-validation.

		$this -> form_entry_array['option_array_list'] = $this -> build_option_array_validate_list($this -> form_entry_array);

		// validates, duh. This is run before any output, to ensure maximum security. Please be aware, if a validation method is not passed in, then data is used completely
		// unsanitized. For security reasons, it's best to always sanitize data before using it.

		$this -> validate_post_array_value();

		if ($this -> form_entry_array['form_ele_wrap'] //makes sure we are wrapping elements that don't need wrapping.
		&& ($this -> form_entry_array['type'] != 'hidden') 
		&& ($this -> form_entry_array['elem'] != 'form_close') 
		&& ($this -> form_entry_array['elem'] != 'form_open')) { 
			
			$string .= "<{$this->form_entry_array['form_ele_wrap']['type']}";
			$string .= ($this -> form_entry_array['form_ele_wrap']['id']) ? $this -> a_id(array('id' => $this -> form_entry_array['form_ele_wrap']['id'])) : "";
			$string .= ($this -> form_entry_array['form_ele_wrap']['class']) ? $this -> a_class(array('class' => $this -> form_entry_array['form_ele_wrap']['class'])) : "";
			$string .= ">";
		}

		$string .= $this -> build_label($this -> form_entry_array, 'before');

		// dynamically calls method for building element, based on type passed in. 
		(method_exists($this, "build_" . $array['elem'])) ? $string .= $this -> {"build_".$array['elem']}($array) : "";

		$string .= $this -> build_label($this -> form_entry_array, 'after');
		  
		$string .= $this -> show_form_error();

		if ($this -> form_entry_array['form_ele_wrap'] // Same as above. Makes sure we are wrapping elements that don't need wrapping.
		&& ($this -> form_entry_array['type'] != 'hidden') 
		&& ($this -> form_entry_array['elem'] != 'form_close') 
		&& ($this -> form_entry_array['elem'] != 'form_open')) {
			$string .= "</{$array['form_ele_wrap']['type']}>";
		}
		
		// this bit here checks if we're rendering manually. If we are, then the form element is returned seperately. If not, then it's added
		// to the form variable for later rendering. 
		(!$this->render_manual) ? $this->entire_form.= $string : ""; 
			return ($this->render_manual) ? $string : ""; 
		
	}

	// -------------------------------------------- Build Functions --------------------------------//
	// This section contains the functions used by the add function, to create the various types of form elements.

	private function build_label($array = array(), $wrap = '') {
		if ((!$array['label']))
			return;

		if (($array['label']['wrap'] == true && $wrap == 'before') || (($array['label']['wrap'] != true) && ($wrap == 'before'))) {
			$string .= "<label ";
			$string .= ($array['id']) ? "for='{$array['id']}'" : "";
			$string .= $this -> a_id(array('id' => $array['label']['id']));
			$string .= $this -> a_class(array('class' => $array['label']['class']));
			$string .= ">";
			$string .= ($array['label']['label_text']) ? "{$array['label']['label_text']}" : "";
		}

		if (($array['label']['wrap'] == true && $wrap == 'after') || (($array['label']['wrap'] != true) && ($wrap == 'before'))) {
			$string .= "</label>";
		}

		return $string;
	}

	private function build_select($array = array()) {

		$string .= $this -> a_select_open($array);

		$string .= ($array['place_holder']) ? $this -> a_select_option(array('name' => $array['place_holder'], 'value' => ' ', 'disabled' => 'false', 'selected' => true)) : "";

		if ($array['option_group']) {
			
			foreach ($array['option_group'] as $group) {
				
				$string .= $this -> a_optgroup_open($group);

				foreach ($group['options'] as $option) 
					$string .= $this -> a_select_option($option);

				$string .= $this -> a_optgroup_closed($group);
			}

		} else {
			
			if ($array['options']) foreach ($array['options'] as $option) 
				$string .= $this -> a_select_option($option);
			
		}

		$string .= $this -> a_select_closed($array);
		return $string;

	}

	private function build_button($array = array()) {
		$string .= "<button";
		$string .= ($array['type']) ? " type='{$array['type']}'" : "";
		$string .= $this -> a_id($array);
		$string .= $this -> a_class($array);
		$string .= $this -> a_name($array);
		$string .= $this -> a_value($array);
		$string .= ">";
		$string .= ($array['icon']) ? "{$array['icon']}" : "";
		$string .= ($array['button_text']) ? "{$array['button_text']}" : "{$array['name']}";
		$string .= "</button>";
		return $string;
	}

	private function build_input($array = array()) {
		$string .= "<input type='{$array['type']}'";
		$string .= $this -> a_name($array);
		$string .= $this -> a_id($array);
		$string .= $this -> a_class($array);
		$string .= $this -> a_value($array);
		$string .= $this -> a_checked($array);
		$string .= $this -> a_required($array);
		$string .= $this -> a_placeholder($array);
		$string .= $this -> a_min($array);
		$string .= $this -> a_max($array);
		$string .= $this -> a_step($array);
		$string .= ">";
		return $string;
	}

	private function build_textarea($array = array()) {
		
		$string .= "<textarea ";
		$string .= $this -> a_name($array);
		$string .= $this -> a_id($array);
		$string .= $this -> a_class($array);
		$string .= $this -> a_required($array);
		$string .= $this -> a_placeholder($array);
		$string	.= ($array['maxlength'] && is_numeric($array['maxlength'])) ? "maxlength ='{$array['maxlength']}'" : "";
		$string	.= ($array['cols'] && is_numeric($array['cols'])) ? "cols ='{$array['cols']}' " : "";
		$string	.= ($array['rows'] && is_numeric($array['rows'])) ? "rows ='{$array['rows']}' " : "";
		$string	.= ($array['readonly']) ? "readonly " : "";
		$string .= ">";
		$string .= ($this -> form_entry_array['repop'] == true && !$array['value']) 
		? $this -> get_post_array_value() 
		: $this->manual_validate(array(
		'validate_value'=>$array['value'], 
		'validate' => array('plain_text'),
		'validate_against'=>$this -> form_entry_array['option_array_list'],
		));
		$string.="</textarea>";
		
		return $string;
		
	}
	
	private function build_form_open($array = array()) {
		$string .= "<form";
		$string .= ($array['method']) ? " method='{$this->form_entry_array['method']}'" : "";
		$string .= ($array['accept']) ? " accept='{$array['accept']}'" : "";
		$string .= ($array['accept-charset']) ? " accept-charset='{$array['accept-charset']}'" : "";
		$string .= ($array['action']) ? " action='{$array['action']}'" : " action=''";
		$string .= $this -> a_name($array);
		$string .= $this -> a_class($array);
		$string .= $this -> a_id($array);
		$string .= ">";
		return $string;
	}

	private function build_form_close($array = array()) {
		$string .= "</form>";
		return $string;
	}

	// ------------------------------------ Helper Functions --------------------------------------------- //
	// These are basically to ensure additional easy of use, as well as uniformity when creating form elements. //

	private function a_checked ($array = array()) {
		if ($array['type']=='checkbox') 
			return (($this -> get_post_array_value($array) && $array['value']!=null) || ($array['value']  && $array['value']!=null)) ? " checked" : "";			
		
		if ($array['type']=='radio') {
				
			if($array['value'] == $this -> get_post_array_value($array))  
				return " checked";	
		
			if($array['selected'] == true) 
				return " checked";
			}
	}
	
	
	private function a_name($array = array()) {
		if (!$array['name'])
			return;
		$string .= ($array['name']) ? " name='{$array['name']}" : "";
		$string .= ($array['name_array'] && $array['name']) ? "[{$array['name_array']}]" : "";
		$string .= "'";
		return $string;
	}

	private function a_class($array = array()) {
		
		$string.= " class='";
		
		$string.= ($array['class']) 
			? "{$array['class']}" 
			: "";
			
		$string.= ($this->form_entry_array['show_errors'] || $this->form_option_array['show_errors']) 
		? $this->form_entry_array['error_class'] 
		: "";
		
		$string.="'";
		
		return $string;
		
	}

	private function a_id($array = array()) {
			
		$string.= " id='";
		
		$string.= ($array['id']) 
			? "{$array['id']}" 
			: "";
			
		$string.= ($this->form_entry_array['show_errors'] || $this->form_option_array['show_errors']) 
		? $this->form_entry_array['error_class'] 
		: "";
		
		$string.="'";
		
		return $string;
		
	}

	private function a_required($array = array()) {
		return ($array['required']) ? " required" : "";
	}

	private function a_disabled($array = array()) {
		return ($array['disabled'] == true) ? " disabled" : "";
	}

	private function a_value($array = array()) {
		if ((!isset($array['value'])) && ( !$this -> get_post_array_value()) )
			return;
		$string .= " value='";
		
		$string .= ($this -> form_entry_array['repop'] == true && !isset($array['value'])) 
		? $this -> get_post_array_value() 
		: $this->manual_validate(array(
		'validate_value'=>$array['value'], 
		'validate' => array('plain_text'),
		'validate_against'=>$this -> form_entry_array['option_array_list'],
		));
		
		$string .= "'";
		
		return $string;
	}

	private function a_select_open($array = array()) {
		$string .= "<select";
		$string .=($array['multi_select']) ? " multiple " :"";
		$string .= $this -> a_name($array);
		$string .= $this -> a_id($array);
		$string .= $this -> a_required($array);
		$string .= $this -> a_class($array);
		$string .= $this -> a_value($array);
		$string .= $this -> a_disabled($array);
		$string .= ">";
		return $string;
	}

	private function a_select_closed($array = array()) {
		$string .= "</select>";
		return $string;
	}

	private function a_optgroup_open($array = array()) {
		$string .= "<optgroup label='";
		$string .= $array['group_label'] . "'";
		$string .= $this -> a_disabled(array('disabled' => $array['group_disabled']));
		$string .= ">";
		return $string;
	}

	private function a_optgroup_closed($array = array()) {
		$string .= "</optgroup>";
		return $string;
	}

	private function a_select_option($array = array()) {

		$string .= "<option";
		$string .= $this -> a_value($array);
		$string .= $this -> a_disabled($array);
		$string .= $this -> a_selected($array);
		$string .= ">";
		$string .= "{$array['name']}";
		$string .= "</option>";
		return $string;
	}

	private function a_placeholder($array = array()) {
		$string .= ($array['place_holder']) ? " placeholder='{$array['place_holder']}'" : "";
		return $string;
	}

	private function a_selected($array = array()) {

		if($array['selected'] == true) 
				return  " selected='selected'";
		
		if(($this ->form_entry_array['force_select'] == true) && $array['value'] == $this -> form_entry_array['value']) 
				return " selected='selected'" ; // used for when we want to over-ride the post_array check, and just force the selection to whatever value we feed it. 
				
		if(($this -> form_entry_array['repop'] == true) && $this -> get_post_array_value($this -> form_entry_array) == $array['value']) 	
			    return " selected='selected'"; 
			    

		if(($this -> form_entry_array['repop'] != true) && (string)$array['value'] == (string)$this -> form_entry_array['value']) 
				return " selected='selected'"; 
		
		if(($this -> form_entry_array['repop'] == true) && $array['value'] == $this -> form_entry_array['value'] && !$this -> get_post_array_value($this -> form_entry_array)) 
				return " selected='selected'"; 
	
	}

	private function a_min($array = array()) {
		if ($array['min'] && (in_array($array['type'], $this -> min_type_array))) 
			return " min='{$array['min']}'";
	}

	private function a_max($array = array()) {
		if ($array['max'] && (in_array($array['type'], $this -> max_type_array))) 
			return " max='{$array['max']}'";
	}

	private function a_step($array = array()) {
		if ($array['step'] && (in_array($array['step'], $this -> step_type_array))) 
			return $string .= " step='{$array['max']}'";
	}

	private function build_option_array_validate_list($array = array()) {

		if ($array['option_group']) {
			foreach ($array['option_group'] as $group) 
				foreach ($group['options'] as $option)
					$options_array[] = $option;

		} else {

			if ($array['options'])
				foreach ($array['options'] as $option)
					$options_array[] = $option;

		}

		return $options_array;

	}

	// --------------------------------------- Other functions ------------------------------------------//
	
	public function add_filter ($filter) {
		/**
		 * @param string, adds filter to validate array.
		 */
		 
	$this->form_entry_array['validate'][] = $filter;
	$this->form_option_array['validate'][] = $filter;	
			
	}
	
	public function remove_filter ($filter) {
	 	/**
		 * @param string, removes filter from validate array.
		 */
	((strtolower($filter)== "all") && is_array($this->form_entry_array['validate'])) 
		? $this->form_entry_array['validate'] = array() : "";
	
	((strtolower($filter)== "all") && is_array($this->form_option_array['validate'])) 
		? $this->form_option_array['validate'] = array() : "";
	 
	if(($key = array_search($filter, $this->form_entry_array['validate'])) !== false) 
	    unset($this->form_entry_array['validate'][$key]);

	if(($key = array_search($filter, $this->form_option_array['validate'])) !== false)
    	unset($this->form_option_array['validate'][$key]);
	
	}
	
	public function list_filters () {
		if(is_array($this->form_entry_array['validate'])) foreach($this->form_entry_array['validate'] as $function) echo "<li>".$function."<li>";
	}	

	public function get_filters () {
		return array('global_filters'=>$this->form_option_array['validate'],
					 'local_filters'=>$this->form_entry_array['validate']);
	}	
	
	public function error_report ($toggle) {
		/**
		 * @param turns on or off form error reporting. 
		 */
		 
	if (($toggle) == true || (strtolower($toggle)) == "on")  
		 $this->form_entry_array['show_errors'] = $this->form_option_array['show_errors'] = true; 
	if (($toggle) == false || (strtolower($toggle)) == "off")  
		 $this->form_entry_array['show_errors'] = $this->form_option_array['show_errors'] = false;
	}
	
	private function get_post_array_value() {// At construct, we store all the Post variables in a key=>pair array. We use this to fetch those values for reference as wel as validation.
		return ($this -> form_entry_array['name'] && ($this -> form_entry_array['name_array'])) 
		? $this -> post_array[$this -> form_entry_array['name']][$this -> form_entry_array['name_array']] 
		: $this -> post_array[$this -> form_entry_array['name']];
	}

	public function show_form () {
		return $this->entire_form;
	}
	
	private function validate_post_array_value($array=array()) {
		/** 
		 * @Param internal form validator. This is only run on the post array. Values passed to the form are validated seperately. 
		 */
			
		$validate = new validator( array(
		'form_entry_array' => $this -> form_entry_array, 
		'validate_value' => ($this -> form_entry_array['name'] && ($this -> form_entry_array['name_array']))  
			? $this -> post_array[$this -> form_entry_array['name']][$this -> form_entry_array['name_array']] 
			: $this -> post_array[$this -> form_entry_array['name']], 
		'post_array' => $this -> post_array));

		$data = $validate -> validator();
		
		// re-sets the post value based on the return of the validator.
		if (($this -> form_entry_array['name']) && ($this -> form_entry_array['name_array'])) {

			$this -> post_array[$this -> form_entry_array['name']][$this -> form_entry_array['name_array']] = $data['validate_value'];

			($data['errors'] == true) ? $this -> form_errors[$this -> form_entry_array['name']][$this -> form_entry_array['name_array']] = $data['errors'] : "";
			
		} else {
			
			$this -> post_array[$this -> form_entry_array['name']] = $data['validate_value'];

			($data['errors'] == true) ? $this -> form_errors[$this -> form_entry_array['name']] = $data['errors'] : "";
			 
		}
		
		
		($data['errors'] == true) ? $this -> form_entry_array['error_class'] = " error" : "";
		
		
	}
	
	public function manual_validate ($array=array()) {
	 /**	
	 * @PARAM array options 'validate' array of filters, if nothing is passed, it uses all currently set filters within the form object. 
	 * 		'validate_value' mandatory, string you want cleaned
	 * 		'validate_against' optional, array or string to compare against.
	 *		'return_errors' optional if set to true, function will return entire $data array, including validation errors, otherwise it returns
	 * 		just the value.   
	 *      'required' optional, if set and value is empty, will throw return an error.  
	 */		
	$validate = new validator( array(
		'validate'=> (!$array['validate']) ? $this->form_option_array['validate'] : $array['validate'], 
		'validate_value' => $array['validate_value'],
		'manual_validate'=> true, 
		'validate_against'=>$array['validate_against'],
		));
		
		
		if (($this -> form_entry_array['name']) && ($this -> form_entry_array['name_array'])) {

			($data['errors'] == true) ? $this -> form_errors[$this -> form_entry_array['name']][$this -> form_entry_array['name_array']] = $data['errors'] : "";
			
		} else {

			($data['errors'] == true) ? $this -> form_errors[$this -> form_entry_array['name']] = $data['errors'] : "";
			 
		}
			
		$data = $validate -> validator();	
		
	return ($array['return_errors']) ? $data : $data['validate_value'];  	
		
	}
	public function render_manual ($toggle = '') {
		
	 (strtolower($toggle) == 'on' || true) ? $this->render_manual = true : "";
	 (strtolower($toggle) == 'off' || false) ? $this->render_manual = false : "";
	 	
	}
	
	public function form_has_errors($array=array()) {
	/** 
	* @param checks if form has errors, and returns status. Can only be used AFTER form is run.  
	*/ 
	return ($this->form_errors !=null) ? true :  false ;
	}
	
	public function show_form_error($array = array()) {
	/**
	 * @param reports back all form validation errors.  
	 */
		$errors = ($this -> form_entry_array['name'] && $this -> form_entry_array['name_array']) 
		? $this -> form_errors[$this -> form_entry_array['name']][$this -> form_entry_array['name_array']]['errors'] 
		: $this -> form_errors[$this -> form_entry_array['name']]['errors'];
		
		
		if ((($errors) && ($this -> form_entry_array['show_errors']))) {
			
			foreach ($errors['error_messages'] as $message) {
				if ($this -> form_entry_array['error_wrap'])
					$string .= "<" . 
					$this -> form_entry_array['error_wrap']['type'] . 
					$this -> a_class($this -> form_entry_array['error_wrap']) . 
					$this -> a_id($this -> form_entry_array['error_wrap']) . ">";

				$string .= $message;

				if ($this -> form_entry_array['error_wrap'])
					$string .= "</" . 
					$this -> form_entry_array['error_wrap']['type'] . 
					">";
			}

			return $string;

		}

	}

} // Class is out. 
}
?>