//弹出层
function showOrHide(flag) {
	if(flag == 1) {
		   document.getElementById("top").style.display = "block";
		   document.getElementById("below").style.display = "none";
   }
   if(flag == 2) {
		  document.getElementById("top").style.display = "none";
		  document.getElementById("below").style.display = "none";
  }
	if(flag == 3) {
		   document.getElementById("login").style.display = "block";
		   document.getElementById("below").style.display = "block";
   }
   if(flag == 4) {
		  document.getElementById("login").style.display = "none";
		  document.getElementById("below").style.display = "none";
  }  
	if(flag == 5) {
		   document.getElementById("score").style.display = "block";
		   document.getElementById("below").style.display = "block";
   }
   if(flag == 6) {
		  document.getElementById("score").style.display = "none";
		  document.getElementById("below").style.display = "none";
  } 
  	if(flag == 7) {
		   document.getElementById("tishi").style.display = "block";
		   document.getElementById("below").style.display = "block";
   }
   if(flag == 8) {
		  document.getElementById("tishi").style.display = "none";
		  document.getElementById("below").style.display = "none";
  }  
}


//返回顶部
$(document).ready(function(){	
	
	// back-to-top
	var $back = $("<div class='back-to-top' id='back-to-top'><a href='javascript:void(0);'>Back to Top</a></div>");
	$back.appendTo(".wrap");

	$(window).scroll(function(){
		if ($(window).scrollTop()>100){
			$(".back-to-top").fadeIn(100);
		}else{
			$(".back-to-top").fadeOut(100);
		}
	});	
	$(".back-to-top").click(function(){
		$('body,html').animate({scrollTop:0},100);
		$("#sideFix li.nav-item").removeClass("current");
		return false;
	});
	
	// sideFix Button	
	$("#sideFix li.nav-item").click(function(){
		$("#sideFix li.nav-item").removeClass("current");
		$(this).addClass("current");
	});
	
	// ui-tab
	$(".ui-tab").each(function(){
		var $tabNav = $(this).find(".ui-tab-nav li");
		var $tabBox = $(this).find(".ui-tab-item");
		
		$(this).find(".ui-tab-nav li:first").addClass("current"); ;
		$(this).find(".ui-tab-item:first").addClass("ui-tab-item-current"); 
		
		$tabNav.mouseover(function(){ 
			var index = $(this).index();
			$tabBox.eq(index).addClass("ui-tab-item-current")
				   .siblings().removeClass("ui-tab-item-current");
			$(this).addClass("current")
			       .siblings().removeClass("current"); 
		});
	}); 
	
});


//fixed
jQuery.fn.smartFloat = function() {
var position = function(element) {
	var top = element.position().top, pos = element.css("position");
	jQuery(window).scroll(function() {
		var scrolls = jQuery(this).scrollTop();
		if (scrolls > top) {
			if (window.XMLHttpRequest) {
				element.css({
					position: "fixed",
					top:0
				});	
			} else {
				element.css({
					top: scrolls
				});	
			}
		}else {
			element.css({
				position: pos,
				top: top
			});	
		}
	});
};
return jQuery(this).each(function() {
	position(jQuery(this));						 
});
};

