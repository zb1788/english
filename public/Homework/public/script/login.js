// JavaScript Document

	$(".tab a").click(function(){
		$(this).siblings().removeClass();
		$(this).addClass("sel");
		$(".mt20").addClass("qk2");
		$(".greenBtn").fadeIn(100);		
		})
		
	$(".tab a:eq(2)").click(function(){
		$(".mt20").removeClass("qk2");
		$(".greenBtn").hide();		
		})
	
	function focuss(t){
		var a=$(t).val();
		//alert(a);
		if (a =='用户名' || a=='验证码'){
			$(t).val("");
			};
		$(t).css('color','#333');
		$(t).parent().removeClass();
		$(t).parent().addClass("bor_hover");	
		}
		
	function blurr(t){
		var c=$(t).attr('id');
		var a=$(t).val();
		if (c=='a2'){
				if (a ==''){
					$(t).val("验证码");
				}
			} else if(c=='a1'){
				if (a ==''){
					$(t).val("用户名");
				}
			}else{
				if (a ==''){
					$(t).val("");
				}
			}
		$(t).css('color','#aaa');
		$(t).parent().removeClass();
		$(t).parent().addClass("bor");	
		}
		
		
/*返回顶部按钮*/
	
(function($) {
$.scrollBtn = function(options) {
var opts = $.extend({}, $.scrollBtn.defaults, options);
var $scrollBtn = $('<div></div>').css({
bottom: opts.bottom + 'px',
right: opts.right + 'px'
}).addClass('scroll-up')
.attr('title', opts.title)

.click(function() {
$('html, body').animate({scrollTop: 0}, opts.duration);
}).appendTo('body');

$(window).bind('scroll', function() {
var scrollTop = $(document).scrollTop(),
viewHeight = $(window).height();
//alert(scrollTop);
//alert(viewHeight);

if(scrollTop <= opts.showScale) {
if($scrollBtn.is(':visible'))
$scrollBtn.fadeOut(500);
} else {
if($scrollBtn.is(':hidden')) 
$scrollBtn.fadeIn(500);
}

 

if(isIE6()) {
var top = viewHeight + scrollTop - $scrollBtn.outerHeight() - opts.bottom;
$scrollBtn.css('top', top + 'px');
}
});

 

function isIE6() {
if($.browser.msie) {
if($.browser.version == '6.0') return true;
}
}
};
 

/**

* -params 

*  -showScale: scroll down how much to show the scrollup button

*  -right: to right of scrollable container 

*  -bottom: to bottom of scrollable container 

*/

$.scrollBtn.defaults = {
showScale: 100,  
right:10,
bottom:10,
duration:200,
title:'返回顶部'
}
})(jQuery);


$.scrollBtn({
showScale: 200,
bottom:20,
right:20
});

/*切换城市*/
	$("h3.py > a").click(function(){
		var num_1 = $("h3.py > a").index(this);
		$(this).siblings().removeClass();
		$(this).addClass("blue8");
		//alert(num_1);
		$(".allCit > ul > li").removeClass();
		$(".allCit > ul > li:eq("+num_1+")").addClass("blueBg3");
		})

//tab
function Show_Tab_List2(num,active,where){
	for(var i=0;i<num;i++){
		if(i!=active){
			document.getElementById(where+"s_"+i).style.display="none";
			
			
			document.getElementById(where+"Tab_"+i).className="";
			}else{
			document.getElementById(where+"s_"+i).style.display="block";
		
		
			document.getElementById(where+"Tab_"+i).className="cur_hz";
			}
	}
}