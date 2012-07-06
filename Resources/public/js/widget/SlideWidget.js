
var SlideWidget = Class.create({

	initialize: function(config) {
		
		var $this = this;
		
		this.config = config;
		
		this.id = config.id;
		
		this.container = $('#' + this.id);
		
		this.element = this.container.get(0);
		
		this.swiper = new Swipe(this.element,{
			startSlide: config.startSlide,
			speed: config.speed,
			auto: config.auto,
			margin: config.margin,
			callback: function(event, index, element){
				$this.onSlideComplete(event, index, element);
				if(config.callback){
			
					config.callback(event, index, element);
				}
			}
		});
		
		$j.widgets[this.id] = this;
		
		this.next_btn = this.container.find('.slide-next');
		this.back_btn = this.container.find('.slide-back');
		
		this.next_btn.on('click', function(event){$this.slideNext(event)});
		this.next_btn.on('touchstart', function(event){$this.slideNext(event)});
		
		this.back_btn.on('click', function(event){$this.slideBack(event)});
		this.back_btn.on('touchstart', function(event){$this.slideBack(event)});
		
	},
	
	start: function(){
		
		this.swiper.resume();
	},
	
	stop: function(){
		
		this.swiper.stop();
	},
	
	onSlideComplete: function(event, index, element){
		
		this.updateIndicators();
	},
	
	slideNext: function(event){
	
		event.preventDefault();
		
		var $target = $(event.target);
		
		if($target.hasClass('disabled')){
			
			return false;
		}
		
		var swiper = this.swiper;
		
		//console.log(swiper.index + ' ' + swiper.length);
		
		if (swiper.index < swiper.length - 1){
			
			swiper.next();
			
		}else{
			
			swiper.goTo(0);
		}
	},
	
	slideBack: function(event){
		
		event.preventDefault();
		
		var $target = $(event.target);
		
		if($target.hasClass('disabled')){
			
			return false;
		}
	
		var swiper = this.swiper;
		
		//console.log(swiper.index + ' ' + swiper.length);
		
		if (swiper.index > 0){
			
			swiper.prev();
			
		}else{
			
			swiper.goTo(swiper.length - 1);
		}
	},
	
	setIndicators: function(indicators_selector){
		
		var $this = this;
		
		this.indicators = $(indicators_selector);
		
		this.indicators.children().on('click', function(event){
			
			$this.swiper.goTo($(this).index());
		})
		
		this.updateIndicators();
	},
	
	updateIndicators: function(){
	
		var index = 0;
		
		var swiper = this.swiper;
		
		if(swiper){
			
			index = swiper.index;
		}
		
		if(this.indicators){
			
			var $indicators = this.indicators.children();
			
			$indicators.removeClass('selected');
			
			$($indicators[index]).addClass('selected');
		}
		
		/*
		
		$back_link = this.back_btn;
		
		$next_link = this.next_btn;
		
		if(swiper && (swiper.index == 0)){
			
			$back_link.addClass('disabled');
			
		}else{
				
			$back_link.removeClass('disabled');
		}
		
		if(swiper && (swiper.index == swiper.length - 1)){
			
			$next_link.addClass('disabled');
			
		}else{
				
			$next_link.removeClass('disabled');
		}
		
		*/
	}
	
});


