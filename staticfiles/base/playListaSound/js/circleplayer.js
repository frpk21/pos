(function (window, $){
	"use strict"	
	var HAPCirclePlayer = function (data){
			
		var self = this,
		isMobile = HAPUtils.isMobile(),
		parent = data.parent,
		circlePlayer = parent.find('.hap-circle-player'),
		loadCanvas = parent.find('.hap-load-canvas'),
		progressCanvas = parent.find('.hap-progress-canvas'),
		strokeSize = parseInt(circlePlayer.attr('data-stroke-size'),10);

		if(loadCanvas.length){
			var loadColor = loadCanvas.attr('data-color'), 
			loadCtx = loadCanvas[0].getContext('2d'),
			circleWidth = loadCanvas.width(),//canvas width and height needs to be the same!
			circleHeight = loadCanvas.height();
		}
		if(progressCanvas.length){
			var progressColor = progressCanvas.attr('data-color'),
			progressCtx = progressCanvas[0].getContext('2d'),
			circleWidth = progressCanvas.width(),
			circleHeight = progressCanvas.height();
		}

		var circ = Math.PI * 2,
		quart = Math.PI / 2,
		circleRadius = circleWidth/2,//same as circleHeight/2
		lastProgressPercent = 0,
		lastLoadPercent = 0;

		this.drawSeekbar = function(loadPercent,t,d){
			if(loadCanvas.length)drawLoadbar(loadPercent);
			if(progressCanvas.length)drawProgressbar(t/d);
		}

		this.clear = function(){
			if(progressCanvas.length)progressCtx.clearRect(0, 0, circleWidth, circleHeight);
			if(loadCanvas.length)loadCtx.clearRect(0, 0, circleWidth, circleHeight);
			lastProgressPercent = 0;
			lastLoadPercent = 0;
		}

		this.setProgress = function(point){

			var x = point.pageX - circlePlayer.offset().left - circleWidth/2,
				y = point.pageY - circlePlayer.offset().top - circleHeight/2,
				mAngle = Math.atan2(y, x);
					
			if (mAngle > -1 * Math.PI && mAngle < -0.5 * Math.PI) {
				mAngle = 2 * Math.PI + mAngle;
			}
			
			var v = Math.max(0, Math.min((mAngle + Math.PI / 2) / 2 * Math.PI * 10))/100;
			
			if(progressCanvas.length){
				progressCtx.clearRect(0, 0, circleWidth, circleHeight);
				drawProgressbar(v);
			}

			return v;

		}

		this.trackTooltip = function(e){

			var x1 = e.pageX - circlePlayer.offset().left,
				y1 = e.pageY - circlePlayer.offset().top,
				x = x1 - circleWidth/2,
				y = y1 - circleHeight/2,
				mAngle = Math.atan2(y,x);
			
			if (mAngle > -1 * Math.PI && mAngle < -0.5 * Math.PI){
				mAngle = 2 * Math.PI + mAngle;
			}
			var v = Math.max(0, Math.min((mAngle + Math.PI / 2) / 2 * Math.PI * 10))/100;
			
			return v;
		
		}

		function drawProgressbar(percent){
			if(progressCanvas.length){
				progressCtx.clearRect(0, 0, circleWidth, circleHeight);
				progressCtx.beginPath();      
				progressCtx.arc(circleWidth/2, circleHeight/2,circleRadius-strokeSize/2,-(quart),((circ) * percent) - quart,false);
				progressCtx.strokeStyle = progressColor;
				progressCtx.lineCap = 'butt';
				progressCtx.lineWidth = strokeSize;
				progressCtx.stroke();
				lastProgressPercent = percent;
			}
		}
		
		function drawLoadbar(percent){
			if(loadCanvas.length){
				loadCtx.clearRect(0, 0, circleWidth, circleHeight);
				loadCtx.beginPath();    
				loadCtx.arc(circleWidth/2, circleHeight/2,circleRadius-strokeSize/2,-(quart),((circ) * percent) - quart,false);
				loadCtx.strokeStyle = loadColor;
				loadCtx.lineCap = 'butt';
				loadCtx.lineWidth = strokeSize;
				loadCtx.stroke();
				lastLoadPercent = percent;
			}
		}

	};	

	window.HAPCirclePlayer = HAPCirclePlayer;

}(window,jQuery));
