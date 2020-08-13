// JavaScript Document

	function resize_ka_height(){
		var clienW =($(window).width()); //浏览器时下窗口可视区域宽度 
		if (clienW >= 1280 && $(".kc_list").hasClass('full')) {
				$(".tika_con").css("max-height","1079px");
				$(".ti_con").css("min-height","873px");
				$(".liucheng").css("width","700px");
			}else {
				$(".tika_con").css("max-height","772px");
				$(".ti_con").css("min-height","863px");	
				$(".liucheng").css("width","610px");
				}
		}
	$(document).ready(function(){
		resize_ka_height();
		})
	$(window).resize(function(){
		setTimeout(resize_ka_height,500);
	})
	
	function adjust_hei(num){
		var clienW =($(window).width()); //浏览器时下窗口可视区域宽度 
		if(num==0 && clienW >= 1280){
			$(".tika_con").css("max-height","1079px");
			$(".ti_con").css("min-height","873px");
			$(".liucheng").css("width","700px");	
		}else {
			$(".tika_con").css("max-height","772px");
			$(".ti_con").css("min-height","863px");	
			$(".liucheng").css("width","610px");
			}
	}
	
	function hid_tree(num){
		if(num==0){
			$(".dtree").hide();
			$(".kc_list").addClass('full');
			$(".kc_con").css('background-image','none');
			$(".rightfloat").fadeIn("200");	
			var clienW =($(window).width()); //浏览器时下窗口可视区域宽度 
			if (clienW >= 1280) {
					$(".tika_con").css("max-height","1079px");
					$(".ti_con").css("min-height","873px");
					$(".liucheng").css("width","700px");
				}else {
					$(".tika_con").css("max-height","772px");
					$(".ti_con").css("min-height","863px");
					$(".liucheng").css("width","610px");	
					}
		}
	 else if(num==1){
			$(".dtree").show();
			$(".kc_list").removeClass('full');
			$(".kc_con").css('background-image','url(../public/images/bg_list.png)');
			$(".rightfloat").fadeOut("200");
			
		}
		
	}
	
	$("#fill").focus(function(){
		$(this).animate( { height: "70px"}, 400);
			})
		
	$("#fill").blur(function(){
		$(this).animate( { height: "40px"}, 400);
			})