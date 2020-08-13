
;(function($){
    $.fn.fix = function(options){
        var defaults = {
            float : 'left',
			minStatue : false,
			skin : 'blue',
			durationTime : 1000	
        }
        var options = $.extend(defaults, options);		

        this.each(function(){			
            //获取对象
			var thisBox = $(this),
				closeBtn = thisBox.find('.td_close_btn' ),
				show_btn = thisBox.find('.td_show_btn' ),
				sideContent = thisBox.find('.td_side_content'),
				sideList = thisBox.find('.td_side_list')
				;	
			var defaultTop = thisBox.offset().top;	//对象的默认top	
			
			thisBox.css(options.float, 0);			
			if(options.minStatue){
				$(".show_btn").css("float", options.float);
				sideContent.css('width', 0);
				show_btn.css('width', 25);
				
			}
			//皮肤控制
			if(options.skin) thisBox.addClass('side_'+options.skin);
				
						
			//核心scroll事件			
			$(window).bind("scroll",function(){
				var offsetTop = defaultTop + $(window).scrollTop() + "px";
	            thisBox.animate({
	                top: offsetTop
	            },
	            {
	                duration: options.durationTime,	
	                queue: false    //此动画将不进入动画队列
	            });
			});	
			//close事件
			closeBtn.bind("click",function(){
				sideContent.animate({width: '0px'},"fast");
            	show_btn.stop(true, true).delay(300).animate({ width: '25px'},"fast");
			});
			//show事件
			 show_btn.click(function() {
	            $(this).animate({width: '0px'},"fast");
	            sideContent.stop(true, true).delay(200).animate({ width: '154px'},"fast");
	        });
				
        });	//end this.each

    };
})(jQuery);



