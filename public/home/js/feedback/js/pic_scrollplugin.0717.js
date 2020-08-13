(function($) {
    $.zz_scroll = function(element, options) {
        var defaults = {
			eachBox:  '.pic_style_gun',
			prevId: 'btnPre',
			nextId: 'btnNext',
			dir: 1,
			type: 3,
			step: 3,
			effect: 'easeInOutExpo',
			loop: true
		}
		var plugin = this;
        plugin.settings = {}
		var $element = $(element);
		
		var init = function() {
			//合并设置
            plugin.settings = $.extend({}, defaults, options);
			
			//每个子宽度
			var eachWidth = $(plugin.settings.eachBox).outerWidth(),
			    //个数
				countbox = $(plugin.settings.eachBox).length,
				overallWid = countbox*eachWidth,
			    //滚动元素
			    $gunMain = $(plugin.settings.eachBox).parent(),
				gunMwidth = countbox*eachWidth,
				
				//滚动距离
				marWidth = 0,
				showWid = eachWidth*plugin.settings.type,
				maxlen = Math.ceil(countbox/plugin.settings.type-1)*plugin.settings.type,
				prevBtn = $('#'+plugin.settings.prevId),
				nextBtn = $('#'+plugin.settings.nextId);
				
			$gunMain.width(gunMwidth);
			if(!plugin.settings.loop){$(plugin.settings.prevId).attr('disabled', 'disabled');}	
				
			function mAni() {
				switch (plugin.settings.dir) {
				case 1: 
                $gunMain.animate({marginLeft: -marWidth}, 600 , plugin.settings.effect);

				
				break;
				case 2:
				$gunMain.animate({marginTop: marTop
                }, 600 , plugin.settings.effect);
				break;
			    }
			}
			
			function checkPos() {
				//alert(maxlen)
				$gunMain.stop();
				 if( marWidth >= (overallWid - showWid)){
						 marWidth = overallWid - showWid;
						 //marWidth = 0;
						 $(nextBtn).addClass("hover").attr("title","已经最后一页");
					}
					else if(marWidth < 0 ){
						marWidth = 0;
						 $(prevBtn).addClass("hover").attr("title","已经是第一页");
					  }
				
			}
				
			$(nextBtn).click(function() {	
				marWidth+=eachWidth*plugin.settings.step;
				$(prevBtn).removeClass("hover").attr("title","");
				checkPos(this);
		        mAni();
				return false;
	        });
			$(prevBtn).click(function() {
				marWidth-=eachWidth*plugin.settings.step;
				$(nextBtn).removeClass("hover").attr("title","");
				checkPos(this);
		        mAni();
				return false;
	        }); 
			$(plugin.settings.eachBox).hover(function(){
				$(this).addClass("hover");
				},function(){
				$(this).removeClass("hover");	
					})
			
		}
		
		init();
		
		$(window).resize(function(){
			init();
			})	
	}
})(jQuery);