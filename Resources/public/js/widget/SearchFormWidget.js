var SearchFormWidget = Class.create({

	initialize: function(config) {
			
		var $this = this;
		
		this.config = config;
		
		this.widget_name = config.name;
		
		this.widget_route = config.route;
		
		this.id = config.id;
		
		this.container = $(this.id);
		
		config.container = this.container;
		
		this.form = new JiveForm(config);
			
		this.form.success(this.post);
		
		this.form.failure(this.failure);
		
		$j.widgets[this.id] = this;
	},
	
	post: function(){
		
		var $this = this;
			
		var data = $this.serializeArray();
		
		data.push({name:'ajax', value:'true'});
	
		$.post(
			this.config.action,
			data,
			function(data){
				
		    	if(data.success) {
		    		
		    		if($($this.clicked).attr('name') == 'reset'){
		    			
		    			$this.resetValues();
		    		}
		    		
		    		$.each(data.listeners, function(id, listener){
		    			
		    			var widget = $j.widgets[id];
		    			
		    			widget.refresh_data();
		    		});
		    	}
			}, 'json');
		
	
	},
	
	failure: function(){
		
		alert('Fail!');
	}
	
	
});