/*
 * JiveForm v1.0 | Max Felker
 * Maintains, validates, and submits form UI components 
*/
 
// START Class
var JiveForm = Class.create({

	initialize: function(config) { 
		
		var $this = this;
		
		this.config = config;
		
		// form DOM
		this.container = $("#"+config.container);
		this.form_name = this.container.attr('name');
		
		// fields collector 
		this.fields = {
			text: this.container.find('input[type=text]'),
			textareas: this.container.find('textarea'),
			radios: this.container.find('input[type=radio]'),
			checkboxes: this.container.find('input[type=checkbox]'),
			selects: this.container.find('select'),
			submits: this.container.find('input[type=submit]'),
			buttons: this.container.find('button'),
		};
		
		//handle button clicks
		this.clicked = null;
		
		this.fields.buttons.click(function(e){
			
			e.preventDefault();
			
			$this.clicked = this;
			
			$this.submit(e);
		});
				
		// when this form submits, prevent it and validate first
		this.container.submit(function(e) {
			$this.submit(e);
		});
		
		// add form to jive
		$j.forms[config.container] = this;

	}, // end init
	
	serializeArray: function() {
		
		var data = this.container.serializeArray();
		
		var click_name = $(this.clicked).attr('name');
		var click_value = $(this.clicked).attr('value');
		
		if(click_name){
			
			data.push({name:click_name, value:click_value});
		}
		
		return data;
	},
	
	resetValues: function(){
		
		for (var field_type in this.fields) {
			
			switch(field_type){
				
				case 'text':
				
					// loop through each field
					this.fields[field_type].each(function() {
					
						// disable field
						$(this).attr('value','');
						
					});
					
					break;
			}
		}
	},
	
	// loops through each input and validates input value
	validate: function() {
		
		var $this = this;
		
		for (var field_type in $this.fields) {
			
			// if not the submit buttons
			if(field_type!="submits" && field_type!="buttons") {
				
				// loop through each field
				$this.fields[field_type].each(function() {
					
					// disable field
					$(this).attr('disabled','disabled');
					
					// loop through jive's regex patterns 
					for(var reg_pattern in $j.regex) {
						
						// if the input has that class
						if($(this).hasClass('valid-'+reg_pattern)) {
							
							// if the test passes
							if($j.regex[reg_pattern].test(this.value)) {
								$(this).addClass('valid');
								$(this).removeClass('invalid');
							} else {
								$(this).addClass('invalid');
								$(this).removeClass('valid');
							}
								
						}
						
					}
					
					// enable field
					$(this).removeAttr('disabled');
					
				}); // end each on field type
				
			} // if not submit buttons
				
			//}
			//
		} // loop through field types
		
		// valid / invalid fields
		this.valid_fields = this.container.find('.valid');
		this.invalid_fields = this.container.find('.invalid');
		
		// if invalid field count is 0, form is valid
		if(this.invalid_fields.length==0) {
			return true;
		} else {
			return false;
		}	

	},

	// the submit override
	submit: function(event) {
		
		// prevent submission
		event.preventDefault();
		
		// define ajax url for posting
		this.ajax_url = this.container.attr('action')+"?"+this.container.serialize();
		
		// validate the submission
		if(this.validate()) {

			// if success call back, run it
			if(this.success_callback) {

				this.success_callback(this);
				
			// default to returning true
			} else {

				return true;
			
			}
		
		// form did not validate
		} else {
		
			// iff failure call back, run it
			if(this.failure_callback) {
			
				this.failure_callback(this);
			
			// else default to returning false	
			} else {
				
				return false;
			
			}

		}

	}, // end submit
	
	// method to set success callback
	success: function(custom_function) {
	
		this.success_callback = custom_function
	
	},
	// method to set failure callback
	failure: function(custom_function) {
	
		this.failure_callback = custom_function;
	
	}
	

}); // END Class

// on dom readyd
/*
$(document).ready(function() {
	
	// find all the tab containers 
	$('.jive-form').each(function() {
		
		// create tabs ui components
		new JiveForm({
		    container:this.id
		});
		
	});	
	
});
*/
