var LinkListWidget = ListWidget.create({

	initialize: function(config) {
		
		this._super( config );
		
		var self = this;
		
		
		this.search_field = this.container.find('input.link-search');
		this.add_button = this.container.find('button.link-add');
		
		this.search_route = this.search_field.data('route');
		this.add_route = this.add_button.data('route');
		
		this.search_field.typeahead({
			
			source: function (query, process) {
				
				//var href = '/dev.php/admin/project/widget/project_members/add_link';
				
				var send = {query: query}
				
				return self.call(self.search_route, send, function(data){
					
					
					
					return process(data.options);
				});
				
			},
			
			updater:function (item) {

				self.do_add_link(item);
				
				return '';
			}
		});
		
	},
	
	
	do_add_link: function(item){
		
		var self = this;
				
		var data = {query: item}
		
		self.call(this.add_route, data, function(data){
			
			self.refresh_data();
			
			//alert(data.id);
			
			//$this.enable_reordering();
		});
	},
	
	add_link: function(target){
		
		var self = this;
		
		var value = this.search_field.attr('value');
		
		self.do_add_link(value);
		
		//alert('here');
		
		//$(target).click();
		
		/*this.file_input.attr('value', '');
		
		this.file_input.click();*/
	}
	
	
});