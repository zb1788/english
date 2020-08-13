$("#kf_btn").toggle(function(){
	$(".kfQQ").animate({right: '-5px'}, "slow");
	$("#kf_btn").removeClass("close_2");
	},function(){
	$(".kfQQ").animate({right: '-126px'}, "slow");
	$("#kf_btn").addClass("close_2");
  }
)
function goTop2() {
$('html, body').stop().animate({'scrollTop': 0});
}

window.onscroll=function(){
	var scrollTop =$(document).scrollTop();
	viewHeight = $(window).height();
	//alert(viewHeight); 
	if(scrollTop > 100){
		$("#goTop").fadeIn(300);
		}else {
		$("#goTop").fadeOut(300);
			}
	if($.browser.msie) {
	if($.browser.version == '6.0'){
		var top = viewHeight + scrollTop -847;
		$(".kfQQ").css('top', top + 'px');
	}
  }
}