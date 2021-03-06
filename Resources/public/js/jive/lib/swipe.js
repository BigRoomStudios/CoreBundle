/*
 * Swipe 1.1
 *
 * Brad Birdsall, Prime
 * Copyright 2011, Licensed GPL & MIT
 *
 * Modified by Sam Mateosian, Big Room Studios
 * added fallback jquery animations, requires jquery and Modernizr
 * 
*/

window.Swipe = function(element, options) {
	  
  // return immediately if element doesn't exist
  if (!element) return null;

  var _this = this;

  // retreive options
  this.options = options || {};
  this.index = this.options.startSlide || 0;
  this.speed = this.options.speed || 200;
  this.callback = this.options.callback || function() {};
  this.delay = this.options.auto || 0;
  this.margin = this.options.margin || 0;
  
  // reference dom elements
  this.container = element;
  this.element = this.container.children[0]; // the slide pane

  // static css
  this.container.style.overflow = 'hidden';
  this.element.style.listStyle = 'none';

  // trigger slider initialization
  this.setup();

  // begin auto slideshow
  this.begin();

  // add event listeners
  if (this.element.addEventListener) {
    this.element.addEventListener('touchstart', this, false);
    this.element.addEventListener('touchmove', this, false);
    this.element.addEventListener('touchend', this, false);
    this.element.addEventListener('webkitTransitionEnd', this, false);
    this.element.addEventListener('msTransitionEnd', this, false);
    this.element.addEventListener('oTransitionEnd', this, false);
    this.element.addEventListener('transitionend', this, false);
    window.addEventListener('resize', this, false);
  }

};

Swipe.prototype = {

  setup: function() {

    // get and measure amt of slides
    this.slides = this.element.children;
    this.length = this.slides.length;
    
    // return immediately if their are less than two slides
    if (this.length < 2) return null;
   
    // determine width of each slide
    //this.width = this.container.getBoundingClientRect().width;
    this.width = $(this.container).width(); //sm - use jquery to get width for ie
    
    //alert(this.width);

    // return immediately if measurement fails
    if (!this.width) return null;

    // hide slider element but keep positioning during setup
    
    this.container.style.visibility = 'hidden';
	
	//fix styles to allow for jquery animation alternative
	if ( ! Modernizr.csstransitions ) {
	
		$(this.element).parent().css('position', 'relative');
		
		var height = $(this.element).height();
		
		//$(this.element).css('position', 'absolute');
		
		$(this.element).css('left', '0px');
		
		//$(this.element).css('height', height + 'px');
		
		$(this.element).children().css('float', 'left');
		
		$(this.element).children().css('transition-timing-function', 'ease-out');
		$(this.element).children().css('-webkit-transition-timing-function', 'ease-out');
		$(this.element).children().css('-moz-transition-timing-function', 'ease-out');
	}
	
	//duplicate first slide append to end of slide show for wrapping:
	$(this.element).append($(this.slides[0]).clone());
	
	//duplicate first slide append to end of slide show for wrapping:
	$(this.element).prepend($(this.slides[this.length-1]).clone());
	
	this.real_length = this.length;
	
	this.length += 2;
	
    // dynamic css
   
	this.resise();
	
    // set start position and force translate to remove initial flickering
    this.slide(this.index, 0); 

    // show slider element
    this.container.style.visibility = 'visible';
	
  },
	
  resise: function(){
  	
  	this.width = $(this.container).width(); //sm - use jquery to get width for ie
  	
  	this.element.style.width = (this.slides.length * (this.width + this.margin)) + 'px';
    
    var index = this.slides.length;
    
    while (index--) {
    	
      var el = this.slides[index];
     
      el.style.width = this.width + 'px';
      el.style.cssFloat = 'left';
      el.style.marginRight = this.margin + 'px';
    }
    
    this.slide(this.index, 0);
  	
  },
  
  slide: function(index, duration) {
	
    var style = this.element.style;
	
	//console.log(index);
	
	// set new index to allow for expression arguments
	this.index = index;

	if ( Modernizr.csstransitions ) {

		// set duration speed (0 represents 1-to-1 scrolling)
	    style.webkitTransitionDuration = style.MozTransitionDuration = style.msTransitionDuration = style.OTransitionDuration = style.transitionDuration = duration + 'ms';
	
	    // translate to given index position
	    style.webkitTransform = 'translate3d(' + -((index+1) * (this.width + this.margin)) + 'px,0,0)';
	    style.msTransform = style.MozTransform = style.OTransform = 'translateX(' + -((index+1) * (this.width + this.margin)) + 'px)';

	}else{
			
		var real_this = this;
		
		var new_left =  0 - ((index+1) * (this.width + this.margin));
		
		
		$(this.element).animate({
	        marginLeft: new_left  //sm - requires the inner element to be position: relative
	    }, {
	    	duration: duration,
	    	easing: 'swing',
	    	queue: false,
	    	complete: function(e) {
	            // animation complete, fire the callback function
	            real_this.transitionEnd(e);
	        }
	    });
	}
	
	this.index = index;
	
  },

  getPos: function() {
    
    // return current index position
    return this.index;

  },
  
  goTo: function(index){
  	
  	clearTimeout(this.interval);
  	
  	this.slide(index, this.speed);
  	
  },
  
  prev: function(delay) {

    // cancel next scheduled automatic transition, if any
    this.delay = delay || 0;
    clearTimeout(this.interval);
	
    // if not at first slide
    if ((this.index >= 0)){
    	
    	 this.slide(this.index-1, this.speed);
  	}
  },

  next: function(delay) {

    // cancel next scheduled automatic transition, if any
    this.delay = delay || 0;
    clearTimeout(this.interval);
	
    if (this.index < this.real_length){
    	
    	this.slide(this.index+1, this.speed); // if not last slide
    }
  },

  begin: function() {

    var _this = this;
	
	clearTimeout(this.interval);
	
    this.interval = (this.delay)
      ? setTimeout(function() { 
        _this.next(_this.delay);
      }, this.delay)
      : 0;
  
  },
  
  stop: function() {
    this.delay = 0;
    clearTimeout(this.interval);
  },
  
  resume: function() {
    this.delay = this.options.auto || 0;
    this.begin();
  },

  handleEvent: function(e) {
    switch (e.type) {
      case 'touchstart': this.onTouchStart(e); break;
      case 'touchmove': this.onTouchMove(e); break;
      case 'touchend': this.onTouchEnd(e); break;
      case 'webkitTransitionEnd':
      case 'msTransitionEnd':
      case 'oTransitionEnd':
      case 'transitionend': this.transitionEnd(e); break;
      case 'resize': this.resise(); break;
    }
  },

  transitionEnd: function(e) {
    
    if (this.delay) this.begin();
	
	//if on fake first slide for wrapping, swap back to the real first slide real quick
    if(this.index >= this.real_length){
    	
    	this.slide(0,0);
    }
    
    //if on fake last slide for wrapping, swap back to the real last slide real quick
    if(this.index <= -1){
    	
    	this.slide(this.real_length-1,0);
    }
    
    $(this.element).children().removeClass('current');
    
    $(this.slides[this.index+1]).addClass('current');
	
    this.callback(e, this.index, this.slides[this.index]);

  },

  onTouchStart: function(e) {
    
    //clearTimeout(this.interval);
    
    this.start = {

      // get touch coordinates for delta calculations in onTouchMove
      pageX: e.touches[0].pageX,
      pageY: e.touches[0].pageY,

      // set initial timestamp of touch sequence
      time: Number( new Date() )

    };

    // used for testing first onTouchMove event
    this.isScrolling = undefined;
    
    // reset deltaX
    this.deltaX = 0;

    // set transition time to 0 for 1-to-1 touch movement
    this.element.style.webkitTransitionDuration = 0;

  },

  onTouchMove: function(e) {
	
	//if on fake first slide for wrapping, swap back to the real first slide real quick
    if(this.index >= this.real_length){
    	
    	this.index = 0;
    } 
	
	//if on fake last slide for wrapping, swap back to the real last slide real quick
    if(this.index <= -1){
    	
    	this.index = this.real_length-1;
    }
	
    // ensure swiping with one touch and not pinching
    if(e.touches.length > 1 || e.scale && e.scale !== 1) return;

    this.deltaX = e.touches[0].pageX - this.start.pageX;
	this.deltaY = e.touches[0].pageY - this.start.pageY;
	
    // determine if scrolling test has run - one time test
    if ( typeof this.isScrolling == 'undefined') {
      this.isScrolling = !!( this.isScrolling || Math.abs(this.deltaX) < Math.abs(e.touches[0].pageY - this.start.pageY) );
    }

    // if user is not trying to scroll vertically
    if (!this.isScrolling) {

      // prevent native scrolling 
      e.preventDefault();

      // cancel slideshow
      clearTimeout(this.interval);

      // increase resistance if first or last slide
      /*this.deltaX = 
        this.deltaX / 
          ( (!this.index && this.deltaX > 0               // if first slide and sliding left
            || this.index == this.length - 1              // or if last slide and sliding right
            && this.deltaX < 0                            // and if sliding at all
          ) ?                      
          ( Math.abs(this.deltaX) / this.width + 1 )      // determine resistance level
          : 1 );*/                                          // no resistance if false
      
      this.deltaX = this.deltaX / 1;
      
      // translate immediately 1-to-1
      this.element.style.webkitTransform = 'translate3d(' + (this.deltaX - (this.index+1) * (this.width + this.margin)) + 'px,0,0)';



    }

  },

  onTouchEnd: function(e) {
	
	//alert('here');
	
    // determine if slide attempt triggers next/prev slide
    var isValidSlide = 
          Number(new Date()) - this.start.time < 250      // if slide duration is less than 250ms
          && Math.abs(this.deltaX) > 20                   // and if slide amt is greater than 20px
          || Math.abs(this.deltaX) > this.width/3;        // or if slide amt is greater than 1/3 the width

    // determine if slide attempt is past start and end
    /*    isPastBounds = 
          !this.index && this.deltaX > 0                          // if first slide and slide amt is greater than 0
          || this.index == this.length - 1 && this.deltaX < 0;    // or if last slide and slide amt is less than 0
	*/
	
    // if not scrolling vertically
    if (!this.isScrolling) {

      // call slide function with slide end value based on isValidSlide and isPastBounds tests
      this.slide( this.index + ( isValidSlide ? (this.deltaX < 0 ? 1 : -1) : 0 ), 200 );

    }

  }

};
