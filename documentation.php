<?php 
//This class aims to be as versatile as possible. There's a pleothora of options, as well as a handful of hooks that can be used to aid
// in the creation of forms. 

//To start off of course we need to include the form class:

include_once('/path-to-form-class/form_class.php');

// Then we set our options. Please note, you do NOT have to pass any arguments when instantiating the class. Also note, any options passed through
// when instantiating are consider "default options". This means, any argument you pass when instantiating the class will be passed to all further
// method calls, unless over-ridden during the method call. This can be a useful way of, for example, passing in a validation filter that you want
// applied to all form elements. 

$options = array(
// the following ONLY apply to the opening form tag. Please read below for further explanation of form_open and form_close

'action'=> '/path-to/some_form.php', // Our action call. Used in the form open tag, this tells the form where to send it's data to. Of course
									// this can be left blank, and then the form will post back to itself.
									
'method'=>'POST', // Can be POST or GET. If left blank, it defaults to POST.
  									 
'accept'=>'',

'accept-charset'=>'',

'name'=>'some_name', // MANDATORY, if you want the form to function. Both for the form open, as well as each element added. If this is not set, the element	
					// will return an error message instead of render.  

'name_array'=>'some_other_name', // If this is set, then the element name will be name[name_array]. Form data will be parsed as name[name_array],
								// please see below for forther information. 
 
// The following can either be passed right in the instantiation of the class, or passed to each $formz->add method call. Again note
// that if this is set on instantiation, it will apply to ALL method calls, unless over-ridden. 

'show_errors'=>true, // if true, whenever a form element does not pass validation (assuming validation method is passed), then error messages will
					// be shown. Of course this only applies if you reload the form.
					
'validation'=>array('method_1','method_2'), // Validation methods. This class comes with a few basic validation modules that can be used
											// for data validation, but you can also create your own modules. Please see below for further info.

'repop'=>true, 								// if set to true, all fields in a form will automatically reload based on the posted data. Very useful
											// if for example, a form fails validation, or you want select menus to retain their status.
											
'id'=>'Some_ID', 							// Adds an ID tag to an element, for CSS and JS purposes
'class'=>'Some_class', 						// Adds an ID tag to an element, for CSS and JS purposes
	
'required'=>true,							// adds a required tag to the element. If field is empty, it will return an error on post. 

'disabled'=>true,							// adds a disabled tag to the element. If field is set, data will be discarded.

'value'=>$some_value,						// assigns the field a value. One caveat, if this is set, and repop is set to true, this value will over-ride
											// the posted data on repopulation. This is intetional.
											
'place_holder'=>'some_text',				// In the case of select menus, this will assign the first option as the placeholder text, and disable it from
											// being selected. In the case of text fields, if they are empty, they will be filled with the text entered.

'elem'=>'input', 							// Mandatory. See below for different element types available. 																																														  					

'type'=>'text',								// Mandatory. See below for different types available.

// One thing of note with labels. If the parent element has an ID, then the label will automatically add a "Label for=parent_id". 

'label'=> array(
				'wrap'=>false,   			  // if set to true, the label will encase (open and close) around the rendered element. If false, it will be written before.
				'label_text'=>'checkbox',    // The text that appears in the table.
				'id'=>'some_id',		    // Assigns class to label. 
				'class'=>'some_class' 	   // Assigns class to label.
			   	), 		

// If form_ele_wrap is set, every added form element will be wrapped. Quite useful for automatically creating extra form markup.  
// Plese note, this will NOT wrap form open, form close or hidden elements. 
  
'form_ele_wrap'=>array(
				'type'=>'li',             // element type to wrap with. Span, div and li are just a few examples, although it can be any valid markup element.
				'class'=>'some_class',	  // Assigns class to wrapper.
				'id'=>'some_id',		  // Assigns ID. 
				      ),
// If error_wrap is set, error messages (if enabled) will be wrapped in the element. 

'error_wrap'=>array(
				'type'=>'span',  		// Same as above.   
				'id'=>'error',			// Same as above. 
				'class'=>'error'		// Same as above. 
				    ),				
				 		
);   

$formz = new formz($options);

// Now 