(function (window, $){
	"use strict"
	var HAPTextScroller = function (data){
			
		var self = this,
		isMobile = HAPUtils.isMobile(),
		settings = data.settings,
		mask = data.mask,
		container = data.container,
		titleScrollSpeed = Math.abs(settings.titleScrollSpeed),
		titleScrollSeparator = settings.titleScrollSeparator,//text inbetween title
		titleScrollCenterOnRest = settings.titleScrollCenterOnRest || false,//center text if in rest position (not scrolling)
		titleScrollPauseOnHover = settings.titleScrollPauseOnHover || true,

		direction = 'left',
		scrollInterval = 100,
		scrollIntervalID,
		scrollValue=0,
		originalString, 
		originalStringSize, 
		scrollStringSize,
		scrollString,
		inited,
		active = false;

		this.setData = function(){

			if(!isMobile && titleScrollPauseOnHover){
				mask.on('mouseenter',function(){   
	            	if(active)if(scrollIntervalID) clearInterval(scrollIntervalID);
	   			}).on('mouseleave',function(){ 
	   				if(active){
						if(scrollIntervalID) clearInterval(scrollIntervalID);
						scrollIntervalID = setInterval(scrollText, scrollInterval);		 
	   				}
			    });   
			}
			
		}

		this.input = function(s) {
			
			originalString = s;
			
			container.html('').css('width', 'auto').html(originalString);
			originalStringSize = container.width();

			if(mask.width() < originalStringSize)self.setScrollString();
			else{
				if(titleScrollCenterOnRest)container.addClass('hap-media-title-center');
				inited=true;
			} 
			
		}

		this.setScrollString = function() {
		
			if(titleScrollCenterOnRest)container.removeClass('hap-media-title-center');

			var format = originalString.replace(/\s/g, '&nbsp;'), t = format + titleScrollSeparator;
			
			container.html('').css('width', 'auto').html(t);
			scrollStringSize = container.width();

			if(scrollStringSize == 0) return;
			var size, z = t;
			//we need to append so many times that its at least 2 one lengths long.
			if (scrollStringSize <= mask.width() * 2) {
				var i, limit = Math.floor(mask.width() * 2 / scrollStringSize);//we lower it down because we already have one text inside.
				for (i=0; i < limit; i++) {
					z+=t;
				}
				size = scrollStringSize*(limit+1);
			} else {//the above loop didnt handle case if text is longer than visibleSpace * 2, then it wouldnt be copied at all and it still need to be copied once.
				z+=t;
				size = scrollStringSize*2;
			}

			container.html(z);
			container.css('width', size+1+'px');
			scrollString=z;

			inited=true;

			self.activate();
		}
		
		this.activate = function() {
			if(active)self.deactivate();
			if(mask.width() >= originalStringSize){
				container.html('').css('width', 'auto').html(originalString);
				if(titleScrollCenterOnRest)container.addClass('hap-media-title-center');
				active=false;
			}else{
				if(scrollIntervalID) clearInterval(scrollIntervalID);
				scrollIntervalID = setInterval(scrollText, scrollInterval);	
				active=true;
			}
		}
		
		this.deactivate = function(value) {
			if(!active)return;
			if(scrollIntervalID) clearInterval(scrollIntervalID);
			if(direction == "left"){
				container.css('left', 0+'px');
				scrollValue=0;
			}
			if(typeof value !== 'undefined')container.html('').css('width', 'auto').html(value);
			active=false;
		}
		
		this.checkSize = function() {
			if(!inited)return;
			if(mask.width() >= originalStringSize){
				if(active)self.deactivate();
				container.html('').css('width', 'auto').html(originalString);
				if(titleScrollCenterOnRest)container.addClass('hap-media-title-center');
			}else{
				self.setScrollString();
			}
		}

		this.setScrollSpeed = function(v){
			titleScrollSpeed = Math.abs(v);
		}

		this.setScrollSeparator = function(v){
			titleScrollSeparator = v;
			self.deactivate();
			self.setScrollString();
		}

		function scrollText() {
			if(direction == "left"){
				scrollValue -= titleScrollSpeed;
				container.css('left', scrollValue+'px');
				if(parseInt(container.css('left'),10) < -scrollStringSize) {
					scrollValue =- titleScrollSpeed;
				}
			}
		}

		this.setData();
		
	};	
	
	window.HAPTextScroller = HAPTextScroller;

}(window,jQuery));		