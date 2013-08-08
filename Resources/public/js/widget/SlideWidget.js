var SlideWidget = Class.create({
	
	initialize: function(config) {
			
		var $this = this;
		
		this.config = config;
		
		this.supports_history = ( window.history && history.pushState ? true : false ); 
		
		this.container = $(config.container);
		
		this.swipe = new Swipe(this.container.get(0),{
			startSlide: config.startSlide,
			speed: config.speed,
			auto: config.auto,
			margin: config.margin,
			callback: function(event, index, elem) {
				
				$this.onSlideComplete(event, index, elem);
			}
		});
		
		$j.widgets[config.container] = this;
		
		this.swipe.stop();
		
		$(function(){
			
			$this.updateSlideIndicators();
		});
		
		this.controls_container = config.container;
		
		if(config.controls_container){
			
			this.controls_container = config.controls_container;
		}
		

		
		/*
		if( Modernizr.touch ){
			
			$(this.controls_container + ' .slide-back').live('touchstart', function(event){$this.slideBack(event)});
			$(this.controls_container + ' .slide-next').live('touchstart', function(event){$this.slideNext(event)});
			
			$(this.controls_container + ' .slide-indicators a').live('touchstart', function(event){
	
				//this is the element clicked:
				$this.indicatorClick(event, this);
			});	
		}
		*/
		
		$(this.controls_container + ' .slide-next').live('click', function(event){$this.slideNext(event)});
		$(this.controls_container + ' .slide-back').live('click', function(event){$this.slideBack(event)});	
		
		$(this.controls_container + ' .slide-indicators a').live('click', function(event){

			//this is the element clicked:
			$this.indicatorClick(event, this);
		});
		
		
		
		
	},
	
	start: function(){
		
		this.swipe.resume();
	},
	
	stop: function(){
		
		this.swipe.stop();
	},
	
	indicatorClick: function(event, elem){
		
		event.preventDefault();
		
		event.stopPropagation();
		
		var key = $(elem).html();
		
		this.swipe.goTo(key-1);
	},
	
	slideTo: function(key){
		
		this.swipe.goTo(key);
	},
	
	onSlideComplete: function(event, index, elem){
		
		if(this.config.callback){
					
			this.config.callback(event, index, elem);
		}
			
		this.updateSlideIndicators();
	},
	
	slideNext: function(event){
		
		event.preventDefault();
		
		event.stopPropagation();
		
		//alert('slideNext');
		
		var $target = $(event.target);
		
		if($target.hasClass('disabled')){
			
			return false;
		}
		
		var swiper = this.swipe;
		
		swiper.next();
	
	},
	
	slideBack: function(event){
		
		event.preventDefault();
		
		event.stopPropagation();
		
		var $target = $(event.target);
		
		if($target.hasClass('disabled')){
			
			return false;
		}
	
		var swiper = this.swipe;
		
		swiper.prev();
		
	},
	
	updateSlideIndicators: function(){
		
		var index = 0;
	
		var swiper = this.swipe;
		
		if(swiper){
			
			index = swiper.index;
		}
		
		var $indicators = $(this.controls_container + ' .slide-indicators a');
		
		$indicators.removeClass('selected');
		
		$($indicators[index]).addClass('selected');
		
		$back_link = $('#slide-back a');
		
		$next_link = $('#slide-next a');
		
		if(swiper && (swiper.index == 0) && $j.page_nav && (!$j.page_nav.get_prev_page())){
			
			$back_link.addClass('disabled');
			
		}else{
				
			$back_link.removeClass('disabled');
		}
		
		if(swiper && (swiper.index == swiper.length - 1) && $j.page_nav && (!$j.page_nav.get_next_page())){
			
			$next_link.addClass('disabled');
			
		}else{
				
			$next_link.removeClass('disabled');
		}
	}
	
	
});