/*
 * EditFormWidget v1.1 | Sam Mateosian & Max Felker
 * Wrapper for Edit Form Widgets UI
*/
var EditFormWidget = Class.create({

	initialize: function(config) {
			
		var $this = this;
		
		// set UI configs
		this.config = config;
		this.widget_name = config.name;
		this.widget_route = config.route;
		this.id = config.id;
		this.entity_id = config.entity_id;
		this.container = $('#'+this.id);
		
		// create jive form
		this.form = new JiveForm({
			container:config.id,
			action:config.action
		});
			
		// set custom form callbacks
		this.form.success(function(){
			$this.post();
		});
		
		this.form.failure(function(){
			$this.failure();
		});
		
		$j.widgets[this.id] = this;
	},
	
	// posts data to a controller on successful form validation
	post: function(){
		
		var $this = this;
		
		// remove all error blurbs
		$('.blurb-error').remove();
		
		$j.messenger.clear();
		
		// set data to send
		var data = this.form.serializeArray();
			
		data.push({name:'ajax', value:'true'});
		
		var action = this.config.action;
		
		if(this.entity_id){
			
			action += '?id=' + this.entity_id;
		}
		
		// send it to controller
		$.post(
			action, 
			data,
			function(data){
				
				// on success
		    	if(data.success) {
		    		
		    		// generate success msg
		    		$j.msg({
					    type:'success',
					    content:"<p><b>Success!</b> Your item was updated.</p>" // should come from server
					});
		    	
		    		// redirect me to the success route
		    		if(data.redirect && data.redirect.url && data.redirect.route){
		    			
		    			if($j.nav){
		    			
		    				$j.nav.go(data.redirect.route, data.redirect.url);
		    			}
		    		}
		    		
		    	} else {
		    		
		    		// sorry bro, something went wrong
		    		$j.msg({
					    type:'failure',
					    sticky:1,
					    content:"<p><b>Oops!</b> Sorry, something bad happened.</p>"
					});
		    		
		    	}
		    	
			}, 'json'); // end post call
	
	}, // end post function
	
	// custom failure call back
	failure: function(){
		
		// remove all error blurbs
		$('.blurb-error').remove();
		
		// find all invalid inputs and append an error blurb
		this.container.find('.invalid').parent().append('<div class="blurb blurb-error"><span>bad format!</span></div>');
		
		
		$j.msg({
		    type:'warning',
		    sticky:1,
		    content:"<p><b>Er...</b> hang on, some of your form entries are bogus.</p>"
		});
	}
	
	
}); // end EditFormWidget