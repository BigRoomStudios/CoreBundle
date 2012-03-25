
var ListWidget = Class.create({

	initialize: function(config) {
		
		var $this = this;
		
		this.col_widths = [];
		
		this.config = config;
		
		this.widget_name = config.name;
		
		this.widget_route = config.route;
		
		this.action = config.action;
		
		this.total = config.total;
		
		var id = this.widget_route + '_' + this.widget_name;
		
		this.container_name = '#' + id;
		
		$j.widgets[id] = this;
		
		this.container = $(this.container_name).first();
		
		this.loaded = [];
		
		this.loaded[config.page] = true;
		
		$(function(){
			
			$(window).resize(function(){
				
				$this.update();
			});
			
			//$this.update();
		});
		
		this.transform_table();
	},
	
	
	update: function(){
		
		//this.$row_container.width(this.$headers_table.width());
		
		this.$row_container.find('table').width(this.$headers_table.width());
	},
	
	transform_table: function(){
		
		var $this = this;
		
		var $table = this.container.find('table').first();
		
		$table.css('table-layout', 'fixed');
		
		
		$table.wrap('<div class="list-container" />');
		
		this.$rows_table = $table;
		
		var $headers_table = $table.clone();
		
		
		
		$headers_table.find('tbody').empty();
	
		$headers_table.wrap('<div class="header-container" />');
		
		
		$headers_table.insertBefore($table);
		
		this.$headers_table = $headers_table;
		
		this.$headers = this.$headers_table.find('th');
		
		
		$table.find('thead').empty();
		
		$table.width(this.container.width());
		
		var $td = $table.find('td:first');
		
		this.row_height = $td.outerHeight(true);
		
		//alert(this.row_height);
		
		this.rows_height = $table.outerHeight();
		
		var total_height = this.row_height * this.total;
		
		var extra_height = total_height - this.rows_height;
		
		var page_height = this.row_height * this.config.page_size;
		
		this.page_height = page_height;
		
		$row_container = $('<div class="row-container" style="position:relative;width:100%;height: ' + (page_height+1) + 'px; overflow-x: hidden; overflow-y:scroll;" />');
		
		this.$row_container = $row_container;
		
		//$table.after('<div class="row-placeholder-bottom" style="height: '+extra_height+'px;" />');
		
		$table.detach();
		
		var $page_table = $table.clone();
		
		$page_table.find('tbody').empty();
		
		this.$page_table = $page_table;
		
		this.redraw_pages();
		
		$page = this.$row_container.find('.page-' + this.config.page);
				
		$page.find('tbody').html($table.find('tbody').html());
		
		
		$headers_table.after($row_container);
		 
		this.update();
		
		this.load_visible_pages();
		
		//update links to ajax functions
		
		$headers_table.find('a').click(function(event){
			
			$this.header_click(this, event);
		});
		
		this.$row_container.scroll(function(event){
			
			$this.load_visible_pages();
		});
		
		this.container.find('button.list-action').click(function(event){
			
			$this.action_click(this, event);
		});
		
		$(this.container_name + ' input.select').live('click', function(event){
			
			$this.select_click(this, event);
		});
	},
	
	redraw_pages: function(){
		
		$this = this;
		
		var $paging = this.container.find('.paging');
		
		var $total_count = $paging.find('.total');
		var $page_count = $paging.find('.page-count');
		var $page_links = $paging.find('.pages');
		
		$total_count.html(this.config.total);
		$page_count.html(this.config.pages);
		
		$page_links.empty();
		
		this.$row_container.empty();
		
		var page_height = this.page_height;
		
		for(var i = 1; i <= this.config.pages; i++){
			
			var link = '<a class="page" href="?list[page]=' + i + '">' + i + '</a> ';
			 
			$page_links.append(link);
			
			var $current_page_table = this.$page_table.clone();
		
			if( i == this.config.pages ){
				
				var last_page_count = this.config.page_size - ((this.config.page_size * this.config.pages) - this.config.total);
				
				page_height = this.row_height * last_page_count;
			}
			
			$current_page_table.data('page', i);
			
			$current_page_table.addClass('page-'+i);
			
			var $page_div = $('<div />').addClass('page').height(page_height).append($current_page_table);
			
			this.$row_container.append($page_div);
			
		}
		
		$page_links.find('a').click(function(event){
			
			$this.page_click(this, event);
			
		});
	},
	
	action_click: function(target, event){
		
		event.preventDefault();
		
		var name = $(target).attr('name');
		
		eval("this."+name+"(target);");
	},
	
	select_click: function(target, event){
			
		var checked = $(target).attr('checked');
		
		var row = $(target).data('id');
		
		this.set_selected(row, checked);
	},
	
	set_selected: function(row, selected){
		
		var $row = this.container.find('tr.row-'+row);
		
		if(selected){
			
			$row.addClass('selected');
			
		}else{
			
			$row.removeClass('selected');
		}
	},
	
	select_all: function(target){
		
		var href = $(target).data('route');
		
		var form = this.container.find('form');
		
		var data = form.serializeArray();
			
		data.push({name:'ajax', value:'true'});
		
		$.post( href, data, function(data){});
		
		this.container.find('input.select').attr('checked', true);
		
		this.container.find('tr').addClass('selected');
	},
	
	select_none: function(target){
		
		var href = $(target).data('route');
		
		var form = this.container.find('form');
		
		var data = form.serializeArray();
			
		data.push({name:'ajax', value:'true'});
		
		$.post( href, data, function(data){});
		
		this.container.find('input.select').attr('checked', false);
		
		this.container.find('tr').removeClass('selected');
	},
	
	delete_selected: function(target){
		
		if(confirm('Are you sure you want to delete the selected items?')){
		
			var $this = this;
			
			var href = $(target).data('route');
			
			var form = this.container.find('form');
			
			var data = form.serializeArray();
				
			data.push({name:'ajax', value:'true'});
			
			$.post( href, data, function(data){
				
				var json = jQuery.parseJSON( data );
				
				$this.handle_data(json, true);
			});
		}
		
		//this.container.find('input.select').attr('checked', true);
	},
	
	page_click: function(target, event){
		
		event.preventDefault();
		
		var href = $(target).attr('href');
		
		var page = $(target).html();
		
		if(!this.loaded[page]){
			
			var action = this.action + '/rows' + href;
			
			this.load(action, false);
		}
		
		//this.set_selected_page(page);
		
		this.scroll_to_page(page);
	},
	
	
	
	header_click: function(target, event){
		
		event.preventDefault();
		
		var href = $(target).attr('href');
		
		var action = this.action + '/rows' + href + '&' + this.widget_name + '[page]=1';
		
		this.load(action, true);
		
		this.$headers_table.find('.selected').removeClass('selected');
		
		$(target).closest('th').addClass('selected');
		
		this.set_selected_page(1);
		
		this.jump_to_page(1);
	},
	
	refresh_data: function(){
		
		var action = this.action + '/rows?' + this.widget_name + '[page]=1';
		
		this.load(action, true);
		
		this.set_selected_page(1);
		
		this.jump_to_page(1);
	},
	
	load_visible_pages: function(){
		
		var $this = this;
		
		var top = 0;
		
		var bottom = this.$row_container.height();
		
		//alert(bottom);
		
		$pages = $row_container.find('.page');
		
		var scrolled_page = false;
		
		$pages.each(function(index){
			
			var page = index + 1;
			
			var page_top =  $(this).position().top;
			
			var page_bottom =  page_top + $(this).height();
			
			var page_top_visible = false;
			
			var page_bottom_visible = false;
			
			//console.log('page: ' + page + ' top:' + page_top);
			
			if(page_top >= top && page_top < bottom){
				
				page_top_visible = true;
			}
			
			if(page_bottom >= top && page_bottom < bottom){
				
				page_bottom_visible = true;
			}
			
			if(page_top_visible && !scrolled_page){
				
				scrolled_page = page;
				
				$this.set_selected_page(page);
			}
			
			if(page_top_visible || page_bottom_visible){
				
				if(!$this.loaded[page]){
				
					var action = $this.action + '/rows?' + $this.widget_name + '[page]=' + page;
					
					$this.loaded[page] = true;
					
					$this.load(action, false);
				}
			}
		})
	},
	
	set_selected_page: function(page){
		
		this.container.find('.paging .current-page').html(page);
	},
	
	jump_to_page: function(page){
		
		var scroll_to = (page - 1) * this.page_height;
						
		this.$row_container.scrollTop(scroll_to);
	},
	
	scroll_to_page: function(page){
		
		var scroll_to = (page - 1) * this.page_height;
		
		$row_container.animate({scrollTop : scroll_to},300);
	},
	
	handle_data: function(json, clear){
		
		var $this = this;
		
		if(json){
			
			$this.config.total = json.total;
			$this.config.page = json.page;
			$this.config.pages = json.pages;
			$this.config.page_size = json.page_size;
			
			var rendered = json.rendered;
		 	
		 	if(clear){
		 	
		 		$this.redraw_pages();
		 		
		 		$this.loaded = [];
		 	}
		 	
			$page = $this.$row_container.find('.page-' + json.page);
			
			$page.find('tbody').html(rendered);
		   	
		   	$this.update();
		   	
		   	$this.loaded[json.page] = true;
		}
	},
	
	load: function(href, clear) {
		
		//console.log(href);
		
		var $this = this;

		$.getJSON(  
			href,  
			{ajax: 1},  
			function(json) {
				
				$this.handle_data(json, clear);
			}  
		);
		
	}
	
});